<?php
/**
 * @copyright Copyright (c) 2025 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 */

namespace OCA\NextcloudQuest\Service;

/**
 * Adventure Theme Service
 * Provides age-themed content for adventure areas (enemies, rewards, visual themes)
 */
class AdventureThemeService {

    /**
     * Get theme configuration for an age
     */
    public function getThemeForAge(string $ageKey): array {
        $themes = $this->getAllThemes();
        return $themes[$ageKey] ?? $themes['stone']; // Default to stone age
    }

    /**
     * Get all age themes
     */
    private function getAllThemes(): array {
        return [
            'stone' => [
                'age_key' => 'stone',
                'age_name' => 'Stone Age',
                'color_primary' => '#8b7355',
                'color_secondary' => '#a0826d',
                'enemies' => [
                    ['name' => 'Wild Wolf', 'health' => 30, 'attack' => 5, 'xp' => 15],
                    ['name' => 'Cave Bear', 'health' => 50, 'attack' => 8, 'xp' => 25],
                    ['name' => 'Sabertooth Cat', 'health' => 40, 'attack' => 7, 'xp' => 20],
                    ['name' => 'Giant Boar', 'health' => 45, 'attack' => 6, 'xp' => 22],
                    ['name' => 'Dire Hyena', 'health' => 35, 'attack' => 6, 'xp' => 18],
                    ['name' => 'Venomous Serpent', 'health' => 25, 'attack' => 9, 'xp' => 20],
                    ['name' => 'Territorial Aurochs', 'health' => 55, 'attack' => 7, 'xp' => 24],
                    ['name' => 'Rival Tribesman', 'health' => 38, 'attack' => 8, 'xp' => 22],
                ],
                'mini_boss' => [
                    'name' => 'Cave Lion Matriarch',
                    'health' => 80,
                    'attack' => 10,
                    'xp' => 60,
                    'description' => 'A fearsome lioness who guards her den with savage ferocity',
                ],
                'boss' => [
                    'name' => 'Mammoth Alpha',
                    'health' => 120,
                    'attack' => 12,
                    'xp' => 100,
                    'description' => 'A massive woolly mammoth that rules the frozen plains'
                ],
                'treasure_pool' => ['stone_spear', 'stone_axe', 'stone_fur_decorated', 'stone_shell_bracelet'],
                'event_themes' => ['cave_paintings', 'hunting_grounds', 'ritual_site', 'ancient_burial'],
                'story_events' => [
                    [
                        'name' => 'Tribal Elder\'s Wisdom',
                        'description' => 'An elder shares ancient knowledge of survival with you, passed down through generations.',
                        'reward' => ['xp' => 75],
                    ],
                    [
                        'name' => 'Sacred Cave Paintings',
                        'description' => 'You discover a hidden cave adorned with mystical paintings. Their energy invigorates you.',
                        'reward' => ['xp' => 50, 'health' => 15],
                    ],
                    [
                        'name' => 'Bountiful Hunt',
                        'description' => 'You stumble upon a herd of deer and bring back a massive haul for the tribe.',
                        'reward' => ['gold' => 120],
                    ],
                    [
                        'name' => 'Healing Spring',
                        'description' => 'A warm spring bubbles up from the earth, its mineral-rich waters mend your wounds.',
                        'reward' => ['health' => 40],
                    ],
                    [
                        'name' => 'Flint Deposit',
                        'description' => 'You find a rich vein of obsidian and flint, invaluable materials for crafting.',
                        'reward' => ['gold' => 180],
                    ],
                    [
                        'name' => 'Fire Dance Ritual',
                        'description' => 'The tribe performs a fire dance under the stars. You feel a primal strength awaken within.',
                        'reward' => ['xp' => 60, 'health' => 10],
                    ],
                    [
                        'name' => 'Wandering Shaman',
                        'description' => 'A lone shaman offers you a potion brewed from ancient herbs. Your body surges with vitality.',
                        'reward' => ['health' => 35, 'xp' => 30],
                    ],
                    [
                        'name' => 'Buried Offering',
                        'description' => 'You unearth a burial mound containing offerings to forgotten spirits.',
                        'reward' => ['gold' => 100, 'xp' => 40],
                    ],
                ],
            ],
            'bronze' => [
                'age_key' => 'bronze',
                'age_name' => 'Bronze Age',
                'color_primary' => '#cd7f32',
                'color_secondary' => '#daa520',
                'enemies' => [
                    ['name' => 'Raider Scout', 'health' => 50, 'attack' => 10, 'xp' => 30],
                    ['name' => 'Bronze Warrior', 'health' => 60, 'attack' => 12, 'xp' => 35],
                    ['name' => 'Desert Nomad', 'health' => 55, 'attack' => 11, 'xp' => 32],
                    ['name' => 'Temple Guard', 'health' => 65, 'attack' => 13, 'xp' => 38],
                    ['name' => 'Tomb Raider', 'health' => 52, 'attack' => 12, 'xp' => 33],
                    ['name' => 'Sand Scorpion Rider', 'health' => 58, 'attack' => 14, 'xp' => 36],
                    ['name' => 'Chariot Archer', 'health' => 48, 'attack' => 15, 'xp' => 37],
                    ['name' => 'Cult Fanatic', 'health' => 62, 'attack' => 11, 'xp' => 34],
                ],
                'mini_boss' => [
                    'name' => 'High Priestess of Ur',
                    'health' => 120,
                    'attack' => 16,
                    'xp' => 90,
                    'description' => 'A zealous priestess who wields divine fire in defense of her temple',
                ],
                'boss' => [
                    'name' => 'Bronze Chieftain',
                    'health' => 180,
                    'attack' => 18,
                    'xp' => 150,
                    'description' => 'A legendary warlord clad in gleaming bronze armor'
                ],
                'treasure_pool' => ['bronze_sword', 'bronze_armor', 'bronze_amulet', 'bronze_dagger'],
                'event_themes' => ['ancient_forge', 'merchant_caravan', 'sacred_temple', 'tribal_gathering'],
                'story_events' => [
                    [
                        'name' => 'Merchant Caravan',
                        'description' => 'A traveling merchant offers you rare goods at a generous price for safe escort.',
                        'reward' => ['gold' => 200],
                    ],
                    [
                        'name' => 'Temple Blessing',
                        'description' => 'A priest at a sun temple performs a ritual of blessing. Divine energy fills your body.',
                        'reward' => ['health' => 35, 'xp' => 40],
                    ],
                    [
                        'name' => 'Ancient Scrolls',
                        'description' => 'You discover clay tablets inscribed with forgotten knowledge of the old kingdoms.',
                        'reward' => ['xp' => 100],
                    ],
                    [
                        'name' => 'Oasis Rest',
                        'description' => 'You find a hidden oasis in the desert. Cool water and shade restore your strength.',
                        'reward' => ['health' => 45],
                    ],
                    [
                        'name' => 'Bronze Hoard',
                        'description' => 'Beneath a crumbling ziggurat, you uncover a cache of bronze ingots and jewelry.',
                        'reward' => ['gold' => 250],
                    ],
                    [
                        'name' => 'Stargazer\'s Gift',
                        'description' => 'An astronomer shares celestial maps that reveal new paths. You gain deep insight.',
                        'reward' => ['xp' => 85],
                    ],
                    [
                        'name' => 'River Trader\'s Bargain',
                        'description' => 'A river trader exchanges exotic spices and medicines for your help repairing his boat.',
                        'reward' => ['health' => 25, 'gold' => 120],
                    ],
                    [
                        'name' => 'Pharaoh\'s Favor',
                        'description' => 'You return a lost relic to a minor pharaoh, who rewards you handsomely.',
                        'reward' => ['gold' => 150, 'xp' => 60],
                    ],
                ],
            ],
            'iron' => [
                'age_key' => 'iron',
                'age_name' => 'Iron Age',
                'color_primary' => '#71706e',
                'color_secondary' => '#a9a9a9',
                'enemies' => [
                    ['name' => 'Iron Legionnaire', 'health' => 70, 'attack' => 15, 'xp' => 45],
                    ['name' => 'Barbarian Raider', 'health' => 80, 'attack' => 17, 'xp' => 50],
                    ['name' => 'Shield Maiden', 'health' => 75, 'attack' => 16, 'xp' => 48],
                    ['name' => 'Celtic Warrior', 'health' => 78, 'attack' => 16, 'xp' => 49],
                    ['name' => 'Phalanx Spearman', 'health' => 82, 'attack' => 14, 'xp' => 46],
                    ['name' => 'War Druid', 'health' => 65, 'attack' => 19, 'xp' => 52],
                    ['name' => 'Siege Engineer', 'health' => 85, 'attack' => 15, 'xp' => 48],
                    ['name' => 'Berserker', 'health' => 60, 'attack' => 20, 'xp' => 55],
                ],
                'mini_boss' => [
                    'name' => 'Centurion Varro',
                    'health' => 170,
                    'attack' => 22,
                    'xp' => 130,
                    'description' => 'A decorated centurion who has never lost a battle formation',
                ],
                'boss' => [
                    'name' => 'Iron Warlord',
                    'health' => 250,
                    'attack' => 25,
                    'xp' => 200,
                    'description' => 'A fearsome conqueror wielding an iron longsword'
                ],
                'treasure_pool' => ['iron_longsword', 'iron_armor', 'iron_helmet', 'iron_battle_axe'],
                'event_themes' => ['iron_mine', 'war_camp', 'fortified_village', 'battlefield'],
                'story_events' => [
                    [
                        'name' => 'Blacksmith\'s Gratitude',
                        'description' => 'You help a master blacksmith repair his forge. He tempers your weapon as thanks.',
                        'reward' => ['xp' => 100, 'gold' => 80],
                    ],
                    [
                        'name' => 'Legion Camp Rest',
                        'description' => 'A friendly legion camp offers you shelter, hot food, and medical attention.',
                        'reward' => ['health' => 50],
                    ],
                    [
                        'name' => 'War Trophy',
                        'description' => 'After a skirmish, you claim abandoned spoils from a retreating warband.',
                        'reward' => ['gold' => 220],
                    ],
                    [
                        'name' => 'Druid\'s Counsel',
                        'description' => 'A druid in a sacred grove teaches you to read the omens of battle.',
                        'reward' => ['xp' => 120],
                    ],
                    [
                        'name' => 'Fortified Spring',
                        'description' => 'You discover a hidden spring within an abandoned hill fort. Its waters heal old wounds.',
                        'reward' => ['health' => 40, 'xp' => 30],
                    ],
                    [
                        'name' => 'Tribute Caravan',
                        'description' => 'A village offers tribute for driving off raiders who tormented them.',
                        'reward' => ['gold' => 280],
                    ],
                    [
                        'name' => 'Battle Tactics Scroll',
                        'description' => 'You recover a scroll detailing advanced military formations and strategies.',
                        'reward' => ['xp' => 130],
                    ],
                    [
                        'name' => 'Warrior\'s Bond',
                        'description' => 'A group of warriors shares their rations and battle stories around a campfire.',
                        'reward' => ['health' => 30, 'gold' => 100],
                    ],
                ],
            ],
            'medieval' => [
                'age_key' => 'medieval',
                'age_name' => 'Medieval Age',
                'color_primary' => '#8b4513',
                'color_secondary' => '#cd853f',
                'enemies' => [
                    ['name' => 'Knight Errant', 'health' => 90, 'attack' => 20, 'xp' => 60],
                    ['name' => 'Crossbowman', 'health' => 85, 'attack' => 19, 'xp' => 58],
                    ['name' => 'Mounted Knight', 'health' => 95, 'attack' => 22, 'xp' => 65],
                    ['name' => 'Tower Guard', 'health' => 100, 'attack' => 21, 'xp' => 63],
                    ['name' => 'Dark Sorcerer', 'health' => 75, 'attack' => 25, 'xp' => 68],
                    ['name' => 'Siege Breaker', 'health' => 110, 'attack' => 18, 'xp' => 62],
                    ['name' => 'Wyvern Hatchling', 'health' => 80, 'attack' => 23, 'xp' => 70],
                    ['name' => 'Royal Assassin', 'health' => 70, 'attack' => 26, 'xp' => 72],
                ],
                'mini_boss' => [
                    'name' => 'Black Knight of Ashenmoor',
                    'health' => 220,
                    'attack' => 28,
                    'xp' => 170,
                    'description' => 'A cursed knight in black plate armor who haunts the old road',
                ],
                'boss' => [
                    'name' => 'Dragon Knight',
                    'health' => 320,
                    'attack' => 32,
                    'xp' => 250,
                    'description' => 'A legendary knight who has slain many dragons'
                ],
                'treasure_pool' => ['medieval_mace', 'medieval_plate_armor', 'medieval_crown', 'medieval_shield'],
                'event_themes' => ['castle_siege', 'tournament_grounds', 'monastery', 'royal_court'],
                'story_events' => [
                    [
                        'name' => 'The King\'s Feast',
                        'description' => 'You are invited to a royal banquet. Rich food and fine wine restore your strength.',
                        'reward' => ['health' => 50],
                    ],
                    [
                        'name' => 'Tournament Victor',
                        'description' => 'You enter a jousting tournament and impress the crowd with your skill.',
                        'reward' => ['xp' => 140, 'gold' => 100],
                    ],
                    [
                        'name' => 'Monastery Library',
                        'description' => 'Monks grant you access to their ancient library. You study texts of warfare and history.',
                        'reward' => ['xp' => 150],
                    ],
                    [
                        'name' => 'Wandering Healer',
                        'description' => 'A traveling monk tends to your injuries with herbal remedies and prayer.',
                        'reward' => ['health' => 45, 'xp' => 30],
                    ],
                    [
                        'name' => 'Noble\'s Reward',
                        'description' => 'A grateful lord rewards you for defending his lands from a marauding band.',
                        'reward' => ['gold' => 320],
                    ],
                    [
                        'name' => 'Dragon Scale Find',
                        'description' => 'You discover shed dragon scales in a mountain cave, worth a fortune to armorers.',
                        'reward' => ['gold' => 250, 'xp' => 50],
                    ],
                    [
                        'name' => 'Enchanted Glade',
                        'description' => 'A hidden forest clearing pulses with fey magic. You rest and feel renewed.',
                        'reward' => ['health' => 60],
                    ],
                    [
                        'name' => 'Squire\'s Training',
                        'description' => 'A retired knight offers to spar with you and share decades of combat wisdom.',
                        'reward' => ['xp' => 160],
                    ],
                ],
            ],
            'renaissance' => [
                'age_key' => 'renaissance',
                'age_name' => 'Renaissance',
                'color_primary' => '#daa520',
                'color_secondary' => '#ffd700',
                'enemies' => [
                    ['name' => 'Musketeer', 'health' => 110, 'attack' => 25, 'xp' => 75],
                    ['name' => 'Rapier Duelist', 'health' => 105, 'attack' => 24, 'xp' => 72],
                    ['name' => 'Mercenary Captain', 'health' => 115, 'attack' => 26, 'xp' => 78],
                    ['name' => 'Naval Officer', 'health' => 120, 'attack' => 27, 'xp' => 80],
                    ['name' => 'Alchemist Adept', 'health' => 95, 'attack' => 30, 'xp' => 82],
                    ['name' => 'Condottiere', 'health' => 125, 'attack' => 25, 'xp' => 77],
                    ['name' => 'Inquisitor', 'health' => 108, 'attack' => 28, 'xp' => 84],
                    ['name' => 'Corsair Raider', 'health' => 100, 'attack' => 29, 'xp' => 81],
                ],
                'mini_boss' => [
                    'name' => 'Maestro di Guerra',
                    'health' => 280,
                    'attack' => 35,
                    'xp' => 210,
                    'description' => 'A legendary war artist who fights with mathematical precision and deadly grace',
                ],
                'boss' => [
                    'name' => 'Grand Master',
                    'health' => 400,
                    'attack' => 40,
                    'xp' => 300,
                    'description' => 'A master strategist and undefeated duelist'
                ],
                'treasure_pool' => ['renaissance_rapier', 'renaissance_doublet', 'renaissance_hat', 'renaissance_pistol'],
                'event_themes' => ['art_gallery', 'opera_house', 'printing_press', 'navigation_guild'],
                'story_events' => [
                    [
                        'name' => 'Patron of the Arts',
                        'description' => 'A wealthy patron commissions you to retrieve a stolen masterpiece. The reward is generous.',
                        'reward' => ['gold' => 350],
                    ],
                    [
                        'name' => 'Inventor\'s Workshop',
                        'description' => 'A brilliant inventor shares blueprints for a remarkable device. You learn much from the plans.',
                        'reward' => ['xp' => 170],
                    ],
                    [
                        'name' => 'Apothecary\'s Elixir',
                        'description' => 'An apothecary brews you a restorative tonic from rare imported herbs.',
                        'reward' => ['health' => 55],
                    ],
                    [
                        'name' => 'Navigator\'s Charts',
                        'description' => 'A cartographer gifts you detailed maps of unexplored territories in exchange for your tales.',
                        'reward' => ['xp' => 130, 'gold' => 80],
                    ],
                    [
                        'name' => 'Masquerade Ball',
                        'description' => 'At a lavish masquerade, you charm nobles into sharing secrets and coin.',
                        'reward' => ['gold' => 300, 'xp' => 50],
                    ],
                    [
                        'name' => 'Printing Press Discovery',
                        'description' => 'You help a printer reproduce a rare manuscript. The knowledge within expands your mind.',
                        'reward' => ['xp' => 180],
                    ],
                    [
                        'name' => 'Sculptor\'s Rest',
                        'description' => 'A sculptor offers lodging in his villa overlooking the sea. You rest peacefully.',
                        'reward' => ['health' => 45, 'xp' => 40],
                    ],
                    [
                        'name' => 'Spice Trade Windfall',
                        'description' => 'You invest in a spice trade voyage that returns with enormous profits.',
                        'reward' => ['gold' => 400],
                    ],
                ],
            ],
            'industrial' => [
                'age_key' => 'industrial',
                'age_name' => 'Industrial Age',
                'color_primary' => '#696969',
                'color_secondary' => '#808080',
                'enemies' => [
                    ['name' => 'Factory Guard', 'health' => 130, 'attack' => 30, 'xp' => 90],
                    ['name' => 'Steam Automaton', 'health' => 140, 'attack' => 32, 'xp' => 95],
                    ['name' => 'Railway Bandit', 'health' => 135, 'attack' => 31, 'xp' => 92],
                    ['name' => 'Coal Baron Enforcer', 'health' => 145, 'attack' => 33, 'xp' => 98],
                    ['name' => 'Clockwork Sentinel', 'health' => 150, 'attack' => 29, 'xp' => 93],
                    ['name' => 'Dynamite Saboteur', 'health' => 120, 'attack' => 36, 'xp' => 100],
                    ['name' => 'Iron Horse Rider', 'health' => 138, 'attack' => 34, 'xp' => 96],
                    ['name' => 'Smog Wraith', 'health' => 125, 'attack' => 35, 'xp' => 97],
                ],
                'mini_boss' => [
                    'name' => 'The Brass Baron',
                    'health' => 350,
                    'attack' => 44,
                    'xp' => 280,
                    'description' => 'A ruthless industrialist piloting a brass-plated steam walker',
                ],
                'boss' => [
                    'name' => 'Iron Titan',
                    'health' => 500,
                    'attack' => 50,
                    'xp' => 400,
                    'description' => 'A massive steam-powered war machine'
                ],
                'treasure_pool' => ['industrial_wrench', 'industrial_goggles', 'industrial_coat', 'industrial_gear'],
                'event_themes' => ['steam_factory', 'railway_station', 'mining_operation', 'inventors_lab'],
                'story_events' => [
                    [
                        'name' => 'Patent Royalties',
                        'description' => 'An inventor pays you royalties for a mechanism you helped design months ago.',
                        'reward' => ['gold' => 380],
                    ],
                    [
                        'name' => 'Steam Bath House',
                        'description' => 'You relax in a luxurious steam-powered bath house. Your muscles unknot and wounds close.',
                        'reward' => ['health' => 55],
                    ],
                    [
                        'name' => 'Engineering Lecture',
                        'description' => 'A visiting professor gives a lecture on advanced mechanics. You absorb every detail.',
                        'reward' => ['xp' => 190],
                    ],
                    [
                        'name' => 'Mine Rescue',
                        'description' => 'You rescue trapped miners from a collapsed shaft. The mining company rewards you well.',
                        'reward' => ['gold' => 300, 'xp' => 80],
                    ],
                    [
                        'name' => 'Clockmaker\'s Gift',
                        'description' => 'A grateful clockmaker gives you a precision timepiece after you recover his stolen tools.',
                        'reward' => ['gold' => 250, 'xp' => 60],
                    ],
                    [
                        'name' => 'Railway Doctor',
                        'description' => 'A doctor aboard a train tends to your injuries with modern medical instruments.',
                        'reward' => ['health' => 60, 'xp' => 30],
                    ],
                    [
                        'name' => 'Factory Blueprint',
                        'description' => 'You acquire blueprints for an advanced steam engine. The knowledge is invaluable.',
                        'reward' => ['xp' => 200],
                    ],
                    [
                        'name' => 'Coal Vein Discovery',
                        'description' => 'You discover an untapped coal seam and sell the mining rights for a handsome sum.',
                        'reward' => ['gold' => 450],
                    ],
                ],
            ],
            'modern' => [
                'age_key' => 'modern',
                'age_name' => 'Modern Age',
                'color_primary' => '#4169e1',
                'color_secondary' => '#6495ed',
                'enemies' => [
                    ['name' => 'Corporate Security', 'health' => 150, 'attack' => 35, 'xp' => 110],
                    ['name' => 'Spec Ops Soldier', 'health' => 160, 'attack' => 38, 'xp' => 115],
                    ['name' => 'Cyber Hacker', 'health' => 155, 'attack' => 36, 'xp' => 112],
                    ['name' => 'Elite Agent', 'health' => 165, 'attack' => 40, 'xp' => 120],
                    ['name' => 'Drone Operator', 'health' => 140, 'attack' => 42, 'xp' => 118],
                    ['name' => 'Shadow Contractor', 'health' => 158, 'attack' => 39, 'xp' => 116],
                    ['name' => 'Armored Enforcer', 'health' => 175, 'attack' => 35, 'xp' => 114],
                    ['name' => 'EMP Specialist', 'health' => 145, 'attack' => 41, 'xp' => 122],
                ],
                'mini_boss' => [
                    'name' => 'Director of Black Operations',
                    'health' => 420,
                    'attack' => 52,
                    'xp' => 350,
                    'description' => 'A shadowy figure who commands a private army from behind encrypted channels',
                ],
                'boss' => [
                    'name' => 'Megacorp CEO',
                    'health' => 600,
                    'attack' => 60,
                    'xp' => 500,
                    'description' => 'A ruthless corporate overlord with unlimited resources'
                ],
                'treasure_pool' => ['modern_suit', 'modern_briefcase', 'modern_sunglasses', 'modern_phone'],
                'event_themes' => ['skyscraper', 'research_lab', 'stock_exchange', 'airport'],
                'story_events' => [
                    [
                        'name' => 'Stock Market Surge',
                        'description' => 'A tip from a grateful informant leads to a perfectly timed stock trade.',
                        'reward' => ['gold' => 500],
                    ],
                    [
                        'name' => 'Private Hospital',
                        'description' => 'A corporate contact arranges VIP medical treatment at a cutting-edge facility.',
                        'reward' => ['health' => 65],
                    ],
                    [
                        'name' => 'Intelligence Briefing',
                        'description' => 'A retired analyst shares classified tactical knowledge over a secure channel.',
                        'reward' => ['xp' => 220],
                    ],
                    [
                        'name' => 'Tech Conference',
                        'description' => 'You attend an exclusive tech summit and gain insights into breakthrough innovations.',
                        'reward' => ['xp' => 180, 'gold' => 100],
                    ],
                    [
                        'name' => 'Whistleblower\'s Reward',
                        'description' => 'You expose a corrupt subsidiary. The parent company pays handsomely for your discretion.',
                        'reward' => ['gold' => 450, 'xp' => 80],
                    ],
                    [
                        'name' => 'Safe House',
                        'description' => 'An ally provides access to a well-stocked safe house. You rest and resupply.',
                        'reward' => ['health' => 50, 'gold' => 150],
                    ],
                    [
                        'name' => 'Satellite Uplink',
                        'description' => 'You tap into a satellite feed revealing hidden caches and strategic positions.',
                        'reward' => ['xp' => 200],
                    ],
                    [
                        'name' => 'Pharmaceutical Breakthrough',
                        'description' => 'A lab scientist offers you an experimental regenerative treatment. It works perfectly.',
                        'reward' => ['health' => 70],
                    ],
                ],
            ],
            'digital' => [
                'age_key' => 'digital',
                'age_name' => 'Digital Age',
                'color_primary' => '#00ced1',
                'color_secondary' => '#00ffff',
                'enemies' => [
                    ['name' => 'AI Sentinel', 'health' => 180, 'attack' => 45, 'xp' => 135],
                    ['name' => 'Virtual Warrior', 'health' => 190, 'attack' => 48, 'xp' => 140],
                    ['name' => 'Data Ghost', 'health' => 185, 'attack' => 46, 'xp' => 137],
                    ['name' => 'Cybernetic Hunter', 'health' => 195, 'attack' => 50, 'xp' => 145],
                    ['name' => 'Firewall Golem', 'health' => 210, 'attack' => 44, 'xp' => 138],
                    ['name' => 'Glitch Phantom', 'health' => 170, 'attack' => 52, 'xp' => 148],
                    ['name' => 'Neural Parasite', 'health' => 175, 'attack' => 51, 'xp' => 142],
                    ['name' => 'Rogue Compiler', 'health' => 200, 'attack' => 47, 'xp' => 144],
                ],
                'mini_boss' => [
                    'name' => 'Kernel Overlord',
                    'health' => 530,
                    'attack' => 65,
                    'xp' => 430,
                    'description' => 'A rogue AI that has seized root access to an entire datacenter',
                ],
                'boss' => [
                    'name' => 'System Administrator',
                    'health' => 750,
                    'attack' => 75,
                    'xp' => 600,
                    'description' => 'A sentient AI that controls the entire network'
                ],
                'treasure_pool' => ['digital_visor', 'digital_gloves', 'digital_implant', 'digital_neural_link'],
                'event_themes' => ['server_room', 'virtual_reality_hub', 'data_center', 'quantum_lab'],
                'story_events' => [
                    [
                        'name' => 'Data Mine Jackpot',
                        'description' => 'You crack an encrypted data vault and extract valuable cryptocurrency.',
                        'reward' => ['gold' => 550],
                    ],
                    [
                        'name' => 'Neural Defrag',
                        'description' => 'A friendly AI runs a diagnostic on your neural implants, optimizing your reflexes.',
                        'reward' => ['health' => 60, 'xp' => 50],
                    ],
                    [
                        'name' => 'Open Source Collective',
                        'description' => 'A hacker collective shares their code library with you. The algorithms are brilliant.',
                        'reward' => ['xp' => 250],
                    ],
                    [
                        'name' => 'Virtual Spa',
                        'description' => 'You enter a virtual reality relaxation program. Your mind and body are fully restored.',
                        'reward' => ['health' => 70],
                    ],
                    [
                        'name' => 'Crypto Bounty',
                        'description' => 'You report a critical zero-day vulnerability. The bug bounty is substantial.',
                        'reward' => ['gold' => 480, 'xp' => 100],
                    ],
                    [
                        'name' => 'Quantum Processing',
                        'description' => 'You gain temporary access to a quantum computer. The processing power unlocks new insights.',
                        'reward' => ['xp' => 270],
                    ],
                    [
                        'name' => 'Holographic Archive',
                        'description' => 'An ancient holographic archive reveals forgotten digital techniques and protocols.',
                        'reward' => ['xp' => 200, 'gold' => 120],
                    ],
                    [
                        'name' => 'Nanite Repair Swarm',
                        'description' => 'A swarm of medical nanites patches your wounds at the molecular level.',
                        'reward' => ['health' => 75],
                    ],
                ],
            ],
            'space' => [
                'age_key' => 'space',
                'age_name' => 'Space Age',
                'color_primary' => '#9370db',
                'color_secondary' => '#ba55d3',
                'enemies' => [
                    ['name' => 'Alien Scout', 'health' => 220, 'attack' => 55, 'xp' => 165],
                    ['name' => 'Space Pirate', 'health' => 230, 'attack' => 58, 'xp' => 170],
                    ['name' => 'Plasma Soldier', 'health' => 240, 'attack' => 60, 'xp' => 175],
                    ['name' => 'Void Wanderer', 'health' => 250, 'attack' => 62, 'xp' => 180],
                    ['name' => 'Nebula Wraith', 'health' => 215, 'attack' => 65, 'xp' => 185],
                    ['name' => 'Gravity Shifter', 'health' => 235, 'attack' => 57, 'xp' => 172],
                    ['name' => 'Antimatter Drone', 'health' => 200, 'attack' => 68, 'xp' => 190],
                    ['name' => 'Star Cultist', 'health' => 245, 'attack' => 59, 'xp' => 178],
                ],
                'mini_boss' => [
                    'name' => 'Warp Commander Zyx',
                    'health' => 700,
                    'attack' => 85,
                    'xp' => 560,
                    'description' => 'A cybernetically enhanced alien warlord who bends spacetime around his flagship',
                ],
                'boss' => [
                    'name' => 'Galactic Overlord',
                    'health' => 1000,
                    'attack' => 100,
                    'xp' => 800,
                    'description' => 'An ancient cosmic being from beyond the stars'
                ],
                'treasure_pool' => ['space_laser', 'space_suit', 'space_helmet', 'space_jetpack'],
                'event_themes' => ['space_station', 'alien_ruins', 'asteroid_field', 'wormhole'],
                'story_events' => [
                    [
                        'name' => 'Quantum Anomaly',
                        'description' => 'You pass through a rift in spacetime, emerging stronger on the other side.',
                        'reward' => ['xp' => 300, 'health' => 30],
                    ],
                    [
                        'name' => 'Alien Trade Hub',
                        'description' => 'A friendly alien species invites you to their orbital bazaar. The deals are incredible.',
                        'reward' => ['gold' => 600],
                    ],
                    [
                        'name' => 'Stasis Pod Recovery',
                        'description' => 'You find an ancient stasis pod with advanced medical technology still functional.',
                        'reward' => ['health' => 80],
                    ],
                    [
                        'name' => 'Star Map Fragment',
                        'description' => 'A fragment of a precursor star map reveals coordinates to uncharted systems.',
                        'reward' => ['xp' => 320],
                    ],
                    [
                        'name' => 'Asteroid Mining Rights',
                        'description' => 'You stake a claim on a platinum-rich asteroid and sell the mineral rights.',
                        'reward' => ['gold' => 700],
                    ],
                    [
                        'name' => 'Cosmic Meditation',
                        'description' => 'Floating in zero gravity near a nebula, you achieve a profound state of inner peace.',
                        'reward' => ['health' => 60, 'xp' => 100],
                    ],
                    [
                        'name' => 'Precursor Archive',
                        'description' => 'An ancient alien database uploads millennia of knowledge directly into your neural link.',
                        'reward' => ['xp' => 350],
                    ],
                    [
                        'name' => 'Derelict Salvage',
                        'description' => 'You salvage a derelict starship, recovering rare alloys and functional technology.',
                        'reward' => ['gold' => 550, 'xp' => 120],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get random enemy for age
     */
    public function getRandomEnemy(string $ageKey): array {
        $theme = $this->getThemeForAge($ageKey);
        $enemies = $theme['enemies'];
        return $enemies[array_rand($enemies)];
    }

    /**
     * Get boss for age
     */
    public function getBoss(string $ageKey): array {
        $theme = $this->getThemeForAge($ageKey);
        return $theme['boss'];
    }

    /**
     * Get mini-boss for age
     */
    public function getMiniBoss(string $ageKey): array {
        $theme = $this->getThemeForAge($ageKey);
        return $theme['mini_boss'];
    }

    /**
     * Get random treasure reward for age
     */
    public function getRandomTreasure(string $ageKey): array {
        $theme = $this->getThemeForAge($ageKey);
        $treasurePool = $theme['treasure_pool'];
        $itemKey = $treasurePool[array_rand($treasurePool)];

        return [
            'type' => 'equipment',
            'item_key' => $itemKey,
            'gold' => mt_rand(50, 150), // Additional gold bonus
        ];
    }

    /**
     * Get random event for age
     * Returns an age-specific story event with thematic narrative and rewards
     */
    public function getRandomEvent(string $ageKey): array {
        $theme = $this->getThemeForAge($ageKey);
        $storyEvents = $theme['story_events'];
        return $storyEvents[array_rand($storyEvents)];
    }

    /**
     * Get age key for current player level
     */
    public function getAgeKeyForLevel(int $level): string {
        if ($level < 10) return 'stone';
        if ($level < 20) return 'bronze';
        if ($level < 30) return 'iron';
        if ($level < 40) return 'medieval';
        if ($level < 50) return 'renaissance';
        if ($level < 60) return 'industrial';
        if ($level < 75) return 'modern';
        if ($level < 100) return 'digital';
        return 'space';
    }

    /**
     * Get theme colors for frontend rendering
     */
    public function getThemeColors(string $ageKey): array {
        $theme = $this->getThemeForAge($ageKey);
        return [
            'primary' => $theme['color_primary'],
            'secondary' => $theme['color_secondary'],
        ];
    }
}
