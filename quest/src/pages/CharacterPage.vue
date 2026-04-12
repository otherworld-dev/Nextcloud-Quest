<template>
	<div class="character-page">
		<div class="page-header">
			<h1 class="page-title">Character</h1>
			<p class="page-subtitle">Track your progression through the ages and equip items.</p>
		</div>

		<!-- Loading -->
		<div v-if="loading.character" class="loading-state">
			<div class="spinner" />
			<p>Loading character...</p>
		</div>

		<template v-else>
			<!-- Top row: Character card + Age progression -->
			<div class="character-top">
				<!-- Character card -->
				<div class="character-card">
					<div class="character-visual">
						<div class="avatar-ring" :style="{ '--age-color': ageColor }">
							<div class="avatar-inner">
								<span class="avatar-initials">{{ initials }}</span>
							</div>
						</div>
						<div class="character-badge">Lv. {{ stats.level.level }}</div>
					</div>
					<div class="character-details">
						<h2 class="character-name">{{ user.displayName || 'Adventurer' }}</h2>
						<div class="character-rank">{{ stats.level.rank_title }}</div>
						<div class="character-age-label">
							<span class="age-icon">{{ currentAge.icon || '\uD83E\uDEA8' }}</span>
							<span>{{ currentAge.name || 'Stone Age' }}</span>
						</div>
						<p class="age-description">{{ currentAge.description || '' }}</p>
					</div>
				</div>

				<!-- Age progression -->
				<div class="age-card">
					<h3 class="card-title">Age Progression</h3>
					<div class="age-progress-info">
						<div class="age-current">
							<span class="age-dot" :style="{ background: ageColor }" />
							<span>{{ currentAge.name || 'Stone Age' }}</span>
						</div>
						<div class="age-arrow">→</div>
						<div class="age-next">
							<span class="age-dot next" />
							<span>{{ nextAge.name || 'Bronze Age' }}</span>
						</div>
					</div>
					<div class="age-progress-bar">
						<div class="age-progress-fill" :style="{ width: ageProgress + '%', background: ageColor }" />
					</div>
					<div class="age-progress-stats">
						<span>{{ nextAge.levels_until || 0 }} levels to go</span>
						<span>{{ ageProgress }}%</span>
					</div>

					<div class="age-stats">
						<div class="age-stat">
							<span class="age-stat-value">{{ customStats.ages_reached || 1 }}</span>
							<span class="age-stat-label">Ages Reached</span>
						</div>
						<div class="age-stat">
							<span class="age-stat-value">{{ customStats.total_ages || 10 }}</span>
							<span class="age-stat-label">Total Ages</span>
						</div>
						<div class="age-stat">
							<span class="age-stat-value">{{ customStats.unlocked_items || 0 }}</span>
							<span class="age-stat-label">Items Unlocked</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Equipment section -->
			<div class="equipment-card">
				<h3 class="card-title">Equipment</h3>
				<div class="equipment-grid">
					<div
						v-for="slot in equipmentSlots"
						:key="slot.key"
						class="equipment-slot"
						:class="{ filled: getEquippedName(slot.key) }"
					>
						<div class="slot-visual">
							<span class="slot-emoji">{{ slot.icon }}</span>
						</div>
						<div class="slot-info">
							<span class="slot-label">{{ slot.label }}</span>
							<span class="slot-item">{{ getEquippedName(slot.key) || 'Empty' }}</span>
						</div>
						<button
							v-if="getEquippedName(slot.key)"
							class="slot-action remove"
							@click="handleUnequip(slot.key)"
						>
							\u2715
						</button>
					</div>
				</div>
			</div>

			<!-- Items catalogue -->
			<div class="items-card">
				<div class="items-header">
					<h3 class="card-title">Items Catalogue</h3>
					<div class="items-filters">
						<button
							v-for="f in itemFilters"
							:key="f.value"
							class="filter-btn"
							:class="{ active: itemFilter === f.value }"
							@click="itemFilter = f.value"
						>
							{{ f.label }}
						</button>
						<select v-model="slotFilter" class="slot-select">
							<option value="all">All Slots</option>
							<option value="clothing">Clothing</option>
							<option value="weapon">Weapons</option>
							<option value="accessory">Accessories</option>
							<option value="headgear">Headgear</option>
						</select>
					</div>
				</div>

				<div v-if="filteredItems.length > 0" class="items-grid">
					<div
						v-for="item in filteredItems"
						:key="item.key"
						class="item-card"
						:class="[
							item.unlocked ? 'unlocked' : 'locked',
							isEquipped(item) ? 'equipped' : '',
							item.rarity
						]"
					>
						<div class="item-header">
							<span class="item-rarity-dot" :class="item.rarity" />
							<span class="item-rarity-label">{{ item.rarity }}</span>
						</div>
						<div class="item-icon">{{ item.icon || '\uD83D\uDCE6' }}</div>
						<div class="item-name">{{ item.name }}</div>
						<div class="item-slot-label">{{ item.slot }}</div>
						<div class="item-footer">
							<button
								v-if="item.unlocked && !isEquipped(item)"
								class="item-btn equip"
								@click="handleEquip(item.key)"
							>
								Equip
							</button>
							<span v-else-if="isEquipped(item)" class="item-status equipped">Equipped</span>
							<span v-else class="item-status locked">Lv. {{ item.unlock_level || '?' }}</span>
						</div>
					</div>
				</div>

				<div v-else class="empty-items">
					<div class="empty-icon">&#x1F392;</div>
					<p class="empty-title">No items available yet</p>
					<p class="empty-desc">Level up and progress through the ages to unlock equipment. Each age brings new weapons, armor, and accessories!</p>
				</div>
			</div>
		</template>
	</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
	name: 'CharacterPage',

	data() {
		return {
			itemFilter: 'all',
			slotFilter: 'all',
			equipmentSlots: [
				{ key: 'clothing', icon: '\uD83D\uDC55', label: 'Clothing' },
				{ key: 'weapon', icon: '\u2694\uFE0F', label: 'Weapon' },
				{ key: 'accessory', icon: '\uD83D\uDCFF', label: 'Accessory' },
				{ key: 'headgear', icon: '\uD83D\uDC51', label: 'Headgear' },
			],
			itemFilters: [
				{ value: 'all', label: 'All' },
				{ value: 'unlocked', label: 'Unlocked' },
				{ value: 'locked', label: 'Locked' },
			],
		}
	},

	computed: {
		...mapState('quest', ['stats', 'user', 'character', 'loading']),

		initials() {
			const name = this.user.displayName || 'A'
			const words = name.trim().split(/\s+/)
			if (words.length >= 2) return (words[0][0] + words[1][0]).toUpperCase()
			return name.substring(0, 2).toUpperCase()
		},

		currentAge() {
			return this.character.current_age || this.character.age || {}
		},

		nextAge() {
			return this.character.next_age || {}
		},

		customStats() {
			return this.character.customization_stats || {}
		},

		ageColor() {
			return this.currentAge.color || '#8b7355'
		},

		ageProgress() {
			if (!this.nextAge.levels_until) return 0
			// Rough estimate: if 5 levels until next age and we're at level 3, that's ~40%
			const totalLevels = (this.nextAge.levels_until || 5) + (this.stats.level.level || 1)
			const progress = ((this.stats.level.level || 1) / totalLevels) * 100
			return Math.min(Math.round(progress), 99)
		},

		appearance() {
			return this.character.appearance || this.character.equipment || {}
		},

		allItems() {
			return this.character.items || []
		},

		filteredItems() {
			let items = this.allItems
			if (this.itemFilter === 'unlocked') items = items.filter(i => i.unlocked)
			if (this.itemFilter === 'locked') items = items.filter(i => !i.unlocked)
			if (this.slotFilter !== 'all') items = items.filter(i => i.slot === this.slotFilter)
			return items
		},
	},

	mounted() {
		this.loadCharacter()
	},

	methods: {
		...mapActions('quest', ['loadCharacter', 'equipItem', 'unequipItem']),

		getEquippedName(slot) {
			const eq = this.appearance
			if (eq[slot]) {
				const item = this.allItems.find(i => i.key === eq[slot])
				return item ? item.name : eq[slot]
			}
			return null
		},

		isEquipped(item) {
			const eq = this.appearance
			return Object.values(eq).includes(item.key)
		},

		async handleEquip(itemKey) {
			try { await this.equipItem(itemKey) } catch (e) { console.error(e) }
		},

		async handleUnequip(slot) {
			try { await this.unequipItem(slot) } catch (e) { console.error(e) }
		},
	},
}
</script>

<style scoped>
.character-page {
	padding: 16px 8px;
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

/* ── Top row ── */
.character-top {
	display: grid;
	grid-template-columns: 1fr 1.5fr;
	gap: 20px;
	margin-bottom: 24px;
}

/* ── Character card ── */
.character-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 28px 24px;
	text-align: center;
	display: flex;
	flex-direction: column;
	align-items: center;
}

.character-visual {
	position: relative;
	margin-bottom: 16px;
}

.avatar-ring {
	width: 120px;
	height: 120px;
	border-radius: 50%;
	padding: 4px;
	background: linear-gradient(135deg, var(--age-color), color-mix(in srgb, var(--age-color) 60%, white));
}

.avatar-inner {
	width: 100%;
	height: 100%;
	border-radius: 50%;
	background: var(--color-background-dark, #292929);
	display: flex;
	align-items: center;
	justify-content: center;
}

.avatar-initials {
	font-size: 36px;
	font-weight: 700;
	color: var(--color-main-text);
}

.character-badge {
	position: absolute;
	bottom: -4px;
	left: 50%;
	transform: translateX(-50%);
	background: var(--color-primary-element, var(--color-primary, #0082c9));
	color: white;
	font-size: 11px;
	font-weight: 700;
	padding: 2px 12px;
	border-radius: 10px;
}

.character-details {
	text-align: center;
}

.character-name {
	font-size: var(--font-size-xlarge);
	font-weight: 700;
	color: var(--color-main-text);
	margin: 0 0 2px;
}

.character-rank {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	margin-bottom: 12px;
}

.character-age-label {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	font-size: var(--font-size-normal);
	font-weight: 600;
	color: var(--color-main-text);
	margin-bottom: 6px;
}

.age-icon {
	font-size: 20px;
}

.age-description {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	line-height: 1.4;
	margin: 0;
}

/* ── Age progression card ── */
.age-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 24px;
}

.card-title {
	font-size: var(--font-size-large);
	font-weight: 600;
	color: var(--color-main-text);
	margin: 0 0 16px;
	padding-bottom: 12px;
	border-bottom: 1px solid var(--color-border);
}

.age-progress-info {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 16px;
	margin-bottom: 12px;
}

.age-current, .age-next {
	display: flex;
	align-items: center;
	gap: 6px;
	font-size: var(--font-size-normal);
	font-weight: 600;
	color: var(--color-main-text);
}

.age-dot {
	width: 10px;
	height: 10px;
	border-radius: 50%;
	background: var(--color-text-light);
}

.age-dot.next {
	background: var(--color-border);
	border: 2px solid var(--color-text-light);
}

.age-arrow {
	color: var(--color-text-light);
	font-size: 18px;
}

.age-progress-bar {
	height: 10px;
	background: var(--color-background-dark);
	border-radius: 5px;
	overflow: hidden;
	margin-bottom: 6px;
}

.age-progress-fill {
	height: 100%;
	border-radius: 5px;
	transition: width var(--transition-slow);
}

.age-progress-stats {
	display: flex;
	justify-content: space-between;
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	margin-bottom: 20px;
}

.age-stats {
	display: flex;
	gap: 12px;
}

.age-stat {
	flex: 1;
	text-align: center;
	padding: 12px 8px;
	background: var(--color-background-hover);
	border-radius: var(--radius-medium);
}

.age-stat-value {
	display: block;
	font-size: var(--font-size-xxlarge);
	font-weight: 700;
	color: var(--color-main-text);
}

.age-stat-label {
	display: block;
	font-size: 11px;
	color: var(--color-text-light);
	margin-top: 2px;
}

/* ── Equipment ── */
.equipment-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 24px;
	margin-bottom: 24px;
}

.equipment-grid {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 12px;
}

.equipment-slot {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 12px;
	background: var(--color-background-hover);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	transition: border-color var(--transition-fast);
}

.equipment-slot.filled {
	border-color: var(--color-primary-element, var(--color-primary, #0082c9));
}

.slot-visual {
	width: 40px;
	height: 40px;
	border-radius: var(--radius-medium);
	background: var(--color-background-dark);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 20px;
	flex-shrink: 0;
}

.slot-info {
	flex: 1;
	min-width: 0;
}

.slot-label {
	display: block;
	font-size: 11px;
	color: var(--color-text-light);
	text-transform: uppercase;
	letter-spacing: 0.5px;
	font-weight: 600;
}

.slot-item {
	display: block;
	font-size: var(--font-size-small);
	font-weight: 500;
	color: var(--color-main-text);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.slot-action {
	background: none;
	border: none;
	cursor: pointer;
	color: var(--color-text-light);
	font-size: 14px;
	padding: 4px;
	border-radius: var(--radius-small);
}

.slot-action:hover {
	color: var(--color-error, #e9322d);
	background: var(--color-background-dark);
}

/* ── Items catalogue ── */
.items-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 24px;
}

.items-header {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	margin-bottom: 0;
}

.items-header .card-title {
	margin-bottom: 0;
	border-bottom: none;
	padding-bottom: 0;
}

.items-filters {
	display: flex;
	gap: 4px;
	align-items: center;
}

.filter-btn {
	padding: 5px 12px;
	border: 1px solid var(--color-border);
	background: var(--color-main-background);
	border-radius: var(--radius-medium);
	cursor: pointer;
	font-size: var(--font-size-small);
	color: var(--color-main-text);
	transition: all var(--transition-fast);
}

.filter-btn.active {
	background: var(--color-primary-element, var(--color-primary, #0082c9));
	color: white;
	border-color: transparent;
}

.slot-select {
	padding: 5px 10px;
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: var(--font-size-small);
	margin-left: 8px;
}

.items-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
	gap: 12px;
	margin-top: 16px;
}

.item-card {
	background: var(--color-background-hover);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-medium);
	padding: 14px 10px;
	text-align: center;
	transition: all var(--transition-fast);
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 4px;
}

.item-card:hover {
	transform: translateY(-2px);
	box-shadow: var(--shadow-md);
}

.item-card.locked {
	opacity: 0.4;
}

.item-card.equipped {
	border-color: var(--color-success);
	background: rgba(70, 186, 97, 0.05);
}

/* Rarity borders */
.item-card.rare { border-color: #2196f3; }
.item-card.epic { border-color: #9c27b0; }
.item-card.legendary { border-color: #ff9800; }

.item-header {
	display: flex;
	align-items: center;
	gap: 4px;
	align-self: flex-end;
}

.item-rarity-dot {
	width: 6px;
	height: 6px;
	border-radius: 50%;
	background: #9e9e9e;
}

.item-rarity-dot.rare { background: #2196f3; }
.item-rarity-dot.epic { background: #9c27b0; }
.item-rarity-dot.legendary { background: #ff9800; }

.item-rarity-label {
	font-size: 9px;
	text-transform: uppercase;
	font-weight: 700;
	color: var(--color-text-light);
	letter-spacing: 0.3px;
}

.item-icon {
	font-size: 28px;
	line-height: 1;
}

.item-name {
	font-size: var(--font-size-small);
	font-weight: 600;
	color: var(--color-main-text);
}

.item-slot-label {
	font-size: 10px;
	color: var(--color-text-light);
	text-transform: uppercase;
}

.item-footer {
	margin-top: 4px;
}

.item-btn {
	padding: 3px 14px;
	border: none;
	border-radius: var(--radius-medium);
	cursor: pointer;
	font-size: 12px;
	font-weight: 600;
	transition: all var(--transition-fast);
}

.item-btn.equip {
	background: var(--color-primary-element, var(--color-primary, #0082c9));
	color: white;
}

.item-btn.equip:hover {
	filter: brightness(1.1);
}

.item-status {
	font-size: 11px;
	font-weight: 600;
}

.item-status.equipped {
	color: var(--color-success);
}

.item-status.locked {
	color: var(--color-text-light);
}

.empty-items {
	text-align: center;
	padding: 40px 20px;
	margin-top: 16px;
	border: 1px dashed var(--color-border);
	border-radius: var(--radius-large);
}

.empty-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
.empty-title { font-size: var(--font-size-large); font-weight: 600; color: var(--color-main-text); margin: 0 0 6px; }
.empty-desc { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; line-height: 1.4; }

/* ── Loading ── */
.loading-state {
	text-align: center;
	padding: 60px;
}

.spinner {
	width: 36px;
	height: 36px;
	border: 3px solid var(--color-border);
	border-top-color: var(--color-primary-element, var(--color-primary, #0082c9));
	border-radius: 50%;
	animation: spin 0.8s linear infinite;
	margin: 0 auto 16px;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}

/* ── Responsive ── */
@media (max-width: 900px) {
	.character-top {
		grid-template-columns: 1fr;
	}

	.equipment-grid {
		grid-template-columns: repeat(2, 1fr);
	}
}

@media (max-width: 600px) {
	.equipment-grid {
		grid-template-columns: 1fr;
	}

	.items-header {
		flex-direction: column;
		gap: 12px;
	}
}
</style>
