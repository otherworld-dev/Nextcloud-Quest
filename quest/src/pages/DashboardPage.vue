<template>
	<div class="dashboard-page">
		<!-- Header -->
		<div class="page-header">
			<h1 class="page-title">Dashboard</h1>
			<p class="page-subtitle">Welcome back! Complete tasks to earn XP and level up.</p>
		</div>

		<!-- Onboarding -->
		<WelcomeBanner />

		<!-- Stat cards -->
		<section class="stats-grid">
			<Tooltip text="Your character level. Earn XP by completing tasks to level up and unlock new ages.">
				<StatCard :icon="icons.level" label="Level" :value="stats.level.level" :subtitle="stats.level.rank_title" />
			</Tooltip>
			<Tooltip text="Total experience points earned across all time. XP comes from tasks, journey encounters, and challenges.">
				<StatCard :icon="icons.xp" label="Total XP" :value="stats.level.lifetime_xp" subtitle="lifetime" />
			</Tooltip>
			<Tooltip text="Consecutive days with at least one task completed. Longer streaks boost your XP multiplier.">
				<StatCard :icon="icons.streak" label="Streak" :value="stats.streak.current_streak" subtitle="days" />
			</Tooltip>
			<Tooltip text="Tasks completed today. Complete more to progress challenges and trigger journey encounters.">
				<StatCard :icon="icons.tasks" label="Tasks Today" :value="stats.tasks_today || 0" subtitle="completed" />
			</Tooltip>
			<Tooltip text="Tasks completed this week (Monday to Sunday).">
				<StatCard :icon="icons.week" label="This Week" :value="stats.tasks_this_week || 0" subtitle="completed" />
			</Tooltip>
			<Tooltip text="Achievements unlocked out of 946 total across 19 categories.">
				<StatCard :icon="icons.achievements" label="Achievements" :value="achievementSummary" :subtitle="achievementSubtitle" />
			</Tooltip>
		</section>

		<!-- Two-column layout: tasks (primary) + sidebar (secondary) -->
		<div class="dashboard-columns">
			<!-- Left: Task lists -->
			<div class="dashboard-main">
				<div class="section-header">
					<h2 class="section-title">My Task Lists</h2>
					<div class="section-controls">
						<button class="btn btn-secondary" @click="loadTasks" :disabled="loading.tasks">
							{{ icons.refresh }} Refresh
						</button>
					</div>
				</div>

				<div class="filter-bar">
					<div class="search-box">
						<input v-model="searchQuery" type="text" class="search-input" placeholder="Search tasks...">
					</div>
					<select v-model="priorityFilter" class="filter-select">
						<option value="all">All Priorities</option>
						<option value="high">High</option>
						<option value="medium">Medium</option>
						<option value="low">Low</option>
					</select>
				</div>

				<div v-if="loading.tasks" class="loading-state">
					<div class="spinner" />
					<p>Loading task lists...</p>
				</div>

				<div v-else-if="filteredLists.length === 0" class="empty-state">
					<div class="empty-state-icon">&#x1F4CB;</div>
					<div class="empty-state-title">No task lists found</div>
					<div class="empty-state-text">
						Quest pulls tasks from Nextcloud's CalDAV calendars with VTODO support.
						Create a calendar with task support, or install the Tasks app to get started.
					</div>
					<button class="btn empty-action" @click="loadTasks">&#x1F504; Try Again</button>
				</div>

				<div v-else class="task-lists-grid">
					<div
						v-for="list in filteredLists"
						:key="list.id || list.uri"
						class="task-list-card"
						:style="{ '--list-color': list.color || 'var(--color-primary)' }"
					>
						<div class="list-header">
							<div class="list-color-bar" />
							<h3 class="list-name">{{ list.displayName || list.name }}</h3>
							<span class="list-count">{{ (list.tasks || []).length }} tasks</span>
						</div>
						<div class="list-tasks">
							<div
								v-for="task in getFilteredTasks(list)"
								:key="task.id || task.uid"
								class="task-item"
								:class="{ completed: task.completed }"
							>
								<input type="checkbox" class="task-checkbox"
									:checked="task.completed == 1"
									:disabled="loading.completingTask || task.completed == 1"
									@click.prevent="handleComplete(task, list)">
								<span class="task-title">{{ task.title || task.summary || 'Untitled task' }}</span>
								<div class="task-meta">
									<span class="task-priority" :class="getPriorityClass(task)">{{ getPriorityLabel(task) }}</span>
									<span class="task-xp">+{{ getTaskXP(task) }}</span>
								</div>
							</div>
							<div v-if="getFilteredTasks(list).length === 0" class="no-tasks">No matching tasks</div>
						</div>
						<div class="add-task-row">
							<input v-if="addingToList === (list.id || list.uri)" v-model="newTaskTitle" type="text"
								class="add-task-input" placeholder="Task title..."
								@keyup.enter="submitNewTask(list)" @keyup.esc="addingToList = null" ref="addTaskInput">
							<select v-if="addingToList === (list.id || list.uri)" v-model="newTaskPriority" class="add-task-priority">
								<option value="low">Low</option>
								<option value="medium">Medium</option>
								<option value="high">High</option>
							</select>
							<button v-if="addingToList === (list.id || list.uri)" class="add-task-btn submit"
								@click="submitNewTask(list)" :disabled="!newTaskTitle.trim()">Add</button>
							<button v-else class="add-task-btn" @click="startAddTask(list)">+ Add Task</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Right: Challenges + Goals + Epics + Achievements -->
			<div class="dashboard-aside">
				<!-- Challenges -->
				<div v-if="challenges.length > 0" class="aside-section">
					<h3 class="aside-title">Challenges</h3>
					<div
						v-for="ch in challenges"
						:key="ch.id"
						class="challenge-mini"
						:class="{ completed: ch.is_completed }"
					>
						<div class="challenge-mini-top">
							<span class="challenge-period" :class="ch.period">{{ ch.period }}</span>
							<span class="challenge-mini-reward">+{{ ch.xp_reward }}</span>
						</div>
						<div class="challenge-mini-title">{{ ch.title }}</div>
						<div class="challenge-bar">
							<div class="challenge-fill" :style="{ width: ch.percentage + '%' }" />
						</div>
						<div class="challenge-mini-footer">{{ ch.progress }}/{{ ch.target }}</div>
					</div>
				</div>

				<!-- Next Goals -->
				<div v-if="nextGoals.length > 0" class="aside-section">
					<h3 class="aside-title">Next Goals</h3>
					<div v-for="goal in nextGoals.slice(0, 3)" :key="goal.key" class="goal-mini">
						<div class="goal-mini-bar">
							<div class="goal-mini-fill" :style="{ width: goal.progress + '%' }" />
						</div>
						<span class="goal-mini-name">{{ goal.name }}</span>
						<span class="goal-mini-pct">{{ goal.progress }}%</span>
					</div>
				</div>

				<!-- Active Epics -->
				<div v-if="activeEpics.length > 0" class="aside-section">
					<h3 class="aside-title">
						Active Epics
						<button class="aside-link" @click="navigateToQuests">View All</button>
					</h3>
					<div
						v-for="epic in activeEpics.slice(0, 3)"
						:key="epic.id"
						class="epic-mini-compact"
						@click="navigateToQuests"
					>
						<span class="epic-emoji-sm">{{ epic.emoji || '\uD83D\uDCDC' }}</span>
						<div class="epic-mini-body">
							<span class="epic-mini-name">{{ epic.title }}</span>
							<div class="epic-mini-bar-sm">
								<div class="epic-mini-fill-sm" :style="{ width: (epic.progress_percentage || 0) + '%', background: epic.color || '#0082c9' }" />
							</div>
						</div>
						<span class="epic-mini-count">{{ epic.completed_tasks }}/{{ epic.total_tasks }}</span>
					</div>
				</div>

				<!-- Recent Achievements -->
				<div class="aside-section">
					<h3 class="aside-title">Recent Achievements</h3>
					<div v-if="recentAchievements.length === 0" class="aside-empty">
						Complete tasks to unlock achievements!
					</div>
					<div v-else>
						<div v-for="a in recentAchievements.slice(0, 4)" :key="a.key" class="achievement-mini">
							<img :src="iconUrl(a.icon)" :alt="a.name" class="achievement-img-xs">
							<span class="achievement-mini-name">{{ a.name }}</span>
							<span class="achievement-mini-date">{{ formatDate(a.unlocked_at) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import { generateFilePath } from '@nextcloud/router'
import { soundTaskComplete } from '../services/audio'
import StatCard from '../components/StatCard.vue'
import Tooltip from '../components/Tooltip.vue'
import WelcomeBanner from '../components/WelcomeBanner.vue'

export default {
	name: 'DashboardPage',

	components: { StatCard, Tooltip, WelcomeBanner },

	data() {
		return {
			searchQuery: '',
			priorityFilter: 'all',
			addingToList: null,
			newTaskTitle: '',
			newTaskPriority: 'medium',
			icons: {
				level: '\u2B50',
				xp: '\u2728',
				streak: '\uD83D\uDD25',
				tasks: '\u2705',
				week: '\uD83D\uDCC5',
				achievements: '\uD83C\uDFC6',
				refresh: '\uD83D\uDD04',
			},
		}
	},

	computed: {
		...mapState('quest', ['stats', 'taskLists', 'achievements', 'epics', 'challenges', 'loading']),
		...mapGetters('quest', ['unlockedAchievements', 'activeEpics']),

		achievementSummary() {
			return `${this.stats.achievements.unlocked}/${this.stats.achievements.total}`
		},

		achievementSubtitle() {
			return `${this.stats.achievements.percentage}% complete`
		},

		filteredLists() {
			return this.taskLists.filter(list => {
				if (!this.searchQuery) return true
				const q = this.searchQuery.toLowerCase()
				const name = (list.displayName || list.name || '').toLowerCase()
				if (name.includes(q)) return true
				return (list.tasks || []).some(t =>
					(t.title || t.summary || '').toLowerCase().includes(q),
				)
			})
		},

		recentAchievements() {
			return this.unlockedAchievements
				.slice()
				.sort((a, b) => new Date(b.unlocked_at) - new Date(a.unlocked_at))
				.slice(0, 5)
		},

		nextGoals() {
			return this.achievements
				.filter(a => {
					if (a.unlocked) return false
					const pct = a.progress_percentage || a.progress || 0
					return pct > 0 && pct < 100
				})
				.map(a => ({
					...a,
					progress: Math.min(a.progress_percentage || a.progress || 0, 99),
				}))
				.sort((a, b) => b.progress - a.progress)
				.slice(0, 4)
		},
	},

	mounted() {
		this.loadTasks()
		this.loadAchievements()
		this.loadEpics()
		this.loadChallenges()
	},

	methods: {
		...mapActions('quest', ['loadTaskLists', 'loadAchievements', 'loadEpics', 'loadChallenges', 'completeTask', 'createTask']),

		navigateToQuests() {
			this.$store.commit('quest/setActivePage', 'quests')
			window.history.pushState({}, '', '/index.php/apps/quest/quests')
		},

		startAddTask(list) {
			this.addingToList = list.id || list.uri
			this.newTaskTitle = ''
			this.newTaskPriority = 'medium'
			this.$nextTick(() => {
				const inputs = this.$refs.addTaskInput
				if (inputs) {
					const input = Array.isArray(inputs) ? inputs[inputs.length - 1] : inputs
					if (input) input.focus()
				}
			})
		},

		async submitNewTask(list) {
			if (!this.newTaskTitle.trim()) return
			try {
				await this.createTask({
					listId: list.id || list.uri,
					title: this.newTaskTitle.trim(),
					priority: this.newTaskPriority,
				})
				this.newTaskTitle = ''
				this.addingToList = null
			} catch (e) {
				console.error('Failed to create task:', e)
			}
		},

		iconUrl(icon) {
			return generateFilePath('quest', '', 'img/achievements/' + (icon || 'default.svg'))
		},

		loadTasks() {
			this.loadTaskLists()
		},

		getFilteredTasks(list) {
			let tasks = list.tasks || []
			if (this.searchQuery) {
				const q = this.searchQuery.toLowerCase()
				tasks = tasks.filter(t => (t.title || t.summary || '').toLowerCase().includes(q))
			}
			if (this.priorityFilter !== 'all') {
				tasks = tasks.filter(t => this.getTaskPriority(t) === this.priorityFilter)
			}
			return tasks
		},

		getTaskPriority(task) {
			if (task.priority === 1 || task.priority === 'high') return 'high'
			if (task.priority === 5 || task.priority === 'medium') return 'medium'
			return 'low'
		},

		getPriorityClass(task) {
			return 'priority-' + this.getTaskPriority(task)
		},

		getPriorityLabel(task) {
			const p = this.getTaskPriority(task)
			return p.charAt(0).toUpperCase() + p.slice(1)
		},

		getTaskXP(task) {
			const p = this.getTaskPriority(task)
			if (p === 'high') return 20
			if (p === 'medium') return 15
			return 10
		},

		async handleComplete(task, list) {
			// Update locally immediately — no flicker
			this.$set(task, 'completed', 1)
			soundTaskComplete()
			try {
				await this.completeTask({
					taskId: task.id || task.uid,
					listId: list.id,
					taskTitle: task.title || task.summary,
					priority: this.getTaskPriority(task),
				})
			} catch (error) {
				// Revert on failure
				this.$set(task, 'completed', 0)
				console.error('Failed to complete task:', error)
			}
		},

		formatDueDate(dateString) {
			if (!dateString) return ''
			try {
				const d = new Date(dateString)
				const now = new Date()
				const diff = d - now
				if (diff < 0) return 'Overdue'
				if (diff < 86400000) return 'Today'
				if (diff < 172800000) return 'Tomorrow'
				return d.toLocaleDateString()
			} catch (e) {
				return ''
			}
		},

		formatDate(dateString) {
			if (!dateString) return ''
			const d = new Date(dateString)
			const now = new Date()
			const diff = now - d
			if (diff < 86400000) return 'Today'
			if (diff < 172800000) return 'Yesterday'
			return d.toLocaleDateString()
		},
	},
}
</script>

<style scoped>
.dashboard-page {
	padding: 16px 8px;
}

/* ── Page header ── */
.page-header {
	margin-bottom: 28px;
	padding-bottom: 16px;
	border-bottom: 1px solid var(--color-border);
}

.page-title {
	font-size: var(--font-size-huge);
	font-weight: 700;
	color: var(--color-main-text);
	margin: 0 0 4px 0;
}

.page-subtitle {
	font-size: var(--font-size-normal);
	color: var(--color-text-light);
	margin: 0;
}

/* ── Stats grid ── */
.stats-grid {
	display: grid;
	grid-template-columns: repeat(6, 1fr);
	gap: 16px;
	margin-bottom: 36px;
}

/* ── Sections ── */
.content-section {
	margin-bottom: 36px;
}

.section-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 16px;
	padding-bottom: 12px;
	border-bottom: 1px solid var(--color-border);
}

.section-title {
	font-size: var(--font-size-xlarge);
	font-weight: 600;
	color: var(--color-main-text);
	margin: 0;
}

.section-controls {
	display: flex;
	gap: 8px;
}

/* ── Buttons ── */
.btn {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 8px 16px;
	border-radius: var(--radius-medium);
	border: 1px solid var(--color-border);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: var(--font-size-small);
	font-weight: 500;
	cursor: pointer;
	transition: all var(--transition-fast);
}

.btn:hover {
	background: var(--color-background-hover);
	border-color: var(--color-border-dark);
}

.btn:disabled {
	opacity: 0.5;
	cursor: not-allowed;
}

.btn-primary {
	background: var(--color-primary);
	color: white;
	border-color: var(--color-primary);
}

.btn-primary:hover {
	background: var(--color-primary-dark);
}

/* ── Filter bar ── */
.filter-bar {
	display: flex;
	gap: 12px;
	margin-bottom: 20px;
	flex-wrap: wrap;
}

.search-box {
	flex: 1;
	min-width: 200px;
}

.search-input {
	width: 100%;
	padding: 10px 14px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	background: var(--color-background-hover);
	color: var(--color-main-text);
	font-size: var(--font-size-normal);
	transition: border-color var(--transition-fast), background var(--transition-fast);
}

.search-input:focus {
	outline: none;
	border-color: var(--color-primary);
	background: var(--color-main-background);
}

.filter-select {
	padding: 10px 14px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	background: var(--color-background-hover);
	color: var(--color-main-text);
	font-size: var(--font-size-small);
}

/* ── Two-column layout ── */
.dashboard-columns {
	display: grid;
	grid-template-columns: 1fr 320px;
	gap: 20px;
	margin-bottom: 24px;
}

.dashboard-main {
	min-width: 0;
}

.dashboard-aside {
	display: flex;
	flex-direction: column;
	gap: 16px;
}

.aside-section {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 14px;
}

.aside-title {
	font-size: var(--font-size-small);
	font-weight: 700;
	color: var(--color-main-text);
	margin: 0 0 10px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.aside-link {
	background: none;
	border: none;
	color: var(--color-primary-element, #0082c9);
	font-size: 11px;
	cursor: pointer;
	text-transform: none;
	font-weight: 500;
}

.aside-empty {
	font-size: 12px;
	color: var(--color-text-light);
	text-align: center;
	padding: 12px;
}

/* ── Sidebar challenges ── */
.challenge-mini {
	padding: 8px 0;
	border-bottom: 1px solid var(--color-border);
}
.challenge-mini:last-child { border-bottom: none; }
.challenge-mini.completed { opacity: 0.4; }
.challenge-mini-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.challenge-mini-reward { font-size: 11px; font-weight: 700; color: var(--color-success, #46ba61); }
.challenge-mini-title { font-size: 12px; font-weight: 600; color: var(--color-main-text); margin-bottom: 4px; }
.challenge-mini-footer { font-size: 11px; color: var(--color-text-light); }

/* ── Sidebar goals ── */
.goal-mini {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 6px 0;
}
.goal-mini-bar { flex: 1; height: 4px; background: var(--color-background-dark); border-radius: 2px; overflow: hidden; }
.goal-mini-fill { height: 100%; border-radius: 2px; background: var(--color-primary-element, #0082c9); }
.goal-mini-name { font-size: 12px; color: var(--color-main-text); flex: 2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.goal-mini-pct { font-size: 11px; font-weight: 600; color: var(--color-text-light); width: 30px; text-align: right; }

/* ── Sidebar epics ── */
.epic-mini-compact {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 6px 0;
	cursor: pointer;
	border-bottom: 1px solid var(--color-border);
}
.epic-mini-compact:last-child { border-bottom: none; }
.epic-emoji-sm { font-size: 16px; flex-shrink: 0; }
.epic-mini-body { flex: 1; min-width: 0; }
.epic-mini-name { display: block; font-size: 12px; font-weight: 600; color: var(--color-main-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.epic-mini-bar-sm { height: 3px; background: var(--color-background-dark); border-radius: 2px; overflow: hidden; margin-top: 3px; }
.epic-mini-fill-sm { height: 100%; border-radius: 2px; }
.epic-mini-count { font-size: 11px; color: var(--color-text-light); flex-shrink: 0; }

/* ── Sidebar achievements ── */
.achievement-mini {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 5px 0;
	border-bottom: 1px solid var(--color-border);
}
.achievement-mini:last-child { border-bottom: none; }
.achievement-img-xs { width: 20px; height: 20px; object-fit: contain; flex-shrink: 0; }
.achievement-mini-name { flex: 1; font-size: 12px; font-weight: 500; color: var(--color-main-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.achievement-mini-date { font-size: 10px; color: var(--color-text-light); flex-shrink: 0; }

/* ── Challenges ── */
.challenges-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 12px;
}

.challenge-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 14px;
	transition: box-shadow var(--transition-fast);
}

.challenge-card:hover { box-shadow: var(--shadow-md); }
.challenge-card.completed { opacity: 0.5; }

.challenge-top {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 8px;
}

.challenge-period {
	font-size: 10px;
	font-weight: 700;
	text-transform: uppercase;
	padding: 2px 8px;
	border-radius: 8px;
	color: white;
}
.challenge-period.daily { background: var(--color-primary-element, #0082c9); }
.challenge-period.weekly { background: #9c27b0; }

.challenge-done {
	color: var(--color-success, #46ba61);
	font-weight: 700;
	font-size: 16px;
}

.challenge-title {
	font-size: var(--font-size-small);
	font-weight: 600;
	color: var(--color-main-text);
	margin-bottom: 2px;
}

.challenge-desc {
	font-size: 12px;
	color: var(--color-text-light);
	margin-bottom: 10px;
}

.challenge-bar {
	height: 4px;
	background: var(--color-background-dark);
	border-radius: 2px;
	overflow: hidden;
	margin-bottom: 6px;
}

.challenge-fill {
	height: 100%;
	border-radius: 2px;
	background: var(--color-primary-element, #0082c9);
	transition: width var(--transition-slow);
}

.challenge-footer {
	display: flex;
	justify-content: space-between;
	font-size: 11px;
}

.challenge-progress { color: var(--color-text-light); }
.challenge-reward { color: var(--color-success, #46ba61); font-weight: 600; }

/* (old epic/goal sections removed — now in sidebar) */

/* ── Task list cards ── */
.task-lists-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
	gap: 16px;
}

.task-list-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	overflow: hidden;
	transition: box-shadow var(--transition-fast);
	border-top: 3px solid var(--list-color);
}

.task-list-card:hover {
	box-shadow: var(--shadow-md);
}

.list-header {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 10px 14px;
	background: var(--color-background-hover);
	border-bottom: 1px solid var(--color-border);
}

.list-color-bar {
	display: none;
}

.list-name {
	flex: 1;
	font-size: var(--font-size-normal);
	font-weight: 700;
	color: var(--color-main-text);
	margin: 0;
}

.list-count {
	font-size: 11px;
	color: var(--color-text-lighter);
	background: var(--color-background-dark);
	padding: 2px 8px;
	border-radius: 10px;
	font-weight: 600;
}

.list-tasks {
	padding: 2px 0;
	max-height: 200px;
	overflow-y: auto;
}

/* ── Task items ── */
.task-item {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 4px 12px;
	transition: background var(--transition-fast);
	border-bottom: 1px solid var(--color-border);
	line-height: 1.3;
}

.task-item:last-child {
	border-bottom: none;
}

.task-item:hover {
	background: var(--color-background-hover);
}

.task-item.completed {
	opacity: 0.35;
}

.task-item.completed .task-title {
	text-decoration: line-through;
}

.task-checkbox {
	width: 15px;
	height: 15px;
	flex-shrink: 0;
	cursor: pointer;
	accent-color: var(--color-success);
	margin: 0;
}

.task-title {
	flex: 1;
	font-size: var(--font-size-small);
	font-weight: 500;
	color: var(--color-main-text);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	min-width: 0;
}

.task-meta {
	display: flex;
	align-items: center;
	gap: 6px;
	flex-shrink: 0;
}

.task-due {
	font-size: 10px;
	color: var(--color-text-lighter);
}

.task-priority {
	font-size: 9px;
	padding: 1px 6px;
	border-radius: 8px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.3px;
}

.priority-high {
	background: rgba(233, 50, 45, 0.12);
	color: var(--color-error);
}

.priority-medium {
	background: rgba(246, 165, 2, 0.12);
	color: var(--color-warning-contrast, #cc5500);
}

.priority-low {
	background: rgba(0, 130, 201, 0.08);
	color: var(--color-primary);
}

.task-xp {
	font-size: 11px;
	font-weight: 700;
	color: var(--color-success);
	flex-shrink: 0;
	white-space: nowrap;
}

/* ── Add task ── */
.add-task-row {
	display: flex;
	gap: 6px;
	padding: 6px 12px;
	border-top: 1px solid var(--color-border);
}

.add-task-input {
	flex: 1;
	padding: 4px 8px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-small);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: var(--font-size-small);
}

.add-task-input:focus {
	outline: none;
	border-color: var(--color-primary-element, #0082c9);
}

.add-task-priority {
	padding: 4px 6px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-small);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: 12px;
}

.add-task-btn {
	padding: 4px 12px;
	border: none;
	border-radius: var(--radius-small);
	background: var(--color-background-hover);
	color: var(--color-text-light);
	cursor: pointer;
	font-size: 12px;
	white-space: nowrap;
	transition: all var(--transition-fast);
}

.add-task-btn:hover {
	background: var(--color-background-dark);
	color: var(--color-main-text);
}

.add-task-btn.submit {
	background: var(--color-primary-element, #0082c9);
	color: white;
}

.add-task-btn.submit:hover {
	filter: brightness(1.1);
}

.add-task-btn:disabled {
	opacity: 0.4;
	cursor: not-allowed;
}

.no-tasks {
	text-align: center;
	padding: 20px;
	color: var(--color-text-lighter);
	font-size: var(--font-size-small);
	font-style: italic;
}

/* (old goal/activity/card styles removed — now in sidebar) */

/* ── Loading & empty states ── */
.loading-state {
	text-align: center;
	padding: 60px 20px;
	color: var(--color-text-lighter);
}

.spinner {
	width: 36px;
	height: 36px;
	border: 3px solid var(--color-border);
	border-top-color: var(--color-primary);
	border-radius: 50%;
	animation: spin 0.8s linear infinite;
	margin: 0 auto 16px;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}

.empty-state {
	text-align: center;
	padding: 48px 20px;
	border: 1px dashed var(--color-border);
	border-radius: var(--radius-large);
	background: var(--color-background-hover);
}

.empty-state.small {
	padding: 24px 16px;
	border: none;
	background: none;
}

.empty-state-icon {
	font-size: 48px;
	margin-bottom: 16px;
	opacity: 0.6;
}

.empty-state-title {
	font-size: var(--font-size-large);
	font-weight: 600;
	color: var(--color-main-text);
	margin-bottom: 6px;
}

.empty-state-text {
	font-size: var(--font-size-normal);
	color: var(--color-text-light);
	line-height: 1.5;
}

.empty-action {
	margin-top: 16px;
}

/* ── Responsive ── */
@media (max-width: 1200px) {
	.stats-grid {
		grid-template-columns: repeat(3, 1fr);
	}

	.dashboard-columns {
		grid-template-columns: 1fr;
	}

	.dashboard-aside {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
	}
}

@media (max-width: 768px) {
	.stats-grid {
		grid-template-columns: repeat(2, 1fr);
	}

	.task-lists-grid {
		grid-template-columns: 1fr;
	}

	.filter-bar {
		flex-direction: column;
	}

	.dashboard-aside {
		grid-template-columns: 1fr;
	}
}
</style>
