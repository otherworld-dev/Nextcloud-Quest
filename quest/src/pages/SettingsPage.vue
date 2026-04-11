<template>
	<div class="settings-page">
		<div class="page-header">
			<h1 class="page-title">Settings</h1>
			<p class="page-subtitle">Configure your quest experience.</p>
		</div>

		<!-- Loading -->
		<div v-if="loading.settings && loading.tasks" class="loading-state">
			<div class="spinner" />
			<p>Loading settings...</p>
		</div>

		<template v-else>
			<!-- Task list selection -->
			<section class="settings-section">
				<div class="section-header">
					<h2 class="section-title">Task List Selection</h2>
					<div class="section-controls">
						<button class="btn" @click="refreshLists" :disabled="loading.tasks">
							🔄 Refresh
						</button>
						<button class="btn btn-primary" @click="saveSettings">
							💾 Save
						</button>
					</div>
				</div>

				<div class="search-box">
					<input v-model="listSearch" type="text" class="search-input" placeholder="Search lists...">
				</div>

				<div class="task-list-settings">
					<div
						v-for="list in filteredSettingsLists"
						:key="list.id || list.uri"
						class="list-setting-item"
					>
						<label class="list-toggle">
							<input
								type="checkbox"
								:checked="isListIncluded(list)"
								@change="toggleList(list)"
							>
							<span class="list-name">{{ list.displayName || list.name }}</span>
						</label>
						<input
							type="color"
							class="color-picker"
							:value="getListColor(list)"
							@input="setListColor(list, $event.target.value)"
						>
					</div>

					<div v-if="filteredSettingsLists.length === 0" class="empty-state small">
						<p>No task lists found. Make sure Nextcloud Tasks is installed.</p>
					</div>
				</div>
			</section>

			<!-- Display preferences -->
			<section class="settings-section">
				<h2 class="section-title">Display Preferences</h2>
				<div class="settings-controls">
					<label class="setting-toggle">
						<input type="checkbox" v-model="localSettings.hide_completed">
						<span>Hide completed tasks</span>
					</label>
				</div>
			</section>

			<!-- Color presets -->
			<section class="settings-section">
				<h2 class="section-title">Color Presets</h2>
				<div class="presets-grid">
					<div
						v-for="preset in colorPresets"
						:key="preset.name"
						class="preset-card"
						@click="applyPreset(preset)"
					>
						<div class="preset-colors">
							<div
								v-for="(color, i) in preset.colors"
								:key="i"
								class="preset-dot"
								:style="{ background: color }"
							/>
						</div>
						<span class="preset-name">{{ preset.name }}</span>
					</div>
				</div>
			</section>

			<!-- Danger zone -->
			<section class="settings-section danger">
				<h2 class="section-title">Data Management</h2>
				<div class="danger-actions">
					<button class="btn" @click="handleExport">Export Data</button>
					<button class="btn" @click="handleBackup">Create Backup</button>
					<button class="btn btn-danger" @click="handleReset">Reset Progress</button>
				</div>
			</section>

			<!-- Summary -->
			<section class="settings-section">
				<h2 class="section-title">Summary</h2>
				<div class="summary-grid">
					<StatCard :icon="icons.lists" label="Total Lists" :value="taskLists.length" />
					<StatCard :icon="icons.included" label="Included" :value="includedCount" />
					<StatCard :icon="icons.colored" label="Colored" :value="coloredCount" />
				</div>
			</section>
		</template>
	</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import StatCard from '../components/StatCard.vue'
import api from '../services/api'

export default {
	name: 'SettingsPage',
	components: { StatCard },

	data() {
		return {
			listSearch: '',
			icons: {
				lists: '\uD83D\uDCCB',
				included: '\u2705',
				colored: '\uD83C\uDFA8',
			},
			localSettings: {
				included_lists: [],
				list_colors: {},
				hide_completed: true,
			},
			colorPresets: [
				{ name: 'Default', colors: ['#0082c9', '#46ba61', '#f59e0b', '#e53e3e'] },
				{ name: 'Warm', colors: ['#f97316', '#dc2626', '#fbbf24', '#fb7185'] },
				{ name: 'Cool', colors: ['#3b82f6', '#06b6d4', '#8b5cf6', '#10b981'] },
				{ name: 'Earth', colors: ['#92400e', '#059669', '#7c2d12', '#365314'] },
			],
		}
	},

	computed: {
		...mapState('quest', ['taskLists', 'settings', 'loading']),

		filteredSettingsLists() {
			if (!this.listSearch) return this.taskLists
			const q = this.listSearch.toLowerCase()
			return this.taskLists.filter(l =>
				(l.displayName || l.name || '').toLowerCase().includes(q),
			)
		},

		includedCount() {
			return this.localSettings.included_lists.length
		},

		coloredCount() {
			return Object.keys(this.localSettings.list_colors).length
		},
	},

	watch: {
		settings: {
			handler(val) {
				this.localSettings = {
					included_lists: [...(val.included_lists || [])],
					list_colors: { ...(val.list_colors || {}) },
					hide_completed: val.hide_completed ?? true,
				}
			},
			immediate: true,
		},
	},

	mounted() {
		this.loadSettings()
		this.loadTaskLists()
	},

	methods: {
		...mapActions('quest', ['loadSettings', 'updateSettings', 'loadTaskLists']),

		refreshLists() {
			this.loadTaskLists()
		},

		isListIncluded(list) {
			const id = list.id || list.uri
			return this.localSettings.included_lists.includes(id)
		},

		toggleList(list) {
			const id = list.id || list.uri
			const idx = this.localSettings.included_lists.indexOf(id)
			if (idx >= 0) {
				this.localSettings.included_lists.splice(idx, 1)
			} else {
				this.localSettings.included_lists.push(id)
			}
		},

		getListColor(list) {
			const id = list.id || list.uri
			return this.localSettings.list_colors[id] || '#0082c9'
		},

		setListColor(list, color) {
			const id = list.id || list.uri
			this.$set(this.localSettings.list_colors, id, color)
		},

		applyPreset(preset) {
			this.taskLists.forEach((list, i) => {
				const id = list.id || list.uri
				this.$set(this.localSettings.list_colors, id, preset.colors[i % preset.colors.length])
			})
		},

		async saveSettings() {
			try {
				await this.updateSettings(this.localSettings)
			} catch (e) {
				console.error('Failed to save settings:', e)
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
			if (!confirm('Are you sure? This will reset all your quest progress. This cannot be undone.')) {
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
.settings-page { padding: 8px 0; }
.page-header { margin-bottom: 24px; }
.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

.settings-section {
	background: var(--color-main-background);
	border-radius: var(--radius-large);
	padding: 24px;
	margin-bottom: 20px;
	box-shadow: var(--shadow-sm);
}

.settings-section.danger {
	border: 1px solid var(--color-error);
}

.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-title { font-size: var(--font-size-xlarge); font-weight: 600; margin: 0 0 16px; }
.section-controls { display: flex; gap: 8px; }

.btn {
	display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
	border-radius: var(--radius-medium); border: 1px solid var(--color-border);
	background: var(--color-main-background); color: var(--color-main-text);
	cursor: pointer; font-size: var(--font-size-small);
}
.btn:hover { background: var(--color-background-hover); }
.btn:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-primary { background: var(--color-primary); color: white; border-color: var(--color-primary); }
.btn-primary:hover { background: var(--color-primary-dark); }
.btn-danger { background: var(--color-error); color: white; border-color: var(--color-error); }

.search-box { margin-bottom: 16px; }
.search-input {
	width: 100%; padding: 8px 12px; border: 1px solid var(--color-border);
	border-radius: var(--radius-medium); background: var(--color-main-background); color: var(--color-main-text);
}

/* Task list settings */
.task-list-settings { display: flex; flex-direction: column; gap: 8px; }
.list-setting-item {
	display: flex; align-items: center; gap: 12px; padding: 12px;
	border-radius: var(--radius-medium); border: 1px solid var(--color-border);
}
.list-toggle { display: flex; align-items: center; gap: 8px; flex: 1; cursor: pointer; }
.list-toggle input { width: 18px; height: 18px; cursor: pointer; }
.list-name { font-size: var(--font-size-normal); }
.color-picker { width: 36px; height: 36px; border: none; border-radius: var(--radius-small); cursor: pointer; padding: 0; }

/* Settings controls */
.settings-controls { display: flex; flex-direction: column; gap: 12px; }
.setting-toggle { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: var(--font-size-normal); }
.setting-toggle input { width: 18px; height: 18px; }

/* Color presets */
.presets-grid { display: flex; gap: 16px; flex-wrap: wrap; }
.preset-card {
	padding: 16px; border: 1px solid var(--color-border); border-radius: var(--radius-medium);
	cursor: pointer; text-align: center; transition: all var(--transition-fast);
}
.preset-card:hover { border-color: var(--color-primary); box-shadow: var(--shadow-md); }
.preset-colors { display: flex; gap: 6px; margin-bottom: 8px; justify-content: center; }
.preset-dot { width: 24px; height: 24px; border-radius: 50%; }
.preset-name { font-size: var(--font-size-small); font-weight: 500; }

/* Danger zone */
.danger-actions { display: flex; gap: 8px; flex-wrap: wrap; }

/* Summary */
.summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }

/* Loading & empty */
.loading-state { text-align: center; padding: 40px; }
.spinner { width: 32px; height: 32px; border: 3px solid var(--color-border); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 20px; }
.empty-state.small p { color: var(--color-text-light); }

@media (max-width: 768px) {
	.summary-grid { grid-template-columns: 1fr; }
}
</style>
