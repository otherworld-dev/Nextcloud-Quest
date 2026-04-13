<?php

namespace OCA\NextcloudQuest\Service;

use OCA\NextcloudQuest\Db\Epic;
use OCA\NextcloudQuest\Db\EpicMapper;
use OCA\NextcloudQuest\Db\EpicTask;
use OCA\NextcloudQuest\Db\EpicTaskMapper;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

class EpicService {
    private EpicMapper $epicMapper;
    private EpicTaskMapper $epicTaskMapper;
    private IDBConnection $db;
    private LoggerInterface $logger;

    public function __construct(
        EpicMapper $epicMapper,
        EpicTaskMapper $epicTaskMapper,
        IDBConnection $db,
        LoggerInterface $logger
    ) {
        $this->epicMapper = $epicMapper;
        $this->epicTaskMapper = $epicTaskMapper;
        $this->db = $db;
        $this->logger = $logger;
    }

    public function getEpics(string $userId): array {
        $epics = $this->epicMapper->findByUserId($userId);
        $result = [];
        foreach ($epics as $epic) {
            $tasks = $this->epicTaskMapper->findByEpicId($epic->getId());
            $data = $epic->jsonSerialize();
            $data['tasks'] = array_map(fn($t) => $t->jsonSerialize(), $tasks);
            $result[] = $data;
        }
        return $result;
    }

    public function getEpic(int $epicId, string $userId): array {
        $epic = $this->epicMapper->findById($epicId, $userId);
        $tasks = $this->epicTaskMapper->findByEpicId($epicId);
        $data = $epic->jsonSerialize();
        $data['tasks'] = array_map(fn($t) => $t->jsonSerialize(), $tasks);
        return $data;
    }

    public function createEpic(string $userId, string $title, ?string $description = null, ?string $emoji = null, ?string $color = null): Epic {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $epic = new Epic();
        $epic->setUserId($userId);
        $epic->setTitle($title);
        $epic->setDescription($description);
        $epic->setEmoji($emoji);
        $epic->setColor($color);
        $epic->setTier('common');
        $epic->setStatus('active');
        $epic->setCreatedAt($now);
        $epic->setUpdatedAt($now);
        return $this->epicMapper->insert($epic);
    }

    public function updateEpic(int $epicId, string $userId, array $data): Epic {
        $epic = $this->epicMapper->findById($epicId, $userId);
        if (isset($data['title'])) $epic->setTitle($data['title']);
        if (isset($data['description'])) $epic->setDescription($data['description']);
        if (isset($data['emoji'])) $epic->setEmoji($data['emoji']);
        if (isset($data['color'])) $epic->setColor($data['color']);
        $epic->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));
        return $this->epicMapper->update($epic);
    }

    public function deleteEpic(int $epicId, string $userId): void {
        $epic = $this->epicMapper->findById($epicId, $userId);
        $this->epicTaskMapper->deleteByEpicId($epicId);
        $this->epicMapper->delete($epic);
    }

    public function addTask(int $epicId, string $userId, string $taskUid, string $listId, ?string $taskTitle = null): EpicTask {
        // Verify ownership
        $epic = $this->epicMapper->findById($epicId, $userId);

        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $task = new EpicTask();
        $task->setEpicId($epicId);
        $task->setUserId($userId);
        $task->setTaskUid($taskUid);
        $task->setListId($listId);
        $task->setTaskTitle($taskTitle);
        $task->setAddedAt($now);
        $task = $this->epicTaskMapper->insert($task);

        // Update epic counts and tier
        $epic->setTotalTasks($epic->getTotalTasks() + 1);
        $epic->setTier(Epic::tierFromTaskCount($epic->getTotalTasks()));
        $epic->setUpdatedAt($now);
        $this->epicMapper->update($epic);

        return $task;
    }

    public function removeTask(int $epicId, string $userId, string $taskUid, string $listId): void {
        $epic = $this->epicMapper->findById($epicId, $userId);

        $tasks = $this->epicTaskMapper->findByTaskUid($taskUid, $listId, $userId);
        foreach ($tasks as $task) {
            if ($task->getEpicId() === $epicId) {
                $wasCompleted = $task->getIsCompleted();
                $this->epicTaskMapper->delete($task);

                $epic->setTotalTasks(max(0, $epic->getTotalTasks() - 1));
                if ($wasCompleted) {
                    $epic->setCompletedTasks(max(0, $epic->getCompletedTasks() - 1));
                    $epic->setTotalXpEarned(max(0, $epic->getTotalXpEarned() - $task->getXpEarned()));
                }
                $epic->setTier(Epic::tierFromTaskCount($epic->getTotalTasks()));
                $epic->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));
                $this->epicMapper->update($epic);
                return;
            }
        }
    }

    /**
     * Called when a task is completed anywhere. Updates all epics containing this task.
     * Returns array of completed epic data (for notifications).
     */
    public function onTaskCompleted(string $userId, string $taskUid, string $listId, int $xpEarned): array {
        $completedEpics = [];

        $epicTasks = $this->epicTaskMapper->findIncompleteByTaskUid($taskUid, $listId, $userId);
        if (empty($epicTasks)) {
            return [];
        }

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        foreach ($epicTasks as $epicTask) {
            // Mark task complete
            $epicTask->setIsCompleted(1);
            $epicTask->setCompletedAt($now);
            $epicTask->setXpEarned($xpEarned);
            $this->epicTaskMapper->update($epicTask);

            // Update epic counters atomically
            $qb = $this->db->getQueryBuilder();
            $qb->update('ncquest_epics')
                ->set('completed_tasks', $qb->createFunction('completed_tasks + 1'))
                ->set('total_xp_earned', $qb->createFunction('total_xp_earned + ' . (int)$xpEarned))
                ->set('updated_at', $qb->createNamedParameter($now))
                ->where($qb->expr()->eq('id', $qb->createNamedParameter($epicTask->getEpicId(), \PDO::PARAM_INT)));
            $qb->executeStatement();

            // Re-read epic to check completion
            try {
                $epic = $this->epicMapper->findById($epicTask->getEpicId(), $userId);
                if ($epic->getCompletedTasks() >= $epic->getTotalTasks() && $epic->getTotalTasks() > 0 && $epic->getStatus() === 'active') {
                    $completedEpics[] = $this->completeEpic($epic, $userId);
                }
            } catch (\Exception $e) {
                $this->logger->error('Failed to check epic completion', ['error' => $e->getMessage()]);
            }
        }

        return $completedEpics;
    }

    /**
     * Mark epic as completed and award bonus XP.
     */
    private function completeEpic(Epic $epic, string $userId): array {
        $now = new \DateTime();
        $nowStr = $now->format('Y-m-d H:i:s');

        // Base: 2x XP earned from sub-tasks
        $baseBonus = $epic->getTotalXpEarned() * 2;

        // Task count bonus: +10 XP per task in the epic
        $taskCountBonus = $epic->getTotalTasks() * 10;

        // Time bonus: +5% per day active, capped at 100% (30 days)
        $createdAt = new \DateTime($epic->getCreatedAt());
        $daysActive = max(0, (int)$now->diff($createdAt)->days);
        $timeMultiplier = 1 + min($daysActive * 0.05, 1.0);

        // Final bonus
        $bonusXp = (int)(($baseBonus + $taskCountBonus) * $timeMultiplier);

        $epic->setStatus('completed');
        $epic->setCompletedAt($nowStr);
        $epic->setBonusXpAwarded($bonusXp);
        $epic->setUpdatedAt($nowStr);
        $this->epicMapper->update($epic);

        // Award bonus XP to user
        $qb = $this->db->getQueryBuilder();
        $qb->update('ncquest_users')
            ->set('current_xp', $qb->createFunction('current_xp + ' . (int)$bonusXp))
            ->set('lifetime_xp', $qb->createFunction('lifetime_xp + ' . (int)$bonusXp))
            ->set('xp_gained_today', $qb->createFunction('xp_gained_today + ' . (int)$bonusXp))
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $qb->executeStatement();

        $this->logger->info('Epic completed', [
            'epic_id' => $epic->getId(),
            'title' => $epic->getTitle(),
            'bonus_xp' => $bonusXp,
            'base_bonus' => $baseBonus,
            'task_count_bonus' => $taskCountBonus,
            'days_active' => $daysActive,
            'time_multiplier' => $timeMultiplier,
            'user' => $userId,
        ]);

        return [
            'id' => $epic->getId(),
            'title' => $epic->getTitle(),
            'emoji' => $epic->getEmoji(),
            'tier' => $epic->getTier(),
            'total_xp' => $epic->getTotalXpEarned(),
            'bonus_xp' => $bonusXp,
            'breakdown' => [
                'base' => $baseBonus,
                'task_bonus' => $taskCountBonus,
                'days_active' => $daysActive,
                'time_multiplier' => round($timeMultiplier, 2),
            ],
        ];
    }
}
