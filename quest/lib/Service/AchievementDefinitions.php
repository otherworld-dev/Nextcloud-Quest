<?php
/**
 * Achievement definitions — generated programmatically to avoid a 5000-line constant.
 */

namespace OCA\NextcloudQuest\Service;

class AchievementDefinitions {

    private static ?array $cache = null;

    public static function getAll(): array {
        if (self::$cache !== null) {
            return self::$cache;
        }
        self::$cache = array_merge(
            self::taskMaster(),
            self::streakKeeper(),
            self::levelChampion(),
            self::speedDemon(),
            self::xpLegends(),
            self::timeMaster(),
            self::consistencyMaster(),
            self::healthMaster(),
            self::priorityMaster(),
            self::weekendWarrior(),
            self::specialDates(),
            self::monthlyMastery(),
            self::comboAchievements(),
            self::rareSecret(),
            self::enduranceTitan(),
            self::worldConqueror(),
            self::communitySocial(),
            self::statisticalMarvels(),
            self::categorySpecialist(),
            self::journeyHero(),
            self::craftingMaster()
        );
        return self::$cache;
    }

    private static function m(string $name, string $desc, string $cat, string $rarity, int $milestone, string $icon = 'default.svg'): array {
        return ['name' => $name, 'description' => $desc, 'icon' => $icon, 'category' => $cat, 'rarity' => $rarity, 'progress_type' => 'milestone', 'milestone' => $milestone];
    }

    private static function s(string $name, string $desc, string $cat, string $rarity, string $icon = 'default.svg'): array {
        return ['name' => $name, 'description' => $desc, 'icon' => $icon, 'category' => $cat, 'rarity' => $rarity, 'progress_type' => 'special'];
    }

    private static function taskMaster(): array {
        $milestones = [
            1 => ['First Step', 'first-step.svg'],
            5 => ['High Five', 'default.svg'],
            10 => ['Task Initiator', 'tasks-10.svg'],
            15 => ['Getting Warmed Up', 'default.svg'],
            20 => ['Score!', 'default.svg'],
            25 => ['Quarter Century', 'default.svg'],
            30 => ['Dirty Thirty', 'default.svg'],
            35 => ['Halfway to Seventy', 'default.svg'],
            40 => ['Forty Winks', 'default.svg'],
            45 => ['Almost Fifty', 'default.svg'],
            50 => ['Half Century', 'tasks-50.svg'],
            60 => ['Sexagenarian', 'default.svg'],
            75 => ['Three Quarters', 'default.svg'],
            100 => ['Centurion', 'tasks-100.svg'],
            125 => ['And a Quarter', 'default.svg'],
            150 => ['Sesquicentennial', 'default.svg'],
            175 => ['Almost Two Hundred', 'default.svg'],
            200 => ['Double Century', 'default.svg'],
            250 => ['Quarter Thousand', 'tasks-250.svg'],
            300 => ['Spartan', 'default.svg'],
            350 => ['Triple Fifty', 'default.svg'],
            400 => ['Four Hundred Club', 'default.svg'],
            450 => ['Almost There', 'default.svg'],
            500 => ['Half Grand', 'tasks-500.svg'],
            600 => ['Six Hundred Strong', 'default.svg'],
            700 => ['Lucky Seven Hundred', 'default.svg'],
            750 => ['Three Quarter Grand', 'default.svg'],
            800 => ['Eight Hundred', 'default.svg'],
            900 => ['Nine Hundred', 'default.svg'],
            1000 => ['Grand Master', 'tasks-1000.svg'],
            1250 => ['Beyond a Thousand', 'default.svg'],
            1500 => ['Fifteen Hundred', 'default.svg'],
            1750 => ['Almost Two Grand', 'default.svg'],
            2000 => ['Two Thousand', 'default.svg'],
            2500 => ['Twenty Five Hundred', 'tasks-2500.svg'],
            3000 => ['Three Thousand', 'default.svg'],
            3500 => ['Thirty Five Hundred', 'default.svg'],
            4000 => ['Four Thousand', 'default.svg'],
            4500 => ['Forty Five Hundred', 'default.svg'],
            5000 => ['Five Grand', 'tasks-5000.svg'],
            7500 => ['Seventy Five Hundred', 'default.svg'],
            10000 => ['Ten Thousand', 'default.svg'],
            15000 => ['Fifteen Thousand', 'default.svg'],
            20000 => ['Twenty Thousand', 'default.svg'],
            25000 => ['Quarter Hundred K', 'default.svg'],
            30000 => ['Thirty Thousand', 'default.svg'],
            40000 => ['Forty Thousand', 'default.svg'],
            50000 => ['Fifty Grand', 'default.svg'],
        ];
        $a = [];
        foreach ($milestones as $n => [$name, $icon]) {
            $key = $n === 1 ? 'first_task' : 'tasks_' . $n;
            $rarity = $n <= 50 ? 'Common' : ($n <= 500 ? 'Rare' : ($n <= 5000 ? 'Epic' : ($n <= 25000 ? 'Legendary' : 'Mythic')));
            $a[$key] = self::m($name, "Complete $n tasks", 'Task Master', $rarity, $n, $icon);
        }
        $a['binary_master'] = self::m('Binary Master', 'Complete exactly 1024 tasks', 'Task Master', 'Epic', 1024, 'binary-master.svg');
        $a['golden_ratio'] = self::m('Golden Ratio', 'Complete exactly 1618 tasks', 'Task Master', 'Epic', 1618, 'golden-ratio.svg');
        $a['tasks_100000'] = self::m('One Hundred Thousand', 'Complete 100000 tasks', 'Task Master', 'Mythic', 100000);
        return $a;
    }

    private static function streakKeeper(): array {
        $names = [
            1 => 'First Flame', 2 => 'Double Down', 3 => 'Hat Trick', 4 => 'Four Play',
            5 => 'High Five Streak', 6 => 'Six Pack', 7 => 'Full Week',
            8 => 'Over a Week', 9 => 'Nine Days', 10 => 'Perfect Ten',
            11 => 'Eleven Strong', 12 => 'Dozen Days', 13 => 'Lucky Thirteen',
            14 => 'Fortnight', 15 => 'Half Month', 16 => 'Sweet Sixteen',
            17 => 'Seventeen', 18 => 'Legal Streak', 19 => 'Nineteen',
            20 => 'Score', 21 => 'Blackjack', 22 => 'Catch-22',
            23 => 'Jordan Number', 24 => 'Full Day Count', 25 => 'Silver Jubilee',
            26 => 'Marathon Letters', 27 => 'Cube Perfect', 28 => 'Lunar Cycle',
            29 => 'Twenty Nine', 30 => 'Monthly Master',
            45 => 'Six Weeks Plus', 60 => 'Two Month Warrior', 90 => 'Quarter Year',
            100 => 'Triple Digits', 120 => 'Four Months', 150 => 'Five Months',
            180 => 'Half Year Hero', 200 => 'Two Hundred Days', 250 => 'Eight Months',
            300 => 'Ten Months', 365 => 'Year Champion', 500 => 'Five Hundred Days',
            730 => 'Two Year Legend', 1000 => 'Thousand Day Titan',
        ];
        $a = [];
        foreach ($names as $n => $name) {
            $key = 'streak_' . $n;
            $icon = match(true) {
                in_array($n, [3,7,14,30,60,100,365]) => "streak-$n.svg",
                default => 'default.svg'
            };
            $rarity = $n <= 7 ? 'Common' : ($n <= 30 ? 'Rare' : ($n <= 100 ? 'Epic' : ($n <= 365 ? 'Legendary' : 'Mythic')));
            $a[$key] = self::m($name, "Maintain a $n-day streak", 'Streak Keeper', $rarity, $n, $icon);
        }
        return $a;
    }

    private static function levelChampion(): array {
        $a = [];
        for ($i = 1; $i <= 100; $i++) {
            $rarity = $i <= 10 ? 'Common' : ($i <= 25 ? 'Rare' : ($i <= 50 ? 'Epic' : 'Legendary'));
            $icon = in_array($i, [5,10,25,50,75,100]) ? "level-$i.svg" : 'default.svg';
            $name = match($i) {
                1 => 'Beginner', 5 => 'Apprentice', 10 => 'Journeyman',
                15 => 'Adept', 20 => 'Expert', 25 => 'Veteran',
                30 => 'Elite', 40 => 'Master', 50 => 'Grand Master',
                60 => 'Champion', 70 => 'Hero', 75 => 'Legend',
                80 => 'Mythical', 90 => 'Immortal', 99 => 'Penultimate',
                100 => 'Centurion',
                default => "Level $i"
            };
            $a['level_' . $i] = self::m($name, "Reach level $i", 'Level Champion', $rarity, $i, $icon);
        }
        return $a;
    }

    private static function speedDemon(): array {
        $vals = [2,3,4,5,6,7,8,9,10,12,14,15,16,18,20,25,30,35,40,50];
        $names = [
            2 => 'Quick Pair', 3 => 'Triple Threat', 4 => 'Quadruple', 5 => 'Speed Five',
            6 => 'Half Dozen Rush', 7 => 'Lucky Sprint', 8 => 'Octane', 9 => 'Nine Lives Speed',
            10 => 'Ten in Sixty', 12 => 'Dozen Dash', 14 => 'Fourteen Fury',
            15 => 'Quarter Hour Blitz', 16 => 'Sweet Speed', 18 => 'Eighteen Express',
            20 => 'Twenty Tornado', 25 => 'Quarter Century Sprint', 30 => 'Thirty Thrash',
            35 => 'Thirty Five Flash', 40 => 'Forty Frenzy', 50 => 'Fifty in an Hour',
        ];
        $a = [];
        foreach ($vals as $n) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 10 ? 'Rare' : ($n <= 20 ? 'Epic' : 'Legendary'));
            $icon = in_array($n, [3,5,10,15]) ? "speed-$n.svg" : 'default.svg';
            $a['speed_' . $n . '_in_hour'] = self::m($names[$n], "Complete $n tasks in one hour", 'Speed Demon', $rarity, $n, $icon);
        }
        // Speed streaks - maintain speed over multiple hours
        foreach ([2,3,4,5] as $n) {
            $rarity = $n <= 2 ? 'Rare' : ($n <= 4 ? 'Epic' : 'Legendary');
            $a['speed_streak_' . $n] = self::s("Speed Streak x$n", "Complete 5+ tasks per hour for $n consecutive hours", 'Speed Demon', $rarity, 'speed-runner.svg');
        }
        // Speed + volume combos
        $a['blitz_20'] = self::s('Blitz 20', 'Complete 20 tasks in 2 hours', 'Speed Demon', 'Epic');
        $a['blitz_50'] = self::s('Blitz 50', 'Complete 50 tasks in 3 hours', 'Speed Demon', 'Legendary');
        $a['speed_demon_day'] = self::s('Speed Demon Day', 'Complete 10+ tasks in an hour 3 times in one day', 'Speed Demon', 'Legendary', 'speed-demon.svg');
        // 30-minute sprints
        foreach ([3,5,8,10,15] as $n) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 10 ? 'Rare' : 'Epic');
            $a['sprint_30m_' . $n] = self::m("30min Sprint x$n", "Complete $n tasks in 30 minutes", 'Speed Demon', $rarity, $n);
        }
        // 10-minute bursts
        foreach ([2,3,5] as $n) {
            $rarity = $n <= 2 ? 'Common' : ($n <= 3 ? 'Rare' : 'Epic');
            $a['burst_10m_' . $n] = self::m("10min Burst x$n", "Complete $n tasks in 10 minutes", 'Speed Demon', $rarity, $n);
        }
        return $a;
    }

    private static function xpLegends(): array {
        $milestones = [100,250,500,750,1000,1500,2000,2500,3000,4000,5000,7500,
            10000,15000,20000,25000,30000,40000,50000,75000,100000,150000,200000,
            250000,300000,400000,500000,750000,1000000,2000000,3000000,5000000,
            7500000,10000000];
        $a = [];
        foreach ($milestones as $n) {
            $rarity = $n <= 5000 ? 'Common' : ($n <= 50000 ? 'Rare' : ($n <= 500000 ? 'Epic' : ($n <= 5000000 ? 'Legendary' : 'Mythic')));
            $fmt = $n >= 1000000 ? ($n / 1000000) . 'M' : ($n >= 1000 ? ($n / 1000) . 'K' : $n);
            $a['xp_' . $n] = self::m("$fmt XP", "Earn $n lifetime XP", 'XP Legends', $rarity, $n, 'default.svg');
        }
        // Special exact values
        $specials = [
            1337 => ['Leet XP', 'Earn exactly 1337 XP'],
            9999 => ['So Close', 'Earn exactly 9999 XP'],
            12345 => ['Sequential', 'Earn exactly 12345 XP'],
            31415 => ['Pi XP', 'Earn exactly 31415 XP'],
            42069 => ['Nice XP', 'Earn exactly 42069 XP'],
            99999 => ['Five Nines', 'Earn exactly 99999 XP'],
            314159 => ['Full Pi', 'Earn exactly 314159 XP'],
            27182 => ['Euler XP', 'Earn exactly 27182 XP'],
        ];
        foreach ($specials as $n => [$name, $desc]) {
            $rarity = $n <= 10000 ? 'Rare' : ($n <= 100000 ? 'Epic' : 'Legendary');
            $a['xp_exact_' . $n] = self::s($name, $desc, 'XP Legends', $rarity);
        }
        $a['perfect_score'] = self::s('Perfect Score', 'Earn exactly 10000 XP', 'XP Legends', 'Epic', 'perfect-score.svg');
        $a['xp_millionaire'] = self::m('XP Millionaire', 'Earn 1000000 lifetime XP', 'XP Legends', 'Mythic', 1000000, 'xp-millionaire.svg');
        return $a;
    }

    private static function timeMaster(): array {
        $a = [];
        $hourNames = [
            0 => 'Midnight', 1 => '1 AM Insomniac', 2 => '2 AM Night Owl', 3 => '3 AM Warrior',
            4 => '4 AM Early Riser', 5 => '5 AM Dawn Patrol', 6 => '6 AM Sunrise', 7 => '7 AM Morning',
            8 => '8 AM Commuter', 9 => '9 AM Office Start', 10 => '10 AM Mid-Morning',
            11 => '11 AM Pre-Lunch', 12 => 'High Noon', 13 => '1 PM Afternoon',
            14 => '2 PM Post-Lunch', 15 => '3 PM Tea Time', 16 => '4 PM Late Afternoon',
            17 => '5 PM Rush Hour', 18 => '6 PM Evening', 19 => '7 PM Dinner Time',
            20 => '8 PM Prime Time', 21 => '9 PM Night Session', 22 => '10 PM Late Night',
            23 => '11 PM Final Hour',
        ];
        foreach ($hourNames as $h => $name) {
            $a['hour_' . $h] = self::s($name, "Complete a task between {$h}:00 and {$h}:59", 'Time Master', 'Common', 'default.svg');
        }
        $a['dawn_raider'] = self::s('Dawn Raider', 'Complete a task before 6 AM', 'Time Master', 'Rare', 'dawn-raider.svg');
        $a['early_bird'] = self::s('Early Bird', 'Complete a task between 6-9 AM', 'Time Master', 'Common', 'early-bird.svg');
        $a['night_owl'] = self::s('Night Owl', 'Complete a task after 9 PM', 'Time Master', 'Common', 'night-owl.svg');
        $a['midnight_warrior'] = self::s('Midnight Warrior', 'Complete a task at midnight', 'Time Master', 'Rare', 'midnight-warrior.svg');
        $a['morning_person'] = self::m('Morning Person', 'Complete 50 tasks before 9 AM', 'Time Master', 'Rare', 50);
        $a['night_person'] = self::m('Night Person', 'Complete 50 tasks after 9 PM', 'Time Master', 'Rare', 50);
        // Extended time milestones
        $a['morning_100'] = self::m('Morning Centurion', 'Complete 100 tasks before 9 AM', 'Time Master', 'Epic', 100);
        $a['night_100'] = self::m('Night Centurion', 'Complete 100 tasks after 9 PM', 'Time Master', 'Epic', 100);
        $a['lunch_rush'] = self::s('Lunch Rush', 'Complete 5+ tasks between 12-1 PM', 'Time Master', 'Common');
        $a['afternoon_blitz'] = self::s('Afternoon Blitz', 'Complete 10+ tasks between 1-5 PM', 'Time Master', 'Rare');
        $a['golden_hour'] = self::s('Golden Hour', 'Complete a task during sunrise (5-6 AM)', 'Time Master', 'Rare');
        $a['witching_hour'] = self::s('Witching Hour', 'Complete a task between 3-4 AM', 'Time Master', 'Epic');
        $a['round_the_clock'] = self::s('Round the Clock', 'Complete tasks in at least 12 different hours in one day', 'Time Master', 'Legendary');
        $a['time_traveler'] = self::s('Time Traveler', 'Complete tasks across all 24 hours (lifetime)', 'Time Master', 'Epic');
        $a['morning_streak_7'] = self::m('Morning Routine', 'Complete a task before 9 AM for 7 consecutive days', 'Time Master', 'Rare', 7);
        $a['morning_streak_30'] = self::m('Morning Habit', 'Complete a task before 9 AM for 30 consecutive days', 'Time Master', 'Epic', 30);
        return $a;
    }

    private static function consistencyMaster(): array {
        $dailyMilestones = [1,2,3,5,8,10,12,15,20,25,30,40,50,75,100];
        $a = [];
        foreach ($dailyMilestones as $n) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 15 ? 'Rare' : ($n <= 50 ? 'Epic' : 'Legendary'));
            $name = match($n) {
                1 => 'Daily Starter', 2 => 'Daily Double', 3 => 'Daily Triple',
                5 => 'Handful', 8 => 'Octave', 10 => 'Daily Ten',
                12 => 'Daily Dozen', 15 => 'Fifteen Today', 20 => 'Daily Score',
                25 => 'Quarter Hundred', 30 => 'Thirty Today', 40 => 'Forty Today',
                50 => 'Half Century Day', 75 => 'Seventy Five Today', 100 => 'Century Day',
                default => "$n Today"
            };
            $icon = $n === 12 ? 'daily-dozen.svg' : 'default.svg';
            $a['daily_' . $n] = self::m($name, "Complete $n tasks in a single day", 'Consistency Master', $rarity, $n, $icon);
        }
        // Weekly perfection
        $weekMilestones = [1,2,3,4,8,12,26,52];
        foreach ($weekMilestones as $n) {
            $rarity = $n <= 2 ? 'Common' : ($n <= 8 ? 'Rare' : ($n <= 26 ? 'Epic' : 'Legendary'));
            $a['perfect_weeks_' . $n] = self::m("$n Perfect Weeks", "Complete tasks every day for $n weeks", 'Consistency Master', $rarity, $n, $n === 1 ? 'perfect-week.svg' : 'default.svg');
        }
        // Monthly
        $monthMilestones = [1,2,3,6,12];
        foreach ($monthMilestones as $n) {
            $rarity = $n <= 1 ? 'Rare' : ($n <= 3 ? 'Epic' : 'Legendary');
            $label = $n === 1 ? 'month' : 'months';
            $a['perfect_months_' . $n] = self::m("$n Perfect " . ucfirst($label), "Complete tasks every day for $n $label", 'Consistency Master', $rarity, $n, $n === 1 ? 'monthly-perfect.svg' : 'default.svg');
        }
        // Combos
        $a['productivity_machine'] = self::s('Productivity Machine', 'Complete 10+ tasks every day for 7 consecutive days', 'Consistency Master', 'Legendary', 'productivity-guru.svg');
        $a['consistency_king'] = self::s('Consistency King', 'Complete 5+ tasks every day for 30 consecutive days', 'Consistency Master', 'Mythic', 'consistent-performer.svg');
        // Early and late day combos
        $a['early_finisher'] = self::s('Early Finisher', 'Complete 5 tasks before noon', 'Consistency Master', 'Common');
        $a['late_push'] = self::s('Late Push', 'Complete 5 tasks after 8 PM', 'Consistency Master', 'Common');
        $a['all_day'] = self::s('All Day Long', 'Complete tasks in morning, afternoon, and evening in one day', 'Consistency Master', 'Rare');
        // Streak + daily combos
        foreach ([3,7,14,30] as $n) {
            $rarity = $n <= 7 ? 'Rare' : ($n <= 14 ? 'Epic' : 'Legendary');
            $a['daily_3_for_' . $n] = self::s("3/Day for $n Days", "Complete 3+ tasks daily for $n consecutive days", 'Consistency Master', $rarity);
        }
        return $a;
    }

    private static function healthMaster(): array {
        $a = [];
        $surviveThresholds = [1, 5, 10, 25, 50];
        foreach ($surviveThresholds as $pct) {
            $rarity = $pct <= 5 ? 'Epic' : ($pct <= 10 ? 'Rare' : 'Common');
            $a['survive_' . $pct . 'pct'] = self::s("Survived at $pct%", "Have health drop to $pct% and survive", 'Health Master', $rarity, $pct <= 5 ? 'phoenix-rising.svg' : 'default.svg');
        }
        $recoveries = [1,5,10,25,50];
        foreach ($recoveries as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 10 ? 'Rare' : ($n <= 25 ? 'Epic' : 'Legendary'));
            $a['recovery_' . $n] = self::m("Recovery x$n", "Recover from below 25% health $n times", 'Health Master', $rarity, $n);
        }
        $fullHealthDays = [1,3,7,14,30,60,90];
        foreach ($fullHealthDays as $n) {
            $rarity = $n <= 3 ? 'Common' : ($n <= 14 ? 'Rare' : ($n <= 30 ? 'Epic' : 'Legendary'));
            $a['full_health_' . $n . 'd'] = self::m("Full Health $n Days", "Maintain 100% health for $n days", 'Health Master', $rarity, $n, $n === 7 ? 'healthy-week.svg' : 'default.svg');
        }
        $penaltySurvives = [1,5,10,25,50];
        foreach ($penaltySurvives as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 10 ? 'Rare' : ($n <= 25 ? 'Epic' : 'Legendary'));
            $a['penalty_survived_' . $n] = self::m("Penalty Survivor x$n", "Receive and recover from $n health penalties", 'Health Master', $rarity, $n);
        }
        $a['iron_constitution'] = self::s('Iron Constitution', 'Never lose health for 30 consecutive days', 'Health Master', 'Legendary', 'health-champion.svg');
        $a['health_phoenix'] = self::s('Phoenix Rising', 'Recover from 1% health to 100%', 'Health Master', 'Epic', 'phoenix-rising.svg');
        $a['zero_tolerance'] = self::s('Zero Tolerance', 'Hit 0 health and recover', 'Health Master', 'Legendary', 'zero-tolerance.svg');
        // Health milestones
        foreach ([100,500,1000,2500] as $n) {
            $rarity = $n <= 100 ? 'Common' : ($n <= 500 ? 'Rare' : ($n <= 1000 ? 'Epic' : 'Legendary'));
            $a['health_regen_' . $n] = self::m("Regen x$n", "Regenerate $n total health points", 'Health Master', $rarity, $n);
        }
        $a['health_tank'] = self::s('Tank', 'Reach 200 max health', 'Health Master', 'Epic');
        $a['health_regen_streak'] = self::s('Healing Streak', 'Regenerate health 7 days in a row', 'Health Master', 'Rare');
        // No penalty streaks
        foreach ([7,14,30,60,90] as $n) {
            $rarity = $n <= 14 ? 'Rare' : ($n <= 30 ? 'Epic' : 'Legendary');
            $a['no_penalty_' . $n] = self::m("No Penalty $n Days", "Avoid health penalties for $n consecutive days", 'Health Master', $rarity, $n);
        }
        $a['health_master'] = self::s('Health Master', 'Maintain 90%+ health for 30 days', 'Health Master', 'Legendary', 'health-champion.svg');
        $a['clutch_heal'] = self::s('Clutch Heal', 'Regenerate health when below 5%', 'Health Master', 'Epic');
        $a['health_yo_yo'] = self::s('Yo-Yo Health', 'Go from 100% to below 25% and back 3 times', 'Health Master', 'Rare');
        return $a;
    }

    private static function priorityMaster(): array {
        $a = [];
        $levels = [1,5,10,25,50,100,250,500];
        foreach (['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $key => $label) {
            foreach ($levels as $n) {
                $rarity = $n <= 10 ? 'Common' : ($n <= 50 ? 'Rare' : ($n <= 250 ? 'Epic' : 'Legendary'));
                $icon = $n >= 100 ? 'priority-perfect.svg' : 'default.svg';
                $a[$key . '_priority_' . $n] = self::m("$label Priority x$n", "Complete $n $label priority tasks", 'Priority Master', $rarity, $n, $icon);
            }
        }
        // Mixed priority achievements
        $mixDays = [1,5,10,25,50];
        foreach ($mixDays as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 10 ? 'Rare' : ($n <= 25 ? 'Epic' : 'Legendary'));
            $a['priority_mix_' . $n] = self::m("Priority Mixer x$n", "Complete all 3 priorities in one day $n times", 'Priority Master', $rarity, $n);
        }
        $a['priority_perfectionist'] = self::s('Priority Perfectionist', 'Complete 10+ high priority tasks in one day', 'Priority Master', 'Epic', 'priority-prophet.svg');
        $a['all_high_day'] = self::s('All High Day', 'Complete only high priority tasks for a full day (5+ tasks)', 'Priority Master', 'Rare');
        $a['priority_balance'] = self::s('Perfect Balance', 'Complete equal numbers of each priority in one day', 'Priority Master', 'Epic');
        // Extra priority milestones
        foreach ([1000,2000,5000] as $n) {
            $a['high_priority_' . $n] = self::m("High Priority x$n", "Complete $n high priority tasks", 'Priority Master', 'Mythic', $n);
            $a['medium_priority_' . $n] = self::m("Medium Priority x$n", "Complete $n medium priority tasks", 'Priority Master', 'Mythic', $n);
            $a['low_priority_' . $n] = self::m("Low Priority x$n", "Complete $n low priority tasks", 'Priority Master', 'Mythic', $n);
        }
        return $a;
    }

    private static function weekendWarrior(): array {
        $a = [];
        $satMilestones = [1,5,10,25,50,100];
        foreach ($satMilestones as $n) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 25 ? 'Rare' : ($n <= 50 ? 'Epic' : 'Legendary'));
            $a['saturday_tasks_' . $n] = self::m("Saturday x$n", "Complete $n tasks on Saturdays", 'Weekend Warrior', $rarity, $n);
        }
        foreach ($satMilestones as $n) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 25 ? 'Rare' : ($n <= 50 ? 'Epic' : 'Legendary'));
            $a['sunday_tasks_' . $n] = self::m("Sunday x$n", "Complete $n tasks on Sundays", 'Weekend Warrior', $rarity, $n);
        }
        $bothMilestones = [1,5,10,25,50];
        foreach ($bothMilestones as $n) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 10 ? 'Rare' : ($n <= 25 ? 'Epic' : 'Legendary'));
            $a['weekend_both_' . $n] = self::m("Full Weekend x$n", "Complete tasks on both Saturday and Sunday $n weekends", 'Weekend Warrior', $rarity, $n);
        }
        $weekendStreaks = [1,2,4,8,12,26,52];
        foreach ($weekendStreaks as $n) {
            $rarity = $n <= 2 ? 'Common' : ($n <= 8 ? 'Rare' : ($n <= 26 ? 'Epic' : 'Legendary'));
            $a['weekend_streak_' . $n] = self::m("Weekend Streak x$n", "Complete tasks every weekend for $n consecutive weekends", 'Weekend Warrior', $rarity, $n);
        }
        $a['weekend_warrior'] = self::s('Weekend Warrior', 'Complete tasks on both Saturday and Sunday', 'Weekend Warrior', 'Common', 'weekend-warrior.svg');
        $a['saturday_sprint'] = self::s('Saturday Sprint', 'Complete 10+ tasks on a Saturday', 'Weekend Warrior', 'Rare');
        $a['saturday_marathon'] = self::s('Saturday Marathon', 'Complete 25+ tasks on a Saturday', 'Weekend Warrior', 'Epic');
        $a['sunday_chill'] = self::s('Sunday Chill', 'Complete at least 1 task every Sunday for a month', 'Weekend Warrior', 'Rare');
        $a['sunday_marathon'] = self::s('Sunday Marathon', 'Complete 25+ tasks on a Sunday', 'Weekend Warrior', 'Epic');
        $a['weekend_perfect_month'] = self::s('Perfect Weekend Month', 'Complete tasks every weekend day for a full month', 'Weekend Warrior', 'Epic');
        $a['weekend_50'] = self::m('Weekend Half Century', 'Complete 50 tasks across weekends total', 'Weekend Warrior', 'Rare', 50);
        $a['weekend_200'] = self::m('Weekend Bicentennial', 'Complete 200 tasks across weekends total', 'Weekend Warrior', 'Epic', 200);
        $a['weekend_500'] = self::m('Weekend Legend', 'Complete 500 tasks across weekends total', 'Weekend Warrior', 'Legendary', 500);
        $a['weekend_centurion'] = self::s('Weekend Centurion', 'Complete 100 tasks across weekends', 'Weekend Warrior', 'Epic');
        // Weekend daily targets
        foreach ([5,10,15,20,25] as $n) {
            $a['weekend_day_' . $n] = self::s("Weekend $n", "Complete $n tasks in a single weekend day", 'Weekend Warrior', $n <= 10 ? 'Rare' : 'Epic');
        }
        // Weekend month streaks
        foreach ([2,3,6] as $n) {
            $a['weekend_month_' . $n] = self::m("Weekend $n Months", "Complete tasks every weekend for $n months", 'Weekend Warrior', $n <= 3 ? 'Epic' : 'Legendary', $n);
        }
        return $a;
    }

    private static function specialDates(): array {
        $a = [];
        $months = ['jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June',
                   'jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December'];
        foreach ($months as $k => $name) {
            $a[$k . '_first'] = self::s("$name Opener", "Complete a task on January 1st... $name 1st", 'Special Dates', 'Common');
            $a[$k . '_last'] = self::s("$name Closer", "Complete a task on the last day of $name", 'Special Dates', 'Common');
        }
        // Holidays
        $holidays = [
            'new_year_resolution' => ['New Year Resolution', 'Complete a task on January 1st', 'new-year.svg'],
            'valentines' => ['Valentine Tasker', 'Complete a task on February 14th', 'default.svg'],
            'pi_day' => ['Pi Day', 'Complete a task on March 14th', 'default.svg'],
            'st_patricks' => ['Lucky Tasker', 'Complete a task on March 17th', 'default.svg'],
            'april_fools' => ['April Fooled', 'Complete a task on April 1st', 'default.svg'],
            'earth_day' => ['Earth Day Hero', 'Complete a task on April 22nd', 'default.svg'],
            'may_day' => ['May Day Worker', 'Complete a task on May 1st', 'default.svg'],
            'summer_solstice' => ['Longest Day', 'Complete a task on June 21st', 'default.svg'],
            'halloween' => ['Spooky Tasker', 'Complete a task on October 31st', 'default.svg'],
            'christmas_eve' => ['Christmas Eve Elf', 'Complete a task on December 24th', 'default.svg'],
            'christmas' => ['Christmas Hero', 'Complete a task on December 25th', 'default.svg'],
            'new_years_eve' => ['Year Ender', 'Complete a task on December 31st', 'default.svg'],
            'star_wars_day' => ['May the Tasks Be With You', 'Complete a task on May 4th', 'default.svg'],
        ];
        foreach ($holidays as $k => [$name, $desc, $icon]) {
            $a[$k] = self::s($name, $desc, 'Special Dates', 'Rare', $icon);
        }
        // Seasons
        $a['spring_starter'] = self::s('Spring Starter', 'Complete first task of Spring (March)', 'Special Dates', 'Common');
        $a['summer_starter'] = self::s('Summer Starter', 'Complete first task of Summer (June)', 'Special Dates', 'Common');
        $a['autumn_starter'] = self::s('Autumn Starter', 'Complete first task of Autumn (September)', 'Special Dates', 'Common');
        $a['winter_starter'] = self::s('Winter Starter', 'Complete first task of Winter (December)', 'Special Dates', 'Common');
        $a['four_seasons'] = self::s('Four Seasons', 'Complete tasks in all four seasons', 'Special Dates', 'Rare', 'four-seasons.svg');
        // Special
        $a['palindrome_day'] = self::s('Palindrome Day', 'Complete a task on a palindrome date', 'Special Dates', 'Rare', 'palindrome-power.svg');
        $a['friday_13th'] = self::s('Friday the 13th', 'Complete 13 tasks on Friday the 13th', 'Special Dates', 'Epic', 'lucky-13.svg');
        $a['leap_day_legend'] = self::s('Leap Day Legend', 'Complete a task on February 29th', 'Special Dates', 'Legendary', 'leap-day.svg');
        $a['groundhog_day'] = self::s('Groundhog Day', 'Complete a task on February 2nd', 'Special Dates', 'Rare');
        $a['pirate_day'] = self::s('Pirate Tasker', 'Complete a task on September 19th', 'Special Dates', 'Rare');
        // Year anniversaries
        for ($y = 1; $y <= 5; $y++) {
            $rarity = $y <= 1 ? 'Rare' : ($y <= 3 ? 'Epic' : 'Legendary');
            $a['anniversary_' . $y] = self::s("Year $y Anniversary", "Use Quest for $y years", 'Special Dates', $rarity, $y === 1 ? 'anniversary-hero.svg' : 'default.svg');
        }
        // Monthly centuries
        foreach ($months as $k => $name) {
            $a['century_' . $k] = self::m("$name Century", "Complete 100 tasks in $name", 'Special Dates', 'Epic', 100);
        }
        // Day of week firsts
        $days = ['monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday','thursday'=>'Thursday','friday'=>'Friday'];
        foreach ($days as $k => $d) {
            $a[$k . '_first'] = self::s("First $d", "Complete your first task on a $d", 'Special Dates', 'Common');
        }
        // Sequential dates
        $a['sequential_date'] = self::s('Sequential Date', 'Complete a task on a sequential date (1/2/34, 3/4/56...)', 'Special Dates', 'Epic');
        $a['repeating_date'] = self::s('Repeating Date', 'Complete a task on a repeating date (1/1, 2/2, 3/3...)', 'Special Dates', 'Rare');
        // Equinox/solstice
        $a['spring_equinox'] = self::s('Spring Equinox', 'Complete a task on March 20th', 'Special Dates', 'Rare');
        $a['autumn_equinox'] = self::s('Autumn Equinox', 'Complete a task on September 22nd', 'Special Dates', 'Rare');
        $a['winter_solstice'] = self::s('Shortest Day', 'Complete a task on December 21st', 'Special Dates', 'Rare');
        // Month transitions
        foreach ([1,6,12] as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 6 ? 'Rare' : 'Epic');
            $a['month_transition_' . $n] = self::m("Month Bridger x$n", "Complete tasks on the last and first day of consecutive months $n times", 'Special Dates', $rarity, $n);
        }
        $a['birthday_task'] = self::s('Birthday Bonus', 'Complete 10 tasks on the anniversary of your first task', 'Special Dates', 'Epic', 'birthday-bonus.svg');
        $a['midnight_exact'] = self::s('Stroke of Midnight', 'Complete a task at exactly 00:00', 'Special Dates', 'Legendary');
        return $a;
    }

    private static function monthlyMastery(): array {
        $a = [];
        $monthTasks = [10,25,50,100,250,500];
        foreach ($monthTasks as $n) {
            $rarity = $n <= 25 ? 'Common' : ($n <= 100 ? 'Rare' : ($n <= 250 ? 'Epic' : 'Legendary'));
            $a['month_tasks_' . $n] = self::m("$n in a Month", "Complete $n tasks in a single calendar month", 'Monthly Mastery', $rarity, $n);
        }
        $monthStreaks = [7,10,14,20,28,30,31];
        foreach ($monthStreaks as $n) {
            $rarity = $n <= 10 ? 'Common' : ($n <= 20 ? 'Rare' : 'Epic');
            $a['month_streak_' . $n] = self::m("$n Day Month Streak", "Achieve a $n-day streak within a month", 'Monthly Mastery', $rarity, $n);
        }
        // Quarter and year goals
        $quarterTasks = [100,250,500,1000,2500];
        foreach ($quarterTasks as $n) {
            $rarity = $n <= 250 ? 'Rare' : ($n <= 1000 ? 'Epic' : 'Legendary');
            $a['quarter_tasks_' . $n] = self::m("$n in a Quarter", "Complete $n tasks in a calendar quarter", 'Monthly Mastery', $rarity, $n);
        }
        $yearTasks = [500,1000,2500,5000,10000];
        foreach ($yearTasks as $n) {
            $rarity = $n <= 1000 ? 'Rare' : ($n <= 5000 ? 'Epic' : 'Legendary');
            $a['year_tasks_' . $n] = self::m("$n in a Year", "Complete $n tasks in a calendar year", 'Monthly Mastery', $rarity, $n);
        }
        // Repeated monthly
        $monthlyRepeats = [3,6,12,24];
        foreach ($monthlyRepeats as $n) {
            $rarity = $n <= 6 ? 'Rare' : ($n <= 12 ? 'Epic' : 'Legendary');
            $a['monthly_goal_' . $n] = self::m("Monthly Goal x$n", "Hit 50+ tasks in $n different months", 'Monthly Mastery', $rarity, $n);
        }
        $a['new_month_energy_6'] = self::m('New Month Energy', 'Complete 5+ tasks on the 1st of the month for 6 months', 'Monthly Mastery', 'Epic', 6);
        $a['new_month_energy_12'] = self::m('Full Year Opener', 'Complete 5+ tasks on the 1st of the month for 12 months', 'Monthly Mastery', 'Legendary', 12);
        $a['best_month_ever'] = self::s('Best Month Ever', 'Beat your previous best monthly task count', 'Monthly Mastery', 'Rare');
        $a['monthly_ascension'] = self::s('Monthly Ascension', 'Increase task count every month for 3 consecutive months', 'Monthly Mastery', 'Epic');
        $a['monthly_ascension_6'] = self::s('Half Year Climb', 'Increase task count every month for 6 months', 'Monthly Mastery', 'Legendary');
        // Month-specific records
        foreach (['jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June',
                  'jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December'] as $k => $name) {
            $a['month_record_' . $k] = self::s("$name Record", "Set a personal best task count in $name", 'Monthly Mastery', 'Rare');
        }
        // Perfect month types
        $a['perfect_28'] = self::s('Perfect 28', 'Complete tasks every day of a 28-day month', 'Monthly Mastery', 'Epic');
        $a['perfect_30'] = self::s('Perfect 30', 'Complete tasks every day of a 30-day month', 'Monthly Mastery', 'Epic');
        $a['perfect_31'] = self::s('Perfect 31', 'Complete tasks every day of a 31-day month', 'Monthly Mastery', 'Legendary');
        // Monthly speed records
        foreach ([1,3,5,10] as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 5 ? 'Rare' : 'Epic');
            $a['fast_month_start_' . $n] = self::m("Fast Start x$n", "Complete $n tasks on the 1st day of a month $n times", 'Monthly Mastery', $rarity, $n);
        }
        $a['month_complete_early'] = self::s('Early Bird Month', 'Complete your monthly goal in the first 2 weeks', 'Monthly Mastery', 'Epic');
        $a['consistent_quarters'] = self::s('Consistent Quarters', 'Complete 100+ tasks in 4 consecutive quarters', 'Monthly Mastery', 'Legendary');
        // Seasonal mastery
        foreach (['spring' => 'Spring', 'summer' => 'Summer', 'autumn' => 'Autumn', 'winter' => 'Winter'] as $k => $s) {
            $a[$k . '_mastery_100'] = self::m("$s Century", "Complete 100 tasks during $s", 'Monthly Mastery', 'Rare', 100);
            $a[$k . '_mastery_500'] = self::m("$s Legend", "Complete 500 tasks during $s", 'Monthly Mastery', 'Epic', 500);
        }
        $a['all_seasons_100'] = self::s('All Seasons Century', 'Complete 100+ tasks in each season in a year', 'Monthly Mastery', 'Legendary');
        $a['year_round'] = self::s('Year Round', 'Complete tasks in all 12 months of a year', 'Monthly Mastery', 'Rare');
        $a['quarterly_improvement'] = self::s('Quarterly Improvement', 'Improve task count each quarter for a full year', 'Monthly Mastery', 'Legendary');
        return $a;
    }

    private static function comboAchievements(): array {
        $a = [];
        // Level + Streak
        $combos = [
            [10, 7], [15, 14], [20, 21], [25, 30], [30, 30],
            [40, 45], [50, 60], [60, 90], [75, 100], [100, 365],
        ];
        foreach ($combos as [$lvl, $str]) {
            $rarity = $lvl <= 20 ? 'Rare' : ($lvl <= 50 ? 'Epic' : 'Legendary');
            $a["combo_lvl{$lvl}_str{$str}"] = self::s("Level $lvl + $str Streak", "Reach level $lvl while maintaining a $str-day streak", 'Combo Achievements', $rarity);
        }
        // Multi-day high output
        $multiDay = [3,5,7,10,14,21,30];
        foreach ($multiDay as $n) {
            $rarity = $n <= 5 ? 'Rare' : ($n <= 14 ? 'Epic' : 'Legendary');
            $a['multi_day_10x' . $n] = self::s("10+ for $n Days", "Complete 10+ tasks for $n consecutive days", 'Combo Achievements', $rarity);
        }
        $multiDay5 = [3,5,7,14,30];
        foreach ($multiDay5 as $n) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 14 ? 'Rare' : 'Epic');
            $a['multi_day_5x' . $n] = self::s("5+ for $n Days", "Complete 5+ tasks for $n consecutive days", 'Combo Achievements', $rarity);
        }
        // XP + Task combos
        $xpTaskCombos = [[1000,50],[2500,100],[5000,200],[10000,500],[25000,1000],[50000,2000],[100000,5000]];
        foreach ($xpTaskCombos as [$xp,$tasks]) {
            $rarity = $xp <= 5000 ? 'Rare' : ($xp <= 50000 ? 'Epic' : 'Legendary');
            $xpFmt = $xp >= 1000 ? ($xp/1000).'K' : $xp;
            $a["combo_xp{$xp}_tasks{$tasks}"] = self::s("{$xpFmt} XP from $tasks Tasks", "Earn $xp XP from $tasks completed tasks", 'Combo Achievements', $rarity);
        }
        // Special combos
        $a['triple_threat'] = self::s('Triple Threat', '10+ tasks, 3+ streak, and level up in one day', 'Combo Achievements', 'Legendary');
        $a['perfectionist'] = self::s('Perfectionist', '100% health, active streak, and 10+ tasks today', 'Combo Achievements', 'Epic');
        $a['unstoppable'] = self::s('Unstoppable', 'Level 50+ with 100+ day streak', 'Combo Achievements', 'Mythic');
        $a['morning_streak'] = self::s('Morning Streak', 'Complete a task before 7 AM for 7 consecutive days', 'Combo Achievements', 'Epic');
        $a['night_streak'] = self::s('Night Streak', 'Complete a task after 10 PM for 7 consecutive days', 'Combo Achievements', 'Epic');
        $a['speed_consistency'] = self::s('Speed + Consistency', '5 tasks in one hour AND 20 tasks today', 'Combo Achievements', 'Epic');
        $a['weekend_level_up'] = self::s('Weekend Level Up', 'Level up on a weekend', 'Combo Achievements', 'Rare');
        $a['streak_level_up'] = self::s('Streak Level Up', 'Level up while on a 14+ day streak', 'Combo Achievements', 'Rare');
        $a['double_level_day'] = self::s('Double Level Day', 'Gain 2 levels in one day', 'Combo Achievements', 'Epic');
        $a['full_health_level_up'] = self::s('Healthy Ascent', 'Level up with 100% health', 'Combo Achievements', 'Common');
        $a['morning_level_up'] = self::s('Morning Ascent', 'Level up before 9 AM', 'Combo Achievements', 'Rare');
        $a['night_level_up'] = self::s('Night Ascent', 'Level up after 9 PM', 'Combo Achievements', 'Rare');
        $a['health_streak_7'] = self::s('Healthy Streak', 'Full health throughout a 7-day streak', 'Combo Achievements', 'Rare');
        $a['health_streak_30'] = self::s('Iron Health', 'Full health throughout a 30-day streak', 'Combo Achievements', 'Legendary');
        // Variety combos
        $a['all_priorities_week'] = self::s('Priority Rainbow', 'Complete all 3 priorities every day for a week', 'Combo Achievements', 'Epic');
        $a['all_lists_day'] = self::s('List Juggler', 'Complete tasks from 5+ lists in one day', 'Combo Achievements', 'Rare');
        // Streak + Daily combos
        foreach ([7,14,30] as $s) {
            foreach ([5,10,20] as $d) {
                $rarity = ($s >= 30 || $d >= 20) ? 'Legendary' : (($s >= 14 || $d >= 10) ? 'Epic' : 'Rare');
                $a["combo_str{$s}_daily{$d}"] = self::s("$s Streak + $d/Day", "Maintain $s-day streak while completing $d+ tasks daily", 'Combo Achievements', $rarity);
            }
        }
        // Level + XP combos
        foreach ([25,50,75,100] as $l) {
            $rarity = $l <= 25 ? 'Rare' : ($l <= 50 ? 'Epic' : 'Legendary');
            $xpNeeded = $l * 1000;
            $a["combo_lvl{$l}_xp"] = self::s("Level $l + {$xpNeeded} XP", "Reach level $l with {$xpNeeded}+ lifetime XP", 'Combo Achievements', $rarity);
        }
        return $a;
    }

    private static function rareSecret(): array {
        $a = [];
        // Fibonacci
        $fibs = [1,2,3,5,8,13,21,34,55,89,144,233,377,610,987];
        foreach ($fibs as $n) {
            $rarity = $n <= 13 ? 'Common' : ($n <= 89 ? 'Rare' : ($n <= 377 ? 'Epic' : 'Legendary'));
            $a['fib_' . $n] = self::s("Fibonacci $n", "Reach exactly $n total tasks", 'Rare & Secret', $rarity);
        }
        // Primes
        $primes = [2,3,5,7,11,13,17,19,23,29,31,37,41,43,47];
        foreach ($primes as $n) {
            $a['prime_' . $n] = self::s("Prime $n", "Reach exactly $n total tasks", 'Rare & Secret', $n <= 13 ? 'Common' : 'Rare');
        }
        // Powers of 2
        $pows = [2,4,8,16,32,64,128,256,512,1024];
        foreach ($pows as $n) {
            $rarity = $n <= 16 ? 'Common' : ($n <= 128 ? 'Rare' : ($n <= 512 ? 'Epic' : 'Legendary'));
            $a['pow2_' . $n] = self::s("Power of 2: $n", "Reach exactly $n total tasks", 'Rare & Secret', $rarity);
        }
        // Lucky numbers
        $a['lucky_7'] = self::s('Lucky Seven', 'Reach exactly 7 total tasks', 'Rare & Secret', 'Common');
        $a['lucky_77'] = self::s('Double Lucky', 'Reach exactly 77 total tasks', 'Rare & Secret', 'Rare');
        $a['lucky_777'] = self::s('Triple Lucky', 'Reach exactly 777 total tasks', 'Rare & Secret', 'Epic');
        $a['lucky_7777'] = self::s('Jackpot', 'Reach exactly 7777 total tasks', 'Rare & Secret', 'Legendary');
        // Fun
        $a['answer_42'] = self::s('Answer to Everything', 'Reach exactly 42 total tasks', 'Rare & Secret', 'Rare');
        $a['nice_69'] = self::s('Nice', 'Reach exactly 69 total tasks', 'Rare & Secret', 'Rare');
        $a['century_exact'] = self::s('Exact Century', 'Reach exactly 100 total tasks', 'Rare & Secret', 'Rare');
        $a['millennium_exact'] = self::s('Exact Millennium', 'Reach exactly 1000 total tasks', 'Rare & Secret', 'Epic', 'millennium-master.svg');
        $a['round_500'] = self::s('Half Grand Exact', 'Reach exactly 500 total tasks', 'Rare & Secret', 'Rare');
        // Round numbers
        foreach ([200,300,400,600,700,800,900,1500,2000,2500,3000,5000] as $n) {
            $rarity = $n <= 500 ? 'Rare' : ($n <= 2000 ? 'Epic' : 'Legendary');
            $a['round_' . $n] = self::s("Round $n", "Reach exactly $n total tasks", 'Rare & Secret', $rarity);
        }
        // Repeating digits
        foreach ([11,22,33,44,55,66,88,99,111,222,333,444,555,666,888,999] as $n) {
            $rarity = $n <= 99 ? 'Common' : ($n <= 555 ? 'Rare' : 'Epic');
            $a['repeat_' . $n] = self::s("Repeating $n", "Reach exactly $n total tasks", 'Rare & Secret', $rarity);
        }
        return $a;
    }

    private static function enduranceTitan(): array {
        $a = [];
        $activeMonths = [1,2,3,4,5,6,9,12,18,24,36];
        foreach ($activeMonths as $n) {
            $rarity = $n <= 3 ? 'Common' : ($n <= 9 ? 'Rare' : ($n <= 18 ? 'Epic' : 'Legendary'));
            $a['active_months_' . $n] = self::m("$n Months Active", "Be active for $n months", 'Endurance Titan', $rarity, $n);
        }
        $activeWeeks = [10,25,50,100,200];
        foreach ($activeWeeks as $n) {
            $rarity = $n <= 25 ? 'Rare' : ($n <= 100 ? 'Epic' : 'Legendary');
            $a['active_weeks_' . $n] = self::m("$n Active Weeks", "Complete tasks in $n different weeks", 'Endurance Titan', $rarity, $n);
        }
        $activeDiffMonths = [3,6,12,24,36,48,60];
        foreach ($activeDiffMonths as $n) {
            $rarity = $n <= 6 ? 'Common' : ($n <= 18 ? 'Rare' : ($n <= 36 ? 'Epic' : 'Legendary'));
            $a['active_diff_months_' . $n] = self::m("$n Different Months", "Complete tasks in $n different months", 'Endurance Titan', $rarity, $n);
        }
        $beatRecords = [1,3,5,10,25];
        foreach ($beatRecords as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 5 ? 'Rare' : ($n <= 10 ? 'Epic' : 'Legendary'));
            $a['beat_record_' . $n] = self::m("Record Breaker x$n", "Beat your longest streak record $n times", 'Endurance Titan', $rarity, $n);
        }
        $a['comeback_king'] = self::s('Comeback King', 'Return after 7+ day break and start new streak', 'Endurance Titan', 'Rare', 'comeback-king.svg');
        $a['persistent_26'] = self::m('Half Year Persistent', 'Complete at least 1 task per week for 26 weeks', 'Endurance Titan', 'Epic', 26);
        $a['persistent_52'] = self::m('Year Persistent', 'Complete at least 1 task per week for 52 weeks', 'Endurance Titan', 'Legendary', 52, 'year-dominator.svg');
        $a['iron_will'] = self::s('Iron Will', 'Maintain streak through a full weekend', 'Endurance Titan', 'Common');
        $a['never_give_up'] = self::s('Never Give Up', 'Start a new streak after losing a 30+ day streak', 'Endurance Titan', 'Epic', 'never-give-up.svg');
        // Comeback streaks
        foreach ([2,3,5,10] as $n) {
            $rarity = $n <= 3 ? 'Rare' : 'Epic';
            $a['comeback_' . $n] = self::m("Comeback x$n", "Start a new streak after a break $n times", 'Endurance Titan', $rarity, $n);
        }
        // Lifetime days active
        foreach ([50,100,200,365,500,730,1000] as $n) {
            $rarity = $n <= 100 ? 'Common' : ($n <= 365 ? 'Rare' : ($n <= 730 ? 'Epic' : 'Legendary'));
            $a['days_active_' . $n] = self::m("$n Days Active", "Be active on $n total days", 'Endurance Titan', $rarity, $n);
        }
        return $a;
    }

    private static function worldConqueror(): array {
        $a = [];
        for ($i = 1; $i <= 10; $i++) {
            $rarity = $i <= 3 ? 'Common' : ($i <= 6 ? 'Rare' : ($i <= 8 ? 'Epic' : 'Legendary'));
            $a['area_' . $i] = self::s("Area $i Complete", "Complete adventure area $i", 'World Conqueror', $rarity);
        }
        $bosses = [1,3,5,10,20];
        foreach ($bosses as $n) {
            $rarity = $n <= 3 ? 'Common' : ($n <= 5 ? 'Rare' : ($n <= 10 ? 'Epic' : 'Legendary'));
            $a['bosses_' . $n] = self::m("Boss Slayer x$n", "Defeat $n bosses", 'World Conqueror', $rarity, $n, $n === 1 ? 'boss-slayer.svg' : 'default.svg');
        }
        $nodes = [10,25,50,100,250];
        foreach ($nodes as $n) {
            $rarity = $n <= 25 ? 'Common' : ($n <= 100 ? 'Rare' : 'Epic');
            $a['explore_' . $n] = self::m("Explorer x$n", "Explore $n map nodes", 'World Conqueror', $rarity, $n);
        }
        $treasures = [5,10,25,50,100];
        foreach ($treasures as $n) {
            $rarity = $n <= 10 ? 'Common' : ($n <= 50 ? 'Rare' : 'Epic');
            $a['treasures_' . $n] = self::m("Treasure Hunter x$n", "Find $n treasures", 'World Conqueror', $rarity, $n);
        }
        // Shop visits
        foreach ([5,10,25,50] as $n) {
            $rarity = $n <= 10 ? 'Common' : ($n <= 25 ? 'Rare' : 'Epic');
            $a['shops_' . $n] = self::m("Shopkeeper x$n", "Visit $n shops", 'World Conqueror', $rarity, $n);
        }
        // Events
        foreach ([5,10,25] as $n) {
            $rarity = $n <= 10 ? 'Common' : 'Rare';
            $a['events_' . $n] = self::m("Event Seeker x$n", "Discover $n events", 'World Conqueror', $rarity, $n);
        }
        $a['world_complete'] = self::s('World Complete', 'Complete all adventure areas', 'World Conqueror', 'Mythic');
        $a['speed_run'] = self::s('Speed Runner', 'Complete an area in under 10 tasks', 'World Conqueror', 'Epic', 'speed-runner.svg');
        $a['no_damage_area'] = self::s('Flawless Victory', 'Complete an area without losing health', 'World Conqueror', 'Epic', 'flawless-victory.svg');
        $a['all_nodes'] = self::s('Completionist', 'Explore every node in an area', 'World Conqueror', 'Rare');
        $a['boss_streak'] = self::m('Boss Rush', 'Defeat 3 bosses in a row without losing health', 'World Conqueror', 'Legendary', 3);
        return $a;
    }

    private static function communitySocial(): array {
        $a = [];
        $positions = [1 => 'Number One', 3 => 'Top Three', 5 => 'Top Five', 10 => 'Top Ten', 25 => 'Top 25', 50 => 'Top 50'];
        foreach ($positions as $n => $name) {
            $rarity = $n <= 3 ? 'Legendary' : ($n <= 10 ? 'Epic' : ($n <= 25 ? 'Rare' : 'Common'));
            $a['leaderboard_top_' . $n] = self::s($name, "Reach #$n on the leaderboard", 'Community & Social', $rarity);
        }
        $rises = [10,25,50,100];
        foreach ($rises as $n) {
            $rarity = $n <= 25 ? 'Rare' : 'Epic';
            $a['rank_rise_' . $n] = self::m("Rise $n Ranks", "Rise $n positions on the leaderboard", 'Community & Social', $rarity, $n);
        }
        $maintain = [7,30,90];
        foreach ($maintain as $n) {
            $rarity = $n <= 7 ? 'Epic' : 'Legendary';
            $a['maintain_top1_' . $n] = self::m("Top for $n Days", "Maintain #1 position for $n days", 'Community & Social', $rarity, $n);
        }
        $a['helper'] = self::m('Helper', 'Help 5 users', 'Community & Social', 'Common', 5, 'helpful-hero.svg');
        $a['team_player'] = self::m('Team Player', 'Help 25 users', 'Community & Social', 'Rare', 25, 'team-player.svg');
        $a['mentor'] = self::m('Mentor', 'Help 100 users', 'Community & Social', 'Epic', 100, 'wise-mentor.svg');
        $a['inspiration'] = self::m('Inspiration', 'Share achievements 50 times', 'Community & Social', 'Rare', 50, 'inspiration.svg');
        $a['trendsetter'] = self::s('Trendsetter', 'Be the first to unlock a new achievement', 'Community & Social', 'Mythic');
        $a['social_butterfly'] = self::s('Social Butterfly', 'Interact with 10 different users', 'Community & Social', 'Rare', 'social-butterfly.svg');
        // Leaderboard duration
        foreach ([1,7,30,90,365] as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 30 ? 'Rare' : ($n <= 90 ? 'Epic' : 'Legendary'));
            $label = $n === 1 ? '1 Day' : "$n Days";
            $a['on_leaderboard_' . $n] = self::m("On Board $label", "Appear on leaderboard for $label", 'Community & Social', $rarity, $n);
        }
        // Achievement sharing
        foreach ([1,10,25,100] as $n) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 10 ? 'Rare' : ($n <= 25 ? 'Epic' : 'Legendary'));
            $a['share_' . $n] = self::m("Share x$n", "Share $n achievements", 'Community & Social', $rarity, $n);
        }
        return $a;
    }

    private static function statisticalMarvels(): array {
        $a = [];
        $avgMilestones = [1,2,3,5,10];
        foreach ($avgMilestones as $n) {
            $rarity = $n <= 2 ? 'Common' : ($n <= 5 ? 'Rare' : 'Epic');
            $a['avg_daily_' . $n] = self::s("Average $n/Day", "Maintain an average of $n tasks per day over 30 days", 'Statistical Marvels', $rarity);
        }
        $completionRates = [25,50,75,90,95,99];
        foreach ($completionRates as $n) {
            $rarity = $n <= 50 ? 'Common' : ($n <= 75 ? 'Rare' : ($n <= 95 ? 'Epic' : 'Legendary'));
            $a['completion_rate_' . $n] = self::s("$n% Completion Rate", "Achieve a $n% task completion rate", 'Statistical Marvels', $rarity);
        }
        $bestHour = [10,25,50,100];
        foreach ($bestHour as $n) {
            $rarity = $n <= 25 ? 'Rare' : 'Epic';
            $a['best_hour_' . $n] = self::m("Power Hour x$n", "Complete $n tasks in your most active hour", 'Statistical Marvels', $rarity, $n);
        }
        $bestDay = [25,50,100,200];
        foreach ($bestDay as $n) {
            $rarity = $n <= 50 ? 'Rare' : ($n <= 100 ? 'Epic' : 'Legendary');
            $a['best_day_' . $n] = self::m("Best Day x$n", "Complete $n tasks on your best day ever", 'Statistical Marvels', $rarity, $n);
        }
        $hourVariety = [5,10,15,20,24];
        foreach ($hourVariety as $n) {
            $rarity = $n <= 10 ? 'Common' : ($n <= 20 ? 'Rare' : 'Epic');
            $a['hour_variety_' . $n] = self::m("$n Hour Spread", "Complete tasks at $n different hours", 'Statistical Marvels', $rarity, $n);
        }
        $weekdayWeeks = [1,4,12,26,52];
        foreach ($weekdayWeeks as $n) {
            $rarity = $n <= 4 ? 'Common' : ($n <= 12 ? 'Rare' : ($n <= 26 ? 'Epic' : 'Legendary'));
            $a['weekday_streak_' . $n] = self::m("Weekday Warrior x$n", "Complete tasks every weekday for $n weeks", 'Statistical Marvels', $rarity, $n);
        }
        $a['variety_king'] = self::s('Variety King', 'Complete tasks from 10 different lists', 'Statistical Marvels', 'Rare', 'variety-king.svg');
        $a['all_days_week'] = self::s('Full Week', 'Complete tasks on all 7 days of the week', 'Statistical Marvels', 'Common');
        // Improvement tracking
        foreach ([2,5,10] as $n) {
            $a['improve_weekly_' . $n] = self::s("Improve x$n Weeks", "Improve weekly task count for $n consecutive weeks", 'Statistical Marvels', $n <= 2 ? 'Rare' : ($n <= 5 ? 'Epic' : 'Legendary'));
        }
        $a['consistent_average'] = self::s('Consistent Average', 'Maintain same daily average (±1) for 30 days', 'Statistical Marvels', 'Epic');
        $a['double_average'] = self::s('Double Up', 'Double your 30-day average in a single day', 'Statistical Marvels', 'Rare');
        $a['triple_average'] = self::s('Triple Threat Day', 'Triple your 30-day average in a single day', 'Statistical Marvels', 'Epic');
        $a['most_productive_month'] = self::s('Most Productive Month', 'Set a new monthly task record', 'Statistical Marvels', 'Rare');
        $a['balanced_week'] = self::s('Balanced Week', 'Complete similar number of tasks each day of the week (±2)', 'Statistical Marvels', 'Epic');
        // Milestone totals
        foreach ([1000,5000,10000,25000,50000] as $n) {
            $rarity = $n <= 5000 ? 'Rare' : ($n <= 25000 ? 'Epic' : 'Legendary');
            $fmt = $n >= 1000 ? ($n/1000).'K' : $n;
            $a['lifetime_tasks_' . $n] = self::m("Lifetime $fmt", "Complete $n lifetime tasks", 'Statistical Marvels', $rarity, $n);
        }
        $a['efficiency_master'] = self::s('Efficiency Master', 'Average 15+ XP per task over 100 tasks', 'Statistical Marvels', 'Epic', 'efficiency-expert.svg');
        $a['peak_performance'] = self::s('Peak Performance', 'Complete your most tasks ever in a single hour', 'Statistical Marvels', 'Rare');
        return $a;
    }

    private static function categorySpecialist(): array {
        $a = [];
        $listsUsed = [1,2,3,5,10];
        foreach ($listsUsed as $n) {
            $rarity = $n <= 2 ? 'Common' : ($n <= 5 ? 'Rare' : 'Epic');
            $a['lists_used_' . $n] = self::m("$n Lists Used", "Complete tasks from $n different task lists", 'Category Specialist', $rarity, $n);
        }
        $listTasks = [10,25,50,100,250,500];
        foreach ($listTasks as $n) {
            $rarity = $n <= 25 ? 'Common' : ($n <= 100 ? 'Rare' : ($n <= 250 ? 'Epic' : 'Legendary'));
            $a['single_list_' . $n] = self::m("List Expert x$n", "Complete $n tasks from a single list", 'Category Specialist', $rarity, $n);
        }
        $a['list_clearer'] = self::s('List Clearer', 'Complete all tasks from a list', 'Category Specialist', 'Rare');
        $a['multi_list_day'] = self::s('Multi-Tasker', 'Complete tasks from 3+ lists in one day', 'Category Specialist', 'Common', 'multitasker.svg');
        $a['focused_worker'] = self::s('Focused Worker', 'Complete 10+ tasks from the same list in one day', 'Category Specialist', 'Rare', 'focus-master.svg');
        $multiListDays = [5,10,25,50];
        foreach ($multiListDays as $n) {
            $rarity = $n <= 10 ? 'Rare' : 'Epic';
            $a['multi_list_days_' . $n] = self::m("Multi-List x$n", "Complete tasks from 3+ lists in a day $n times", 'Category Specialist', $rarity, $n);
        }
        $focusedDays = [5,10,25];
        foreach ($focusedDays as $n) {
            $rarity = $n <= 10 ? 'Rare' : 'Epic';
            $a['focused_days_' . $n] = self::m("Focus Master x$n", "Complete 10+ tasks from one list in a day $n times", 'Category Specialist', $rarity, $n);
        }
        $a['jack_of_trades'] = self::s('Jack of All Trades', 'Complete tasks from every list you have', 'Category Specialist', 'Epic', 'jack-of-trades.svg');
        $a['project_pioneer'] = self::m('Project Pioneer', 'Be the first to complete a task from a new list', 'Category Specialist', 'Common', 1, 'project-pioneer.svg');
        // List mastery
        foreach ([1000,2500,5000] as $n) {
            $a['single_list_' . $n] = self::m("List Legend x$n", "Complete $n tasks from a single list", 'Category Specialist', 'Legendary', $n);
        }
        $a['list_perfectionist'] = self::s('List Perfectionist', 'Clear all tasks from 3 different lists', 'Category Specialist', 'Epic');
        $a['list_explorer'] = self::m('List Explorer', 'Complete tasks from 15 different lists', 'Category Specialist', 'Legendary', 15);
        $a['daily_list_variety'] = self::s('Daily Variety', 'Complete tasks from a different list each day for 7 days', 'Category Specialist', 'Rare');
        // List completion milestones
        foreach ([2,5,10] as $n) {
            $a['lists_cleared_' . $n] = self::m("Lists Cleared x$n", "Clear all tasks from $n different lists", 'Category Specialist', $n <= 2 ? 'Rare' : ($n <= 5 ? 'Epic' : 'Legendary'), $n);
        }
        $a['list_balanced'] = self::s('Balanced Lists', 'Complete similar tasks from each list in a week', 'Category Specialist', 'Rare');
        $a['new_list_streak'] = self::m('New List Streak', 'Complete tasks from a new list 5 days in a row', 'Category Specialist', 'Rare', 5);
        // List dominance
        foreach ([50,75,90] as $pct) {
            $rarity = $pct <= 50 ? 'Rare' : ($pct <= 75 ? 'Epic' : 'Legendary');
            $a['list_dominance_' . $pct] = self::s("$pct% Dominance", "Complete $pct% of tasks from your most active list in a week", 'Category Specialist', $rarity);
        }
        $a['all_lists_week'] = self::s('All Lists Week', 'Complete tasks from every list in one week', 'Category Specialist', 'Rare');
        $a['list_rotation'] = self::s('List Rotation', 'Complete from a different list each day for 5 days', 'Category Specialist', 'Rare');
        $a['empty_inbox'] = self::s('Empty Inbox', 'Have zero pending tasks across all lists', 'Category Specialist', 'Legendary');
        return $a;
    }

    private static function journeyHero(): array {
        $a = [];

        // First encounter
        $a['journey_first_encounter'] = self::s('First Encounter', 'Complete your first journey encounter', 'Journey Hero', 'Common', 'default.svg');

        // Battle milestones
        $battleMilestones = [10 => 'Battle Tested', 25 => 'Seasoned Fighter', 50 => 'War Veteran', 100 => 'Battle Centurion', 250 => 'War Machine', 500 => 'Battle Legend'];
        foreach ($battleMilestones as $n => $name) {
            $rarity = $n <= 25 ? 'Common' : ($n <= 100 ? 'Rare' : ($n <= 250 ? 'Epic' : 'Legendary'));
            $a['journey_battles_' . $n] = self::m($name, "Win $n battles", 'Journey Hero', $rarity, $n);
        }

        // Treasure milestones
        $treasureMilestones = [5 => 'Treasure Finder', 10 => 'Treasure Seeker', 25 => 'Treasure Collector', 50 => 'Treasure Hoarder', 100 => 'Treasure Legend'];
        foreach ($treasureMilestones as $n => $name) {
            $rarity = $n <= 10 ? 'Common' : ($n <= 25 ? 'Rare' : ($n <= 50 ? 'Epic' : 'Legendary'));
            $a['journey_treasures_' . $n] = self::m($name, "Find $n treasures", 'Journey Hero', $rarity, $n);
        }

        // Event milestones
        $eventMilestones = [5 => 'Event Participant', 10 => 'Event Enthusiast', 25 => 'Event Veteran', 50 => 'Event Master'];
        foreach ($eventMilestones as $n => $name) {
            $rarity = $n <= 10 ? 'Common' : ($n <= 25 ? 'Rare' : 'Epic');
            $a['journey_events_' . $n] = self::m($name, "Complete $n events", 'Journey Hero', $rarity, $n);
        }

        // Boss milestones
        $bossMilestones = [1 => 'Boss Slayer', 3 => 'Boss Hunter', 5 => 'Boss Crusher', 9 => 'Boss Annihilator'];
        foreach ($bossMilestones as $n => $name) {
            $rarity = $n <= 1 ? 'Rare' : ($n <= 3 ? 'Epic' : ($n <= 5 ? 'Legendary' : 'Mythic'));
            $a['journey_bosses_' . $n] = self::m($name, "Defeat $n bosses", 'Journey Hero', $rarity, $n);
        }

        // Mini-boss milestones
        $miniBossMilestones = [1 => 'Mini-Boss Down', 5 => 'Mini-Boss Hunter', 10 => 'Mini-Boss Slayer', 25 => 'Mini-Boss Nemesis'];
        foreach ($miniBossMilestones as $n => $name) {
            $rarity = $n <= 1 ? 'Common' : ($n <= 5 ? 'Rare' : ($n <= 10 ? 'Epic' : 'Legendary'));
            $a['journey_mini_bosses_' . $n] = self::m($name, "Defeat $n mini-bosses", 'Journey Hero', $rarity, $n);
        }

        // Prestige milestones
        $prestigeMilestones = [1 => 'First Prestige', 2 => 'Double Prestige', 3 => 'Triple Prestige', 5 => 'Prestige Master'];
        foreach ($prestigeMilestones as $n => $name) {
            $rarity = $n <= 1 ? 'Rare' : ($n <= 2 ? 'Epic' : 'Legendary');
            $a['journey_prestige_' . $n] = self::m($name, "Reach prestige $n", 'Journey Hero', $rarity, $n);
        }

        // Win streak specials
        $winStreaks = [3 => 'Hat Trick Wins', 5 => 'Winning Streak', 10 => 'Unstoppable Force'];
        foreach ($winStreaks as $n => $name) {
            $rarity = $n <= 3 ? 'Rare' : ($n <= 5 ? 'Epic' : 'Legendary');
            $a['journey_win_streak_' . $n] = self::s($name, "Win $n battles in a row", 'Journey Hero', $rarity);
        }

        // Special achievements
        $a['journey_no_damage'] = self::s('Untouchable', 'Complete an encounter without taking damage', 'Journey Hero', 'Epic');
        $a['journey_all_ages'] = self::s('Timeless Warrior', 'Encounter enemies from all 9 ages', 'Journey Hero', 'Legendary');

        return $a;
    }

    private static function craftingMaster(): array {
        $a = [];

        // First craft
        $a['craft_first'] = self::s('Forge First', 'Craft your first item', 'Crafting Master', 'Common', 'default.svg');

        // Craft count milestones
        $craftMilestones = [5 => 'Apprentice Smith', 10 => 'Journeyman Smith', 25 => 'Expert Smith', 50 => 'Master Smith', 100 => 'Legendary Smith'];
        foreach ($craftMilestones as $n => $name) {
            $rarity = $n <= 5 ? 'Common' : ($n <= 10 ? 'Rare' : ($n <= 25 ? 'Epic' : 'Legendary'));
            $a['craft_' . $n] = self::m($name, "Craft $n items", 'Crafting Master', $rarity, $n);
        }

        // Rarity crafting specials
        $a['craft_rare'] = self::s('Rare Find', 'Craft a rare item', 'Crafting Master', 'Rare');
        $a['craft_epic'] = self::s('Epic Creation', 'Craft an epic item', 'Crafting Master', 'Epic');
        $a['craft_legendary'] = self::s('Legendary Forge', 'Craft a legendary item', 'Crafting Master', 'Legendary');

        // All slots
        $a['craft_all_slots'] = self::s('Full Set', 'Craft items for all 4 equipment slots', 'Crafting Master', 'Epic');

        // Age-specific crafting
        $ages = [
            'stone' => 'Stone Age', 'bronze' => 'Bronze Age', 'iron' => 'Iron Age',
            'medieval' => 'Medieval', 'renaissance' => 'Renaissance', 'industrial' => 'Industrial',
            'modern' => 'Modern', 'digital' => 'Digital', 'space' => 'Space Age',
        ];
        foreach ($ages as $key => $label) {
            $a['craft_age_' . $key] = self::s("$label Crafter", "Craft an item from the $label", 'Crafting Master', 'Rare');
        }

        // Collection specials
        $a['craft_collector'] = self::s('Item Collector', 'Own 50% of all craftable items', 'Crafting Master', 'Legendary');
        $a['craft_hoarder'] = self::s('Item Hoarder', 'Own 10 copies of any single item', 'Crafting Master', 'Epic');

        return $a;
    }
}
