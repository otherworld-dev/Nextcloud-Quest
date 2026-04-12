<template>
	<div class="modal-overlay" @click.self="$emit('close')">
		<div class="modal">
			<div class="modal-header">
				<h3>{{ editing ? 'Edit Epic' : 'Create Epic' }}</h3>
				<button class="modal-close" @click="$emit('close')">&times;</button>
			</div>

			<div class="modal-body">
				<!-- Title -->
				<div class="field">
					<label class="field-label">Title *</label>
					<input v-model="form.title" type="text" class="field-input" placeholder="e.g., Kitchen Renovation" autofocus>
				</div>

				<!-- Description -->
				<div class="field">
					<label class="field-label">Description</label>
					<textarea v-model="form.description" class="field-textarea" placeholder="What's this epic about?" rows="2" />
				</div>

				<!-- Emoji + Color -->
				<div class="field-row">
					<div class="field">
						<label class="field-label">Icon</label>
						<div class="emoji-grid">
							<button
								v-for="e in emojis"
								:key="e"
								class="emoji-btn"
								:class="{ selected: form.emoji === e }"
								@click="form.emoji = e"
							>{{ e }}</button>
						</div>
					</div>
					<div class="field">
						<label class="field-label">Color</label>
						<div class="color-grid">
							<button
								v-for="c in colors"
								:key="c"
								class="color-btn"
								:class="{ selected: form.color === c }"
								:style="{ background: c }"
								@click="form.color = c"
							/>
						</div>
					</div>
				</div>

				<!-- Task picker -->
				<div class="field" v-if="!editing">
					<label class="field-label">Select Tasks ({{ selectedCount }} selected)</label>
					<input v-model="taskSearch" type="text" class="field-input sm" placeholder="Search tasks...">
					<div class="task-picker">
						<div v-for="list in filteredLists" :key="list.id" class="picker-list">
							<div class="picker-list-name">{{ list.displayName || list.name }}</div>
							<label
								v-for="task in getPickableTasks(list)"
								:key="task.id"
								class="picker-task"
							>
								<input type="checkbox" :checked="isSelected(task, list)" @change="toggleTask(task, list)">
								<span>{{ task.title || task.summary || 'Untitled' }}</span>
							</label>
						</div>
						<div v-if="filteredLists.length === 0" class="picker-empty">No tasks found</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn" @click="$emit('close')">Cancel</button>
				<button class="btn primary" @click="submit" :disabled="!form.title.trim() || saving">
					{{ saving ? 'Saving...' : (editing ? 'Save' : 'Create Epic') }}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex'

export default {
	name: 'CreateEpicModal',

	props: {
		editing: { type: Object, default: null },
	},

	data() {
		return {
			saving: false,
			taskSearch: '',
			selectedTasks: [],
			form: {
				title: this.editing?.title || '',
				description: this.editing?.description || '',
				emoji: this.editing?.emoji || '\uD83D\uDCDC',
				color: this.editing?.color || '#0082c9',
			},
			emojis: [
				'\uD83D\uDCDC', '\u2694\uFE0F', '\uD83C\uDFF0', '\uD83D\uDE80',
				'\uD83C\uDF1F', '\uD83D\uDD25', '\uD83C\uDFC6', '\uD83D\uDCA1',
				'\uD83C\uDFAF', '\uD83D\uDEE0\uFE0F', '\uD83C\uDF31', '\uD83D\uDCDA',
				'\uD83C\uDFA8', '\uD83E\uDDE9', '\u2B50', '\uD83D\uDC8E',
			],
			colors: ['#0082c9', '#46ba61', '#e9322d', '#8b5cf6', '#f59e0b', '#ec407a', '#00bcd4', '#795548'],
		}
	},

	computed: {
		...mapState('quest', ['taskLists']),

		filteredLists() {
			if (!this.taskSearch) return this.taskLists
			const q = this.taskSearch.toLowerCase()
			return this.taskLists.filter(l => {
				const name = (l.displayName || l.name || '').toLowerCase()
				if (name.includes(q)) return true
				return (l.tasks || []).some(t => (t.title || t.summary || '').toLowerCase().includes(q))
			})
		},

		selectedCount() {
			return this.selectedTasks.length
		},
	},

	methods: {
		getPickableTasks(list) {
			let tasks = (list.tasks || []).filter(t => !t.completed)
			if (this.taskSearch) {
				const q = this.taskSearch.toLowerCase()
				tasks = tasks.filter(t => (t.title || t.summary || '').toLowerCase().includes(q))
			}
			return tasks
		},

		isSelected(task, list) {
			return this.selectedTasks.some(s => s.task_uid === String(task.id) && s.list_id === String(list.id))
		},

		toggleTask(task, list) {
			const key = String(task.id) + ':' + String(list.id)
			const idx = this.selectedTasks.findIndex(s => s.task_uid === String(task.id) && s.list_id === String(list.id))
			if (idx >= 0) {
				this.selectedTasks.splice(idx, 1)
			} else {
				this.selectedTasks.push({
					task_uid: String(task.id),
					list_id: String(list.id),
					task_title: task.title || task.summary || 'Untitled',
				})
			}
		},

		async submit() {
			if (!this.form.title.trim()) return
			this.saving = true
			try {
				if (this.editing) {
					await this.$store.dispatch('quest/updateEpic', { id: this.editing.id, data: this.form })
				} else {
					const epic = await this.$store.dispatch('quest/createEpic', this.form)
					// Add selected tasks
					for (const task of this.selectedTasks) {
						await this.$store.dispatch('quest/addTaskToEpic', { epicId: epic.id, taskData: task })
					}
				}
				this.$emit('close')
				this.$store.dispatch('quest/loadEpics')
			} catch (e) {
				console.error('Failed to save epic:', e)
			} finally {
				this.saving = false
			}
		},
	},
}
</script>

<style scoped>
.modal-overlay {
	position: fixed; inset: 0; background: rgba(0,0,0,0.6);
	display: flex; align-items: center; justify-content: center;
	z-index: var(--z-modal-backdrop);
}

.modal {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	width: 90%; max-width: 560px; max-height: 85vh;
	display: flex; flex-direction: column;
	z-index: var(--z-modal);
}

.modal-header {
	display: flex; justify-content: space-between; align-items: center;
	padding: 16px 20px; border-bottom: 1px solid var(--color-border);
}
.modal-header h3 { margin: 0; font-size: var(--font-size-large); }
.modal-close { background: none; border: none; font-size: 22px; cursor: pointer; color: var(--color-text-light); }

.modal-body { padding: 20px; overflow-y: auto; flex: 1; }

.modal-footer {
	display: flex; justify-content: flex-end; gap: 8px;
	padding: 12px 20px; border-top: 1px solid var(--color-border);
}

.field { margin-bottom: 16px; }
.field-label { display: block; font-size: var(--font-size-small); font-weight: 600; color: var(--color-main-text); margin-bottom: 6px; }
.field-input, .field-textarea {
	width: 100%; padding: 8px 12px; border: 1px solid var(--color-border);
	border-radius: var(--radius-medium); background: var(--color-main-background);
	color: var(--color-main-text); font-size: var(--font-size-normal); font-family: inherit;
}
.field-input:focus, .field-textarea:focus { outline: none; border-color: var(--color-primary-element, #0082c9); }
.field-input.sm { padding: 6px 10px; font-size: var(--font-size-small); margin-bottom: 8px; }
.field-textarea { resize: vertical; }

.field-row { display: flex; gap: 16px; }
.field-row .field { flex: 1; }

.emoji-grid { display: flex; flex-wrap: wrap; gap: 4px; }
.emoji-btn {
	width: 36px; height: 36px; font-size: 18px; border: 1px solid var(--color-border);
	border-radius: var(--radius-small); background: var(--color-main-background); cursor: pointer;
	display: flex; align-items: center; justify-content: center;
}
.emoji-btn.selected { border-color: var(--color-primary-element, #0082c9); background: var(--color-background-hover); }

.color-grid { display: flex; gap: 6px; }
.color-btn {
	width: 28px; height: 28px; border-radius: 50%; border: 2px solid transparent; cursor: pointer;
}
.color-btn.selected { border-color: var(--color-main-text); }

.task-picker {
	max-height: 240px; overflow-y: auto; border: 1px solid var(--color-border);
	border-radius: var(--radius-medium); padding: 4px;
}

.picker-list-name {
	font-size: 12px; font-weight: 700; color: var(--color-text-light); text-transform: uppercase;
	letter-spacing: 0.5px; padding: 6px 8px 2px;
}

.picker-task {
	display: flex; align-items: center; gap: 8px; padding: 4px 8px;
	border-radius: var(--radius-small); cursor: pointer; font-size: var(--font-size-small);
}
.picker-task:hover { background: var(--color-background-hover); }
.picker-task input { width: 14px; height: 14px; accent-color: var(--color-primary-element, #0082c9); }

.picker-empty { padding: 16px; text-align: center; color: var(--color-text-light); font-size: var(--font-size-small); }

.btn {
	padding: 8px 18px; border: 1px solid var(--color-border); border-radius: var(--radius-medium);
	background: var(--color-main-background); color: var(--color-main-text); cursor: pointer;
	font-size: var(--font-size-small); font-weight: 500;
}
.btn:hover { background: var(--color-background-hover); }
.btn.primary { background: var(--color-primary-element, #0082c9); color: white; border-color: transparent; }
.btn.primary:hover { filter: brightness(1.1); }
.btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
