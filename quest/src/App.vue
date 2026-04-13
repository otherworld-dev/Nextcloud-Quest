<template>
	<div id="nextcloud-quest-wrapper" class="quest-wrapper" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
		<AppSidebar :collapsed="sidebarCollapsed" @toggle="sidebarCollapsed = !sidebarCollapsed" />

		<main class="quest-main">
			<div class="main-container">
				<component :is="pageComponent" />
			</div>
		</main>

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
import LeaderboardPage from './pages/LeaderboardPage.vue'
import SettingsPage from './pages/SettingsPage.vue'

const PAGE_MAP = {
	dashboard: 'DashboardPage',
	quests: 'QuestsPage',
	achievements: 'AchievementsPage',
	character: 'CharacterPage',
	adventure: 'AdventurePage',
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
		LeaderboardPage,
		SettingsPage,
	},

	data() {
		return {
			sidebarCollapsed: false,
		}
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
</style>
