<template>
	<div class="achievements-page">
		<div class="page-header">
			<h1 class="page-title">Achievements</h1>
			<p class="page-subtitle">Track your progress and unlock rewards for completing milestones.</p>
		</div>

		<!-- Stats -->
		<section class="stats-grid">
			<StatCard :icon="icons.progress" label="Progress" :value="stats.achievements.percentage + '%'" :subtitle="stats.achievements.unlocked + ' of ' + stats.achievements.total" />
			<StatCard :icon="icons.latest" label="Latest" :value="latestAchievement ? latestAchievement.name : 'None'" :subtitle="latestAchievement ? formatDate(latestAchievement.unlocked_at) : '-'" />
			<StatCard :icon="icons.rare" label="Rare+" :value="rareCount" subtitle="rare achievements" />
			<StatCard :icon="icons.points" label="Points" :value="totalPoints" subtitle="total earned" />
		</section>

		<!-- Filters -->
		<section class="content-section">
			<div class="section-header">
				<h2 class="section-title">Your Achievements</h2>
				<div class="view-toggle">
					<button class="toggle-btn" :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'">Grid</button>
					<button class="toggle-btn" :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'">List</button>
				</div>
			</div>

			<div class="filter-bar">
				<div class="search-box">
					<input v-model="search" type="text" class="search-input" placeholder="Search achievements...">
				</div>
				<select v-model="categoryFilter" class="filter-select">
					<option value="all">All Categories</option>
					<option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
				</select>
				<select v-model="statusFilter" class="filter-select">
					<option value="all">All Status</option>
					<option value="unlocked">Unlocked</option>
					<option value="locked">Locked</option>
				</select>
				<select v-model="rarityFilter" class="filter-select">
					<option value="all">All Rarities</option>
					<option value="common">Common</option>
					<option value="rare">Rare</option>
					<option value="epic">Epic</option>
					<option value="legendary">Legendary</option>
				</select>
			</div>
		</section>

		<!-- Loading -->
		<div v-if="loading.achievements" class="loading-state">
			<div class="spinner" />
			<p>Loading achievements...</p>
		</div>

		<!-- Grid view -->
		<div v-else-if="viewMode === 'grid'" class="achievements-grid">
			<div
				v-for="a in filtered"
				:key="a.key"
				class="achievement-card"
				:class="[a.unlocked ? 'unlocked' : 'locked', a.rarity]"
				@click="selectedAchievement = a"
			>
				<div class="card-header">
					<span class="rarity-badge" :class="a.rarity">{{ a.rarity }}</span>
				</div>
				<div class="card-icon"><img :src="iconUrl(a.icon)" :alt="a.name" class="achievement-img"></div>
				<div class="card-name">{{ a.name }}</div>
				<div class="card-desc">{{ a.description }}</div>
				<div class="card-progress">
					<div class="progress-bar">
						<div class="progress-fill" :style="{ width: getProgress(a) + '%' }" />
					</div>
					<span class="progress-text">{{ getProgress(a) }}%</span>
				</div>
				<div class="card-status" :class="a.unlocked ? 'unlocked' : 'locked'">
					{{ a.unlocked ? 'Unlocked' : 'Locked' }}
				</div>
			</div>
		</div>

		<!-- List view -->
		<div v-else class="achievements-list">
			<div
				v-for="a in filtered"
				:key="a.key"
				class="achievement-list-item"
				:class="[a.unlocked ? 'unlocked' : 'locked']"
				@click="selectedAchievement = a"
			>
				<span class="list-icon"><img :src="iconUrl(a.icon)" :alt="a.name" class="achievement-img-sm"></span>
				<div class="list-info">
					<span class="list-name">{{ a.name }}</span>
					<span class="list-desc">{{ a.description }}</span>
					<div class="list-meta">
						<span class="rarity-badge" :class="a.rarity">{{ a.rarity }}</span>
						<span v-if="a.category" class="category-tag">{{ a.category }}</span>
					</div>
				</div>
				<div class="list-progress">
					<span class="progress-text">{{ getProgress(a) }}%</span>
				</div>
			</div>
		</div>

		<!-- Empty state -->
		<div v-if="!loading.achievements && filtered.length === 0" class="empty-state">
			<div class="empty-state-icon">🏆</div>
			<div class="empty-state-title">No achievements found</div>
			<div class="empty-state-text">Try adjusting your filters.</div>
		</div>

		<!-- Detail modal -->
		<div v-if="selectedAchievement" class="modal-overlay" @click.self="selectedAchievement = null">
			<div class="modal">
				<div class="modal-header">
					<h3>Achievement Details</h3>
					<button class="modal-close" @click="selectedAchievement = null">&times;</button>
				</div>
				<div class="modal-body">
					<div class="detail-icon"><img :src="iconUrl(selectedAchievement.icon)" :alt="selectedAchievement.name" class="achievement-img-lg"></div>
					<span class="rarity-badge large" :class="selectedAchievement.rarity">{{ selectedAchievement.rarity }}</span>
					<h2 class="detail-name">{{ selectedAchievement.name }}</h2>
					<p class="detail-desc">{{ selectedAchievement.description }}</p>
					<div class="detail-meta">
						<div class="meta-row">
							<span class="meta-label">Category</span>
							<span class="meta-value">{{ selectedAchievement.category }}</span>
						</div>
						<div v-if="selectedAchievement.unlocked" class="meta-row">
							<span class="meta-label">Unlocked</span>
							<span class="meta-value">{{ formatDate(selectedAchievement.unlocked_at) }}</span>
						</div>
					</div>
					<div class="detail-progress">
						<div class="progress-bar large">
							<div class="progress-fill" :style="{ width: getProgress(selectedAchievement) + '%' }" />
						</div>
						<span class="progress-text">{{ getProgress(selectedAchievement) }}%</span>
					</div>
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

		formatDate(dateString) {
			if (!dateString) return '-'
			return new Date(dateString).toLocaleDateString()
		},
	},
}
</script>

<style scoped>
.achievements-page { padding: 8px 0; }
.page-header { margin-bottom: 24px; }
.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

.stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
	gap: var(--grid-gap);
	margin-bottom: 24px;
}

.content-section { margin-bottom: 24px; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-title { font-size: var(--font-size-xlarge); font-weight: 600; margin: 0; }

.view-toggle { display: flex; gap: 2px; }
.toggle-btn {
	padding: 6px 14px; border: 1px solid var(--color-border); background: var(--color-main-background);
	cursor: pointer; font-size: var(--font-size-small); color: var(--color-main-text);
}
.toggle-btn:first-child { border-radius: var(--radius-medium) 0 0 var(--radius-medium); }
.toggle-btn:last-child { border-radius: 0 var(--radius-medium) var(--radius-medium) 0; }
.toggle-btn.active { background: var(--color-primary); color: white; border-color: var(--color-primary); }

.filter-bar { display: flex; gap: 12px; flex-wrap: wrap; }
.search-box { flex: 1; min-width: 200px; }
.search-input { width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-medium); background: var(--color-main-background); color: var(--color-main-text); }
.search-input:focus { outline: none; border-color: var(--color-primary); }
.filter-select { padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-medium); background: var(--color-main-background); color: var(--color-main-text); }

/* Achievement grid */
.achievements-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
	gap: 16px;
}

.achievement-card {
	background: var(--color-main-background);
	border-radius: var(--radius-large);
	padding: 16px;
	box-shadow: var(--shadow-sm);
	cursor: pointer;
	transition: all var(--transition-normal);
	text-align: center;
	border: 2px solid transparent;
}
.achievement-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.achievement-card.locked { opacity: 0.65; }
.achievement-card.unlocked { border-color: var(--color-success); }
.achievement-card.common { border-color: #9e9e9e; }
.achievement-card.rare { border-color: #2196f3; }
.achievement-card.epic { border-color: #9c27b0; }
.achievement-card.legendary { border-color: #ff9800; animation: glow 3s ease-in-out infinite alternate; }
@keyframes glow { from { box-shadow: 0 0 10px rgba(255,152,0,0.2); } to { box-shadow: 0 0 20px rgba(255,152,0,0.4); } }

.card-header { text-align: right; margin-bottom: 8px; }
.card-icon { margin-bottom: 8px; }
.achievement-img { width: 64px; height: 64px; object-fit: contain; }
.achievement-img-sm { width: 36px; height: 36px; object-fit: contain; }
.achievement-img-lg { width: 96px; height: 96px; object-fit: contain; }
.card-name { font-size: var(--font-size-normal); font-weight: 600; margin-bottom: 4px; }
.card-desc { font-size: var(--font-size-small); color: var(--color-text-light); margin-bottom: 12px; line-height: 1.4; }

.rarity-badge {
	display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px;
	font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: white;
}
.rarity-badge.common { background: #9e9e9e; }
.rarity-badge.rare { background: #2196f3; }
.rarity-badge.epic { background: #9c27b0; }
.rarity-badge.legendary { background: linear-gradient(45deg, #ff9800, #ffb74d); }
.rarity-badge.large { padding: 4px 12px; font-size: 12px; }

.progress-bar { height: 6px; background: var(--color-background-dark); border-radius: 3px; overflow: hidden; }
.progress-bar.large { height: 10px; border-radius: 5px; }
.progress-fill { height: 100%; background: linear-gradient(90deg, var(--color-primary), var(--color-success)); transition: width var(--transition-slow); }
.card-progress { margin-bottom: 8px; }
.progress-text { font-size: var(--font-size-small); font-weight: 600; color: var(--color-primary); }

.card-status {
	font-size: var(--font-size-small); font-weight: 600; padding: 4px 12px; border-radius: 12px; display: inline-block;
}
.card-status.unlocked { background: var(--color-success); color: white; }
.card-status.locked { background: var(--color-background-dark); color: var(--color-text-light); }

/* List view */
.achievements-list { display: flex; flex-direction: column; gap: 8px; }
.achievement-list-item {
	display: flex; align-items: center; gap: 16px; padding: 16px; background: var(--color-main-background);
	border-radius: var(--radius-large); box-shadow: var(--shadow-sm); cursor: pointer; transition: all var(--transition-fast);
}
.achievement-list-item:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); }
.achievement-list-item.locked { opacity: 0.65; }
.list-icon { font-size: 36px; flex-shrink: 0; }
.list-info { flex: 1; }
.list-name { display: block; font-weight: 600; font-size: var(--font-size-normal); margin-bottom: 2px; }
.list-desc { display: block; font-size: var(--font-size-small); color: var(--color-text-light); margin-bottom: 6px; }
.list-meta { display: flex; gap: 8px; }
.category-tag { font-size: 11px; padding: 2px 8px; border-radius: 10px; background: var(--color-background-dark); color: var(--color-text-light); }
.list-progress { text-align: right; flex-shrink: 0; }

/* Modal */
.modal-overlay {
	position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex;
	align-items: center; justify-content: center; z-index: var(--z-modal-backdrop);
}
.modal {
	background: var(--color-main-background); border-radius: var(--radius-large); width: 90%;
	max-width: 500px; max-height: 80vh; overflow-y: auto; z-index: var(--z-modal);
}
.modal-header {
	display: flex; justify-content: space-between; align-items: center; padding: 16px 20px;
	border-bottom: 1px solid var(--color-border);
}
.modal-header h3 { margin: 0; }
.modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--color-text-light); }
.modal-body { padding: 20px; text-align: center; }
.detail-icon { margin-bottom: 12px; }
.detail-name { font-size: var(--font-size-xxlarge); font-weight: 600; margin: 12px 0 8px; }
.detail-desc { font-size: var(--font-size-normal); color: var(--color-text-light); margin-bottom: 20px; }
.detail-meta { text-align: left; margin-bottom: 20px; }
.meta-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border); }
.meta-row:last-child { border-bottom: none; }
.meta-label { color: var(--color-text-light); }
.meta-value { font-weight: 600; }
.detail-progress { margin-top: 16px; }

/* Loading & empty */
.loading-state { text-align: center; padding: 40px; color: var(--color-text-lighter); }
.spinner { width: 32px; height: 32px; border: 3px solid var(--color-border); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 40px 20px; }
.empty-state-icon { font-size: 48px; margin-bottom: 12px; }
.empty-state-title { font-size: var(--font-size-large); font-weight: 600; margin-bottom: 4px; }
.empty-state-text { color: var(--color-text-light); }

@media (max-width: 768px) {
	.achievements-grid { grid-template-columns: 1fr; }
	.filter-bar { flex-direction: column; }
}
</style>
