<template>
	<div class="achievements-page">
		<div class="page-header">
			<h1 class="page-title">Achievements</h1>
			<p class="page-subtitle">Track your progress and unlock rewards for completing milestones.</p>
		</div>

		<!-- Stats -->
		<section class="stats-row">
			<StatCard :icon="icons.progress" label="Progress" :value="stats.achievements.percentage + '%'" :subtitle="stats.achievements.unlocked + ' of ' + stats.achievements.total" />
			<StatCard :icon="icons.latest" label="Latest" :value="latestAchievement ? latestAchievement.name : 'None'" :subtitle="latestAchievement ? formatDate(latestAchievement.unlocked_at) : '-'" />
			<StatCard :icon="icons.rare" label="Rare+" :value="rareCount" subtitle="rare achievements" />
			<StatCard :icon="icons.points" label="Points" :value="totalPoints" subtitle="total earned" />
		</section>

		<!-- Category tabs + filters -->
		<section class="toolbar">
			<div class="category-tabs">
				<button
					class="cat-tab"
					:class="{ active: categoryFilter === 'all' }"
					@click="categoryFilter = 'all'"
				>
					All ({{ achievements.length }})
				</button>
				<button
					v-for="cat in categories"
					:key="cat"
					class="cat-tab"
					:class="{ active: categoryFilter === cat }"
					@click="categoryFilter = cat"
				>
					{{ cat }} ({{ countByCategory(cat) }})
				</button>
			</div>
			<div class="toolbar-right">
				<input v-model="search" type="text" class="search-input" placeholder="Search...">
				<select v-model="statusFilter" class="filter-select">
					<option value="all">All</option>
					<option value="unlocked">Unlocked</option>
					<option value="locked">Locked</option>
				</select>
				<select v-model="rarityFilter" class="filter-select">
					<option value="all">Rarity</option>
					<option value="common">Common</option>
					<option value="rare">Rare</option>
					<option value="epic">Epic</option>
					<option value="legendary">Legendary</option>
				</select>
				<div class="view-toggle">
					<button class="vt-btn" :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'">▦</button>
					<button class="vt-btn" :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'">☰</button>
				</div>
			</div>
		</section>

		<!-- Loading -->
		<div v-if="loading.achievements" class="loading-state">
			<div class="spinner" />
			<p>Loading achievements...</p>
		</div>

		<!-- Grid view -->
		<div v-else-if="viewMode === 'grid' && filtered.length > 0" class="grid">
			<div
				v-for="a in filtered"
				:key="a.key"
				class="card"
				:class="[a.unlocked ? 'unlocked' : 'locked', a.rarity]"
				@click="selectedAchievement = a"
			>
				<span class="card-rarity" :class="a.rarity">{{ a.rarity }}</span>
				<img :src="iconUrl(a.icon)" :alt="a.name" class="card-img">
				<div class="card-name">{{ a.name }}</div>
				<div class="card-desc">{{ a.description }}</div>
				<div class="card-bar">
					<div class="card-bar-fill" :class="a.rarity" :style="{ width: getProgress(a) + '%' }" />
				</div>
				<div class="card-footer">
					<span class="card-pct">{{ getProgress(a) }}%</span>
					<span class="card-status-badge" :class="a.unlocked ? 'done' : 'todo'">
						{{ a.unlocked ? '✓ Unlocked' : 'Locked' }}
					</span>
				</div>
			</div>
		</div>

		<!-- List view -->
		<div v-else-if="viewMode === 'list' && filtered.length > 0" class="list">
			<div
				v-for="a in filtered"
				:key="a.key"
				class="list-row"
				:class="[a.unlocked ? 'unlocked' : 'locked']"
				@click="selectedAchievement = a"
			>
				<img :src="iconUrl(a.icon)" :alt="a.name" class="list-img">
				<div class="list-body">
					<span class="list-name">{{ a.name }}</span>
					<span class="list-desc">{{ a.description }}</span>
				</div>
				<span class="rarity-pill" :class="a.rarity">{{ a.rarity }}</span>
				<div class="list-bar-wrap">
					<div class="list-bar">
						<div class="list-bar-fill" :class="a.rarity" :style="{ width: getProgress(a) + '%' }" />
					</div>
					<span class="list-pct">{{ getProgress(a) }}%</span>
				</div>
				<span v-if="a.unlocked" class="list-check">✓</span>
			</div>
		</div>

		<!-- Empty -->
		<div v-else-if="!loading.achievements" class="empty-state">
			<div class="empty-icon">🏆</div>
			<div class="empty-title">No achievements found</div>
			<div class="empty-text">Try adjusting your filters.</div>
		</div>

		<!-- Detail modal -->
		<div v-if="selectedAchievement" class="modal-overlay" @click.self="selectedAchievement = null">
			<div class="modal" :class="selectedAchievement.rarity">
				<button class="modal-x" @click="selectedAchievement = null">×</button>
				<div class="modal-top">
					<img :src="iconUrl(selectedAchievement.icon)" :alt="selectedAchievement.name" class="modal-img">
					<span class="rarity-pill large" :class="selectedAchievement.rarity">{{ selectedAchievement.rarity }}</span>
				</div>
				<h2 class="modal-name">{{ selectedAchievement.name }}</h2>
				<p class="modal-desc">{{ selectedAchievement.description }}</p>
				<div class="modal-meta">
					<div class="meta-item">
						<span class="meta-k">Category</span>
						<span class="meta-v">{{ selectedAchievement.category }}</span>
					</div>
					<div v-if="selectedAchievement.points" class="meta-item">
						<span class="meta-k">Points</span>
						<span class="meta-v">{{ selectedAchievement.points }}</span>
					</div>
					<div v-if="selectedAchievement.unlocked" class="meta-item">
						<span class="meta-k">Unlocked</span>
						<span class="meta-v">{{ formatDate(selectedAchievement.unlocked_at) }}</span>
					</div>
				</div>
				<div class="modal-progress">
					<div class="modal-bar">
						<div class="modal-bar-fill" :class="selectedAchievement.rarity" :style="{ width: getProgress(selectedAchievement) + '%' }" />
					</div>
					<span class="modal-pct">{{ getProgress(selectedAchievement) }}%</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import { generateFilePath } from '@nextcloud/router'
import StatCard from '../components/StatCard.vue'

export default {
	name: 'AchievementsPage',
	components: { StatCard },

	data() {
		return {
			search: '',
			categoryFilter: 'all',
			statusFilter: 'all',
			rarityFilter: 'all',
			viewMode: 'grid',
			selectedAchievement: null,
			icons: {
				progress: '\uD83D\uDCCA',
				latest: '\uD83C\uDD95',
				rare: '\uD83D\uDC8E',
				points: '\u2B50',
			},
		}
	},

	computed: {
		...mapState('quest', ['stats', 'achievements', 'loading']),
		...mapGetters('quest', ['unlockedAchievements']),

		categories() {
			const cats = new Set(this.achievements.map(a => a.category).filter(Boolean))
			return [...cats].sort()
		},

		latestAchievement() {
			return this.unlockedAchievements
				.slice()
				.sort((a, b) => new Date(b.unlocked_at) - new Date(a.unlocked_at))[0] || null
		},

		rareCount() {
			return this.unlockedAchievements.filter(a =>
				['rare', 'epic', 'legendary'].includes(a.rarity),
			).length
		},

		totalPoints() {
			return this.unlockedAchievements.reduce((sum, a) => sum + (a.points || 0), 0)
		},

		filtered() {
			return this.achievements.filter(a => {
				if (this.search) {
					const q = this.search.toLowerCase()
					if (!(a.name || '').toLowerCase().includes(q)
						&& !(a.description || '').toLowerCase().includes(q)) {
						return false
					}
				}
				if (this.categoryFilter !== 'all' && a.category !== this.categoryFilter) return false
				if (this.statusFilter === 'unlocked' && !a.unlocked) return false
				if (this.statusFilter === 'locked' && a.unlocked) return false
				if (this.rarityFilter !== 'all' && a.rarity !== this.rarityFilter) return false
				return true
			})
		},
	},

	mounted() {
		this.loadAchievements()
	},

	methods: {
		...mapActions('quest', ['loadAchievements']),

		iconUrl(icon) {
			const file = icon || 'default.svg'
			return generateFilePath('quest', '', 'img/achievements/' + file)
		},

		getProgress(a) {
			if (a.unlocked) return 100
			return a.progress_percentage || a.progress || 0
		},

		countByCategory(cat) {
			return this.achievements.filter(a => a.category === cat).length
		},

		formatDate(dateString) {
			if (!dateString) return '-'
			return new Date(dateString).toLocaleDateString()
		},
	},
}
</script>

<style scoped>
.achievements-page {
	padding: 16px 8px;
}

.page-header {
	margin-bottom: 24px;
	padding-bottom: 16px;
	border-bottom: 1px solid var(--color-border);
}

.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

/* ── Stats row ── */
.stats-row {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 16px;
	margin-bottom: 24px;
}

/* ── Toolbar ── */
.toolbar {
	display: flex;
	flex-direction: column;
	gap: 12px;
	margin-bottom: 20px;
}

.category-tabs {
	display: flex;
	gap: 4px;
	overflow-x: auto;
	padding-bottom: 4px;
}

.cat-tab {
	padding: 6px 14px;
	border: 1px solid var(--color-border);
	border-radius: 20px;
	background: var(--color-main-background);
	color: var(--color-main-text);
	cursor: pointer;
	font-size: var(--font-size-small);
	white-space: nowrap;
	transition: all var(--transition-fast);
}

.cat-tab:hover { background: var(--color-background-hover); }
.cat-tab.active {
	background: var(--color-primary-element, #0082c9);
	color: white;
	border-color: transparent;
}

.toolbar-right {
	display: flex;
	gap: 8px;
	align-items: center;
	flex-wrap: wrap;
}

.search-input {
	flex: 1;
	min-width: 140px;
	padding: 6px 12px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: var(--font-size-small);
}

.search-input:focus { outline: none; border-color: var(--color-primary-element, #0082c9); }

.filter-select {
	padding: 6px 10px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: var(--font-size-small);
}

.view-toggle { display: flex; gap: 2px; }
.vt-btn {
	padding: 6px 10px;
	border: 1px solid var(--color-border);
	background: var(--color-main-background);
	cursor: pointer;
	font-size: 14px;
	color: var(--color-main-text);
}
.vt-btn:first-child { border-radius: var(--radius-medium) 0 0 var(--radius-medium); }
.vt-btn:last-child { border-radius: 0 var(--radius-medium) var(--radius-medium) 0; }
.vt-btn.active { background: var(--color-primary-element, #0082c9); color: white; border-color: transparent; }

/* ── Grid view ── */
.grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
	gap: 14px;
}

.card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 14px;
	cursor: pointer;
	transition: all var(--transition-normal);
	text-align: center;
	display: flex;
	flex-direction: column;
	align-items: center;
	position: relative;
}

.card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
.card.locked { opacity: 0.5; }
.card.unlocked { border-color: var(--color-success, #46ba61); }
.card.rare { border-color: #2196f3; }
.card.epic { border-color: #9c27b0; }
.card.legendary { border-color: #ff9800; animation: glow 3s ease-in-out infinite alternate; }
@keyframes glow { from { box-shadow: 0 0 8px rgba(255,152,0,0.15); } to { box-shadow: 0 0 16px rgba(255,152,0,0.35); } }

.card-rarity {
	position: absolute;
	top: 8px;
	right: 8px;
	font-size: 9px;
	padding: 2px 6px;
	border-radius: 8px;
	font-weight: 700;
	text-transform: uppercase;
	color: white;
	letter-spacing: 0.3px;
}
.card-rarity.common { background: #9e9e9e; }
.card-rarity.rare { background: #2196f3; }
.card-rarity.epic { background: #9c27b0; }
.card-rarity.legendary { background: linear-gradient(45deg, #ff9800, #ffb74d); }

.card-img { width: 52px; height: 52px; object-fit: contain; margin: 8px 0; }
.card-name { font-size: var(--font-size-small); font-weight: 600; color: var(--color-main-text); margin-bottom: 2px; }
.card-desc { font-size: 11px; color: var(--color-text-light); line-height: 1.3; margin-bottom: 10px; flex: 1; }

.card-bar {
	width: 100%;
	height: 4px;
	background: var(--color-background-dark);
	border-radius: 2px;
	overflow: hidden;
	margin-bottom: 6px;
}
.card-bar-fill { height: 100%; border-radius: 2px; transition: width var(--transition-slow); }
.card-bar-fill.common { background: #9e9e9e; }
.card-bar-fill.rare { background: #2196f3; }
.card-bar-fill.epic { background: #9c27b0; }
.card-bar-fill.legendary { background: #ff9800; }

.card-footer { display: flex; justify-content: space-between; align-items: center; width: 100%; }
.card-pct { font-size: 11px; font-weight: 600; color: var(--color-text-light); }

.card-status-badge {
	font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px;
}
.card-status-badge.done { background: var(--color-success, #46ba61); color: white; }
.card-status-badge.todo { background: var(--color-background-dark); color: var(--color-text-light); }

/* ── List view ── */
.list {
	display: flex;
	flex-direction: column;
	gap: 4px;
}

.list-row {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 10px 16px;
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	cursor: pointer;
	transition: all var(--transition-fast);
}

.list-row:hover { background: var(--color-background-hover); }
.list-row.locked { opacity: 0.5; }

.list-img { width: 32px; height: 32px; object-fit: contain; flex-shrink: 0; }

.list-body { flex: 1; min-width: 0; }
.list-name { display: block; font-size: var(--font-size-small); font-weight: 600; color: var(--color-main-text); }
.list-desc { display: block; font-size: 12px; color: var(--color-text-light); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.rarity-pill {
	font-size: 9px; padding: 2px 8px; border-radius: 10px; font-weight: 700;
	text-transform: uppercase; color: white; flex-shrink: 0;
}
.rarity-pill.common { background: #9e9e9e; }
.rarity-pill.rare { background: #2196f3; }
.rarity-pill.epic { background: #9c27b0; }
.rarity-pill.legendary { background: #ff9800; }
.rarity-pill.large { padding: 4px 14px; font-size: 11px; }

.list-bar-wrap { width: 100px; flex-shrink: 0; display: flex; align-items: center; gap: 6px; }
.list-bar { flex: 1; height: 4px; background: var(--color-background-dark); border-radius: 2px; overflow: hidden; }
.list-bar-fill { height: 100%; border-radius: 2px; }
.list-bar-fill.common { background: #9e9e9e; }
.list-bar-fill.rare { background: #2196f3; }
.list-bar-fill.epic { background: #9c27b0; }
.list-bar-fill.legendary { background: #ff9800; }
.list-pct { font-size: 11px; font-weight: 600; color: var(--color-text-light); width: 30px; text-align: right; }

.list-check { color: var(--color-success, #46ba61); font-weight: 700; font-size: 16px; flex-shrink: 0; }

/* ── Modal ── */
.modal-overlay {
	position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex;
	align-items: center; justify-content: center; z-index: var(--z-modal-backdrop);
}

.modal {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	width: 90%; max-width: 420px;
	padding: 28px;
	text-align: center;
	position: relative;
	z-index: var(--z-modal);
	border-top: 4px solid #9e9e9e;
}
.modal.rare { border-top-color: #2196f3; }
.modal.epic { border-top-color: #9c27b0; }
.modal.legendary { border-top-color: #ff9800; }

.modal-x {
	position: absolute; top: 12px; right: 16px; background: none; border: none;
	font-size: 22px; cursor: pointer; color: var(--color-text-light);
}
.modal-x:hover { color: var(--color-main-text); }

.modal-top { margin-bottom: 12px; }
.modal-img { width: 80px; height: 80px; object-fit: contain; margin-bottom: 8px; }

.modal-name { font-size: var(--font-size-xxlarge); font-weight: 700; color: var(--color-main-text); margin: 8px 0 6px; }
.modal-desc { font-size: var(--font-size-normal); color: var(--color-text-light); line-height: 1.5; margin-bottom: 20px; }

.modal-meta { text-align: left; margin-bottom: 20px; }
.meta-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border); }
.meta-item:last-child { border-bottom: none; }
.meta-k { color: var(--color-text-light); }
.meta-v { font-weight: 600; color: var(--color-main-text); }

.modal-progress { margin-top: 8px; }
.modal-bar { height: 8px; background: var(--color-background-dark); border-radius: 4px; overflow: hidden; margin-bottom: 6px; }
.modal-bar-fill { height: 100%; border-radius: 4px; }
.modal-bar-fill.common { background: #9e9e9e; }
.modal-bar-fill.rare { background: #2196f3; }
.modal-bar-fill.epic { background: #9c27b0; }
.modal-bar-fill.legendary { background: #ff9800; }
.modal-pct { font-size: var(--font-size-small); font-weight: 700; color: var(--color-main-text); }

/* ── Empty / loading ── */
.empty-state { text-align: center; padding: 48px 20px; }
.empty-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
.empty-title { font-size: var(--font-size-large); font-weight: 600; color: var(--color-main-text); margin-bottom: 4px; }
.empty-text { color: var(--color-text-light); }

.loading-state { text-align: center; padding: 48px; }
.spinner { width: 32px; height: 32px; border: 3px solid var(--color-border); border-top-color: var(--color-primary-element, #0082c9); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Responsive ── */
@media (max-width: 900px) {
	.stats-row { grid-template-columns: repeat(2, 1fr); }
	.grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
	.list-bar-wrap { display: none; }
}

@media (max-width: 600px) {
	.toolbar-right { flex-direction: column; }
	.grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
