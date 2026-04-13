<template>
	<div class="adventure-page">
		<div class="page-header">
			<h1 class="page-title">Adventure</h1>
			<p class="page-subtitle">Complete tasks to advance your journey and encounter battles, treasures, and events.</p>
		</div>

		<div v-if="loading.journey" class="loading-state">
			<div class="spinner" />
			<p>Loading journey...</p>
		</div>

		<template v-else>
			<!-- Journey progress -->
			<section class="journey-progress">
				<div class="age-badge" :style="{ background: journey.theme_colors?.primary || '#8b7355' }">
					{{ journey.current_age_name || 'Stone Age' }}
					<span v-if="journey.prestige_level > 0" class="prestige-stars">
						{{ '\u2B50'.repeat(Math.min(journey.prestige_level, 5)) }}
					</span>
				</div>
				<div class="step-bar">
					<div class="step-label">Next encounter in {{ journey.steps_remaining || 0 }} steps</div>
					<div class="step-track">
						<div
							class="step-fill"
							:style="{
								width: stepProgress + '%',
								background: journey.theme_colors?.primary || '#8b7355'
							}"
						/>
					</div>
				</div>
				<Tooltip text="Power = Level × 10 + equipment bonuses. Higher power means better battle win chance." position="left">
					<div class="power-badge">
						Power: {{ journey.player_power || 0 }}
					</div>
				</Tooltip>
			</section>

			<!-- Stats -->
			<section class="stats-row">
				<Tooltip text="Battles are fought automatically based on your Power rating vs enemy strength.">
					<StatCard :icon="icons.battle" label="Battles Won" :value="journey.battles_won || 0" :subtitle="(journey.battles_lost || 0) + ' lost'" />
				</Tooltip>
				<Tooltip text="Items found from treasure encounters and battle victories. Equip them to boost Power.">
					<StatCard :icon="icons.treasure" label="Treasures" :value="journey.treasures_found || 0" subtitle="found" />
				</Tooltip>
				<Tooltip text="Bosses appear every 20 encounters. Defeat all 9 age bosses to reach Prestige.">
					<StatCard :icon="icons.boss" label="Bosses" :value="journey.bosses_defeated || 0" subtitle="defeated" />
				</Tooltip>
				<Tooltip text="Total encounters triggered. Every 3 task completions triggers a new encounter.">
					<StatCard :icon="icons.encounters" label="Encounters" :value="journey.encounters_completed || 0" subtitle="total" />
				</Tooltip>
			</section>

			<!-- Encounter history -->
			<section class="content-section">
				<div class="section-header">
					<h2 class="section-title">Encounter History</h2>
					<div class="filter-tabs">
						<button
							v-for="f in logFilters"
							:key="f.value"
							class="filter-btn"
							:class="{ active: logFilter === f.value }"
							@click="logFilter = f.value"
						>{{ f.label }}</button>
					</div>
				</div>

				<div v-if="filteredLog.length > 0" class="encounter-list">
					<div
						v-for="entry in filteredLog"
						:key="entry.id"
						class="encounter-entry"
						:class="[entry.encounter_type, entry.outcome]"
					>
						<div class="encounter-icon">
							{{ typeIcon(entry.encounter_type) }}
						</div>
						<div class="encounter-body">
							<span class="encounter-name">{{ entry.encounter_data?.encounter_name || 'Unknown' }}</span>
							<span class="encounter-age">{{ ageLabel(entry.age_key) }}</span>
						</div>
						<span class="encounter-outcome" :class="entry.outcome">
							{{ outcomeLabel(entry.outcome) }}
						</span>
						<div class="encounter-rewards">
							<span v-if="entry.rewards?.xp" class="reward xp">+{{ entry.rewards.xp }} XP</span>
							<span v-if="entry.rewards?.item_name" class="reward item">{{ entry.rewards.item_name }}</span>
							<span v-if="entry.rewards?.health_change" class="reward hp" :class="entry.rewards.health_change > 0 ? 'heal' : 'damage'">
								{{ entry.rewards.health_change > 0 ? '+' : '' }}{{ entry.rewards.health_change }} HP
							</span>
						</div>
						<span class="encounter-time">{{ formatTime(entry.created_at) }}</span>
					</div>
				</div>

				<div v-else class="empty-state">
					<div class="empty-icon">&#x1F5FA;&#xFE0F;</div>
					<div class="empty-title">No encounters yet</div>
					<div class="empty-desc">Complete {{ journey.steps_remaining || 3 }} more tasks to trigger your first encounter!</div>
				</div>
			</section>
		</template>
	</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import StatCard from '../components/StatCard.vue'
import Tooltip from '../components/Tooltip.vue'

export default {
	name: 'AdventurePage',
	components: { StatCard, Tooltip },

	data() {
		return {
			logFilter: 'all',
			icons: {
				battle: '\u2694\uFE0F',
				treasure: '\uD83D\uDCE6',
				boss: '\uD83D\uDC80',
				encounters: '\u2728',
			},
			logFilters: [
				{ value: 'all', label: 'All' },
				{ value: 'battle', label: 'Battles' },
				{ value: 'treasure', label: 'Treasure' },
				{ value: 'event', label: 'Events' },
				{ value: 'boss', label: 'Bosses' },
			],
		}
	},

	computed: {
		...mapState('quest', ['journey', 'loading']),

		stepProgress() {
			const taken = (this.journey.steps_per_encounter || 3) - (this.journey.steps_remaining || 0)
			const total = this.journey.steps_per_encounter || 3
			return total > 0 ? (taken / total) * 100 : 0
		},

		filteredLog() {
			if (this.logFilter === 'all') return this.journey.log || []
			return (this.journey.log || []).filter(e => e.encounter_type === this.logFilter)
		},
	},

	mounted() {
		this.loadJourney()
		this.loadJourneyLog()
	},

	methods: {
		...mapActions('quest', ['loadJourney', 'loadJourneyLog']),

		typeIcon(type) {
			const map = { battle: '\u2694\uFE0F', mini_boss: '\uD83D\uDC79', boss: '\uD83D\uDC80', treasure: '\uD83D\uDCE6', event: '\uD83D\uDCDC' }
			return map[type] || '\u2728'
		},

		ageLabel(key) {
			const map = {
				stone: 'Stone Age', bronze: 'Bronze Age', iron: 'Iron Age',
				medieval: 'Medieval', renaissance: 'Renaissance', industrial: 'Industrial',
				modern: 'Modern', digital: 'Digital', space: 'Space Age',
			}
			return map[key] || key
		},

		outcomeLabel(outcome) {
			const map = { win: 'Victory', lose: 'Defeat', found: 'Found', resolved: 'Resolved' }
			return map[outcome] || outcome
		},

		formatTime(dateStr) {
			if (!dateStr) return ''
			const d = new Date(dateStr)
			const now = new Date()
			const diff = now - d
			if (diff < 60000) return 'Just now'
			if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago'
			if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago'
			return d.toLocaleDateString()
		},
	},
}
</script>

<style scoped>
.adventure-page { padding: 16px 8px; }

.page-header {
	margin-bottom: 24px;
	padding-bottom: 16px;
	border-bottom: 1px solid var(--color-border);
}
.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

/* Journey progress */
.journey-progress {
	display: flex;
	align-items: center;
	gap: 16px;
	padding: 16px 20px;
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	margin-bottom: 20px;
}

.age-badge {
	padding: 8px 16px;
	border-radius: 20px;
	color: white;
	font-weight: 700;
	font-size: var(--font-size-small);
	white-space: nowrap;
	flex-shrink: 0;
}

.prestige-stars { margin-left: 4px; }

.step-bar { flex: 1; }
.step-label { font-size: 12px; color: var(--color-text-light); margin-bottom: 4px; }
.step-track { height: 8px; background: var(--color-background-dark); border-radius: 4px; overflow: hidden; }
.step-fill { height: 100%; border-radius: 4px; transition: width var(--transition-slow); }

.power-badge {
	padding: 6px 14px;
	background: var(--color-background-hover);
	border-radius: var(--radius-medium);
	font-size: var(--font-size-small);
	font-weight: 700;
	color: var(--color-main-text);
	white-space: nowrap;
}

/* Stats */
.stats-row {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 16px;
	margin-bottom: 24px;
}

/* Encounter list */
.content-section { margin-bottom: 24px; }
.section-header {
	display: flex; justify-content: space-between; align-items: center;
	margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border);
}
.section-title { font-size: var(--font-size-xlarge); font-weight: 600; margin: 0; }

.filter-tabs { display: flex; gap: 4px; }
.filter-btn {
	padding: 4px 12px; border: 1px solid var(--color-border); border-radius: 16px;
	background: var(--color-main-background); color: var(--color-main-text);
	cursor: pointer; font-size: 12px; transition: all var(--transition-fast);
}
.filter-btn.active {
	background: var(--color-primary-element, #0082c9); color: white; border-color: transparent;
}

.encounter-list { display: flex; flex-direction: column; gap: 4px; }

.encounter-entry {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 10px 16px;
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	transition: background var(--transition-fast);
}
.encounter-entry:hover { background: var(--color-background-hover); }

.encounter-entry.mini_boss { border-left: 3px solid #e91e63; }
.encounter-entry.boss { border-left: 3px solid #ff9800; }
.encounter-entry.battle.win { border-left: 3px solid var(--color-success, #46ba61); }
.encounter-entry.battle.lose { border-left: 3px solid var(--color-error, #e9322d); }
.encounter-entry.treasure { border-left: 3px solid #2196f3; }
.encounter-entry.event { border-left: 3px solid #9c27b0; }

.encounter-icon { font-size: 20px; flex-shrink: 0; width: 28px; text-align: center; }

.encounter-body { flex: 1; min-width: 0; }
.encounter-name {
	display: block; font-size: var(--font-size-small); font-weight: 600; color: var(--color-main-text);
	white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.encounter-age { display: block; font-size: 11px; color: var(--color-text-light); }

.encounter-outcome {
	font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 8px;
	text-transform: uppercase; flex-shrink: 0;
}
.encounter-outcome.win { background: rgba(70,186,97,0.15); color: var(--color-success, #46ba61); }
.encounter-outcome.lose { background: rgba(233,50,45,0.15); color: var(--color-error, #e9322d); }
.encounter-outcome.found { background: rgba(33,150,243,0.15); color: #2196f3; }
.encounter-outcome.resolved { background: rgba(156,39,176,0.15); color: #9c27b0; }

.encounter-rewards { display: flex; gap: 6px; flex-shrink: 0; }
.reward {
	font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 6px;
}
.reward.xp { color: var(--color-primary-element, #0082c9); }
.reward.item { color: #2196f3; }
.reward.hp.heal { color: var(--color-success, #46ba61); }
.reward.hp.damage { color: var(--color-error, #e9322d); }

.encounter-time { font-size: 11px; color: var(--color-text-light); flex-shrink: 0; white-space: nowrap; }

/* Empty / loading */
.empty-state { text-align: center; padding: 48px 20px; border: 1px dashed var(--color-border); border-radius: var(--radius-large); }
.empty-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
.empty-title { font-size: var(--font-size-large); font-weight: 600; color: var(--color-main-text); margin-bottom: 6px; }
.empty-desc { color: var(--color-text-light); }

.loading-state { text-align: center; padding: 48px; }
.spinner {
	width: 32px; height: 32px; border: 3px solid var(--color-border);
	border-top-color: var(--color-primary-element, #0082c9); border-radius: 50%;
	animation: spin 0.8s linear infinite; margin: 0 auto 12px;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 768px) {
	.stats-row { grid-template-columns: repeat(2, 1fr); }
	.journey-progress { flex-direction: column; align-items: stretch; }
	.encounter-rewards { display: none; }
}
</style>
