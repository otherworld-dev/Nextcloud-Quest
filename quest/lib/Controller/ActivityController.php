<?php

namespace OCA\NextcloudQuest\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IDBConnection;
use OCP\IRequest;
use OCP\IUserSession;

class ActivityController extends Controller {
    private IUserSession $userSession;
    private IDBConnection $db;

    public function __construct($appName, IRequest $request, IUserSession $userSession, IDBConnection $db) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->db = $db;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getFeed(): JSONResponse {
        try {
            $userId = $this->userSession->getUser()->getUID();
            $limit = (int)($this->request->getParam('limit', 30));
            $offset = (int)($this->request->getParam('offset', 0));

            $events = [];

            // Task completions from history
            $qb = $this->db->getQueryBuilder();
            $qb->select('task_title', 'xp_earned', 'completed_at')
                ->from('ncquest_history')
                ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
                ->orderBy('completed_at', 'DESC')
                ->setMaxResults(50);
            $result = $qb->executeQuery();
            while ($row = $result->fetch()) {
                $events[] = [
                    'type' => 'task',
                    'icon' => 'task',
                    'title' => 'Completed: ' . ($row['task_title'] ?: 'Untitled task'),
                    'detail' => '+' . $row['xp_earned'] . ' XP',
                    'timestamp' => $row['completed_at'],
                ];
            }
            $result->closeCursor();

            // Journey encounters
            $qb2 = $this->db->getQueryBuilder();
            $qb2->select('encounter_type', 'age_key', 'encounter_data', 'outcome', 'rewards', 'created_at')
                ->from('ncquest_journey_log')
                ->where($qb2->expr()->eq('user_id', $qb2->createNamedParameter($userId)))
                ->orderBy('created_at', 'DESC')
                ->setMaxResults(50);
            $result2 = $qb2->executeQuery();
            while ($row = $result2->fetch()) {
                $data = json_decode($row['encounter_data'] ?? '{}', true);
                $rewards = json_decode($row['rewards'] ?? '{}', true);
                $name = $data['encounter_name'] ?? 'Unknown';
                $iconMap = ['battle' => 'battle', 'boss' => 'boss', 'treasure' => 'treasure', 'event' => 'event'];

                $detail = '';
                if ($row['outcome'] === 'win') $detail = 'Victory! +' . ($rewards['xp'] ?? 0) . ' XP';
                elseif ($row['outcome'] === 'lose') $detail = 'Defeated. ' . ($rewards['health_change'] ?? 0) . ' HP';
                elseif ($row['outcome'] === 'found') $detail = 'Found ' . ($rewards['item_name'] ?? 'item');
                else $detail = 'Resolved';

                $events[] = [
                    'type' => 'journey',
                    'icon' => $iconMap[$row['encounter_type']] ?? 'journey',
                    'title' => $name,
                    'detail' => $detail,
                    'timestamp' => $row['created_at'],
                ];
            }
            $result2->closeCursor();

            // Achievement unlocks
            $qb3 = $this->db->getQueryBuilder();
            $qb3->select('achievement_key', 'achievement_category', 'unlocked_at')
                ->from('ncquest_achievements')
                ->where($qb3->expr()->eq('user_id', $qb3->createNamedParameter($userId)))
                ->orderBy('unlocked_at', 'DESC')
                ->setMaxResults(50);
            $result3 = $qb3->executeQuery();
            $allDefs = \OCA\NextcloudQuest\Service\AchievementDefinitions::getAll();
            while ($row = $result3->fetch()) {
                $def = $allDefs[$row['achievement_key']] ?? [];
                $events[] = [
                    'type' => 'achievement',
                    'icon' => 'achievement',
                    'title' => 'Achievement: ' . ($def['name'] ?? $row['achievement_key']),
                    'detail' => $def['description'] ?? $row['achievement_category'],
                    'timestamp' => $row['unlocked_at'],
                ];
            }
            $result3->closeCursor();

            // Challenge completions
            $qb4 = $this->db->getQueryBuilder();
            $qb4->select('title', 'xp_reward', 'period', 'created_at')
                ->from('ncquest_challenges')
                ->where($qb4->expr()->eq('user_id', $qb4->createNamedParameter($userId)))
                ->andWhere($qb4->expr()->eq('is_completed', $qb4->createNamedParameter(1, \PDO::PARAM_INT)))
                ->orderBy('created_at', 'DESC')
                ->setMaxResults(50);
            $result4 = $qb4->executeQuery();
            while ($row = $result4->fetch()) {
                $events[] = [
                    'type' => 'challenge',
                    'icon' => 'challenge',
                    'title' => 'Challenge: ' . $row['title'],
                    'detail' => '+' . $row['xp_reward'] . ' XP (' . $row['period'] . ')',
                    'timestamp' => $row['created_at'],
                ];
            }
            $result4->closeCursor();

            // Sort all by timestamp DESC
            usort($events, function ($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // Paginate
            $total = count($events);
            $events = array_slice($events, $offset, $limit);

            return new JSONResponse([
                'status' => 'success',
                'data' => $events,
                'total' => $total,
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
