/**
 * @copyright Copyright (c) 2025 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 *
 * Quest API Service - Single source of truth for all API communication
 */

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

class QuestAPI {

	constructor() {
		this.baseURL = generateUrl('/apps/quest/api')
	}

	// ─── Stats ───────────────────────────────────────────────

	async getStats() {
		const { data } = await axios.get(`${this.baseURL}/stats`)
		return data
	}

	// ─── Tasks ───────────────────────────────────────────────

	async getQuestLists() {
		const { data } = await axios.get(`${this.baseURL}/quest-lists`)
		return data
	}

	async createTask(listId, title, priority = 'medium', description = null, dueDate = null) {
		const { data } = await axios.post(`${this.baseURL}/create-task`, {
			list_id: listId,
			title,
			priority,
			description,
			due_date: dueDate,
		})
		return data
	}

	async completeTask(taskId, listId, taskTitle, priority = 'medium') {
		const { data } = await axios.post(`${this.baseURL}/complete-quest`, {
			task_id: taskId,
			list_id: listId,
			task_title: taskTitle,
			priority,
		})
		return data
	}

	// ─── Achievements ────────────────────────────────────────

	async getAchievements() {
		const { data } = await axios.get(`${this.baseURL}/achievements`)
		return data
	}

	async getAchievementsByCategory() {
		const { data } = await axios.get(`${this.baseURL}/achievements/categories`)
		return data
	}

	async getRecentAchievements() {
		const { data } = await axios.get(`${this.baseURL}/achievements/recent`)
		return data
	}

	async getAchievementStats() {
		const { data } = await axios.get(`${this.baseURL}/achievements/stats`)
		return data
	}

	async getAchievementsByRarity(rarity) {
		const { data } = await axios.get(`${this.baseURL}/achievements/rarity/${rarity}`)
		return data
	}

	async getAchievementProgress(achievementKey) {
		const { data } = await axios.get(`${this.baseURL}/achievements/progress/${achievementKey}`)
		return data
	}

	// ─── History & Leaderboard ───────────────────────────────

	async getHistory(limit = 50, offset = 0) {
		const { data } = await axios.get(`${this.baseURL}/history`, {
			params: { limit, offset },
		})
		return data
	}

	async getLeaderboard(orderBy = 'lifetime_xp', limit = 10, offset = 0) {
		const { data } = await axios.get(`${this.baseURL}/leaderboard`, {
			params: { orderBy, limit, offset },
		})
		return data
	}

	// ─── Character ───────────────────────────────────────────

	async getCharacterData() {
		const { data } = await axios.get(`${this.baseURL}/character/data`)
		return data
	}

	async getAvailableItems() {
		const { data } = await axios.get(`${this.baseURL}/character/items`)
		return data
	}

	async getCustomizationData() {
		const { data } = await axios.get(`${this.baseURL}/character/customization`)
		return data
	}

	async updateAppearance(appearance) {
		const { data } = await axios.put(`${this.baseURL}/character/appearance`, appearance)
		return data
	}

	async getAvatarConfig() {
		const { data } = await axios.get(`${this.baseURL}/character/avatar`)
		return data
	}

	async updateAvatarConfig(config) {
		const { data } = await axios.put(`${this.baseURL}/character/avatar`, config)
		return data
	}

	async equipItem(itemKey) {
		const { data } = await axios.post(`${this.baseURL}/character/equip/${itemKey}`)
		return data
	}

	async unequipItem(slot) {
		const { data } = await axios.delete(`${this.baseURL}/character/unequip/${slot}`)
		return data
	}

	async getAges() {
		const { data } = await axios.get(`${this.baseURL}/character/ages`)
		return data
	}

	async getProgressionStats() {
		const { data } = await axios.get(`${this.baseURL}/character/progression`)
		return data
	}

	// ─── Epics ───────────────────────────────────────────────

	async getEpics() {
		const { data } = await axios.get(`${this.baseURL}/epics`)
		return data
	}

	async getEpic(id) {
		const { data } = await axios.get(`${this.baseURL}/epics/${id}`)
		return data
	}

	async createEpic(epicData) {
		const { data } = await axios.post(`${this.baseURL}/epics`, epicData)
		return data
	}

	async updateEpic(id, epicData) {
		const { data } = await axios.put(`${this.baseURL}/epics/${id}`, epicData)
		return data
	}

	async deleteEpic(id) {
		const { data } = await axios.delete(`${this.baseURL}/epics/${id}`)
		return data
	}

	async addTaskToEpic(epicId, taskData) {
		const { data } = await axios.post(`${this.baseURL}/epics/${epicId}/tasks`, taskData)
		return data
	}

	async removeTaskFromEpic(epicId, taskData) {
		const { data } = await axios.delete(`${this.baseURL}/epics/${epicId}/tasks`, { data: taskData })
		return data
	}

	// ─── Journey ─────────────────────────────────────────────

	async getJourneyStatus() {
		const { data } = await axios.get(`${this.baseURL}/journey/status`)
		return data
	}

	async getJourneyLog(limit = 20, offset = 0) {
		const { data } = await axios.get(`${this.baseURL}/journey/log`, {
			params: { limit, offset },
		})
		return data
	}

	// ─── Settings ────────────────────────────────────────────

	async getSettings() {
		const { data } = await axios.get(`${this.baseURL}/settings`)
		return data
	}

	async updateSettings(settings) {
		const { data } = await axios.put(`${this.baseURL}/settings`, settings)
		return data
	}

	async getAvailableCalendars() {
		const { data } = await axios.get(`${this.baseURL}/settings/calendars`)
		return data
	}

	async exportData() {
		const { data } = await axios.post(`${this.baseURL}/settings/export`)
		return data
	}

	async importData(importData) {
		const { data } = await axios.post(`${this.baseURL}/settings/import`, importData)
		return data
	}

	async resetProgress() {
		const { data } = await axios.post(`${this.baseURL}/settings/reset-progress`)
		return data
	}

	async resetToDefaults() {
		const { data } = await axios.post(`${this.baseURL}/settings/reset`)
		return data
	}

	async createBackup() {
		const { data } = await axios.post(`${this.baseURL}/settings/backup`)
		return data
	}

	async getBackups() {
		const { data } = await axios.get(`${this.baseURL}/settings/backups`)
		return data
	}

	async restoreBackup(backupId) {
		const { data } = await axios.post(`${this.baseURL}/settings/backup/${backupId}/restore`)
		return data
	}

}

export default new QuestAPI()
