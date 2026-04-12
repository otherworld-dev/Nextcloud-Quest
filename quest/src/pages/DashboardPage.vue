<template>
	<div class="dashboard-page">
		<!-- Header -->
		<div class="page-header">
			<h1 class="page-title">Dashboard</h1>
			<p class="page-subtitle">Welcome back! Complete tasks to earn XP and level up.</p>
		</div>

		<!-- Stat cards -->
		<section class="stats-grid">
			<StatCard :icon="icons.level" label="Level" :value="stats.level.level" :subtitle="stats.level.rank_title" />
			<StatCard :icon="icons.xp" label="Total XP" :value="stats.level.lifetime_xp" subtitle="lifetime" />
			<StatCard :icon="icons.streak" label="Streak" :value="stats.streak.current_streak" subtitle="days" />
			<StatCard :icon="icons.tasks" label="Tasks Today" :value="stats.tasks_today || 0" subtitle="completed" />
			<StatCard :icon="icons.week" label="This Week" :value="stats.tasks_this_week || 0" subtitle="completed" />
			<StatCard :icon="icons.achievements" label="Achievements" :value="achievementSummary" :subtitle="achievementSubtitle" />
		</section>

		<!-- Task lists -->
		<section class="content-section">
			<div class="section-header">
				<h2 class="section-title">My Task Lists</h2>
				<div class="section-controls">
					<button class="btn btn-secondary" @click="loadTasks" :disabled="loading.tasks">
						{{ icons.refresh }} Refresh
					</button>
				</div>
			</div>

			<!-- Search & filters -->
			<div class="filter-bar">
				<div class="search-box">
					<input
						v-model="searchQuery"
						type="text"
						class="search-input"
						placeholder="Search tasks..."
					>
				</div>
				<select v-model="priorityFilter" class="filter-select">
					<option value="all">All Priorities</option>
					<option value="high">High</option>
					<option value="medium">Medium</option>
					<option value="low">Low</option>
				</select>
			</div>

			<!-- Loading -->
			<div v-if="loading.tasks" class="loading-state">
				<div class="spinner" />
				<p>Loading task lists...</p>
			</div>

			<!-- Empty state -->
			<div v-else-if="filteredLists.length === 0" class="empty-state">
				<div class="empty-state-icon">&#x1F4CB;</div>
				<div class="empty-state-title">No task lists found</div>
				<div class="empty-state-text">
					Connect to Nextcloud Tasks to see your lists here.
				</div>
			</div>

			<!-- Task lists grid -->
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
							<input
								type="checkbox"
								class="task-checkbox"
								:checked="task.completed == 1"
								:disabled="loading.completingTask || task.completed == 1"
								@click.prevent="handleComplete(task, list)"
							>

							<span class="task-title">{{ task.title || task.summary || 'Untitled task' }}</span>
							<div class="task-meta">
								<span class="task-priority" :class="getPriorityClass(task)">
									{{ getPriorityLabel(task) }}
								</span>
								<span class="task-xp">+{{ getTaskXP(task) }}</span>
							</div>
						</div>

						<div v-if="getFilteredTasks(list).length === 0" class="no-tasks">
							No matching tasks
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Next goals -->
		<section v-if="nextGoals.length > 0" class="content-section">
			<div class="section-header">
				<h2 class="section-title">Next Goals</h2>
			</div>
			<div class="goals-grid">
				<div v-for="goal in nextGoals" :key="goal.key" class="goal-card">
					<div class="goal-progress-ring">
						<svg width="48" height="48" viewBox="0 0 48 48">
							<circle cx="24" cy="24" r="20" fill="none" stroke="var(--color-border)" stroke-width="3" />
							<circle cx="24" cy="24" r="20" fill="none" stroke="var(--color-primary-element, #0082c9)"
								stroke-width="3" stroke-linecap="round"
								:stroke-dasharray="125.6"
								:stroke-dashoffset="125.6 * (1 - goal.progress / 100)"
								transform="rotate(-90 24 24)" />
						</svg>
						<span class="goal-pct">{{ goal.progress }}%</span>
					</div>
					<div class="goal-info">
						<span class="goal-name">{{ goal.name }}</span>
						<span class="goal-desc">{{ goal.description }}</span>
					</div>
					<span class="goal-rarity" :class="goal.rarity">{{ goal.rarity }}</span>
				</div>
			</div>
		</section>

		<!-- Recent activity -->
		<section class="content-section">
			<div class="section-header">
				<h2 class="section-title">Recent Activity</h2>
			</div>

			<div class="activity-grid">
				<!-- Recent achievements -->
				<div class="card">
					<h3 class="card-title">Recent Achievements</h3>
					<div v-if="recentAchievements.length === 0" class="empty-state small">
						<div class="empty-state-icon">&#x1F3C6;</div>
						<div class="empty-state-text">Complete tasks to unlock achievements!</div>
					</div>
					<div v-else class="achievement-list">
						<div v-for="a in recentAchievements" :key="a.key" class="achievement-item">
							<span class="achievement-icon">{{ a.icon || '&#x1F3C6;' }}</span>
							<div class="achievement-info">
								<span class="achievement-name">{{ a.name }}</span>
								<span class="achievement-date">{{ formatDate(a.unlocked_at) }}</span>
							</div>
						</div>
					</div>
				</div>

				<!-- Quick stats -->
				<div class="card">
					<h3 class="card-title">Quick Stats</h3>
					<div class="stats-list">
						<div class="stat-row">
							<span class="stat-label">Longest Streak</span>
							<span class="stat-value">{{ stats.streak.longest_streak }} days</span>
						</div>
						<div class="stat-row">
							<span class="stat-label">Total Tasks</span>
							<span class="stat-value">{{ stats.total_tasks || 0 }}</span>
						</div>
						<div class="stat-row">
							<span class="stat-label">Current Rank</span>
							<span class="stat-value">{{ stats.level.rank_title }}</span>
						</div>
						<div class="stat-row">
							<span class="stat-label">Health</span>
							<span class="stat-value">{{ stats.health.current_health }}/{{ stats.health.max_health }}</span>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</template>

<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import StatCard from '../components/StatCard.vue'

export default {
	name: 'DashboardPage',

	components: { StatCard },

	data() {
		return {
			searchQuery: '',
			priorityFilter: 'all',
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
		...mapState('quest', ['stats', 'taskLists', 'achievements', 'loading']),
		...mapGetters('quest', ['unlockedAchievements']),

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
				.filter(a => !a.unlocked && (a.progress_percentage || a.progress || 0) > 0)
				.map(a => ({
					...a,
					progress: a.progress_percentage || a.progress || 0,
				}))
				.sort((a, b) => b.progress - a.progress)
				.slice(0, 4)
		},
	},

	mounted() {
		this.loadTasks()
		this.loadAchievements()
	},

	methods: {
		...mapActions('quest', ['loadTaskLists', 'loadAchievements', 'completeTask']),

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

.no-tasks {
	text-align: center;
	padding: 20px;
	color: var(--color-text-lighter);
	font-size: var(--font-size-small);
	font-style: italic;
}

/* ── Next goals ── */
.goals-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
	gap: 12px;
}

.goal-card {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 16px;
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	transition: box-shadow var(--transition-fast);
}

.goal-card:hover {
	box-shadow: var(--shadow-md);
}

.goal-progress-ring {
	position: relative;
	width: 48px;
	height: 48px;
	flex-shrink: 0;
}

.goal-pct {
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 10px;
	font-weight: 700;
	color: var(--color-main-text);
}

.goal-info {
	flex: 1;
	min-width: 0;
}

.goal-name {
	display: block;
	font-size: var(--font-size-small);
	font-weight: 600;
	color: var(--color-main-text);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.goal-desc {
	display: block;
	font-size: 12px;
	color: var(--color-text-light);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.goal-rarity {
	font-size: 9px;
	padding: 2px 6px;
	border-radius: 8px;
	font-weight: 700;
	text-transform: uppercase;
	color: white;
	flex-shrink: 0;
}

.goal-rarity.common { background: #9e9e9e; }
.goal-rarity.rare { background: #2196f3; }
.goal-rarity.epic { background: #9c27b0; }
.goal-rarity.legendary { background: #ff9800; }

/* ── Activity section ── */
.activity-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 20px;
}

.card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 24px;
	transition: box-shadow var(--transition-fast);
}

.card:hover {
	box-shadow: var(--shadow-md);
}

.card-title {
	font-size: var(--font-size-large);
	font-weight: 600;
	color: var(--color-main-text);
	margin: 0 0 16px 0;
	padding-bottom: 12px;
	border-bottom: 1px solid var(--color-border);
}

/* ── Achievement list ── */
.achievement-list {
	display: flex;
	flex-direction: column;
}

.achievement-item {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 10px 0;
	border-bottom: 1px solid var(--color-border);
}

.achievement-item:last-child {
	border-bottom: none;
}

.achievement-icon {
	font-size: 28px;
	width: 40px;
	height: 40px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: var(--color-background-hover);
	border-radius: var(--radius-medium);
}

.achievement-info {
	flex: 1;
}

.achievement-name {
	display: block;
	font-weight: 600;
	color: var(--color-main-text);
	font-size: var(--font-size-normal);
}

.achievement-date {
	display: block;
	font-size: var(--font-size-small);
	color: var(--color-text-lighter);
	margin-top: 2px;
}

/* ── Stats list ── */
.stats-list {
	display: flex;
	flex-direction: column;
}

.stat-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 10px 0;
	border-bottom: 1px solid var(--color-border);
}

.stat-row:last-child {
	border-bottom: none;
}

.stat-label {
	font-size: var(--font-size-normal);
	color: var(--color-text-light);
}

.stat-value {
	font-weight: 600;
	color: var(--color-primary);
	font-size: var(--font-size-normal);
}

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
}

/* ── Responsive ── */
@media (max-width: 1200px) {
	.stats-grid {
		grid-template-columns: repeat(3, 1fr);
	}
}

@media (max-width: 768px) {
	.stats-grid {
		grid-template-columns: repeat(2, 1fr);
	}

	.task-lists-grid {
		grid-template-columns: 1fr;
	}

	.activity-grid {
		grid-template-columns: 1fr;
	}

	.filter-bar {
		flex-direction: column;
	}
}
</style>
