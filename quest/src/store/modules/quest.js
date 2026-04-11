/**
 * @copyright Copyright (c) 2025 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 *
 * Vuex store — single source of truth for all Quest state.
 * No legacy QuestApp/StatsService integration.
 */

import api from '../../services/api'

const state = {
	user: {
		uid: null,
		displayName: null,
	},
	activePage: 'dashboard',

	stats: {
		level: {
			level: 1,
			rank_title: 'Task Novice',
			xp: 0,
			lifetime_xp: 0,
			xp_to_next: 100,
			progress_percentage: 0,
		},
		health: {
			current_health: 100,
			max_health: 100,
			percentage: 100,
		},
		streak: {
			current_streak: 0,
			longest_streak: 0,
			last_completion: null,
			is_active_today: false,
			grace_period_ends: null,
		},
		achievements: {
			total: 0,
			unlocked: 0,
			percentage: 0,
		},
		tasks_today: 0,
		tasks_this_week: 0,
		total_tasks: 0,
		leaderboard_rank: null,
	},

	// Task lists from Nextcloud Tasks
	taskLists: [],

	// Achievement data
	achievements: [],

	// Completion history
	history: [],
	historyStats: {
		total_tasks: 0,
		total_xp: 0,
		average_per_day: 0,
		daily_stats: {},
	},

	// Character data
	character: {
		age: null,
		equipment: {},
		items: [],
		ages: [],
	},

	// Settings
	settings: {
		included_lists: [],
		list_colors: {},
		hide_completed: true,
	},

	// Notification queue
	notifications: [],

	// Loading states
	loading: {
		stats: false,
		tasks: false,
		achievements: false,
		history: false,
		character: false,
		settings: false,
		completingTask: false,
	},
}

const mutations = {
	setUser(state, user) {
		state.user = { ...state.user, ...user }
	},

	setActivePage(state, page) {
		state.activePage = page
	},

	setStats(state, stats) {
		state.stats = { ...state.stats, ...stats }
	},

	setTaskLists(state, lists) {
		state.taskLists = lists
	},

	setAchievements(state, achievements) {
		state.achievements = achievements
	},

	setHistory(state, { history, stats }) {
		state.history = history
		if (stats) {
			state.historyStats = stats
		}
	},

	setCharacter(state, data) {
		state.character = { ...state.character, ...data }
	},

	setSettings(state, settings) {
		state.settings = { ...state.settings, ...settings }
	},

	setLoading(state, { type, loading }) {
		state.loading[type] = loading
	},

	addToHistory(state, entry) {
		state.history.unshift(entry)
		if (state.history.length > 100) {
			state.history = state.history.slice(0, 100)
		}
	},

	unlockAchievement(state, achievement) {
		const exists = state.achievements.find(a => a.key === achievement.key)
		if (!exists) {
			state.achievements.push({
				...achievement,
				unlocked: true,
				unlocked_at: new Date().toISOString(),
			})
		} else if (!exists.unlocked) {
			exists.unlocked = true
			exists.unlocked_at = new Date().toISOString()
		}
		state.stats.achievements.unlocked = state.achievements.filter(a => a.unlocked).length
		state.stats.achievements.percentage = state.stats.achievements.total
			? Math.round((state.stats.achievements.unlocked / state.stats.achievements.total) * 100)
			: 0
	},

	pushNotification(state, notification) {
		state.notifications.push({
			id: Date.now() + Math.random(),
			...notification,
		})
	},

	dismissNotification(state, id) {
		state.notifications = state.notifications.filter(n => n.id !== id)
	},
}

const actions = {
	async loadStats({ commit }) {
		commit('setLoading', { type: 'stats', loading: true })
		try {
			const response = await api.getStats()
			if (response.status === 'success') {
				const data = response.data || response
				// Map API response shape to store state shape
				const mapped = { ...data }
				if (data.tasks) {
					mapped.tasks_today = data.tasks.completed_today || 0
					mapped.tasks_this_week = data.tasks.completed_this_week || 0
					mapped.total_tasks = data.tasks.total_completed || 0
				}
				commit('setStats', mapped)
			}
		} catch (error) {
			console.error('Failed to load stats:', error)
		} finally {
			commit('setLoading', { type: 'stats', loading: false })
		}
	},

	async loadTaskLists({ commit }) {
		commit('setLoading', { type: 'tasks', loading: true })
		try {
			const response = await api.getQuestLists()
			if (response.status === 'success') {
				commit('setTaskLists', response.data || response.lists || [])
			}
		} catch (error) {
			console.error('Failed to load task lists:', error)
		} finally {
			commit('setLoading', { type: 'tasks', loading: false })
		}
	},

	async loadAchievements({ commit }) {
		commit('setLoading', { type: 'achievements', loading: true })
		try {
			const response = await api.getAchievements()
			if (response.status === 'success') {
				commit('setAchievements', response.achievements || response.data || [])
			}
		} catch (error) {
			console.error('Failed to load achievements:', error)
		} finally {
			commit('setLoading', { type: 'achievements', loading: false })
		}
	},

	async loadHistory({ commit }, { limit = 50, offset = 0 } = {}) {
		commit('setLoading', { type: 'history', loading: true })
		try {
			const response = await api.getHistory(limit, offset)
			if (response.status === 'success') {
				commit('setHistory', response.data || { history: [], stats: {} })
			}
		} catch (error) {
			console.error('Failed to load history:', error)
		} finally {
			commit('setLoading', { type: 'history', loading: false })
		}
	},

	async completeTask({ commit, dispatch }, { taskId, taskTitle, priority = 'medium' }) {
		commit('setLoading', { type: 'completingTask', loading: true })
		try {
			const response = await api.completeTask(taskId, taskTitle, priority)
			if (response.status === 'success') {
				const result = response.data || response

				// Update stats from response
				if (result.user_stats) {
					commit('setStats', {
						level: {
							level: result.user_stats.level,
							xp: result.user_stats.xp,
							lifetime_xp: result.user_stats.lifetime_xp,
							rank_title: result.user_stats.rank_title,
							xp_to_next: result.user_stats.xp_to_next,
							progress_percentage: result.user_stats.progress_percentage,
						},
					})
				}
				if (result.health) {
					commit('setStats', {
						health: {
							current_health: result.health.current_health,
							max_health: result.health.max_health,
							percentage: result.health.percentage,
						},
					})
				}
				if (result.streak) {
					commit('setStats', {
						streak: {
							current_streak: result.streak.current_streak,
							longest_streak: result.streak.longest_streak,
							last_completion: new Date().toISOString(),
							is_active_today: true,
						},
					})
				}

				// Add to history
				commit('addToHistory', {
					task_id: taskId,
					task_title: taskTitle,
					xp_earned: result.xp_earned || 0,
					completed_at: new Date().toISOString(),
				})

				// Handle new achievements
				const newAchievements = result.achievements || result.new_achievements || []
				if (Array.isArray(newAchievements)) {
					newAchievements.forEach(a => {
						commit('unlockAchievement', a)
						commit('pushNotification', {
							type: 'achievement',
							title: 'Achievement Unlocked!',
							message: a.name,
							data: a,
						})
					})
				}

				// Handle level up
				if (result.xp?.leveled_up || result.user_stats?.leveled_up) {
					commit('pushNotification', {
						type: 'level_up',
						title: 'Level Up!',
						message: `You reached Level ${result.user_stats?.level || result.xp?.level}`,
						data: result.user_stats || result.xp,
					})
				}

				// XP notification
				commit('pushNotification', {
					type: 'xp_gain',
					title: `+${result.xp_earned || 0} XP`,
					message: taskTitle,
				})

				// Refresh full stats in background
				dispatch('loadStats')

				return result
			}
		} catch (error) {
			console.error('Failed to complete task:', error)
			throw error
		} finally {
			commit('setLoading', { type: 'completingTask', loading: false })
		}
	},

	async loadCharacter({ commit }) {
		commit('setLoading', { type: 'character', loading: true })
		try {
			const response = await api.getCharacterData()
			if (response.status === 'success') {
				commit('setCharacter', response.data || response)
			}
		} catch (error) {
			console.error('Failed to load character:', error)
		} finally {
			commit('setLoading', { type: 'character', loading: false })
		}
	},

	async equipItem({ commit, dispatch }, itemKey) {
		try {
			const response = await api.equipItem(itemKey)
			if (response.status === 'success') {
				dispatch('loadCharacter')
			}
			return response
		} catch (error) {
			console.error('Failed to equip item:', error)
			throw error
		}
	},

	async unequipItem({ commit, dispatch }, slot) {
		try {
			const response = await api.unequipItem(slot)
			if (response.status === 'success') {
				dispatch('loadCharacter')
			}
			return response
		} catch (error) {
			console.error('Failed to unequip item:', error)
			throw error
		}
	},

	async loadSettings({ commit }) {
		commit('setLoading', { type: 'settings', loading: true })
		try {
			const response = await api.getSettings()
			if (response.status === 'success') {
				commit('setSettings', response.data || response)
			}
		} catch (error) {
			console.error('Failed to load settings:', error)
		} finally {
			commit('setLoading', { type: 'settings', loading: false })
		}
	},

	async updateSettings({ commit }, settings) {
		try {
			const response = await api.updateSettings(settings)
			if (response.status === 'success') {
				commit('setSettings', settings)
			}
			return response
		} catch (error) {
			console.error('Failed to update settings:', error)
			throw error
		}
	},
}

const getters = {
	unlockedAchievements: state => state.achievements.filter(a => a.unlocked),
	lockedAchievements: state => state.achievements.filter(a => !a.unlocked),

	isStreakActive: state => {
		if (!state.stats.streak.last_completion) return false
		const last = new Date(state.stats.streak.last_completion)
		const now = new Date()
		return last.toDateString() === now.toDateString()
	},

	streakAtRisk: state => {
		if (state.stats.streak.current_streak === 0) return false
		if (state.stats.streak.is_active_today) return false
		return true
	},

	healthPercentage: state => {
		const { current_health, max_health } = state.stats.health
		return max_health ? Math.round((current_health / max_health) * 100) : 100
	},

	xpPercentage: state => state.stats.level.progress_percentage || 0,
}

export default {
	namespaced: true,
	state,
	mutations,
	actions,
	getters,
}
