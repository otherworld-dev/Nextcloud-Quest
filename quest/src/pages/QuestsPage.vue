<template>
	<div class="quests-page">
		<div class="page-header">
			<div class="header-left">
				<h1 class="page-title">Quests</h1>
				<p class="page-subtitle">Create Epics to group tasks and earn bonus XP on completion.</p>
			</div>
			<button class="btn primary" @click="showCreateModal = true">+ Create Epic</button>
		</div>

		<!-- Tabs -->
		<div class="tabs">
			<button class="tab" :class="{ active: tab === 'active' }" @click="tab = 'active'">
				Active ({{ activeEpics.length }})
			</button>
			<button class="tab" :class="{ active: tab === 'completed' }" @click="tab = 'completed'">
				Completed ({{ completedEpics.length }})
			</button>
		</div>

		<!-- Loading -->
		<div v-if="loading.epics" class="loading-state">
			<div class="spinner" />
			<p>Loading epics...</p>
		</div>

		<!-- Epic grid -->
		<div v-else-if="currentEpics.length > 0" class="epics-grid">
			<EpicCard
				v-for="epic in currentEpics"
				:key="epic.id"
				:epic="epic"
				@edit="editEpic"
				@delete="confirmDelete"
			/>
		</div>

		<!-- Empty state -->
		<div v-else class="empty-state">
			<div class="empty-icon">{{ tab === 'active' ? '\uD83D\uDCDC' : '\uD83C\uDFC6' }}</div>
			<div class="empty-title" v-if="tab === 'active'">No active Epics</div>
			<div class="empty-title" v-else>No completed Epics yet</div>
			<div class="empty-desc" v-if="tab === 'active'">
				Epics group multiple tasks into a larger quest. Complete all tasks in an Epic to earn a 2x XP bonus!
			</div>
			<div class="empty-desc" v-else>
				Complete all tasks in an active Epic to see it here.
			</div>
			<button v-if="tab === 'active'" class="btn primary" @click="showCreateModal = true">Create Your First Epic</button>
		</div>

		<!-- Create/Edit modal -->
		<CreateEpicModal
			v-if="showCreateModal"
			:editing="editingEpic"
			@close="closeModal"
		/>
	</div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import EpicCard from '../components/EpicCard.vue'
import CreateEpicModal from '../components/CreateEpicModal.vue'

export default {
	name: 'QuestsPage',
	components: { EpicCard, CreateEpicModal },

	data() {
		return {
			tab: 'active',
			showCreateModal: false,
			editingEpic: null,
		}
	},

	computed: {
		...mapState('quest', ['loading']),
		...mapGetters('quest', ['activeEpics', 'completedEpics']),

		currentEpics() {
			return this.tab === 'active' ? this.activeEpics : this.completedEpics
		},
	},

	mounted() {
		this.loadEpics()
		this.loadTaskLists()
	},

	methods: {
		...mapActions('quest', ['loadEpics', 'loadTaskLists', 'deleteEpic']),

		editEpic(epic) {
			this.editingEpic = epic
			this.showCreateModal = true
		},

		closeModal() {
			this.showCreateModal = false
			this.editingEpic = null
		},

		async confirmDelete(epic) {
			if (!confirm(`Delete "${epic.title}"? This cannot be undone.`)) return
			try {
				await this.deleteEpic(epic.id)
			} catch (e) {
				console.error('Delete failed:', e)
			}
		},
	},
}
</script>

<style scoped>
.quests-page {
	padding: 16px 8px;
}

.page-header {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	margin-bottom: 20px;
	padding-bottom: 16px;
	border-bottom: 1px solid var(--color-border);
}

.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

.btn {
	padding: 8px 18px; border: 1px solid var(--color-border); border-radius: var(--radius-medium);
	background: var(--color-main-background); color: var(--color-main-text); cursor: pointer;
	font-size: var(--font-size-small); font-weight: 600; white-space: nowrap;
}
.btn.primary { background: var(--color-primary-element, #0082c9); color: white; border-color: transparent; }
.btn.primary:hover { filter: brightness(1.1); }

/* Tabs */
.tabs {
	display: flex;
	gap: 4px;
	margin-bottom: 20px;
}

.tab {
	padding: 8px 20px;
	border: 1px solid var(--color-border);
	background: var(--color-main-background);
	cursor: pointer;
	font-size: var(--font-size-small);
	font-weight: 500;
	color: var(--color-main-text);
	transition: all var(--transition-fast);
}

.tab:first-child { border-radius: var(--radius-medium) 0 0 var(--radius-medium); }
.tab:last-child { border-radius: 0 var(--radius-medium) var(--radius-medium) 0; }
.tab.active {
	background: var(--color-primary-element, #0082c9);
	color: white;
	border-color: transparent;
}

/* Grid */
.epics-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
	gap: 16px;
}

/* Empty */
.empty-state {
	text-align: center;
	padding: 60px 20px;
	border: 1px dashed var(--color-border);
	border-radius: var(--radius-large);
}
.empty-icon { font-size: 56px; margin-bottom: 16px; opacity: 0.5; }
.empty-title { font-size: var(--font-size-xlarge); font-weight: 600; color: var(--color-main-text); margin-bottom: 8px; }
.empty-desc { font-size: var(--font-size-normal); color: var(--color-text-light); margin-bottom: 20px; line-height: 1.5; max-width: 400px; margin-left: auto; margin-right: auto; }

/* Loading */
.loading-state { text-align: center; padding: 48px; }
.spinner {
	width: 32px; height: 32px; border: 3px solid var(--color-border);
	border-top-color: var(--color-primary-element, #0082c9); border-radius: 50%;
	animation: spin 0.8s linear infinite; margin: 0 auto 12px;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 768px) {
	.page-header { flex-direction: column; gap: 12px; }
	.epics-grid { grid-template-columns: 1fr; }
}
</style>
