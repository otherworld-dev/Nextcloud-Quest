# Changelog

All notable changes to Quest will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-04-13

### Added
- **Full Vue.js Migration** — complete frontend rewrite from hybrid legacy JS to unified Vue architecture with client-side routing
- **Epic Quest System** — group tasks into collections with dynamic bonus XP (scales with task count and duration)
- **Journey Adventure** — passive auto-adventure with encounters every 3 task completions
  - 108 enemies, 9 bosses, 9 mini-bosses across 9 historical ages
  - 72 unique story events with narrative text
  - Loot drops, combat resolution based on character power
  - Prestige system for endless progression
- **Pixel Art Avatar** — 32x32 customizable character with 6 skin tones, 8 hair styles, 8 hair colors, 2 body types
  - All 4 equipment slots visible on avatar (clothing, weapon, accessory, headgear)
  - Per-item unique colors and distinct visual types
- **Item Crafting** — combine 3 copies of an item to forge one of higher rarity
- **Daily/Weekly Challenges** — 23 auto-generated challenge templates with rotating goals
- **121 Equipment Items** with unique SVG sprites across 9 ages and 4 rarity tiers
- **1000 Achievements** across 19 categories (was 95)
  - Journey achievements: battles, treasures, bosses, prestige
  - Crafting achievements: items crafted, rarity tiers, age-specific
  - Task, streak, level, speed, time, consistency, health, priority, weekend, special date, monthly, combo, rare/secret, endurance, world, community, statistical, category specialist
- **Leaderboard Page** — sortable rankings with medal emojis
- **Activity Feed** — unified timeline from tasks, encounters, achievements, challenges
- **Sound Effects** — synthesized audio via Web Audio API for all game events
- **Keyboard Shortcuts** — Alt+key navigation, ? for help overlay
- **Tooltips** — explanations for all game mechanics
- **Onboarding** — welcome banner with 4-step checklist for new users
- **Task Creation** — add tasks directly from dashboard with inline form
- **Settings** — task list filtering, display/notification/gameplay/privacy preferences with persistence
- **Dark Mode** — full theme support using Nextcloud's CSS variables
- **Mobile Responsive** — hamburger menu, adaptive layouts

### Changed
- Dashboard redesigned with two-column layout (tasks primary, sidebar for challenges/goals/epics/achievements)
- Achievement page with category pill tabs, compact grid/list views, rarity-colored progress bars
- Character page with age progression, equipment slots with sprites, power rating, forge section
- Adventure page replaced placeholder with journey progress, encounter history, power display
- Settings page with organized sections, task list selection, save notifications
- Notifications now auto-dismiss reliably with progress bar animation
- XP display shows level percentage instead of raw lifetime XP
- All colors use Quest design tokens readable in both light and dark mode

### Fixed
- Nextcloud 34 compatibility — replaced all deprecated `QueryBuilder::execute()` with `executeQuery()`/`executeStatement()`
- Replaced all `getDatabaseConnection()` with `get(IDBConnection::class)`
- Fixed task priority parsing — `(int)(string)` cast for Sabre VObject properties
- Fixed settings save — read body from php://input, disabled broken audit logging
- Fixed item unlock display — `is_unlocked` vs `unlocked` property mismatch
- Fixed equipment display — broken when ages table was empty
- Fixed notification stacking — replaced Vue watcher with polling interval
- Fixed achievement unlocking — included new achievements in task completion response

### Removed
- All legacy JavaScript files (25K+ lines)
- All legacy CSS files (14K+ lines)
- Old grid-based adventure map system
- Duplicate Dashboard/Quests pages (merged)
- Debug `error_log()` calls from controllers

## [1.0.0] - 2025-08-03

### Added
- Initial release of Quest
- XP and level progression
- 95 achievements with rarity system
- Daily streak tracking
- Task integration via CalDAV
- Character system with age progression
- Basic adventure map (grid-based)
- Settings and preferences
