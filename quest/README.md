# Quest for Nextcloud

Turn your task management into an RPG adventure. Quest gamifies Nextcloud task completion with XP, levels, achievements, equipment, and procedurally generated encounters.

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/P5P31KQRF1)

![Quest Dashboard](screenshots/readme%20screenshot.png)

## Features

### Core Progression
- **XP & Levels** — earn experience from task completions, level up through 100+ levels across 9 historical ages (Stone Age to Space Age)
- **1000 Achievements** across 19 categories with rarity tiers (Common, Rare, Epic, Legendary, Mythic)
- **Daily Streaks** with XP multiplier bonuses
- **Health System** with regeneration on task completion and penalties for overdue tasks

### Epic Quest System
- Group tasks into **Epics** — collections with bonus XP on completion
- Dynamic rewards: more tasks + longer duration = bigger XP payout
- Tier system: Common, Uncommon, Rare, Epic, Legendary

### Journey Adventure
- Passive auto-adventure triggered every 3 task completions
- **108 enemies**, 9 bosses, 9 mini-bosses across 9 ages
- **72 story events** with narrative text
- Loot drops and equipment affecting combat power
- Prestige system for endless progression

### Character System
- **Pixel art avatar** with customizable appearance (6 skin tones, 8 hair styles, 8 hair colors)
- **121 equipment items** with unique SVG sprites visible on avatar
- 4 equipment slots: clothing, weapon, accessory, headgear
- **Item crafting** — combine 3 copies to forge higher rarity

### Daily & Weekly Challenges
- 23 auto-generated challenge templates rotating daily/weekly
- Time-limited goals with bonus XP rewards (Power Hour, Morning Sprint, Weekday Warrior, etc.)

### Social & Tracking
- **Leaderboard** with sortable rankings (XP, Level, Streak, Tasks)
- **Activity feed** combining tasks, encounters, achievements, and challenges
- **Sound effects** for all key moments (synthesized, no audio files)
- **Tooltips** explaining game mechanics
- **Keyboard shortcuts** (Alt+D/Q/A/C/J/L/F/S, ? for help)

### Settings
- Task list selection and filtering
- Display, notification, gameplay, and privacy preferences
- Data export, backup, and reset

## Requirements

- Nextcloud 28–34
- PHP 8.1+
- Node.js 20+ (for building)

## Installation

### From Nextcloud App Store
Search for "Quest" in your Nextcloud app store and install.

### Manual Installation
```bash
# Clone into your Nextcloud apps directory
cd /path/to/nextcloud/apps
git clone https://github.com/otherworld-dev/Nextcloud-Quest.git quest

# Install dependencies
cd quest
composer install --no-dev
npm ci

# Build frontend
npm run build

# Enable the app
sudo -u www-data php /path/to/nextcloud/occ app:enable quest
```

## Development

```bash
# Install all dependencies
composer install
npm ci

# Development build with watch
npm run dev

# Production build
npm run build

# Linting
npm run lint
npm run stylelint

# PHP code style
composer run cs:check
composer run cs:fix
```

## Architecture

### Backend (PHP)
- **Service Layer** — XPService, AchievementService, CharacterService, EpicService, JourneyService, ChallengeService
- **Controllers** — QuestController, CharacterController, EpicController, JourneyController, ChallengeController, ActivityController, SettingsController
- **Database** — 21 migrations, entity/mapper pattern with Nextcloud's QueryBuilder
- **Integration** — CalDAV via TasksApiIntegration for Nextcloud Tasks

### Frontend (Vue.js)
- **Vue 2.7** with Vuex store, client-side routing
- **8 pages** — Dashboard, Epics, Achievements, Character, Adventure, Activity, Leaderboard, Settings
- **8 components** — AppSidebar, PixelAvatar, StatCard, EpicCard, CreateEpicModal, NotificationStack, WelcomeBanner, Tooltip
- **Services** — API client, audio (Web Audio API), keyboard shortcuts

### Content
- 1000 achievements across 19 categories
- 121 equipment items with SVG sprites across 9 ages
- 108 enemies + 18 bosses/mini-bosses
- 72 story events
- 23 challenge templates
- 110 achievement icons

## Task Integration

Quest integrates with Nextcloud's CalDAV system. Any calendar with VTODO support appears as a task list. Tasks can be created and completed directly from the Quest dashboard.

## License

AGPL-3.0 — see [COPYING](COPYING) for details.
