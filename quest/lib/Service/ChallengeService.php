<?php

namespace OCA\NextcloudQuest\Service;

use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

class ChallengeService {
    private IDBConnection $db;
    private LoggerInterface $logger;

    private const DAILY_TEMPLATES = [
        ['type' => 'tasks_today', 'title' => 'Task Warrior', 'desc' => 'Complete %d tasks today', 'targets' => [3, 5, 8, 10], 'xp' => [30, 50, 80, 120]],
        ['type' => 'high_priority', 'title' => 'High Stakes', 'desc' => 'Complete %d high priority tasks today', 'targets' => [1, 2, 3, 5], 'xp' => [25, 50, 75, 125]],
        ['type' => 'speed_hour', 'title' => 'Speed Run', 'desc' => 'Complete %d tasks in one hour', 'targets' => [3, 5, 7, 10], 'xp' => [40, 60, 90, 150]],
        ['type' => 'morning_tasks', 'title' => 'Early Bird', 'desc' => 'Complete %d tasks before noon', 'targets' => [2, 3, 5], 'xp' => [30, 50, 80]],
        ['type' => 'evening_tasks', 'title' => 'Night Shift', 'desc' => 'Complete %d tasks after 6 PM', 'targets' => [2, 3, 5], 'xp' => [30, 50, 80]],
        ['type' => 'any_priority', 'title' => 'Priority Mix', 'desc' => 'Complete tasks of all 3 priorities today', 'targets' => [1], 'xp' => [60]],
        ['type' => 'multi_list', 'title' => 'List Hopper', 'desc' => 'Complete tasks from %d different lists today', 'targets' => [2, 3], 'xp' => [40, 70]],
        ['type' => 'no_low_priority', 'title' => 'High Standards', 'desc' => 'Complete %d tasks without any low priority', 'targets' => [3, 5], 'xp' => [40, 70]],
        ['type' => 'first_hour', 'title' => 'Power Hour', 'desc' => 'Complete %d tasks in your first hour of the day', 'targets' => [3, 5], 'xp' => [40, 75]],
        ['type' => 'all_lists', 'title' => 'List Explorer', 'desc' => 'Complete tasks from %d different lists', 'targets' => [2, 3, 4], 'xp' => [35, 55, 80]],
        ['type' => 'before_lunch', 'title' => 'Morning Sprint', 'desc' => 'Complete %d tasks before 1 PM', 'targets' => [3, 5, 8], 'xp' => [30, 50, 90]],
        ['type' => 'after_dinner', 'title' => 'Evening Push', 'desc' => 'Complete %d tasks after 7 PM', 'targets' => [2, 3, 5], 'xp' => [30, 50, 80]],
        ['type' => 'zero_breaks', 'title' => 'Non-Stop', 'desc' => 'Complete %d tasks with no more than 10 min between each', 'targets' => [3, 5], 'xp' => [50, 90]],
    ];

    private const WEEKLY_TEMPLATES = [
        ['type' => 'tasks_week', 'title' => 'Weekly Grind', 'desc' => 'Complete %d tasks this week', 'targets' => [15, 25, 40, 60], 'xp' => [75, 125, 200, 300]],
        ['type' => 'streak_maintain', 'title' => 'Streak Guardian', 'desc' => 'Maintain a %d-day streak this week', 'targets' => [3, 5, 7], 'xp' => [60, 100, 175]],
        ['type' => 'daily_minimum', 'title' => 'Consistency', 'desc' => 'Complete at least %d tasks every day for 5 days', 'targets' => [1, 3, 5], 'xp' => [50, 100, 175]],
        ['type' => 'weekend_tasks', 'title' => 'Weekend Warrior', 'desc' => 'Complete %d tasks over the weekend', 'targets' => [5, 10, 15], 'xp' => [50, 100, 150]],
        ['type' => 'total_xp_week', 'title' => 'XP Hunter', 'desc' => 'Earn %d XP this week', 'targets' => [200, 500, 1000], 'xp' => [50, 100, 200]],
        ['type' => 'perfect_weekdays', 'title' => 'Weekday Warrior', 'desc' => 'Complete tasks every weekday (Mon-Fri)', 'targets' => [5], 'xp' => [150]],
        ['type' => 'high_priority_week', 'title' => 'Priority Focus', 'desc' => 'Complete %d high priority tasks this week', 'targets' => [5, 10, 20], 'xp' => [75, 150, 250]],
        ['type' => 'journey_encounters', 'title' => 'Adventurer', 'desc' => 'Trigger %d journey encounters this week', 'targets' => [5, 10], 'xp' => [75, 150]],
        ['type' => 'craft_items', 'title' => 'Master Smith', 'desc' => 'Craft %d items this week', 'targets' => [1, 3], 'xp' => [100, 200]],
        ['type' => 'achievement_hunter', 'title' => 'Achievement Hunter', 'desc' => 'Unlock %d achievements this week', 'targets' => [3, 5, 10], 'xp' => [75, 125, 250]],
    ];

    public function __construct(IDBConnection $db, LoggerInterface $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * Get active challenges for a user, generating new ones if expired.
     */
    public function getChallenges(string $userId): array {
        $this->generateIfNeeded($userId);

        $now = new \DateTime();
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('ncquest_challenges')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->andWhere($qb->expr()->gt('expires_at', $qb->createNamedParameter($now, 'datetime')))
            ->orderBy('period', 'ASC')
            ->addOrderBy('created_at', 'ASC');
        $result = $qb->executeQuery();
        $rows = $result->fetchAll();
        $result->closeCursor();

        return array_map(function ($row) {
            $row['progress'] = (int)$row['progress'];
            $row['target'] = (int)$row['target'];
            $row['xp_reward'] = (int)$row['xp_reward'];
            $row['is_completed'] = (int)$row['is_completed'];
            $row['is_claimed'] = (int)$row['is_claimed'];
            $row['percentage'] = $row['target'] > 0 ? min(100, round(($row['progress'] / $row['target']) * 100)) : 0;
            return $row;
        }, $rows);
    }

    /**
     * Called on task completion. Updates progress on relevant challenges.
     * Returns array of completed challenge data for notifications.
     */
    public function onTaskCompleted(string $userId, string $priority, int $hour): array {
        $challenges = $this->getChallenges($userId);
        $completed = [];

        foreach ($challenges as $ch) {
            if ($ch['is_completed']) continue;

            $shouldIncrement = false;

            switch ($ch['challenge_type']) {
                case 'tasks_today':
                case 'tasks_week':
                    $shouldIncrement = true;
                    break;
                case 'high_priority':
                    $shouldIncrement = ($priority === 'high');
                    break;
                case 'morning_tasks':
                    $shouldIncrement = ($hour < 12);
                    break;
                case 'evening_tasks':
                    $shouldIncrement = ($hour >= 18);
                    break;
                case 'speed_hour':
                    // Checked separately — need recent history
                    break;
                case 'any_priority':
                    // Checked separately — need today's priority mix
                    break;
                case 'multi_list':
                    // Checked separately — need today's list variety
                    break;
                case 'streak_maintain':
                case 'daily_minimum':
                case 'weekend_tasks':
                case 'total_xp_week':
                    $shouldIncrement = true;
                    break;
            }

            if ($shouldIncrement) {
                $newProgress = $ch['progress'] + 1;
                $isNowComplete = $newProgress >= $ch['target'];

                $qb = $this->db->getQueryBuilder();
                $qb->update('ncquest_challenges')
                    ->set('progress', $qb->createNamedParameter($newProgress))
                    ->set('is_completed', $qb->createNamedParameter($isNowComplete ? 1 : 0));
                if ($isNowComplete) {
                    $qb->set('is_claimed', $qb->createNamedParameter(1));
                }
                $qb->where($qb->expr()->eq('id', $qb->createNamedParameter($ch['id'], \PDO::PARAM_INT)));
                $qb->executeStatement();

                if ($isNowComplete) {
                    // Award XP
                    $this->awardXP($userId, $ch['xp_reward']);
                    $completed[] = [
                        'title' => $ch['title'],
                        'xp_reward' => $ch['xp_reward'],
                        'period' => $ch['period'],
                    ];
                }
            }
        }

        return $completed;
    }

    private function generateIfNeeded(string $userId): void {
        $now = new \DateTime();

        // Check if daily challenges exist and are not expired
        $dailyCount = $this->countActiveChallenges($userId, 'daily');
        if ($dailyCount === 0) {
            $this->generateDaily($userId, $now);
        }

        $weeklyCount = $this->countActiveChallenges($userId, 'weekly');
        if ($weeklyCount === 0) {
            $this->generateWeekly($userId, $now);
        }
    }

    private function countActiveChallenges(string $userId, string $period): int {
        $now = new \DateTime();
        $qb = $this->db->getQueryBuilder();
        $qb->select($qb->createFunction('COUNT(*) as cnt'))
            ->from('ncquest_challenges')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->andWhere($qb->expr()->eq('period', $qb->createNamedParameter($period)))
            ->andWhere($qb->expr()->gt('expires_at', $qb->createNamedParameter($now, 'datetime')));
        $result = $qb->executeQuery();
        $count = (int)$result->fetchOne();
        $result->closeCursor();
        return $count;
    }

    private function generateDaily(string $userId, \DateTime $now): void {
        $endOfDay = clone $now;
        $endOfDay->setTime(23, 59, 59);

        // Pick 3 random daily templates
        $templates = self::DAILY_TEMPLATES;
        shuffle($templates);
        $selected = array_slice($templates, 0, 3);

        foreach ($selected as $tpl) {
            $idx = array_rand($tpl['targets']);
            $target = $tpl['targets'][$idx];
            $xp = $tpl['xp'][$idx];
            $desc = sprintf($tpl['desc'], $target);

            $this->insertChallenge($userId, $tpl['type'], 'daily', $tpl['title'], $desc, $target, $xp, $endOfDay, $now);
        }
    }

    private function generateWeekly(string $userId, \DateTime $now): void {
        $endOfWeek = clone $now;
        $daysUntilSunday = 7 - (int)$now->format('N');
        $endOfWeek->modify("+{$daysUntilSunday} days");
        $endOfWeek->setTime(23, 59, 59);

        // Pick 2 random weekly templates
        $templates = self::WEEKLY_TEMPLATES;
        shuffle($templates);
        $selected = array_slice($templates, 0, 2);

        foreach ($selected as $tpl) {
            $idx = array_rand($tpl['targets']);
            $target = $tpl['targets'][$idx];
            $xp = $tpl['xp'][$idx];
            $desc = sprintf($tpl['desc'], $target);

            $this->insertChallenge($userId, $tpl['type'], 'weekly', $tpl['title'], $desc, $target, $xp, $endOfWeek, $now);
        }
    }

    private function insertChallenge(string $userId, string $type, string $period, string $title, string $desc, int $target, int $xp, \DateTime $expires, \DateTime $created): void {
        $qb = $this->db->getQueryBuilder();
        $qb->insert('ncquest_challenges')
            ->values([
                'user_id' => $qb->createNamedParameter($userId),
                'challenge_type' => $qb->createNamedParameter($type),
                'period' => $qb->createNamedParameter($period),
                'title' => $qb->createNamedParameter($title),
                'description' => $qb->createNamedParameter($desc),
                'target' => $qb->createNamedParameter($target, \PDO::PARAM_INT),
                'progress' => $qb->createNamedParameter(0, \PDO::PARAM_INT),
                'xp_reward' => $qb->createNamedParameter($xp, \PDO::PARAM_INT),
                'is_completed' => $qb->createNamedParameter(0, \PDO::PARAM_INT),
                'is_claimed' => $qb->createNamedParameter(0, \PDO::PARAM_INT),
                'expires_at' => $qb->createNamedParameter($expires, 'datetime'),
                'created_at' => $qb->createNamedParameter($created, 'datetime'),
            ]);
        $qb->executeStatement();
    }

    private function awardXP(string $userId, int $xp): void {
        $qb = $this->db->getQueryBuilder();
        $qb->update('ncquest_users')
            ->set('current_xp', $qb->createFunction('current_xp + ' . (int)$xp))
            ->set('lifetime_xp', $qb->createFunction('lifetime_xp + ' . (int)$xp))
            ->set('xp_gained_today', $qb->createFunction('xp_gained_today + ' . (int)$xp))
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $qb->executeStatement();
    }
}
