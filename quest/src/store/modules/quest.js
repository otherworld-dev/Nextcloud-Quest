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

	// Epics
	epics: [],

	// Journey
	journey: {
		current_age_key: 'stone',
		current_age_name: 'Stone Age',
		steps_taken: 0,
		steps_per_encounter: 3,
		steps_remaining: 3,
		total_steps: 0,
		encounters_completed: 0,
		bosses_defeated: 0,
		battles_won: 0,
		battles_lost: 0,
		treasures_found: 0,
		events_completed: 0,
		prestige_level: 0,
		player_power: 10,
		theme_colors: {},
		log: [],
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
		epics: false,
		journey: false,
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

	setEpics(state, epics) {
		state.epics = epics
	},

	addEpic(state, epic) {
		state.epics.unshift(epic)
	},

	setJourney(state, data) {
		state.journey = { ...state.journey, ...data }
	},

	setJourneyLog(state, log) {
		state.journey.log = log
	},

	removeEpicFromList(state, epicId) {
		state.epics = state.epics.filter(e => e.id !== epicId)
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

	async completeTask({ commit, dispatch }, { taskId, listId, taskTitle, priority = 'medium' }) {
		commit('setLoading', { type: 'completingTask', loading: true })
		try {
			const response = await api.completeTask(taskId, listId, taskTitle, priority)
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

				// Handle completed epics
				const completedEpics = result.completed_epics || []
				completedEpics.forEach(epic => {
					commit('pushNotification', {
						type: 'epic_complete',
						title: `${epic.emoji || '\u{1F3C6}'} Epic Complete!`,
						message: `${epic.title} — +${epic.bonus_xp} bonus XP`,
						data: epic,
					})
				})

				// Handle journey encounter
				const encounter = result.journey_encounter
				if (encounter) {
					const typeIcons = { battle: '\u2694\uFE0F', boss: '\uD83D\uDC80', treasure: '\uD83D\uDCE6', event: '\uD83D\uDCDC' }
					const icon = typeIcons[encounter.encounter_type] || '\u2728'
					let title = ''
					let type = 'journey'

					if (encounter.encounter_type === 'battle' || encounter.encounter_type === 'boss') {
						if (encounter.outcome === 'win') {
							title = `${icon} Defeated ${encounter.encounter_name}!`
							type = encounter.encounter_type === 'boss' ? 'journey_boss' : 'journey_win'
						} else {
							title = `${icon} Defeated by ${encounter.encounter_name}`
							type = 'journey_lose'
						}
					} else if (encounter.encounter_type === 'treasure') {
						title = `${icon} Found ${encounter.rewards?.item_name || 'treasure'}!`
						type = 'journey_treasure'
					} else {
						title = `${icon} ${encounter.encounter_name}`
						type = 'journey_event'
					}

					const rewardParts = []
					if (encounter.rewards?.xp) rewardParts.push(`+${encounter.rewards.xp} XP`)
					if (encounter.rewards?.item_name) rewardParts.push(encounter.rewards.item_name)
					if (encounter.rewards?.health_change) rewardParts.push(`${encounter.rewards.health_change} HP`)

					commit('pushNotification', { type, title, message: rewardParts.join(' | ') || encounter.age_name })
				}

				// Update task counts from response
				if (result.stats) {
					commit('setStats', {
						tasks_today: result.stats.tasks_today || 0,
						tasks_this_week: result.stats.tasks_this_week || 0,
						total_tasks: result.stats.total_tasks || 0,
					})
				}

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
			const [charResponse, itemsResponse] = await Promise.all([
				api.getCharacterData(),
				api.getAvailableItems(),
			])
			const charData = charResponse.status === 'success' ? (charResponse.data || {}) : {}
			const itemsData = itemsResponse.status === 'success' ? (itemsResponse.data || {}) : {}
			commit('setCharacter', {
				...charData,
				items: itemsData.items || itemsData.available_items || [],
			})
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

	async loadJourney({ commit }) {
		commit('setLoading', { type: 'journey', loading: true })
		try {
			const response = await api.getJourneyStatus()
			if (response.status === 'success') {
				commit('setJourney', response.data || {})
			}
		} catch (error) {
			console.error('Failed to load journey:', error)
		} finally {
			commit('setLoading', { type: 'journey', loading: false })
		}
	},

	async loadJourneyLog({ commit }, { limit = 20, offset = 0 } = {}) {
		try {
			const response = await api.getJourneyLog(limit, offset)
			if (response.status === 'success') {
				commit('setJourneyLog', response.data || [])
			}
		} catch (error) {
			console.error('Failed to load journey log:', error)
		}
	},

	async loadEpics({ commit }) {
		commit('setLoading', { type: 'epics', loading: true })
		try {
			const response = await api.getEpics()
			if (response.status === 'success') {
				commit('setEpics', response.data || [])
			}
		} catch (error) {
			console.error('Failed to load epics:', error)
		} finally {
			commit('setLoading', { type: 'epics', loading: false })
		}
	},

	async createEpic({ commit }, epicData) {
		try {
			const response = await api.createEpic(epicData)
			if (response.status === 'success') {
				commit('addEpic', response.data)
				return response.data
			}
		} catch (error) {
			console.error('Failed to create epic:', error)
			throw error
		}
	},

	async updateEpic({ dispatch }, { id, data }) {
		try {
			await api.updateEpic(id, data)
			dispatch('loadEpics')
		} catch (error) {
			console.error('Failed to update epic:', error)
			throw error
		}
	},

	async deleteEpic({ commit }, epicId) {
		try {
			await api.deleteEpic(epicId)
			commit('removeEpicFromList', epicId)
		} catch (error) {
			console.error('Failed to delete epic:', error)
			throw error
		}
	},

	async addTaskToEpic({ dispatch }, { epicId, taskData }) {
		try {
			await api.addTaskToEpic(epicId, taskData)
			dispatch('loadEpics')
		} catch (error) {
			console.error('Failed to add task to epic:', error)
			throw error
		}
	},

	async removeTaskFromEpic({ dispatch }, { epicId, taskData }) {
		try {
			await api.removeTaskFromEpic(epicId, taskData)
			dispatch('loadEpics')
		} catch (error) {
			console.error('Failed to remove task from epic:', error)
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

	activeEpics: state => state.epics.filter(e => e.status === 'active'),
	completedEpics: state => state.epics.filter(e => e.status === 'completed'),
}

export default {
	namespaced: true,
	state,
	mutations,
	actions,
	getters,
}
