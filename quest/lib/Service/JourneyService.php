<?php

namespace OCA\NextcloudQuest\Service;

use OCA\NextcloudQuest\Db\CharacterItemMapper;
use OCA\NextcloudQuest\Db\CharacterUnlockMapper;
use OCA\NextcloudQuest\Db\QuestMapper;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

class JourneyService {
    private IDBConnection $db;
    private AdventureThemeService $themeService;
    private CharacterItemMapper $itemMapper;
    private CharacterUnlockMapper $unlockMapper;
    private QuestMapper $questMapper;
    private LoggerInterface $logger;

    private const RARITY_POWER = ['common' => 5, 'rare' => 10, 'epic' => 20, 'legendary' => 40];

    public function __construct(
        IDBConnection $db,
        AdventureThemeService $themeService,
        CharacterItemMapper $itemMapper,
        CharacterUnlockMapper $unlockMapper,
        QuestMapper $questMapper,
        LoggerInterface $logger
    ) {
        $this->db = $db;
        $this->themeService = $themeService;
        $this->itemMapper = $itemMapper;
        $this->unlockMapper = $unlockMapper;
        $this->questMapper = $questMapper;
        $this->logger = $logger;
    }

    public function getJourneyStatus(string $userId): array {
        $journey = $this->getOrCreateJourney($userId);
        $quest = $this->questMapper->findByUserId($userId);
        $ageKey = $this->themeService->getAgeKeyForLevel($quest->getLevel());
        $colors = $this->themeService->getThemeColors($ageKey);

        $ageNames = [
            'stone' => 'Stone Age', 'bronze' => 'Bronze Age', 'iron' => 'Iron Age',
            'medieval' => 'Medieval', 'renaissance' => 'Renaissance', 'industrial' => 'Industrial',
            'modern' => 'Modern', 'digital' => 'Digital', 'space' => 'Space Age',
        ];

        return array_merge($journey, [
            'current_age_name' => $ageNames[$ageKey] ?? $ageKey,
            'theme_colors' => $colors,
            'player_power' => $this->calculatePlayerPower($userId),
            'steps_remaining' => max(0, ($journey['steps_per_encounter'] ?? 3) - ($journey['steps_taken'] ?? 0)),
        ]);
    }

    public function getJourneyLog(string $userId, int $limit = 20, int $offset = 0): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('ncquest_journey_log')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->orderBy('created_at', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $result = $qb->executeQuery();
        $rows = $result->fetchAll();
        $result->closeCursor();

        return array_map(function ($row) {
            $row['encounter_data'] = json_decode($row['encounter_data'] ?? '{}', true);
            $row['rewards'] = json_decode($row['rewards'] ?? '{}', true);
            return $row;
        }, $rows);
    }

    /**
     * Called on every task completion. Returns encounter data if one triggers, null otherwise.
     */
    public function onTaskCompleted(string $userId, int $xpEarned): ?array {
        $journey = $this->getOrCreateJourney($userId);

        // Atomic step increment
        $qb = $this->db->getQueryBuilder();
        $qb->update('ncquest_journey')
            ->set('steps_taken', $qb->createFunction('steps_taken + 1'))
            ->set('total_steps', $qb->createFunction('total_steps + 1'))
            ->set('updated_at', $qb->createNamedParameter((new \DateTime())->format('Y-m-d H:i:s')))
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $qb->executeStatement();

        $newSteps = ($journey['steps_taken'] ?? 0) + 1;
        $stepsNeeded = $journey['steps_per_encounter'] ?? 3;

        if ($newSteps < $stepsNeeded) {
            return null;
        }

        // Encounter triggered — reset counter
        $encountersCompleted = ($journey['encounters_completed'] ?? 0) + 1;
        $qb2 = $this->db->getQueryBuilder();
        $qb2->update('ncquest_journey')
            ->set('steps_taken', $qb2->createNamedParameter(0))
            ->set('encounters_completed', $qb2->createNamedParameter($encountersCompleted))
            ->where($qb2->expr()->eq('user_id', $qb2->createNamedParameter($userId)));
        $qb2->executeStatement();

        // Get player's current age
        try {
            $quest = $this->questMapper->findByUserId($userId);
            $ageKey = $this->themeService->getAgeKeyForLevel($quest->getLevel());
        } catch (\Exception $e) {
            $ageKey = 'stone';
        }

        // Update age on journey record
        $qb3 = $this->db->getQueryBuilder();
        $qb3->update('ncquest_journey')
            ->set('current_age_key', $qb3->createNamedParameter($ageKey))
            ->where($qb3->expr()->eq('user_id', $qb3->createNamedParameter($userId)));
        $qb3->executeStatement();

        // Roll encounter type
        $type = $this->rollEncounterType($encountersCompleted);
        $prestige = $journey['prestige_level'] ?? 0;

        // Resolve
        switch ($type) {
            case 'boss':
                $result = $this->resolveBoss($userId, $ageKey, $prestige);
                break;
            case 'mini_boss':
                $result = $this->resolveMiniBoss($userId, $ageKey, $prestige);
                break;
            case 'treasure':
                $result = $this->resolveTreasure($userId, $ageKey, $prestige);
                break;
            case 'event':
                $result = $this->resolveEvent($userId, $ageKey);
                break;
            default:
                $result = $this->resolveBattle($userId, $ageKey, $prestige);
                break;
        }

        // Log encounter
        $this->logEncounter($userId, $type, $ageKey, $result);

        // Check prestige
        $journey = $this->getOrCreateJourney($userId);
        if ($this->checkPrestige($journey)) {
            $result['prestige_up'] = true;
            $result['new_prestige'] = ($journey['prestige_level'] ?? 0) + 1;
        }

        $result['encounter_type'] = $type;
        $result['age_key'] = $ageKey;

        $ageNames = [
            'stone' => 'Stone Age', 'bronze' => 'Bronze Age', 'iron' => 'Iron Age',
            'medieval' => 'Medieval', 'renaissance' => 'Renaissance', 'industrial' => 'Industrial',
            'modern' => 'Modern', 'digital' => 'Digital', 'space' => 'Space Age',
        ];
        $result['age_name'] = $ageNames[$ageKey] ?? $ageKey;

        return $result;
    }

    private function rollEncounterType(int $encountersCompleted): string {
        // Force boss every 20 encounters
        if ($encountersCompleted > 0 && $encountersCompleted % 20 === 0) {
            return 'boss';
        }
        // Force mini-boss every 10 encounters (but not on boss encounters)
        if ($encountersCompleted > 0 && $encountersCompleted % 10 === 0) {
            return 'mini_boss';
        }

        $roll = mt_rand(1, 100);
        if ($roll <= 55) return 'battle';
        if ($roll <= 75) return 'treasure';
        if ($roll <= 92) return 'event';
        if ($roll <= 97) return 'mini_boss';
        return 'boss';
    }

    private function resolveBattle(string $userId, string $ageKey, int $prestige): array {
        $enemy = $this->themeService->getRandomEnemy($ageKey);
        $enemyHealth = (int)($enemy['health'] * (1 + min($prestige, 10) * 0.5));

        $playerPower = $this->calculatePlayerPower($userId);
        $winChance = min(95, max(30, ($playerPower / ($playerPower + $enemyHealth)) * 100));
        $won = mt_rand(1, 100) <= $winChance;

        $rewards = [];
        if ($won) {
            $rewards['xp'] = $enemy['xp'] ?? 25;
            $this->awardXP($userId, $rewards['xp']);
            $this->updateJourneyStat($userId, 'battles_won');

            // Roll loot
            $loot = $this->rollLootDrop($ageKey, $prestige);
            if ($loot) {
                $rewards['item_key'] = $loot['item_key'];
                $rewards['item_name'] = $loot['item_name'];
                $rewards['item_rarity'] = $loot['item_rarity'];
                $this->unlockItem($userId, $loot['item_key']);
            }
        } else {
            $penalty = mt_rand(10, 20);
            $rewards['health_change'] = -$penalty;
            $this->applyHealthPenalty($userId, $penalty);
            $this->updateJourneyStat($userId, 'battles_lost');
        }

        return [
            'encounter_name' => $enemy['name'],
            'outcome' => $won ? 'win' : 'lose',
            'rewards' => $rewards,
            'enemy' => $enemy,
            'player_power' => $playerPower,
            'win_chance' => round($winChance),
        ];
    }

    private function resolveBoss(string $userId, string $ageKey, int $prestige): array {
        $boss = $this->themeService->getBoss($ageKey);
        $bossHealth = (int)($boss['health'] * (1 + min($prestige, 10) * 0.5));

        $playerPower = $this->calculatePlayerPower($userId);
        $winChance = min(80, max(20, ($playerPower / ($playerPower + $bossHealth)) * 100));
        $won = mt_rand(1, 100) <= $winChance;

        $rewards = [];
        if ($won) {
            $rewards['xp'] = $boss['xp'] ?? 100;
            $this->awardXP($userId, $rewards['xp']);
            $this->updateJourneyStat($userId, 'bosses_defeated');
            $this->updateJourneyStat($userId, 'battles_won');

            // Guaranteed rare+ loot
            $loot = $this->rollLootDrop($ageKey, $prestige, true);
            if ($loot) {
                $rewards['item_key'] = $loot['item_key'];
                $rewards['item_name'] = $loot['item_name'];
                $rewards['item_rarity'] = $loot['item_rarity'];
                $this->unlockItem($userId, $loot['item_key']);
            }
        } else {
            $penalty = mt_rand(15, 30);
            $rewards['health_change'] = -$penalty;
            $this->applyHealthPenalty($userId, $penalty);
            $this->updateJourneyStat($userId, 'battles_lost');
        }

        return [
            'encounter_name' => $boss['name'],
            'outcome' => $won ? 'win' : 'lose',
            'rewards' => $rewards,
            'enemy' => $boss,
            'is_boss' => true,
            'player_power' => $playerPower,
            'win_chance' => round($winChance),
        ];
    }

    private function resolveMiniBoss(string $userId, string $ageKey, int $prestige): array {
        $miniBoss = $this->themeService->getMiniBoss($ageKey);
        $bossHealth = (int)($miniBoss['health'] * (1 + min($prestige, 10) * 0.5));

        $playerPower = $this->calculatePlayerPower($userId);
        $winChance = min(85, max(25, ($playerPower / ($playerPower + $bossHealth)) * 100));
        $won = mt_rand(1, 100) <= $winChance;

        $rewards = [];
        if ($won) {
            $rewards['xp'] = $miniBoss['xp'] ?? 60;
            $this->awardXP($userId, $rewards['xp']);
            $this->updateJourneyStat($userId, 'battles_won');

            // Good chance of loot
            $loot = $this->rollLootDrop($ageKey, $prestige, mt_rand(1, 100) <= 60);
            if ($loot) {
                $rewards['item_key'] = $loot['item_key'];
                $rewards['item_name'] = $loot['item_name'];
                $rewards['item_rarity'] = $loot['item_rarity'];
                $this->unlockItem($userId, $loot['item_key']);
            }
        } else {
            $penalty = mt_rand(12, 25);
            $rewards['health_change'] = -$penalty;
            $this->applyHealthPenalty($userId, $penalty);
            $this->updateJourneyStat($userId, 'battles_lost');
        }

        return [
            'encounter_name' => $miniBoss['name'],
            'outcome' => $won ? 'win' : 'lose',
            'rewards' => $rewards,
            'enemy' => $miniBoss,
            'is_mini_boss' => true,
            'player_power' => $playerPower,
            'win_chance' => round($winChance),
        ];
    }

    private function resolveTreasure(string $userId, string $ageKey, int $prestige): array {
        $loot = $this->rollLootDrop($ageKey, $prestige);
        $this->updateJourneyStat($userId, 'treasures_found');

        $rewards = [];
        if ($loot) {
            $rewards['item_key'] = $loot['item_key'];
            $rewards['item_name'] = $loot['item_name'];
            $rewards['item_rarity'] = $loot['item_rarity'];
            $alreadyOwned = $this->unlockItem($userId, $loot['item_key']);
            if ($alreadyOwned) {
                $bonusXp = 15;
                $rewards['xp'] = $bonusXp;
                $rewards['already_owned'] = true;
                $this->awardXP($userId, $bonusXp);
            }
        }

        return [
            'encounter_name' => $loot ? $loot['item_name'] : 'Empty Chest',
            'outcome' => 'found',
            'rewards' => $rewards,
        ];
    }

    private function resolveEvent(string $userId, string $ageKey): array {
        $event = $this->themeService->getRandomEvent($ageKey);
        $this->updateJourneyStat($userId, 'events_completed');

        $rewards = [];
        if (isset($event['reward'])) {
            $r = $event['reward'];
            if (isset($r['gold'])) $rewards['gold'] = $r['gold'];
            if (isset($r['xp'])) {
                $rewards['xp'] = $r['xp'];
                $this->awardXP($userId, $r['xp']);
            }
            if (isset($r['health'])) {
                $rewards['health_change'] = $r['health'];
                if ($r['health'] > 0) {
                    $this->healPlayer($userId, $r['health']);
                }
            }
        }

        return [
            'encounter_name' => $event['name'] ?? 'Mysterious Event',
            'outcome' => 'resolved',
            'rewards' => $rewards,
        ];
    }

    private function rollLootDrop(string $ageKey, int $prestige, bool $guaranteedRare = false): ?array {
        // Roll rarity
        $rareBonus = min($prestige * 10, 30);
        $roll = mt_rand(1, 100);
        if ($guaranteedRare) {
            $rarity = $roll <= (12 + $rareBonus) ? 'legendary' : ($roll <= (37 + $rareBonus) ? 'epic' : 'rare');
        } else {
            if ($roll <= 3) $rarity = 'legendary';
            elseif ($roll <= 15 + $rareBonus) $rarity = 'epic';
            elseif ($roll <= 40 + $rareBonus) $rarity = 'rare';
            else $rarity = 'common';
        }

        // Query items for this age+rarity
        try {
            $items = $this->itemMapper->findByAge($ageKey);
            $filtered = array_filter($items, fn($item) => strtolower($item->getItemRarity()) === $rarity);

            // Fallback to lower rarity if empty
            if (empty($filtered)) {
                $filtered = array_filter($items, fn($item) => strtolower($item->getItemRarity()) === 'common');
            }
            if (empty($filtered)) {
                $filtered = $items;
            }
            if (empty($filtered)) {
                return null;
            }

            $filtered = array_values($filtered);
            $item = $filtered[array_rand($filtered)];

            return [
                'item_key' => $item->getItemKey(),
                'item_name' => $item->getItemName(),
                'item_rarity' => $item->getItemRarity(),
            ];
        } catch (\Exception $e) {
            $this->logger->warning('Loot drop failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function calculatePlayerPower(string $userId): int {
        try {
            $quest = $this->questMapper->findByUserId($userId);
            $power = $quest->getLevel() * 10;

            $slots = [
                $quest->getCharacterEquippedClothing(),
                $quest->getCharacterEquippedWeapon(),
                $quest->getCharacterEquippedAccessory(),
                $quest->getCharacterEquippedHeadgear(),
            ];

            foreach ($slots as $itemKey) {
                if ($itemKey) {
                    try {
                        $item = $this->itemMapper->findByKey($itemKey);
                        $rarity = strtolower($item->getItemRarity());
                        $power += self::RARITY_POWER[$rarity] ?? 0;
                    } catch (\Exception $e) {
                        // Item not found, skip
                    }
                }
            }

            return $power;
        } catch (\Exception $e) {
            return 10;
        }
    }

    private function getOrCreateJourney(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('ncquest_journey')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $result = $qb->executeQuery();
        $row = $result->fetch();
        $result->closeCursor();

        if ($row) return $row;

        // Create default journey
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $qb2 = $this->db->getQueryBuilder();
        $qb2->insert('ncquest_journey')
            ->values([
                'user_id' => $qb2->createNamedParameter($userId),
                'current_age_key' => $qb2->createNamedParameter('stone'),
                'steps_taken' => $qb2->createNamedParameter(0),
                'total_steps' => $qb2->createNamedParameter(0),
                'steps_per_encounter' => $qb2->createNamedParameter(3),
                'encounters_completed' => $qb2->createNamedParameter(0),
                'bosses_defeated' => $qb2->createNamedParameter(0),
                'battles_won' => $qb2->createNamedParameter(0),
                'battles_lost' => $qb2->createNamedParameter(0),
                'treasures_found' => $qb2->createNamedParameter(0),
                'events_completed' => $qb2->createNamedParameter(0),
                'prestige_level' => $qb2->createNamedParameter(0),
                'updated_at' => $qb2->createNamedParameter($now),
            ]);
        $qb2->executeStatement();

        return $this->getOrCreateJourney($userId);
    }

    private function logEncounter(string $userId, string $type, string $ageKey, array $data): void {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $qb = $this->db->getQueryBuilder();
        $qb->insert('ncquest_journey_log')
            ->values([
                'user_id' => $qb->createNamedParameter($userId),
                'encounter_type' => $qb->createNamedParameter($type),
                'age_key' => $qb->createNamedParameter($ageKey),
                'encounter_data' => $qb->createNamedParameter(json_encode([
                    'encounter_name' => $data['encounter_name'] ?? '',
                    'enemy' => $data['enemy'] ?? null,
                    'is_boss' => $data['is_boss'] ?? false,
                ])),
                'outcome' => $qb->createNamedParameter($data['outcome'] ?? 'unknown'),
                'rewards' => $qb->createNamedParameter(json_encode($data['rewards'] ?? [])),
                'created_at' => $qb->createNamedParameter($now),
            ]);
        $qb->executeStatement();
    }

    private function updateJourneyStat(string $userId, string $field): void {
        $allowed = ['battles_won', 'battles_lost', 'treasures_found', 'events_completed', 'bosses_defeated'];
        if (!in_array($field, $allowed)) return;

        $qb = $this->db->getQueryBuilder();
        $qb->update('ncquest_journey')
            ->set($field, $qb->createFunction("$field + 1"))
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $qb->executeStatement();
    }

    private function awardXP(string $userId, int $xp): void {
        $qb = $this->db->getQueryBuilder();
        $qb->update('ncquest_users')
            ->set('current_xp', $qb->createFunction('current_xp + ' . (int)$xp))
            ->set('lifetime_xp', $qb->createFunction('lifetime_xp + ' . (int)$xp))
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $qb->executeStatement();
    }

    private function applyHealthPenalty(string $userId, int $amount): void {
        $qb = $this->db->getQueryBuilder();
        $qb->update('ncquest_users')
            ->set('current_health', $qb->createFunction('GREATEST(0, current_health - ' . (int)$amount . ')'))
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $qb->executeStatement();
    }

    private function healPlayer(string $userId, int $amount): void {
        $qb = $this->db->getQueryBuilder();
        $qb->update('ncquest_users')
            ->set('current_health', $qb->createFunction('LEAST(max_health, current_health + ' . (int)$amount . ')'))
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        $qb->executeStatement();
    }

    /**
     * @return bool true if item was already owned (quantity incremented)
     */
    private function unlockItem(string $userId, string $itemKey): bool {
        try {
            if ($this->unlockMapper->hasUnlocked($userId, $itemKey)) {
                // Increment quantity for duplicates (used in crafting)
                $qb = $this->db->getQueryBuilder();
                $qb->update('quest_char_unlocks')
                    ->set('quantity', $qb->createFunction('quantity + 1'))
                    ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
                    ->andWhere($qb->expr()->eq('item_key', $qb->createNamedParameter($itemKey)));
                $qb->executeStatement();
                return true;
            }
            $this->unlockMapper->createUnlock($userId, $itemKey, 'quest', 'Journey encounter');
            return false;
        } catch (\Exception $e) {
            return true;
        }
    }

    private function checkPrestige(array $journey): bool {
        $bossesNeeded = 9 * (($journey['prestige_level'] ?? 0) + 1);
        if (($journey['bosses_defeated'] ?? 0) >= $bossesNeeded) {
            $qb = $this->db->getQueryBuilder();
            $qb->update('ncquest_journey')
                ->set('prestige_level', $qb->createFunction('prestige_level + 1'))
                ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($journey['user_id'])));
            $qb->executeStatement();
            return true;
        }
        return false;
    }
}
