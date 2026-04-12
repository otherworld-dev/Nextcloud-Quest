<template>
	<div class="settings-page">
		<div class="page-header">
			<h1 class="page-title">Settings</h1>
			<p class="page-subtitle">Configure your quest experience.</p>
		</div>

		<div v-if="loading.settings" class="loading-state">
			<div class="spinner" />
			<p>Loading settings...</p>
		</div>

		<template v-else>
			<!-- Task list selection -->
			<section class="settings-section">
				<div class="section-head">
					<div>
						<h3 class="section-title">Task Lists</h3>
						<p class="section-desc">Choose which Nextcloud task lists to include in Quest.</p>
					</div>
					<div class="section-actions">
						<button class="btn" @click="refreshLists" :disabled="loading.tasks">Refresh</button>
						<button class="btn primary" @click="saveSettings">Save</button>
					</div>
				</div>

				<div v-if="taskLists.length > 0" class="list-grid">
					<label
						v-for="list in taskLists"
						:key="list.id || list.uri"
						class="list-item"
					>
						<input
							type="checkbox"
							:checked="isListIncluded(list)"
							@change="toggleList(list)"
						>
						<span class="list-color" :style="{ background: getListColor(list) }" />
						<span class="list-label">{{ list.displayName || list.name }}</span>
						<span class="list-task-count">{{ (list.tasks || []).length }} tasks</span>
						<input
							type="color"
							class="color-input"
							:value="getListColor(list)"
							@input="setListColor(list, $event.target.value)"
							title="Set list color"
						>
					</label>
				</div>
				<div v-else class="empty-msg">No task lists found. Install the Tasks app to get started.</div>
			</section>

			<!-- Display -->
			<section class="settings-section">
				<h3 class="section-title">Display</h3>
				<div class="settings-grid">
					<label class="setting-row">
						<span class="setting-label">Show XP popup on completion</span>
						<input type="checkbox" v-model="local.general.show_xp_popup" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Show streak counter in sidebar</span>
						<input type="checkbox" v-model="local.general.show_streak_counter" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Show level progress in sidebar</span>
						<input type="checkbox" v-model="local.general.show_level_progress" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Compact view</span>
						<input type="checkbox" v-model="local.general.compact_view" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Enable animations</span>
						<input type="checkbox" v-model="local.themes.enable_animations" @change="markDirty">
					</label>
				</div>
			</section>

			<!-- Notifications -->
			<section class="settings-section">
				<h3 class="section-title">Notifications</h3>
				<div class="settings-grid">
					<label class="setting-row">
						<span class="setting-label">Achievement unlocks</span>
						<input type="checkbox" v-model="local.notifications.notify_achievements" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Level ups</span>
						<input type="checkbox" v-model="local.notifications.notify_level_up" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Streak milestones</span>
						<input type="checkbox" v-model="local.notifications.notify_streak_milestones" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Streak reminder</span>
						<input type="checkbox" v-model="local.notifications.notify_streak_reminder" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Daily summary</span>
						<input type="checkbox" v-model="local.notifications.notify_daily_summary" @change="markDirty">
					</label>
				</div>
			</section>

			<!-- Gameplay -->
			<section class="settings-section">
				<h3 class="section-title">Gameplay</h3>
				<div class="settings-grid">
					<label class="setting-row">
						<span class="setting-label">Difficulty</span>
						<select v-model="local.gameplay.difficulty_level" @change="markDirty" class="setting-select">
							<option value="easy">Easy</option>
							<option value="normal">Normal</option>
							<option value="hard">Hard</option>
						</select>
					</label>
					<label class="setting-row">
						<span class="setting-label">Streak grace period (hours)</span>
						<select v-model="local.gameplay.streak_grace_period" @change="markDirty" class="setting-select">
							<option value="6">6 hours</option>
							<option value="12">12 hours</option>
							<option value="24">24 hours</option>
						</select>
					</label>
					<label class="setting-row">
						<span class="setting-label">Weekend streak bonus</span>
						<input type="checkbox" v-model="local.gameplay.weekend_streak_bonus" @change="markDirty">
					</label>
				</div>
			</section>

			<!-- Privacy -->
			<section class="settings-section">
				<h3 class="section-title">Privacy</h3>
				<div class="settings-grid">
					<label class="setting-row">
						<span class="setting-label">Show on leaderboard</span>
						<input type="checkbox" v-model="local.privacy.show_on_leaderboard" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Anonymous leaderboard name</span>
						<input type="checkbox" v-model="local.privacy.anonymous_leaderboard" @change="markDirty">
					</label>
					<label class="setting-row">
						<span class="setting-label">Share achievements</span>
						<input type="checkbox" v-model="local.privacy.share_achievements" @change="markDirty">
					</label>
				</div>
			</section>

			<!-- Data management -->
			<section class="settings-section danger">
				<h3 class="section-title">Data Management</h3>
				<p class="section-desc">Export, backup, or reset your quest data.</p>
				<div class="data-actions">
					<button class="btn" @click="handleExport">Export Data</button>
					<button class="btn" @click="handleBackup">Create Backup</button>
					<button class="btn danger" @click="handleReset">Reset All Progress</button>
				</div>
			</section>

			<!-- Save bar -->
			<div v-if="dirty" class="save-bar">
				<span>You have unsaved changes</span>
				<button class="btn primary" @click="saveSettings">Save Settings</button>
			</div>
		</template>
	</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import api from '../services/api'

export default {
	name: 'SettingsPage',

	data() {
		return {
			dirty: false,
			local: {
				general: {},
				themes: {},
				notifications: {},
				gameplay: {},
				privacy: {},
				character: {},
			},
			listSettings: {
				included_lists: [],
				list_colors: {},
			},
		}
	},

	computed: {
		...mapState('quest', ['taskLists', 'settings', 'loading']),
	},

	watch: {
		settings: {
			handler(val) {
				if (val && typeof val === 'object') {
					this.local = {
						general: { ...(val.general || {}) },
						themes: { ...(val.themes || {}) },
						notifications: { ...(val.notifications || {}) },
						gameplay: { ...(val.gameplay || {}) },
						privacy: { ...(val.privacy || {}) },
						character: { ...(val.character || {}) },
					}
				}
			},
			immediate: true,
			deep: true,
		},
	},

	mounted() {
		this.loadSettings()
		this.loadTaskLists()
	},

	methods: {
		...mapActions('quest', ['loadSettings', 'updateSettings', 'loadTaskLists']),

		markDirty() {
			this.dirty = true
		},

		refreshLists() {
			this.loadTaskLists()
		},

		isListIncluded(list) {
			return this.listSettings.included_lists.includes(list.id || list.uri)
		},

		toggleList(list) {
			const id = list.id || list.uri
			const idx = this.listSettings.included_lists.indexOf(id)
			if (idx >= 0) {
				this.listSettings.included_lists.splice(idx, 1)
			} else {
				this.listSettings.included_lists.push(id)
			}
			this.dirty = true
		},

		getListColor(list) {
			return this.listSettings.list_colors[list.id || list.uri] || list.color || '#0082c9'
		},

		setListColor(list, color) {
			this.$set(this.listSettings.list_colors, list.id || list.uri, color)
			this.dirty = true
		},

		async saveSettings() {
			try {
				await this.updateSettings(this.local)
				this.dirty = false
			} catch (e) {
				console.error('Failed to save:', e)
			}
		},

		async handleExport() {
			try {
				const data = await api.exportData()
				const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
				const url = URL.createObjectURL(blob)
				const a = document.createElement('a')
				a.href = url
				a.download = 'quest-export.json'
				a.click()
				URL.revokeObjectURL(url)
			} catch (e) {
				console.error('Export failed:', e)
			}
		},

		async handleBackup() {
			try {
				await api.createBackup()
			} catch (e) {
				console.error('Backup failed:', e)
			}
		},

		async handleReset() {
			if (!confirm('This will permanently reset ALL your quest progress including XP, achievements, and character data. This cannot be undone. Are you sure?')) {
				return
			}
			try {
				await api.resetProgress()
				window.location.reload()
			} catch (e) {
				console.error('Reset failed:', e)
			}
		},
	},
}
</script>

<style scoped>
.settings-page {
	padding: 16px 8px;
	max-width: 800px;
}

.page-header {
	margin-bottom: 24px;
	padding-bottom: 16px;
	border-bottom: 1px solid var(--color-border);
}

.page-title {
	font-size: var(--font-size-huge);
	font-weight: 700;
	color: var(--color-main-text);
	margin: 0 0 4px;
}

.page-subtitle {
	font-size: var(--font-size-normal);
	color: var(--color-text-light);
	margin: 0;
}

/* ── Sections ── */
.settings-section {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 20px;
	margin-bottom: 16px;
}

.settings-section.danger {
	border-color: var(--color-error, #e9322d);
}

.section-head {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	margin-bottom: 16px;
}

.section-title {
	font-size: var(--font-size-large);
	font-weight: 600;
	color: var(--color-main-text);
	margin: 0 0 4px;
}

.section-desc {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	margin: 0;
}

.section-actions {
	display: flex;
	gap: 8px;
	flex-shrink: 0;
}

/* ── Buttons ── */
.btn {
	padding: 6px 16px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	background: var(--color-main-background);
	color: var(--color-main-text);
	cursor: pointer;
	font-size: var(--font-size-small);
	font-weight: 500;
	transition: all var(--transition-fast);
}

.btn:hover {
	background: var(--color-background-hover);
}

.btn:disabled {
	opacity: 0.5;
	cursor: not-allowed;
}

.btn.primary {
	background: var(--color-primary-element, #0082c9);
	color: white;
	border-color: transparent;
}

.btn.primary:hover {
	filter: brightness(1.1);
}

.btn.danger {
	color: var(--color-error, #e9322d);
	border-color: var(--color-error, #e9322d);
}

.btn.danger:hover {
	background: var(--color-error, #e9322d);
	color: white;
}

/* ── Task list grid ── */
.list-grid {
	display: flex;
	flex-direction: column;
	gap: 4px;
}

.list-item {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 8px 12px;
	border-radius: var(--radius-medium);
	cursor: pointer;
	transition: background var(--transition-fast);
}

.list-item:hover {
	background: var(--color-background-hover);
}

.list-item input[type="checkbox"] {
	width: 16px;
	height: 16px;
	accent-color: var(--color-primary-element, #0082c9);
	cursor: pointer;
}

.list-color {
	width: 12px;
	height: 12px;
	border-radius: 50%;
	flex-shrink: 0;
}

.list-label {
	flex: 1;
	font-size: var(--font-size-normal);
	color: var(--color-main-text);
}

.list-task-count {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
}

.color-input {
	width: 28px;
	height: 28px;
	border: none;
	border-radius: var(--radius-small);
	cursor: pointer;
	padding: 0;
	background: none;
}

/* ── Settings rows ── */
.settings-grid {
	display: flex;
	flex-direction: column;
	gap: 2px;
}

.setting-row {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 8px 4px;
	border-bottom: 1px solid var(--color-border);
	cursor: pointer;
}

.setting-row:last-child {
	border-bottom: none;
}

.setting-row:hover {
	background: var(--color-background-hover);
	border-radius: var(--radius-small);
}

.setting-label {
	font-size: var(--font-size-normal);
	color: var(--color-main-text);
}

.setting-row input[type="checkbox"] {
	width: 16px;
	height: 16px;
	accent-color: var(--color-primary-element, #0082c9);
	cursor: pointer;
}

.setting-select {
	padding: 4px 8px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-small);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: var(--font-size-small);
}

/* ── Data actions ── */
.data-actions {
	display: flex;
	gap: 8px;
	margin-top: 12px;
	flex-wrap: wrap;
}

/* ── Save bar ── */
.save-bar {
	position: sticky;
	bottom: 0;
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 12px 20px;
	background: var(--color-background-hover);
	border: 1px solid var(--color-primary-element, #0082c9);
	border-radius: var(--radius-large);
	margin-top: 16px;
	font-size: var(--font-size-small);
	color: var(--color-main-text);
}

/* ── Empty / loading ── */
.empty-msg {
	padding: 16px;
	text-align: center;
	color: var(--color-text-light);
	font-size: var(--font-size-small);
}

.loading-state {
	text-align: center;
	padding: 60px;
}

.spinner {
	width: 36px;
	height: 36px;
	border: 3px solid var(--color-border);
	border-top-color: var(--color-primary-element, #0082c9);
	border-radius: 50%;
	animation: spin 0.8s linear infinite;
	margin: 0 auto 16px;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}
</style>
