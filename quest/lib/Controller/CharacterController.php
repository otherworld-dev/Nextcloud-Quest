<?php
/**
 * @copyright Copyright (c) 2025 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 */

namespace OCA\NextcloudQuest\Controller;

use OCA\NextcloudQuest\Service\CharacterService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;

/**
 * Controller for character customization and progression
 */
class CharacterController extends Controller {
    
    /** @var IUserSession */
    private $userSession;
    
    /** @var CharacterService */
    private $characterService;

    public function __construct(
        $appName,
        IRequest $request,
        IUserSession $userSession,
        CharacterService $characterService = null
    ) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->characterService = $characterService;
    }

    /**
     * Debug endpoint to check age system specifically
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function debugAgeSystem() {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse(['error' => 'User not found'], 401);
            }

            $userId = $user->getUID();
            $db = \OC::$server->get(\OCP\IDBConnection::class);
            $questMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\QuestMapper::class);
            $ageMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\CharacterAgeMapper::class);

            // Get user level
            $quest = $questMapper->findByUserId($userId);
            $userLevel = $quest->getLevel();

            // Get all ages from database
            $qb = $db->getQueryBuilder();
            $qb->select('*')
                ->from('ncquest_character_ages')
                ->orderBy('min_level', 'ASC');
            $result = $qb->executeQuery();
            $agesInDb = $result->fetchAll();
            $result->closeCursor();

            // Get age for user's level
            $currentAge = $ageMapper->getAgeForLevel($userLevel);

            return new JSONResponse([
                'user_level' => $userLevel,
                'ages_in_database' => $agesInDb,
                'age_for_current_level' => $currentAge ? $currentAge->jsonSerialize() : null,
                'character_current_age_field' => $quest->getCharacterCurrentAge()
            ]);

        } catch (\Throwable $e) {
            return new JSONResponse([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Debug endpoint to check character system status
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function debugStatus() {
        $status = [
            'service_available' => $this->characterService !== null,
            'user_logged_in' => $this->userSession->getUser() !== null,
            'tables_check' => [],
            'errors' => []
        ];

        if (!$this->characterService) {
            $status['errors'][] = 'CharacterService not injected';
            return new JSONResponse($status);
        }

        try {
            $db = \OC::$server->get(\OCP\IDBConnection::class);
            $tables = [
                'quest_char_ages',
                'quest_char_items',
                'quest_char_unlocks',
                'quest_char_progress',
                'ncquest_character_ages',
                'ncquest_character_items',
                'ncquest_character_unlocks',
                'ncquest_character_progression',
                'ncquest_users'
            ];

            foreach ($tables as $table) {
                try {
                    $qb = $db->getQueryBuilder();
                    $qb->select($qb->createFunction('COUNT(*)'))
                        ->from($table);
                    $result = $qb->executeQuery();
                    $count = $result->fetchOne();
                    $status['tables_check'][$table] = ['exists' => true, 'rows' => $count];
                } catch (\Exception $e) {
                    $status['tables_check'][$table] = ['exists' => false, 'error' => $e->getMessage()];
                }
            }

            if ($this->userSession->getUser()) {
                $userId = $this->userSession->getUser()->getUID();
                try {
                    $data = $this->characterService->getCharacterData($userId);
                    $status['character_data_test'] = 'SUCCESS';
                } catch (\Throwable $e) {
                    $status['character_data_test'] = 'FAILED';
                    $status['errors'][] = [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ];
                }
            }
        } catch (\Throwable $e) {
            $status['errors'][] = 'Fatal: ' . $e->getMessage();
        }

        return new JSONResponse($status);
    }

    /**
     * Get character data for the current user
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getCharacterData() {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }

            if (!$this->characterService) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Character service not available'
                ], 503);
            }

            $userId = $user->getUID();
            $characterData = $this->characterService->getCharacterData($userId);

            return new JSONResponse([
                'status' => 'success',
                'data' => $characterData
            ]);

        } catch (\Throwable $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to get character data: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Get available character items for customization
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getAvailableItems() {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }
            
            if (!$this->characterService) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Character service not available'
                ], 503);
            }

            $userId = $user->getUID();
            $itemsData = $this->characterService->getAvailableItems($userId);

            return new JSONResponse([
                'status' => 'success',
                'data' => $itemsData
            ]);

        } catch (\Throwable $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to get available items: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get character customization interface data
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getCustomizationData() {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }
            
            if (!$this->characterService) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Character service not available'
                ], 503);
            }

            $userId = $user->getUID();
            $customizationData = $this->characterService->getCustomizationData($userId);

            return new JSONResponse([
                'status' => 'success',
                'data' => $customizationData
            ]);

        } catch (\Throwable $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to get customization data: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Update character appearance
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param string $clothing
     * @param string $weapon
     * @param string $accessory
     * @param string $headgear
     * @return JSONResponse
     */
    public function updateAppearance($clothing = null, $weapon = null, $accessory = null, $headgear = null) {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }
            
            if (!$this->characterService) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Character service not available'
                ], 503);
            }

            $userId = $user->getUID();
            
            // Build appearance array from provided parameters
            $appearance = [];
            if ($clothing !== null) $appearance['clothing'] = $clothing;
            if ($weapon !== null) $appearance['weapon'] = $weapon;
            if ($accessory !== null) $appearance['accessory'] = $accessory;
            if ($headgear !== null) $appearance['headgear'] = $headgear;

            if (empty($appearance)) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'No appearance data provided'
                ], 400);
            }

            $result = $this->characterService->updateCharacterAppearance($userId, $appearance);

            if ($result['success']) {
                return new JSONResponse([
                    'status' => 'success',
                    'data' => $result
                ]);
            } else {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => $result['error'] ?? 'Failed to update appearance'
                ], 400);
            }

        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to update appearance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Equip a specific item
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param string $itemKey
     * @return JSONResponse
     */
    public function equipItem(string $itemKey) {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }
            
            if (!$this->characterService) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Character service not available'
                ], 503);
            }

            $userId = $user->getUID();
            
            // First get the item to determine its type
            try {
                $itemMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\CharacterItemMapper::class);
                $item = $itemMapper->findByKey($itemKey);
                $itemType = $item->getItemType();
            } catch (\Exception $e) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Item not found'
                ], 404);
            }
            
            // Update appearance with the item in the correct slot
            $appearance = [$itemType => $itemKey];
            $result = $this->characterService->updateCharacterAppearance($userId, $appearance);

            if ($result['success']) {
                return new JSONResponse([
                    'status' => 'success',
                    'data' => [
                        'item_key' => $itemKey,
                        'item_type' => $itemType,
                        'equipped' => true,
                        'appearance' => $result['appearance']
                    ]
                ]);
            } else {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => $result['error'] ?? 'Failed to equip item'
                ], 400);
            }

        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to equip item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unequip an item from a specific slot
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param string $slot
     * @return JSONResponse
     */
    public function unequipItem(string $slot) {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }
            
            if (!$this->characterService) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Character service not available'
                ], 503);
            }

            $validSlots = ['clothing', 'weapon', 'accessory', 'headgear'];
            if (!in_array($slot, $validSlots)) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Invalid equipment slot'
                ], 400);
            }

            $userId = $user->getUID();
            
            // Update appearance to remove the item (set to empty string)
            $appearance = [$slot => ''];
            $result = $this->characterService->updateCharacterAppearance($userId, $appearance);

            if ($result['success']) {
                return new JSONResponse([
                    'status' => 'success',
                    'data' => [
                        'slot' => $slot,
                        'equipped' => false,
                        'appearance' => $result['appearance']
                    ]
                ]);
            } else {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => $result['error'] ?? 'Failed to unequip item'
                ], 400);
            }

        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to unequip item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get character ages with progression status
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getAges() {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }

            $userId = $user->getUID();
            
            // Get age mapper and progression mapper
            $ageMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\CharacterAgeMapper::class);
            $progressionMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\CharacterProgressionMapper::class);
            $questMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\QuestMapper::class);
            
            // Get user's current level
            $quest = $questMapper->findByUserId($userId);
            $userLevel = $quest->getLevel();
            
            // Get all ages
            $allAges = $ageMapper->findAllActive();
            $reachedAges = $progressionMapper->getReachedAgeKeys($userId);
            
            $agesData = [];
            foreach ($allAges as $age) {
                $ageData = $age->jsonSerialize();
                $ageData['is_reached'] = in_array($age->getAgeKey(), $reachedAges);
                $ageData['is_current'] = $age->containsLevel($userLevel);
                $ageData['can_reach'] = $userLevel >= $age->getMinLevel();
                $agesData[] = $ageData;
            }

            return new JSONResponse([
                'status' => 'success',
                'data' => $agesData
            ]);

        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to get ages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manually recalculate and update character age based on current level
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function recalculateAge() {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }

            if (!$this->characterService) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'Character service not available'
                ], 503);
            }

            $userId = $user->getUID();
            $questMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\QuestMapper::class);
            $quest = $questMapper->findByUserId($userId);

            $currentLevel = $quest->getLevel();
            $lifetimeXp = $quest->getLifetimeXp();

            // Force age progression check
            $newAge = $this->characterService->checkAgeProgression($userId, $currentLevel, $lifetimeXp);

            return new JSONResponse([
                'status' => 'success',
                'message' => 'Age recalculated successfully',
                'data' => [
                    'current_level' => $currentLevel,
                    'lifetime_xp' => $lifetimeXp,
                    'new_age' => $newAge ? $newAge->jsonSerialize() : null,
                    'character_data' => $this->characterService->getCharacterData($userId)
                ]
            ]);

        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to recalculate age: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get character progression statistics
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getProgressionStats() {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return new JSONResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 401);
            }

            $userId = $user->getUID();
            
            // Get progression mapper and unlock mapper
            $progressionMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\CharacterProgressionMapper::class);
            $unlockMapper = \OC::$server->get(\OCA\NextcloudQuest\Db\CharacterUnlockMapper::class);
            
            $progressionStats = $progressionMapper->getUserProgressionStats($userId);
            $unlockStats = $unlockMapper->getUserUnlockStats($userId);

            return new JSONResponse([
                'status' => 'success',
                'data' => [
                    'progression' => $progressionStats,
                    'unlocks' => $unlockStats
                ]
            ]);

        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to get progression stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forge/craft items — combine 3 items of same rarity to get 1 of next tier
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function craftItem() {
        try {
            $user = $this->userSession->getUser();
            $userId = $user->getUID();
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $itemKey = $input['item_key'] ?? null;

            if (!$itemKey) {
                return new JSONResponse(['status' => 'error', 'message' => 'item_key required'], 400);
            }

            $db = \OC::$server->get(\OCP\IDBConnection::class);

            // Get the item details
            $qb = $db->getQueryBuilder();
            $qb->select('*')->from('quest_char_items')
                ->where($qb->expr()->eq('item_key', $qb->createNamedParameter($itemKey)));
            $result = $qb->executeQuery();
            $item = $result->fetch();
            $result->closeCursor();

            if (!$item) {
                return new JSONResponse(['status' => 'error', 'message' => 'Item not found'], 404);
            }

            // Check user has quantity >= 3
            $qb2 = $db->getQueryBuilder();
            $qb2->select('quantity')->from('quest_char_unlocks')
                ->where($qb2->expr()->eq('user_id', $qb2->createNamedParameter($userId)))
                ->andWhere($qb2->expr()->eq('item_key', $qb2->createNamedParameter($itemKey)));
            $result2 = $qb2->executeQuery();
            $qty = (int)($result2->fetchOne() ?: 0);
            $result2->closeCursor();

            if ($qty < 3) {
                return new JSONResponse(['status' => 'error', 'message' => "Need 3 copies (have $qty)"], 400);
            }

            // Determine next rarity
            $rarityOrder = ['common' => 'rare', 'rare' => 'epic', 'epic' => 'legendary'];
            $currentRarity = strtolower($item['item_rarity']);
            $nextRarity = $rarityOrder[$currentRarity] ?? null;

            if (!$nextRarity) {
                return new JSONResponse(['status' => 'error', 'message' => 'Legendary items cannot be forged further'], 400);
            }

            // Find items of next rarity in same age and type
            $qb3 = $db->getQueryBuilder();
            $qb3->select('item_key', 'item_name', 'item_rarity')->from('quest_char_items')
                ->where($qb3->expr()->eq('age_key', $qb3->createNamedParameter($item['age_key'])))
                ->andWhere($qb3->expr()->eq('item_type', $qb3->createNamedParameter($item['item_type'])))
                ->andWhere($qb3->expr()->eq('item_rarity', $qb3->createNamedParameter($nextRarity)));
            $result3 = $qb3->executeQuery();
            $candidates = $result3->fetchAll();
            $result3->closeCursor();

            if (empty($candidates)) {
                // Fallback: any item of next rarity in same age
                $qb4 = $db->getQueryBuilder();
                $qb4->select('item_key', 'item_name', 'item_rarity')->from('quest_char_items')
                    ->where($qb4->expr()->eq('age_key', $qb4->createNamedParameter($item['age_key'])))
                    ->andWhere($qb4->expr()->eq('item_rarity', $qb4->createNamedParameter($nextRarity)));
                $result4 = $qb4->executeQuery();
                $candidates = $result4->fetchAll();
                $result4->closeCursor();
            }

            if (empty($candidates)) {
                return new JSONResponse(['status' => 'error', 'message' => 'No items of next rarity available'], 400);
            }

            // Pick random result
            $forgedItem = $candidates[array_rand($candidates)];

            // Deduct 3 from source item
            $qb5 = $db->getQueryBuilder();
            $qb5->update('quest_char_unlocks')
                ->set('quantity', $qb5->createFunction('quantity - 3'))
                ->where($qb5->expr()->eq('user_id', $qb5->createNamedParameter($userId)))
                ->andWhere($qb5->expr()->eq('item_key', $qb5->createNamedParameter($itemKey)));
            $qb5->executeStatement();

            // Add/increment the forged item
            $qb6 = $db->getQueryBuilder();
            $qb6->select('id', 'quantity')->from('quest_char_unlocks')
                ->where($qb6->expr()->eq('user_id', $qb6->createNamedParameter($userId)))
                ->andWhere($qb6->expr()->eq('item_key', $qb6->createNamedParameter($forgedItem['item_key'])));
            $result6 = $qb6->executeQuery();
            $existing = $result6->fetch();
            $result6->closeCursor();

            if ($existing) {
                $qb7 = $db->getQueryBuilder();
                $qb7->update('quest_char_unlocks')
                    ->set('quantity', $qb7->createFunction('quantity + 1'))
                    ->where($qb7->expr()->eq('id', $qb7->createNamedParameter($existing['id'], \PDO::PARAM_INT)));
                $qb7->executeStatement();
            } else {
                $qb7 = $db->getQueryBuilder();
                $qb7->insert('quest_char_unlocks')->values([
                    'user_id' => $qb7->createNamedParameter($userId),
                    'item_key' => $qb7->createNamedParameter($forgedItem['item_key']),
                    'unlocked_at' => $qb7->createNamedParameter((new \DateTime())->format('Y-m-d H:i:s')),
                    'unlock_method' => $qb7->createNamedParameter('craft'),
                    'unlock_reason' => $qb7->createNamedParameter('Forged from 3x ' . $item['item_name']),
                    'quantity' => $qb7->createNamedParameter(1, \PDO::PARAM_INT),
                ]);
                $qb7->executeStatement();
            }

            return new JSONResponse([
                'status' => 'success',
                'data' => [
                    'consumed' => ['item_key' => $itemKey, 'item_name' => $item['item_name'], 'count' => 3],
                    'forged' => $forgedItem,
                ],
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get avatar configuration
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getAvatarConfig() {
        try {
            $user = $this->userSession->getUser();
            $userId = $user->getUID();

            $db = \OC::$server->get(\OCP\IDBConnection::class);
            $qb = $db->getQueryBuilder();
            $qb->select('character_appearance_data')
                ->from('ncquest_users')
                ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
            $result = $qb->executeQuery();
            $row = $result->fetchOne();
            $result->closeCursor();

            $config = $row ? json_decode($row, true) : null;
            if (!$config) {
                $config = [
                    'skin_tone' => '3',
                    'hair_style' => 'short',
                    'hair_color' => 'brown',
                    'body_type' => 'default',
                ];
            }

            return new JSONResponse(['status' => 'success', 'data' => $config]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update avatar configuration
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function updateAvatarConfig() {
        try {
            $user = $this->userSession->getUser();
            $userId = $user->getUID();
            $input = json_decode(file_get_contents('php://input'), true) ?? [];

            $config = [
                'skin_tone' => $input['skin_tone'] ?? '3',
                'hair_style' => $input['hair_style'] ?? 'short',
                'hair_color' => $input['hair_color'] ?? 'brown',
                'body_type' => $input['body_type'] ?? 'default',
            ];

            $db = \OC::$server->get(\OCP\IDBConnection::class);
            $qb = $db->getQueryBuilder();
            $qb->update('ncquest_users')
                ->set('character_appearance_data', $qb->createNamedParameter(json_encode($config)))
                ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
            $qb->executeStatement();

            return new JSONResponse(['status' => 'success', 'data' => $config]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}