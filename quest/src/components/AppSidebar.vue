<template>
	<aside class="quest-sidebar" :class="{ collapsed: collapsed }">
		<button class="sidebar-toggle" @click="$emit('toggle')" aria-label="Toggle sidebar">
			<svg width="20" height="20" viewBox="0 0 20 20">
				<path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
			</svg>
		</button>

		<!-- Logo -->
		<div class="sidebar-header">
			<div class="quest-logo">
				<img :src="logoUrl" alt="Quest" class="logo-icon">
				<span v-if="!collapsed" class="logo-text">Quest</span>
			</div>
		</div>

		<!-- Character Section -->
		<div class="character-section">
			<div class="character-avatar-container">
				<div class="character-avatar">
					<PixelAvatar
						:skin-tone="avatar.skin_tone"
						:hair-style="avatar.hair_style"
						:hair-color="avatar.hair_color"
						:body-type="avatar.body_type"
						:equipped-weapon="equippedWeapon"
						:equipped-headgear="equippedHeadgear"
						:equipped-clothing="equippedClothing"
						:equipped-accessory="equippedAccessory"
						:age-key="stats.level.level >= 100 ? 'space' : stats.level.level >= 75 ? 'digital' : stats.level.level >= 60 ? 'modern' : stats.level.level >= 50 ? 'industrial' : stats.level.level >= 40 ? 'renaissance' : stats.level.level >= 30 ? 'medieval' : stats.level.level >= 20 ? 'iron' : stats.level.level >= 10 ? 'bronze' : 'stone'"
						:size="collapsed ? 48 : 80"
					/>
					<div class="avatar-level-badge">
						<span>{{ stats.level.level }}</span>
					</div>
				</div>
			</div>

			<template v-if="!collapsed">
				<div class="character-name">{{ user.displayName || 'Adventurer' }}</div>
				<div class="character-rank">{{ stats.level.rank_title }}</div>

				<!-- Gauges -->
				<div class="character-gauges">
					<Tooltip text="Complete tasks to earn XP and level up. Higher levels unlock new ages and equipment." position="right">
						<div class="gauge">
							<div class="gauge-header">
								<span class="gauge-label">Lv. {{ stats.level.level }}</span>
								<span class="gauge-value">{{ Math.round(stats.level.progress_percentage || 0) }}%</span>
							</div>
							<div class="gauge-bar">
								<div class="gauge-fill xp" :style="{ width: xpPercentage + '%' }" />
							</div>
						</div>
					</Tooltip>

					<Tooltip text="Health drops when you lose battles or miss deadlines. Regenerates when you complete tasks." position="right">
						<div class="gauge">
							<div class="gauge-header">
								<span class="gauge-label">Health</span>
								<span class="gauge-value">{{ stats.health.current_health }}/{{ stats.health.max_health }}</span>
							</div>
							<div class="gauge-bar">
								<div class="gauge-fill health" :style="{ width: healthPercentage + '%' }" />
							</div>
						</div>
					</Tooltip>

					<Tooltip text="Complete at least one task per day to maintain your streak. Longer streaks boost XP multipliers." position="right">
						<div class="gauge streak">
							<div class="gauge-header">
								<span class="gauge-label">Streak</span>
								<span class="gauge-value streak-value" :class="{ active: stats.streak.is_active_today }">
									{{ stats.streak.current_streak }} days
								</span>
							</div>
						</div>
					</Tooltip>
				</div>
			</template>
		</div>

		<!-- Navigation -->
		<nav class="sidebar-nav">
			<ul class="nav-list">
				<li v-for="item in navItems" :key="item.page" class="nav-item">
					<a
						:href="item.url"
						class="nav-link"
						:class="{ active: activePage === item.page }"
						:title="item.label"
						@click.prevent="navigate(item)"
					>
						<span class="nav-icon">{{ item.icon }}</span>
						<span v-if="!collapsed" class="nav-text">{{ item.label }}</span>
					</a>
				</li>
			</ul>
		</nav>

		<div v-if="!collapsed" class="sidebar-footer">
			<button class="sound-toggle" @click="toggleSound" :title="soundOn ? 'Mute sounds' : 'Enable sounds'">
				{{ soundOn ? '\uD83D\uDD0A' : '\uD83D\uDD07' }}
			</button>
			<div class="app-version">Quest v1.0</div>
		</div>
	</aside>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import { generateFilePath, generateUrl } from '@nextcloud/router'
import { isSoundEnabled, setSoundEnabled } from '../services/audio'
import PixelAvatar from './PixelAvatar.vue'
import Tooltip from './Tooltip.vue'

export default {
	name: 'AppSidebar',

	components: { PixelAvatar, Tooltip },

	props: {
		collapsed: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			soundOn: isSoundEnabled(),
		}
	},

	methods: {
		toggleSound() {
			this.soundOn = !this.soundOn
			setSoundEnabled(this.soundOn)
			localStorage.setItem('quest-sound-enabled', this.soundOn)
		},

		navigate(item) {
			this.$store.commit('quest/setActivePage', item.page)
			window.history.pushState({}, '', item.url)
		},
	},

	computed: {
		...mapState('quest', ['user', 'stats', 'activePage', 'avatar', 'character']),

		equippedWeapon() {
			return this.character?.appearance?.weapon || this.character?.equipment?.weapon || null
		},

		equippedHeadgear() {
			return this.character?.appearance?.headgear || this.character?.equipment?.headgear || null
		},

		equippedClothing() {
			return this.character?.appearance?.clothing || this.character?.equipment?.clothing || null
		},

		equippedAccessory() {
			return this.character?.appearance?.accessory || this.character?.equipment?.accessory || null
		},
		...mapGetters('quest', ['healthPercentage', 'xpPercentage']),

		logoUrl() {
			return generateFilePath('quest', '', 'img/app.svg')
		},

		initials() {
			const name = this.user.displayName || 'A'
			const words = name.trim().split(/\s+/)
			if (words.length >= 2) {
				return (words[0][0] + words[1][0]).toUpperCase()
			}
			return name.substring(0, 2).toUpperCase()
		},

		xpCircumference() {
			return 2 * Math.PI * 44
		},

		xpOffset() {
			const pct = this.xpPercentage / 100
			return this.xpCircumference * (1 - pct)
		},

		navItems() {
			return [
				{ page: 'dashboard', icon: '\u{1F3E0}', label: 'Dashboard', url: generateUrl('/apps/quest/') },
				{ page: 'quests', icon: '\u{1F4DC}', label: 'Epics', url: generateUrl('/apps/quest/quests') },
				{ page: 'achievements', icon: '\u{1F3C6}', label: 'Achievements', url: generateUrl('/apps/quest/achievements') },
				{ page: 'character', icon: '\u{1F3A8}', label: 'Character', url: generateUrl('/apps/quest/character') },
				{ page: 'adventure', icon: '\u{1F5FA}\uFE0F', label: 'Adventure', url: generateUrl('/apps/quest/adventure') },
				{ page: 'activity', icon: '\u{1F4CB}', label: 'Activity', url: generateUrl('/apps/quest/activity') },
				{ page: 'leaderboard', icon: '\u{1F451}', label: 'Leaderboard', url: generateUrl('/apps/quest/leaderboard') },
				{ page: 'settings', icon: '\u{2699}\uFE0F', label: 'Settings', url: generateUrl('/apps/quest/settings') },
			]
		},
	},
}
</script>

<style scoped>
.quest-sidebar {
	position: fixed;
	top: var(--header-height, 50px);
	left: 0;
	width: var(--sidebar-width);
	height: calc(100vh - var(--header-height, 50px));
	flex-shrink: 0;
	background: var(--color-main-background);
	border-right: 1px solid var(--color-border);
	display: flex;
	flex-direction: column;
	z-index: var(--z-sidebar);
	transition: width var(--transition-normal);
	overflow-y: auto;
	overflow-x: hidden;
}

.quest-sidebar.collapsed {
	width: var(--sidebar-collapsed-width);
}

.sidebar-toggle {
	position: absolute;
	top: 12px;
	right: 12px;
	background: none;
	border: none;
	cursor: pointer;
	color: var(--color-text-light);
	padding: 4px;
	border-radius: var(--radius-small);
	z-index: 2;
}

.sidebar-toggle:hover {
	background: var(--color-background-hover);
}

/* When collapsed, center the toggle */
.collapsed .sidebar-toggle {
	position: relative;
	top: auto;
	right: auto;
	display: flex;
	margin: 8px auto;
}

.sidebar-header {
	padding: 16px;
	border-bottom: 1px solid var(--color-border);
}

.quest-logo {
	display: flex;
	align-items: center;
	gap: 10px;
}

.collapsed .sidebar-header {
	padding: 8px;
	text-align: center;
}

.collapsed .quest-logo {
	justify-content: center;
}

.logo-icon {
	width: 32px;
	height: 32px;
}

.logo-text {
	font-size: var(--font-size-large);
	font-weight: 700;
	color: var(--color-main-text);
}

/* Character section */
.character-section {
	padding: 16px;
	border-bottom: 1px solid var(--color-border);
	text-align: center;
}

.character-avatar-container {
	display: flex;
	justify-content: center;
	margin-bottom: 12px;
}

.character-avatar {
	position: relative;
	display: flex;
	justify-content: center;
}

.avatar-level-badge {
	position: absolute;
	bottom: 2px;
	right: 2px;
	width: 28px;
	height: 28px;
	border-radius: 50%;
	background: var(--color-primary);
	color: white;
	font-size: 12px;
	font-weight: 700;
	display: flex;
	align-items: center;
	justify-content: center;
	border: 2px solid var(--color-main-background);
	z-index: 2;
}


.character-name {
	font-size: var(--font-size-normal);
	font-weight: 600;
	color: var(--color-main-text);
	margin-bottom: 2px;
}

.character-rank {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	margin-bottom: 12px;
}

/* Gauges */
.character-gauges {
	display: flex;
	flex-direction: column;
	gap: 10px;
	text-align: left;
	padding: 0 4px;
}

.gauge-header {
	display: flex;
	justify-content: space-between;
	font-size: 12px;
	margin-bottom: 4px;
}

.gauge-label {
	color: var(--color-text-light);
	font-weight: 500;
}

.gauge-value {
	color: var(--color-main-text);
	font-weight: 600;
	font-variant-numeric: tabular-nums;
}

.gauge-bar {
	height: 8px;
	background: var(--color-background-dark);
	border-radius: 4px;
	overflow: hidden;
}

.gauge-fill {
	height: 100%;
	border-radius: 4px;
	transition: width var(--transition-slow);
}

.gauge-fill.xp {
	background: linear-gradient(90deg, var(--color-primary), #00d4ff);
}

.gauge-fill.health {
	background: linear-gradient(90deg, #e53e3e, #48bb78);
}

.streak-value {
	color: var(--color-text-light);
}

.streak-value.active {
	color: var(--color-warning);
}

/* Navigation */
.sidebar-nav {
	flex: 1;
	padding: 8px;
}

.nav-list {
	list-style: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-direction: column;
	gap: 2px;
}

.nav-link {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 10px 12px;
	border-radius: var(--radius-medium);
	color: var(--color-main-text);
	text-decoration: none;
	transition: background var(--transition-fast);
	font-size: var(--font-size-normal);
}

.nav-link:hover {
	background: var(--color-background-hover);
}

.nav-link.active {
	background: var(--color-primary-light);
	color: var(--color-primary);
	font-weight: 600;
}

.nav-icon {
	font-size: 18px;
	width: 24px;
	text-align: center;
	flex-shrink: 0;
}

.sidebar-footer {
	padding: 12px 16px;
	border-top: 1px solid var(--color-border);
}

.sidebar-footer {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
}

.sound-toggle {
	background: none;
	border: none;
	cursor: pointer;
	font-size: 16px;
	padding: 2px;
	opacity: 0.6;
	transition: opacity var(--transition-fast);
}
.sound-toggle:hover { opacity: 1; }

.app-version {
	font-size: 11px;
	color: var(--color-text-lighter);
	text-align: center;
}

/* Collapsed state */
.collapsed .character-section {
	padding: 8px;
}

.collapsed .character-avatar-container {
	margin-bottom: 4px;
}

.collapsed .character-avatar {
	width: 48px;
	height: 48px;
}

.collapsed .avatar-content {
	margin: 4px;
}

.collapsed .avatar-initials {
	font-size: 16px;
}

.collapsed .avatar-level-badge {
	width: 20px;
	height: 20px;
	font-size: 10px;
	bottom: 0;
	right: 0;
}


.collapsed .nav-link {
	justify-content: center;
	padding: 10px;
}

@media (max-width: 768px) {
	.quest-sidebar {
		transform: translateX(-100%);
		transition: transform var(--transition-normal);
	}

	.quest-sidebar.open {
		transform: translateX(0);
	}
}
</style>
