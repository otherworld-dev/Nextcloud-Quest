<?php
/**
 * @copyright Copyright (c) 2026 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 */

namespace OCA\NextcloudQuest\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Additional Character Items Seed Data Migration
 * Adds ~60 new equipment items across all ages
 */
class Version1021Date20260413180000 extends SimpleMigrationStep {

    public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
        $connection = \OC::$server->get(OCPIDBConnection::class);
        $now = new \DateTime();

        $items = $this->getCharacterItems();

        $insertCount = 0;

        foreach ($items as $item) {
            // Check if item already exists
            $qb = $connection->getQueryBuilder();
            $qb->select('id')
                ->from('quest_char_items')
                ->where($qb->expr()->eq('item_key', $qb->createNamedParameter($item['item_key'])));

            $result = $qb->executeQuery();
            $exists = $result->fetch();
            $result->closeCursor();

            if (!$exists) {
                // Insert the item
                $qb = $connection->getQueryBuilder();
                $qb->insert('quest_char_items')
                    ->values([
                        'item_key' => $qb->createNamedParameter($item['item_key']),
                        'item_name' => $qb->createNamedParameter($item['item_name']),
                        'item_type' => $qb->createNamedParameter($item['item_type']),
                        'age_key' => $qb->createNamedParameter($item['age_key']),
                        'item_description' => $qb->createNamedParameter($item['item_description']),
                        'unlock_level' => isset($item['unlock_level'])
                            ? $qb->createNamedParameter($item['unlock_level'], \PDO::PARAM_INT)
                            : $qb->createNamedParameter(null, \PDO::PARAM_NULL),
                        'unlock_achievement' => $qb->createNamedParameter(null, \PDO::PARAM_NULL),
                        'item_rarity' => $qb->createNamedParameter($item['item_rarity']),
                        'sprite_path' => $qb->createNamedParameter($item['sprite_path']),
                        'sprite_layer' => $qb->createNamedParameter($item['sprite_layer'], \PDO::PARAM_INT),
                        'is_default' => $qb->createNamedParameter($item['is_default'] ?? false, \PDO::PARAM_BOOL),
                        'is_active' => $qb->createNamedParameter(true, \PDO::PARAM_BOOL),
                        'created_at' => $qb->createNamedParameter($now, 'datetime'),
                    ]);

                $qb->executeStatement();
                $insertCount++;
            }
        }

        $output->info("Created {$insertCount} new character items");
    }

    private function getCharacterItems(): array {
        $items = [];

        $items = array_merge($items, $this->getStoneAgeItems());
        $items = array_merge($items, $this->getBronzeAgeItems());
        $items = array_merge($items, $this->getIronAgeItems());
        $items = array_merge($items, $this->getMedievalAgeItems());
        $items = array_merge($items, $this->getRenaissanceItems());
        $items = array_merge($items, $this->getIndustrialItems());
        $items = array_merge($items, $this->getModernItems());
        $items = array_merge($items, $this->getDigitalItems());
        $items = array_merge($items, $this->getSpaceItems());

        return $items;
    }

    private function getStoneAgeItems(): array {
        return [
            // Weapons
            ['item_key' => 'stone_throwing_rocks', 'item_name' => 'Throwing Rocks', 'item_type' => 'weapon', 'age_key' => 'stone', 'item_description' => 'A handful of smooth stones for throwing', 'unlock_level' => 2, 'item_rarity' => 'common', 'sprite_path' => 'characters/stone/weapons/throwing_rocks.svg', 'sprite_layer' => 20],
            ['item_key' => 'stone_obsidian_blade', 'item_name' => 'Obsidian Blade', 'item_type' => 'weapon', 'age_key' => 'stone', 'item_description' => 'Razor-sharp blade carved from volcanic glass', 'unlock_level' => 8, 'item_rarity' => 'epic', 'sprite_path' => 'characters/stone/weapons/obsidian_blade.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'stone_woven_sandals', 'item_name' => 'Woven Sandals', 'item_type' => 'clothing', 'age_key' => 'stone', 'item_description' => 'Sandals woven from plant fibers', 'unlock_level' => 2, 'item_rarity' => 'common', 'sprite_path' => 'characters/stone/clothing/woven_sandals.svg', 'sprite_layer' => 10],
            ['item_key' => 'stone_mammoth_cloak', 'item_name' => 'Mammoth Hide Cloak', 'item_type' => 'clothing', 'age_key' => 'stone', 'item_description' => 'A massive cloak sewn from mammoth hide', 'unlock_level' => 9, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/stone/clothing/mammoth_cloak.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'stone_tooth_necklace', 'item_name' => 'Tooth Necklace', 'item_type' => 'accessory', 'age_key' => 'stone', 'item_description' => 'Necklace strung with predator teeth', 'unlock_level' => 5, 'item_rarity' => 'rare', 'sprite_path' => 'characters/stone/accessories/tooth_necklace.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'stone_feather_crown', 'item_name' => 'Feather Crown', 'item_type' => 'headgear', 'age_key' => 'stone', 'item_description' => 'A crown adorned with colorful feathers', 'unlock_level' => 8, 'item_rarity' => 'epic', 'sprite_path' => 'characters/stone/headgear/feather_crown.svg', 'sprite_layer' => 30],
        ];
    }

    private function getBronzeAgeItems(): array {
        return [
            // Weapons
            ['item_key' => 'bronze_sling', 'item_name' => 'Bronze Sling', 'item_type' => 'weapon', 'age_key' => 'bronze', 'item_description' => 'A leather sling with bronze shot', 'unlock_level' => 11, 'item_rarity' => 'common', 'sprite_path' => 'characters/bronze/weapons/sling.svg', 'sprite_layer' => 20],
            ['item_key' => 'bronze_khopesh', 'item_name' => 'Khopesh Sword', 'item_type' => 'weapon', 'age_key' => 'bronze', 'item_description' => 'Curved sickle-shaped sword of ancient warriors', 'unlock_level' => 15, 'item_rarity' => 'rare', 'sprite_path' => 'characters/bronze/weapons/khopesh.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'bronze_leather_vest', 'item_name' => 'Leather Vest', 'item_type' => 'clothing', 'age_key' => 'bronze', 'item_description' => 'Sturdy leather vest with bronze studs', 'unlock_level' => 14, 'item_rarity' => 'rare', 'sprite_path' => 'characters/bronze/clothing/leather_vest.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'bronze_anklet', 'item_name' => 'Bronze Anklet', 'item_type' => 'accessory', 'age_key' => 'bronze', 'item_description' => 'Decorative bronze ankle band', 'unlock_level' => 10, 'item_rarity' => 'common', 'sprite_path' => 'characters/bronze/accessories/anklet.svg', 'sprite_layer' => 15],
            ['item_key' => 'bronze_scarab_pendant', 'item_name' => 'Scarab Pendant', 'item_type' => 'accessory', 'age_key' => 'bronze', 'item_description' => 'Sacred scarab pendant with protective enchantment', 'unlock_level' => 18, 'item_rarity' => 'epic', 'sprite_path' => 'characters/bronze/accessories/scarab_pendant.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'bronze_war_mask', 'item_name' => 'War Mask', 'item_type' => 'headgear', 'age_key' => 'bronze', 'item_description' => 'Fearsome bronze war mask', 'unlock_level' => 17, 'item_rarity' => 'epic', 'sprite_path' => 'characters/bronze/headgear/war_mask.svg', 'sprite_layer' => 30],
        ];
    }

    private function getIronAgeItems(): array {
        return [
            // Weapons
            ['item_key' => 'iron_war_hammer', 'item_name' => 'War Hammer', 'item_type' => 'weapon', 'age_key' => 'iron', 'item_description' => 'Heavy iron war hammer for crushing armor', 'unlock_level' => 23, 'item_rarity' => 'rare', 'sprite_path' => 'characters/iron/weapons/war_hammer.svg', 'sprite_layer' => 20],
            ['item_key' => 'iron_javelin', 'item_name' => 'Iron Javelin', 'item_type' => 'weapon', 'age_key' => 'iron', 'item_description' => 'Iron-tipped throwing javelin', 'unlock_level' => 21, 'item_rarity' => 'common', 'sprite_path' => 'characters/iron/weapons/javelin.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'iron_scale_mail', 'item_name' => 'Scale Mail', 'item_type' => 'clothing', 'age_key' => 'iron', 'item_description' => 'Armor made of overlapping iron scales', 'unlock_level' => 26, 'item_rarity' => 'epic', 'sprite_path' => 'characters/iron/clothing/scale_mail.svg', 'sprite_layer' => 10],
            ['item_key' => 'iron_cloak', 'item_name' => 'Iron Age Cloak', 'item_type' => 'clothing', 'age_key' => 'iron', 'item_description' => 'Heavy woolen cloak with iron clasp', 'unlock_level' => 24, 'item_rarity' => 'rare', 'sprite_path' => 'characters/iron/clothing/cloak.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'iron_arm_guard', 'item_name' => 'Arm Guard', 'item_type' => 'accessory', 'age_key' => 'iron', 'item_description' => 'Protective iron arm guard', 'unlock_level' => 20, 'item_rarity' => 'common', 'sprite_path' => 'characters/iron/accessories/arm_guard.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'iron_war_crown', 'item_name' => 'War Crown', 'item_type' => 'headgear', 'age_key' => 'iron', 'item_description' => 'Iron crown worn by warlords', 'unlock_level' => 28, 'item_rarity' => 'epic', 'sprite_path' => 'characters/iron/headgear/war_crown.svg', 'sprite_layer' => 30],
        ];
    }

    private function getMedievalAgeItems(): array {
        return [
            // Weapons
            ['item_key' => 'medieval_flail', 'item_name' => 'Morning Star Flail', 'item_type' => 'weapon', 'age_key' => 'medieval', 'item_description' => 'Spiked ball on a chain, devastating in combat', 'unlock_level' => 34, 'item_rarity' => 'rare', 'sprite_path' => 'characters/medieval/weapons/flail.svg', 'sprite_layer' => 20],
            ['item_key' => 'medieval_crossbow', 'item_name' => 'Crossbow', 'item_type' => 'weapon', 'age_key' => 'medieval', 'item_description' => 'Powerful mechanical crossbow', 'unlock_level' => 37, 'item_rarity' => 'epic', 'sprite_path' => 'characters/medieval/weapons/crossbow.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'medieval_chain_armor', 'item_name' => 'Chain Armor', 'item_type' => 'clothing', 'age_key' => 'medieval', 'item_description' => 'Full suit of interlocking chain links', 'unlock_level' => 31, 'item_rarity' => 'rare', 'sprite_path' => 'characters/medieval/clothing/chain_armor.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'medieval_signet_ring', 'item_name' => 'Signet Ring', 'item_type' => 'accessory', 'age_key' => 'medieval', 'item_description' => 'Ring bearing a noble house seal', 'unlock_level' => 36, 'item_rarity' => 'epic', 'sprite_path' => 'characters/medieval/accessories/signet_ring.svg', 'sprite_layer' => 15],
            ['item_key' => 'medieval_holy_grail', 'item_name' => 'Holy Grail', 'item_type' => 'accessory', 'age_key' => 'medieval', 'item_description' => 'The legendary chalice of myth', 'unlock_level' => 39, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/medieval/accessories/holy_grail.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'medieval_wizard_hat', 'item_name' => 'Wizard Hat', 'item_type' => 'headgear', 'age_key' => 'medieval', 'item_description' => 'Tall pointed hat of a learned wizard', 'unlock_level' => 33, 'item_rarity' => 'rare', 'sprite_path' => 'characters/medieval/headgear/wizard_hat.svg', 'sprite_layer' => 30],
        ];
    }

    private function getRenaissanceItems(): array {
        return [
            // Weapons
            ['item_key' => 'renaissance_sabre', 'item_name' => 'Cavalry Sabre', 'item_type' => 'weapon', 'age_key' => 'renaissance', 'item_description' => 'Curved blade favored by cavalry officers', 'unlock_level' => 47, 'item_rarity' => 'epic', 'sprite_path' => 'characters/renaissance/weapons/sabre.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'renaissance_vest', 'item_name' => 'Embroidered Vest', 'item_type' => 'clothing', 'age_key' => 'renaissance', 'item_description' => 'Finely embroidered silk vest', 'unlock_level' => 42, 'item_rarity' => 'rare', 'sprite_path' => 'characters/renaissance/clothing/vest.svg', 'sprite_layer' => 10],
            ['item_key' => 'renaissance_noble_cape', 'item_name' => 'Noble Cape', 'item_type' => 'clothing', 'age_key' => 'renaissance', 'item_description' => 'Luxurious cape trimmed with gold thread', 'unlock_level' => 49, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/renaissance/clothing/noble_cape.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'renaissance_compass', 'item_name' => 'Navigation Compass', 'item_type' => 'accessory', 'age_key' => 'renaissance', 'item_description' => 'Brass compass for charting new worlds', 'unlock_level' => 44, 'item_rarity' => 'rare', 'sprite_path' => 'characters/renaissance/accessories/compass.svg', 'sprite_layer' => 15],
            ['item_key' => 'renaissance_telescope', 'item_name' => 'Telescope', 'item_type' => 'accessory', 'age_key' => 'renaissance', 'item_description' => 'A finely crafted optical telescope', 'unlock_level' => 48, 'item_rarity' => 'epic', 'sprite_path' => 'characters/renaissance/accessories/telescope.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'renaissance_beret', 'item_name' => 'Artist Beret', 'item_type' => 'headgear', 'age_key' => 'renaissance', 'item_description' => 'Soft beret worn by Renaissance artists', 'unlock_level' => 41, 'item_rarity' => 'common', 'sprite_path' => 'characters/renaissance/headgear/beret.svg', 'sprite_layer' => 30],
        ];
    }

    private function getIndustrialItems(): array {
        return [
            // Weapons
            ['item_key' => 'industrial_dynamite', 'item_name' => 'Dynamite', 'item_type' => 'weapon', 'age_key' => 'industrial', 'item_description' => 'Explosive sticks of nitroglycerin', 'unlock_level' => 56, 'item_rarity' => 'epic', 'sprite_path' => 'characters/industrial/weapons/dynamite.svg', 'sprite_layer' => 20],
            ['item_key' => 'industrial_cane_sword', 'item_name' => 'Cane Sword', 'item_type' => 'weapon', 'age_key' => 'industrial', 'item_description' => 'A gentleman\'s walking cane concealing a blade', 'unlock_level' => 58, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/industrial/weapons/cane_sword.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'industrial_overalls', 'item_name' => 'Work Overalls', 'item_type' => 'clothing', 'age_key' => 'industrial', 'item_description' => 'Sturdy overalls for factory work', 'unlock_level' => 51, 'item_rarity' => 'common', 'sprite_path' => 'characters/industrial/clothing/overalls.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'industrial_monocle', 'item_name' => 'Monocle', 'item_type' => 'accessory', 'age_key' => 'industrial', 'item_description' => 'A single corrective lens of distinction', 'unlock_level' => 53, 'item_rarity' => 'rare', 'sprite_path' => 'characters/industrial/accessories/monocle.svg', 'sprite_layer' => 15],
            ['item_key' => 'industrial_compass', 'item_name' => 'Pocket Compass', 'item_type' => 'accessory', 'age_key' => 'industrial', 'item_description' => 'Brass pocket compass for navigation', 'unlock_level' => 50, 'item_rarity' => 'common', 'sprite_path' => 'characters/industrial/accessories/compass.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'industrial_bowler_hat', 'item_name' => 'Bowler Hat', 'item_type' => 'headgear', 'age_key' => 'industrial', 'item_description' => 'A proper bowler hat for the modern gentleman', 'unlock_level' => 54, 'item_rarity' => 'rare', 'sprite_path' => 'characters/industrial/headgear/bowler_hat.svg', 'sprite_layer' => 30],
        ];
    }

    private function getModernItems(): array {
        return [
            // Weapons
            ['item_key' => 'modern_taser', 'item_name' => 'Taser', 'item_type' => 'weapon', 'age_key' => 'modern', 'item_description' => 'Electroshock weapon for non-lethal takedowns', 'unlock_level' => 63, 'item_rarity' => 'rare', 'sprite_path' => 'characters/modern/weapons/taser.svg', 'sprite_layer' => 20],
            ['item_key' => 'modern_drone', 'item_name' => 'Combat Drone', 'item_type' => 'weapon', 'age_key' => 'modern', 'item_description' => 'Autonomous aerial combat drone', 'unlock_level' => 72, 'item_rarity' => 'epic', 'sprite_path' => 'characters/modern/weapons/drone.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'modern_hoodie', 'item_name' => 'Tech Hoodie', 'item_type' => 'clothing', 'age_key' => 'modern', 'item_description' => 'Comfortable hoodie with smart fabric', 'unlock_level' => 60, 'item_rarity' => 'common', 'sprite_path' => 'characters/modern/clothing/hoodie.svg', 'sprite_layer' => 10],
            ['item_key' => 'modern_kevlar_vest', 'item_name' => 'Kevlar Vest', 'item_type' => 'clothing', 'age_key' => 'modern', 'item_description' => 'Bulletproof tactical vest', 'unlock_level' => 70, 'item_rarity' => 'epic', 'sprite_path' => 'characters/modern/clothing/kevlar_vest.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'modern_smartwatch', 'item_name' => 'Smartwatch', 'item_type' => 'accessory', 'age_key' => 'modern', 'item_description' => 'Advanced smartwatch with health monitoring', 'unlock_level' => 65, 'item_rarity' => 'rare', 'sprite_path' => 'characters/modern/accessories/smartwatch.svg', 'sprite_layer' => 15],
            ['item_key' => 'modern_earpiece', 'item_name' => 'Tactical Earpiece', 'item_type' => 'accessory', 'age_key' => 'modern', 'item_description' => 'Covert communications earpiece', 'unlock_level' => 69, 'item_rarity' => 'epic', 'sprite_path' => 'characters/modern/accessories/earpiece.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'modern_beret', 'item_name' => 'Military Beret', 'item_type' => 'headgear', 'age_key' => 'modern', 'item_description' => 'Distinguished military beret', 'unlock_level' => 64, 'item_rarity' => 'rare', 'sprite_path' => 'characters/modern/headgear/beret.svg', 'sprite_layer' => 30],
        ];
    }

    private function getDigitalItems(): array {
        return [
            // Weapons
            ['item_key' => 'digital_code_blade', 'item_name' => 'Code Blade', 'item_type' => 'weapon', 'age_key' => 'digital', 'item_description' => 'A blade materialized from pure code', 'unlock_level' => 78, 'item_rarity' => 'rare', 'sprite_path' => 'characters/digital/weapons/code_blade.svg', 'sprite_layer' => 20],
            ['item_key' => 'digital_photon_sword', 'item_name' => 'Photon Sword', 'item_type' => 'weapon', 'age_key' => 'digital', 'item_description' => 'Sword forged from concentrated photon energy', 'unlock_level' => 92, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/digital/weapons/photon_sword.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'digital_holo_suit', 'item_name' => 'Holographic Suit', 'item_type' => 'clothing', 'age_key' => 'digital', 'item_description' => 'Suit projecting holographic armor plating', 'unlock_level' => 82, 'item_rarity' => 'epic', 'sprite_path' => 'characters/digital/clothing/holo_suit.svg', 'sprite_layer' => 10],
            ['item_key' => 'digital_quantum_cloak', 'item_name' => 'Quantum Cloak', 'item_type' => 'clothing', 'age_key' => 'digital', 'item_description' => 'Cloak that phases between quantum states', 'unlock_level' => 97, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/digital/clothing/quantum_cloak.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'digital_data_gauntlet', 'item_name' => 'Data Gauntlet', 'item_type' => 'accessory', 'age_key' => 'digital', 'item_description' => 'Gauntlet that interfaces directly with data streams', 'unlock_level' => 79, 'item_rarity' => 'rare', 'sprite_path' => 'characters/digital/accessories/data_gauntlet.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'digital_mind_crown', 'item_name' => 'Mind Crown', 'item_type' => 'headgear', 'age_key' => 'digital', 'item_description' => 'Neural interface crown for direct thought control', 'unlock_level' => 95, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/digital/headgear/mind_crown.svg', 'sprite_layer' => 30],
        ];
    }

    private function getSpaceItems(): array {
        return [
            // Weapons
            ['item_key' => 'space_plasma_blade', 'item_name' => 'Plasma Blade', 'item_type' => 'weapon', 'age_key' => 'space', 'item_description' => 'Blade of superheated plasma contained by force fields', 'unlock_level' => 102, 'item_rarity' => 'rare', 'sprite_path' => 'characters/space/weapons/plasma_blade.svg', 'sprite_layer' => 20],
            ['item_key' => 'space_nova_cannon', 'item_name' => 'Nova Cannon', 'item_type' => 'weapon', 'age_key' => 'space', 'item_description' => 'Handheld cannon harnessing stellar energy', 'unlock_level' => 115, 'item_rarity' => 'epic', 'sprite_path' => 'characters/space/weapons/nova_cannon.svg', 'sprite_layer' => 20],

            // Clothing
            ['item_key' => 'space_void_armor', 'item_name' => 'Void Armor', 'item_type' => 'clothing', 'age_key' => 'space', 'item_description' => 'Armor infused with the darkness of the void', 'unlock_level' => 110, 'item_rarity' => 'epic', 'sprite_path' => 'characters/space/clothing/void_armor.svg', 'sprite_layer' => 10],
            ['item_key' => 'space_warp_cloak', 'item_name' => 'Warp Cloak', 'item_type' => 'clothing', 'age_key' => 'space', 'item_description' => 'Cloak that warps spacetime around the wearer', 'unlock_level' => 145, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/space/clothing/warp_cloak.svg', 'sprite_layer' => 10],

            // Accessories
            ['item_key' => 'space_gravity_boots', 'item_name' => 'Gravity Boots', 'item_type' => 'accessory', 'age_key' => 'space', 'item_description' => 'Boots with personal gravity field generators', 'unlock_level' => 103, 'item_rarity' => 'rare', 'sprite_path' => 'characters/space/accessories/gravity_boots.svg', 'sprite_layer' => 15],
            ['item_key' => 'space_nebula_amulet', 'item_name' => 'Nebula Amulet', 'item_type' => 'accessory', 'age_key' => 'space', 'item_description' => 'Amulet containing a captured nebula fragment', 'unlock_level' => 125, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/space/accessories/nebula_amulet.svg', 'sprite_layer' => 15],

            // Headgear
            ['item_key' => 'space_star_crown', 'item_name' => 'Star Crown', 'item_type' => 'headgear', 'age_key' => 'space', 'item_description' => 'Crown forged in the heart of a dying star', 'unlock_level' => 135, 'item_rarity' => 'legendary', 'sprite_path' => 'characters/space/headgear/star_crown.svg', 'sprite_layer' => 30],
        ];
    }
}
