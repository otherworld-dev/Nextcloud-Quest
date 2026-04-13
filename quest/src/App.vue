<template>
	<div id="nextcloud-quest-wrapper" class="quest-wrapper" :class="{ 'sidebar-collapsed': sidebarCollapsed, 'sidebar-mobile-open': mobileMenuOpen }">
		<AppSidebar :collapsed="sidebarCollapsed" :class="{ open: mobileMenuOpen }" @toggle="toggleSidebar" />

		<main class="quest-main">
			<button class="mobile-menu-btn" @click="mobileMenuOpen = true">☰</button>
			<div class="main-container">
				<component :is="pageComponent" />
			</div>
		</main>

		<div v-if="mobileMenuOpen" class="mobile-overlay" @click="mobileMenuOpen = false" />

		<NotificationStack />
	</div>
</template>

<script>
import { mapState } from 'vuex'
import AppSidebar from './components/AppSidebar.vue'
import NotificationStack from './components/NotificationStack.vue'
import DashboardPage from './pages/DashboardPage.vue'
import AchievementsPage from './pages/AchievementsPage.vue'
import CharacterPage from './pages/CharacterPage.vue'
import AdventurePage from './pages/AdventurePage.vue'
import QuestsPage from './pages/QuestsPage.vue'
import ActivityPage from './pages/ActivityPage.vue'
import LeaderboardPage from './pages/LeaderboardPage.vue'
import SettingsPage from './pages/SettingsPage.vue'

const PAGE_MAP = {
	dashboard: 'DashboardPage',
	quests: 'QuestsPage',
	achievements: 'AchievementsPage',
	character: 'CharacterPage',
	adventure: 'AdventurePage',
	activity: 'ActivityPage',
	leaderboard: 'LeaderboardPage',
	settings: 'SettingsPage',
}

export default {
	name: 'App',

	components: {
		AppSidebar,
		NotificationStack,
		DashboardPage,
		AchievementsPage,
		CharacterPage,
		AdventurePage,
		QuestsPage,
		ActivityPage,
		LeaderboardPage,
		SettingsPage,
	},

	data() {
		return {
			sidebarCollapsed: false,
			mobileMenuOpen: false,
		}
	},

	methods: {
		toggleSidebar() {
			if (window.innerWidth <= 768) {
				this.mobileMenuOpen = !this.mobileMenuOpen
			} else {
				this.sidebarCollapsed = !this.sidebarCollapsed
			}
		},
	},

	computed: {
		...mapState('quest', ['activePage']),

		pageComponent() {
			return PAGE_MAP[this.activePage] || 'DashboardPage'
		},
	},
}
</script>

<style scoped>
.quest-wrapper {
	display: flex;
	width: 100%;
	min-height: calc(100vh - var(--header-height, 50px));
	background: var(--color-main-background);
}

.quest-main {
	flex: 1;
	min-width: 0;
	margin-left: var(--sidebar-width);
	min-height: calc(100vh - var(--header-height, 50px));
	transition: margin-left var(--transition-normal);
}

.sidebar-collapsed .quest-main {
	margin-left: var(--sidebar-collapsed-width);
}

.main-container {
	padding: var(--container-padding);
}

.mobile-menu-btn {
	display: none;
}

.mobile-overlay {
	display: none;
}

@media (max-width: 768px) {
	.quest-main {
		margin-left: 0;
	}

	.sidebar-collapsed .quest-main {
		margin-left: 0;
	}

	.mobile-menu-btn {
		display: block;
		position: fixed;
		top: calc(var(--header-height, 50px) + 8px);
		left: 8px;
		z-index: calc(var(--z-sidebar) - 1);
		background: var(--color-main-background);
		border: 1px solid var(--color-border);
		border-radius: var(--radius-medium);
		padding: 6px 10px;
		font-size: 18px;
		cursor: pointer;
		box-shadow: var(--shadow-md);
	}

	.mobile-overlay {
		display: block;
		position: fixed;
		inset: 0;
		background: rgba(0, 0, 0, 0.5);
		z-index: calc(var(--z-sidebar) - 1);
	}
}
</style>
