<template>
	<div class="character-page">
		<div class="page-header">
			<h1 class="page-title">Character</h1>
			<p class="page-subtitle">Customize your character's appearance and equipment.</p>
		</div>

		<!-- Loading -->
		<div v-if="loading.character" class="loading-state">
			<div class="spinner" />
			<p>Loading character...</p>
		</div>

		<template v-else>
			<!-- Character preview + info -->
			<div class="character-layout">
				<div class="preview-section">
					<div class="preview-card">
						<div class="preview-avatar">
							<div class="avatar-display">
								<div class="avatar-circle">
									<span class="avatar-initials">{{ initials }}</span>
								</div>
								<div class="avatar-level">Lv. {{ stats.level.level }}</div>
							</div>
						</div>

						<div class="character-info">
							<div class="info-row">
								<span class="info-label">Current Age</span>
								<span class="info-value">{{ character.age?.name || 'Stone Age' }}</span>
							</div>
							<div class="info-row">
								<span class="info-label">Rank</span>
								<span class="info-value">{{ stats.level.rank_title }}</span>
							</div>
							<div class="info-row">
								<span class="info-label">Items Unlocked</span>
								<span class="info-value">{{ unlockedItemCount }} / {{ totalItemCount }}</span>
							</div>
						</div>
					</div>
				</div>

				<!-- Equipment slots -->
				<div class="equipment-section">
					<h2 class="section-title">Equipment</h2>
					<div class="equipment-slots">
						<div
							v-for="slot in equipmentSlots"
							:key="slot.key"
							class="equipment-slot"
						>
							<div class="slot-icon">{{ slot.icon }}</div>
							<div class="slot-details">
								<div class="slot-name">{{ slot.label }}</div>
								<div class="slot-equipped">
									{{ getEquippedName(slot.key) || 'None' }}
								</div>
							</div>
							<button
								v-if="getEquippedName(slot.key)"
								class="slot-btn unequip"
								@click="handleUnequip(slot.key)"
							>
								Remove
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Items grid -->
			<section class="items-section">
				<div class="section-header">
					<h2 class="section-title">Available Items</h2>
					<div class="filter-tabs">
						<button
							v-for="f in itemFilters"
							:key="f.value"
							class="filter-tab"
							:class="{ active: itemFilter === f.value }"
							@click="itemFilter = f.value"
						>
							{{ f.label }}
						</button>
					</div>
				</div>

				<div class="items-grid">
					<div
						v-for="item in filteredItems"
						:key="item.key"
						class="item-card"
						:class="{ locked: !item.unlocked, equipped: isEquipped(item) }"
					>
						<div class="item-icon">{{ item.icon || '📦' }}</div>
						<div class="item-name">{{ item.name }}</div>
						<span class="item-rarity" :class="item.rarity">{{ item.rarity }}</span>
						<div class="item-slot">{{ item.slot }}</div>
						<button
							v-if="item.unlocked && !isEquipped(item)"
							class="item-equip-btn"
							@click="handleEquip(item.key)"
						>
							Equip
						</button>
						<span v-else-if="isEquipped(item)" class="item-equipped-label">Equipped</span>
						<span v-else class="item-locked-label">Lv. {{ item.unlock_level }}</span>
					</div>
				</div>

				<div v-if="filteredItems.length === 0" class="empty-state">
					<div class="empty-state-icon">📦</div>
					<div class="empty-state-text">No items match your filter.</div>
				</div>
			</section>
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
			equipmentSlots: [
				{ key: 'clothing', icon: '\u{1F455}', label: 'Clothing' },
				{ key: 'weapon', icon: '\u{2694}\uFE0F', label: 'Weapon' },
				{ key: 'accessory', icon: '\u{1F4FF}', label: 'Accessory' },
				{ key: 'headgear', icon: '\u{1F451}', label: 'Headgear' },
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

		allItems() {
			return this.character.items || []
		},

		filteredItems() {
			if (this.itemFilter === 'unlocked') return this.allItems.filter(i => i.unlocked)
			if (this.itemFilter === 'locked') return this.allItems.filter(i => !i.unlocked)
			return this.allItems
		},

		unlockedItemCount() {
			return this.allItems.filter(i => i.unlocked).length
		},

		totalItemCount() {
			return this.allItems.length
		},
	},

	mounted() {
		this.loadCharacter()
	},

	methods: {
		...mapActions('quest', ['loadCharacter', 'equipItem', 'unequipItem']),

		getEquippedName(slot) {
			const eq = this.character.equipment || {}
			if (eq[slot]) {
				const item = this.allItems.find(i => i.key === eq[slot])
				return item ? item.name : eq[slot]
			}
			return null
		},

		isEquipped(item) {
			const eq = this.character.equipment || {}
			return Object.values(eq).includes(item.key)
		},

		async handleEquip(itemKey) {
			try {
				await this.equipItem(itemKey)
			} catch (e) {
				console.error('Failed to equip:', e)
			}
		},

		async handleUnequip(slot) {
			try {
				await this.unequipItem(slot)
			} catch (e) {
				console.error('Failed to unequip:', e)
			}
		},
	},
}
</script>

<style scoped>
.character-page { padding: 8px 0; }
.page-header { margin-bottom: 24px; }
.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

.character-layout {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 24px;
	margin-bottom: 32px;
}

/* Preview */
.preview-card {
	background: var(--color-main-background);
	border-radius: var(--radius-large);
	padding: 24px;
	box-shadow: var(--shadow-sm);
	text-align: center;
}

.avatar-display { margin-bottom: 20px; }

.avatar-circle {
	width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 8px;
	background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
	display: flex; align-items: center; justify-content: center;
}

.avatar-initials { font-size: 40px; font-weight: 700; color: white; }
.avatar-level { font-size: var(--font-size-large); font-weight: 600; color: var(--color-primary); }

.character-info { text-align: left; }
.info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border); }
.info-row:last-child { border-bottom: none; }
.info-label { color: var(--color-text-light); }
.info-value { font-weight: 600; color: var(--color-main-text); }

/* Equipment */
.equipment-section { }
.section-title { font-size: var(--font-size-xlarge); font-weight: 600; margin: 0 0 16px; }

.equipment-slots { display: flex; flex-direction: column; gap: 12px; }
.equipment-slot {
	display: flex; align-items: center; gap: 12px; padding: 16px;
	background: var(--color-main-background); border-radius: var(--radius-large); box-shadow: var(--shadow-sm);
}
.slot-icon { font-size: 28px; }
.slot-details { flex: 1; }
.slot-name { font-weight: 600; font-size: var(--font-size-normal); }
.slot-equipped { font-size: var(--font-size-small); color: var(--color-text-light); }
.slot-btn {
	padding: 6px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-medium);
	background: var(--color-main-background); cursor: pointer; font-size: var(--font-size-small);
}
.slot-btn:hover { background: var(--color-background-hover); }

/* Items */
.items-section { margin-top: 32px; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.filter-tabs { display: flex; gap: 4px; }
.filter-tab {
	padding: 6px 14px; border: 1px solid var(--color-border); background: var(--color-main-background);
	border-radius: var(--radius-medium); cursor: pointer; font-size: var(--font-size-small);
}
.filter-tab.active { background: var(--color-primary); color: white; border-color: var(--color-primary); }

.items-grid {
	display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px;
}

.item-card {
	background: var(--color-main-background); border-radius: var(--radius-large); padding: 16px;
	box-shadow: var(--shadow-sm); text-align: center; transition: all var(--transition-fast);
}
.item-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.item-card.locked { opacity: 0.5; }
.item-card.equipped { border: 2px solid var(--color-success); }

.item-icon { font-size: 36px; margin-bottom: 8px; }
.item-name { font-size: var(--font-size-small); font-weight: 600; margin-bottom: 4px; }
.item-rarity {
	display: inline-block; padding: 2px 6px; border-radius: 8px; font-size: 10px;
	font-weight: 700; text-transform: uppercase; color: white; margin-bottom: 4px;
}
.item-rarity.common { background: #9e9e9e; }
.item-rarity.rare { background: #2196f3; }
.item-rarity.epic { background: #9c27b0; }
.item-rarity.legendary { background: #ff9800; }
.item-slot { font-size: 11px; color: var(--color-text-lighter); margin-bottom: 8px; }

.item-equip-btn {
	padding: 4px 12px; background: var(--color-primary); color: white; border: none;
	border-radius: var(--radius-medium); cursor: pointer; font-size: var(--font-size-small);
}
.item-equip-btn:hover { background: var(--color-primary-dark); }
.item-equipped-label { font-size: var(--font-size-small); color: var(--color-success); font-weight: 600; }
.item-locked-label { font-size: var(--font-size-small); color: var(--color-text-lighter); }

/* Loading & empty */
.loading-state { text-align: center; padding: 40px; }
.spinner { width: 32px; height: 32px; border: 3px solid var(--color-border); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 40px; }
.empty-state-icon { font-size: 48px; margin-bottom: 12px; }
.empty-state-text { color: var(--color-text-light); }

@media (max-width: 768px) {
	.character-layout { grid-template-columns: 1fr; }
	.items-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
