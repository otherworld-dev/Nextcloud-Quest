<template>
	<canvas
		ref="canvas"
		class="pixel-avatar"
		:width="nativeSize"
		:height="nativeSize"
		:style="{ width: size + 'px', height: size + 'px' }"
	/>
</template>

<script>
const GRID = 32
const SKIN_TONES = {
	'1': '#fde7c8', '2': '#f5d0a9', '3': '#e8b88a',
	'4': '#c68c53', '5': '#8d5524', '6': '#4a2c0a',
}
const HAIR_COLORS = {
	black: '#1a1a1a', brown: '#6b3a2a', blonde: '#d4a843',
	red: '#b03a2e', gray: '#7f8c8d', white: '#ecf0f1',
	blue: '#2980b9', green: '#27ae60',
}
const AGE_COLORS = {
	stone: '#8b7355', bronze: '#cd7f32', iron: '#71706e',
	medieval: '#8b4513', renaissance: '#daa520', industrial: '#696969',
	modern: '#2c3e50', digital: '#00ced1', space: '#9370db',
}

// Helper: draw a filled rect in grid coords
function px(ctx, x, y, w, h, color) {
	ctx.fillStyle = color
	ctx.fillRect(x, y, w || 1, h || 1)
}

// Helper: draw outline then fill
function drawBody(ctx, skin, outline) {
	// Head (8x8 at pos 12,2)
	px(ctx, 12, 2, 8, 8, skin)
	// Head outline
	for (let x = 12; x < 20; x++) { px(ctx, x, 1, 1, 1, outline); px(ctx, x, 10, 1, 1, outline) }
	for (let y = 2; y < 10; y++) { px(ctx, 11, y, 1, 1, outline); px(ctx, 20, y, 1, 1, outline) }

	// Eyes (2px each)
	px(ctx, 14, 5, 2, 2, outline)
	px(ctx, 18, 5, 2, 2, outline)
	px(ctx, 14, 5, 1, 1, '#ffffff')
	px(ctx, 18, 5, 1, 1, '#ffffff')

	// Mouth
	px(ctx, 15, 8, 3, 1, outline)

	// Neck
	px(ctx, 15, 10, 2, 2, skin)

	// Torso (10x8 at 11,12)
	// drawn by clothing layer, skin shows as arms
}

function drawBodyDefault(ctx, skin, outline) {
	drawBody(ctx, skin, outline)
	// Arms
	px(ctx, 9, 12, 2, 7, skin)
	px(ctx, 21, 12, 2, 7, skin)
	// Arm outlines
	px(ctx, 8, 12, 1, 7, outline); px(ctx, 11, 12, 1, 7, outline)
	px(ctx, 21, 12, 1, 7, outline); px(ctx, 23, 12, 1, 7, outline)
	// Hands
	px(ctx, 9, 19, 2, 2, skin); px(ctx, 21, 19, 2, 2, skin)
	// Legs
	px(ctx, 13, 22, 3, 8, skin); px(ctx, 17, 22, 3, 8, skin)
	px(ctx, 12, 22, 1, 8, outline); px(ctx, 16, 22, 1, 8, outline)
	px(ctx, 16, 22, 1, 8, outline); px(ctx, 20, 22, 1, 8, outline)
	// Feet
	px(ctx, 12, 30, 4, 2, outline); px(ctx, 17, 30, 4, 2, outline)
	px(ctx, 13, 30, 2, 1, '#555')
	px(ctx, 18, 30, 2, 1, '#555')
}

function drawBodyBroad(ctx, skin, outline) {
	drawBody(ctx, skin, outline)
	// Wider arms
	px(ctx, 8, 12, 3, 7, skin)
	px(ctx, 21, 12, 3, 7, skin)
	px(ctx, 7, 12, 1, 7, outline); px(ctx, 11, 12, 1, 7, outline)
	px(ctx, 21, 12, 1, 7, outline); px(ctx, 24, 12, 1, 7, outline)
	px(ctx, 8, 19, 3, 2, skin); px(ctx, 21, 19, 3, 2, skin)
	// Wider legs
	px(ctx, 12, 22, 4, 8, skin); px(ctx, 17, 22, 4, 8, skin)
	px(ctx, 11, 22, 1, 8, outline); px(ctx, 16, 22, 1, 8, outline)
	px(ctx, 16, 22, 1, 8, outline); px(ctx, 21, 22, 1, 8, outline)
	px(ctx, 11, 30, 5, 2, outline); px(ctx, 17, 30, 5, 2, outline)
	px(ctx, 12, 30, 3, 1, '#555'); px(ctx, 18, 30, 3, 1, '#555')
}

function drawHair(ctx, style, color) {
	const c = color
	const styles = {
		short() {
			px(ctx, 12, 1, 8, 3, c)
			px(ctx, 11, 2, 1, 2, c)
			px(ctx, 20, 2, 1, 2, c)
		},
		long() {
			px(ctx, 12, 0, 8, 3, c)
			px(ctx, 11, 1, 1, 14, c)
			px(ctx, 20, 1, 1, 14, c)
			px(ctx, 12, 0, 8, 1, c)
		},
		mohawk() {
			px(ctx, 14, -1, 4, 4, c)
			px(ctx, 15, -2, 2, 1, c)
		},
		bald() {},
		ponytail() {
			px(ctx, 12, 0, 8, 3, c)
			px(ctx, 11, 1, 1, 3, c)
			px(ctx, 20, 1, 1, 3, c)
			px(ctx, 21, 4, 2, 2, c)
			px(ctx, 22, 6, 2, 6, c)
		},
		curly() {
			px(ctx, 11, 0, 10, 3, c)
			px(ctx, 10, 1, 1, 4, c)
			px(ctx, 21, 1, 1, 4, c)
			px(ctx, 11, -1, 2, 1, c); px(ctx, 15, -1, 2, 1, c); px(ctx, 19, -1, 2, 1, c)
		},
		spiky() {
			px(ctx, 12, 0, 8, 2, c)
			px(ctx, 12, -2, 2, 2, c); px(ctx, 15, -1, 2, 1, c); px(ctx, 18, -2, 2, 2, c)
			px(ctx, 11, 1, 1, 2, c); px(ctx, 20, 1, 1, 2, c)
		},
		braided() {
			px(ctx, 12, 0, 8, 3, c)
			px(ctx, 11, 1, 1, 4, c); px(ctx, 20, 1, 1, 4, c)
			// Braids
			px(ctx, 10, 5, 1, 2, c); px(ctx, 10, 8, 1, 2, c); px(ctx, 10, 11, 1, 2, c)
			px(ctx, 21, 5, 1, 2, c); px(ctx, 21, 8, 1, 2, c); px(ctx, 21, 11, 1, 2, c)
		},
	}
	if (styles[style]) styles[style]()
}

function drawClothing(ctx, color, outline) {
	// Torso armor/clothing
	px(ctx, 12, 12, 8, 10, color)
	px(ctx, 11, 12, 1, 10, outline); px(ctx, 20, 12, 1, 10, outline)
	px(ctx, 12, 11, 8, 1, outline); px(ctx, 12, 22, 8, 1, outline)
	// Collar
	px(ctx, 14, 11, 4, 1, color)
	// Belt
	px(ctx, 12, 20, 8, 1, outline)
	px(ctx, 15, 20, 2, 1, '#c0a030')
}

function drawWeapon(ctx, itemKey, color) {
	if (!itemKey) return

	if (/pistol|rifle|musket|revolver|blaster|cannon/.test(itemKey)) {
		// Gun — barrel + grip
		px(ctx, 24, 14, 6, 2, '#555')
		px(ctx, 24, 16, 2, 3, '#777')
		px(ctx, 30, 14, 1, 1, '#ff0')
	} else if (/club/.test(itemKey)) {
		// Club — thick bulbous head
		px(ctx, 25, 10, 2, 12, '#6b4423')
		px(ctx, 24, 6, 4, 5, color)
		px(ctx, 23, 7, 6, 3, color)
	} else if (/axe|battle/.test(itemKey)) {
		// Axe — handle + blade head
		px(ctx, 25, 6, 1, 14, '#8B4513')
		px(ctx, 26, 6, 4, 5, color)
		px(ctx, 26, 5, 3, 1, color)
		px(ctx, 26, 11, 3, 1, color)
	} else if (/mace/.test(itemKey)) {
		// Mace — handle + spiked head
		px(ctx, 25, 10, 1, 12, '#8B4513')
		px(ctx, 23, 6, 5, 5, color)
		px(ctx, 22, 7, 1, 3, color)
		px(ctx, 28, 7, 1, 3, color)
	} else if (/spear/.test(itemKey)) {
		// Spear — long shaft + pointed tip
		px(ctx, 25, 8, 1, 16, '#8B4513')
		px(ctx, 24, 4, 3, 5, color)
		px(ctx, 25, 3, 1, 1, color)
	} else if (/staff|quill/.test(itemKey)) {
		// Staff — rod with orb
		px(ctx, 25, 6, 1, 16, '#8B4513')
		px(ctx, 24, 3, 3, 3, color)
		px(ctx, 25, 4, 1, 1, '#fff')
	} else if (/dagger/.test(itemKey)) {
		// Dagger — short blade
		px(ctx, 25, 12, 1, 8, '#aaa')
		px(ctx, 24, 11, 3, 2, color)
		px(ctx, 24, 19, 3, 1, '#8B4513')
		px(ctx, 25, 20, 1, 2, '#8B4513')
	} else {
		// Sword default — blade + crossguard + handle
		px(ctx, 25, 4, 1, 14, '#aaa')
		px(ctx, 24, 3, 3, 2, color)
		px(ctx, 23, 17, 5, 1, '#8B4513')
		px(ctx, 25, 18, 1, 3, '#8B4513')
	}
}

function drawHeadgear(ctx, itemKey, color, outline) {
	if (!itemKey) return

	if (/crown/.test(itemKey)) {
		// Crown — 3 points with gems
		px(ctx, 12, 0, 8, 2, '#ffd700')
		px(ctx, 12, -1, 2, 1, '#ffd700'); px(ctx, 15, -2, 2, 1, '#ffd700'); px(ctx, 18, -1, 2, 1, '#ffd700')
		px(ctx, 13, -1, 1, 1, '#e74c3c'); px(ctx, 16, -2, 1, 1, '#3498db'); px(ctx, 19, -1, 1, 1, '#2ecc71')
	} else if (/hood/.test(itemKey)) {
		// Hood — draped over head and sides
		px(ctx, 11, 0, 10, 4, color)
		px(ctx, 10, 2, 1, 8, color); px(ctx, 21, 2, 1, 8, color)
	} else if (/headband/.test(itemKey)) {
		// Headband — thin band across forehead
		px(ctx, 12, 2, 8, 1, color)
		px(ctx, 11, 2, 1, 1, color); px(ctx, 20, 2, 1, 1, color)
		px(ctx, 16, 1, 1, 1, '#ffd700')
	} else if (/goggles/.test(itemKey)) {
		// Goggles — two round lenses on forehead
		px(ctx, 12, 1, 8, 2, color)
		px(ctx, 13, 3, 3, 2, color); px(ctx, 17, 3, 3, 2, color)
		px(ctx, 14, 3, 1, 1, '#88ccff'); px(ctx, 18, 3, 1, 1, '#88ccff')
	} else if (/visor|headset/.test(itemKey)) {
		// Visor — band with blue lens
		px(ctx, 12, 1, 8, 2, color)
		px(ctx, 12, 4, 9, 2, color)
		px(ctx, 13, 5, 7, 1, '#88ccff')
	} else if (/cap/.test(itemKey)) {
		// Cap — curved brim
		px(ctx, 12, 0, 8, 3, color)
		px(ctx, 11, 1, 1, 2, color)
		px(ctx, 10, 3, 4, 1, color)
	} else if (/hat|top_hat/.test(itemKey)) {
		// Hat — tall with brim
		px(ctx, 13, -3, 6, 5, color)
		px(ctx, 11, 1, 10, 2, color)
	} else {
		// Helmet — full dome
		px(ctx, 11, -1, 10, 5, color)
		px(ctx, 10, 0, 1, 4, outline); px(ctx, 21, 0, 1, 4, outline)
		px(ctx, 11, -2, 10, 1, outline)
		px(ctx, 15, -2, 2, 1, color)
	}
}

function drawAccessory(ctx, itemKey, color) {
	if (!itemKey) return

	if (/shield|banner/.test(itemKey)) {
		// Shield — on left arm
		px(ctx, 5, 13, 4, 6, color)
		px(ctx, 4, 14, 1, 4, '#555'); px(ctx, 9, 14, 1, 4, '#555')
		px(ctx, 6, 14, 2, 2, '#ffd700')
	} else if (/necklace|amulet/.test(itemKey)) {
		// Necklace — chain around neck with pendant
		px(ctx, 14, 10, 1, 2, color); px(ctx, 18, 10, 1, 2, color)
		px(ctx, 15, 11, 3, 1, color)
		px(ctx, 16, 12, 1, 1, '#ffd700')
	} else if (/ring/.test(itemKey)) {
		// Ring — small band on hand
		px(ctx, 9, 18, 2, 1, color)
		px(ctx, 9, 17, 1, 1, '#ffd700')
	} else if (/bracelet/.test(itemKey)) {
		// Bracelet — band on wrist
		px(ctx, 8, 17, 3, 2, color)
		px(ctx, 21, 17, 3, 2, color)
	} else if (/watch/.test(itemKey)) {
		// Watch — on left wrist
		px(ctx, 8, 17, 3, 2, color)
		px(ctx, 9, 17, 1, 1, '#f5f5dc')
	} else if (/phone|smartphone/.test(itemKey)) {
		// Phone — in hand
		px(ctx, 22, 17, 2, 3, '#222')
		px(ctx, 22, 18, 2, 1, color)
	} else if (/jetpack/.test(itemKey)) {
		// Jetpack — on back (behind torso)
		px(ctx, 11, 14, 2, 6, color)
		px(ctx, 19, 14, 2, 6, color)
		px(ctx, 11, 20, 1, 2, '#ff4500')
		px(ctx, 20, 20, 1, 2, '#ff4500')
	} else if (/quantum|neural|interface/.test(itemKey)) {
		// Tech device — glowing on chest
		px(ctx, 15, 14, 2, 2, color)
		px(ctx, 16, 14, 1, 1, '#fff')
	} else if (/quill/.test(itemKey)) {
		// Quill — in hand
		px(ctx, 22, 14, 1, 6, color)
		px(ctx, 22, 13, 2, 2, '#fff')
	} else {
		// Generic — belt item
		px(ctx, 12, 20, 2, 2, color)
	}
}

export default {
	name: 'PixelAvatar',

	props: {
		skinTone: { type: String, default: '3' },
		hairStyle: { type: String, default: 'short' },
		hairColor: { type: String, default: 'brown' },
		bodyType: { type: String, default: 'default' },
		equippedWeapon: { type: String, default: null },
		equippedHeadgear: { type: String, default: null },
		equippedClothing: { type: String, default: null },
		equippedAccessory: { type: String, default: null },
		ageKey: { type: String, default: 'stone' },
		size: { type: Number, default: 128 },
	},

	computed: {
		nativeSize() { return GRID },
	},

	watch: {
		skinTone() { this.render() },
		hairStyle() { this.render() },
		hairColor() { this.render() },
		bodyType() { this.render() },
		equippedWeapon() { this.render() },
		equippedHeadgear() { this.render() },
		equippedClothing() { this.render() },
		equippedAccessory() { this.render() },
		ageKey() { this.render() },
	},

	mounted() { this.render() },

	methods: {
		render() {
			const canvas = this.$refs.canvas
			if (!canvas) return
			const ctx = canvas.getContext('2d')
			ctx.clearRect(0, 0, GRID, GRID)

			const skin = SKIN_TONES[this.skinTone] || SKIN_TONES['3']
			const hairCol = HAIR_COLORS[this.hairColor] || HAIR_COLORS.brown
			const ageCol = AGE_COLORS[this.ageKey] || '#8b7355'
			const outline = '#2c2c2c'
			const clothCol = this.equippedClothing ? this.itemColor(this.equippedClothing) : ageCol

			// Layer 1: Body
			if (this.bodyType === 'broad') {
				drawBodyBroad(ctx, skin, outline)
			} else {
				drawBodyDefault(ctx, skin, outline)
			}

			// Layer 2: Clothing
			drawClothing(ctx, clothCol, outline)

			// Layer 3: Accessory
			drawAccessory(ctx, this.equippedAccessory, this.itemColor(this.equippedAccessory) || '#c0a060')

			// Layer 4: Hair (after clothing so it overlaps collar)
			drawHair(ctx, this.hairStyle, hairCol)

			// Layer 5: Weapon
			drawWeapon(ctx, this.equippedWeapon, this.itemColor(this.equippedWeapon) || '#aaa')

			// Layer 6: Headgear (on top of everything)
			drawHeadgear(ctx, this.equippedHeadgear, this.itemColor(this.equippedHeadgear) || ageCol, outline)
		},

		itemColor(itemKey) {
			if (!itemKey) return null
			// Unique color per item based on key hash + age tint
			const ITEM_COLORS = {
				// Stone
				stone_fur_basic: '#a08060', stone_fur_decorated: '#c89050',
				stone_club: '#7a5c3a', stone_spear: '#8B7355', stone_axe: '#6b4423',
				stone_bone_necklace: '#d4c4a0', stone_shell_bracelet: '#b0d0d0',
				stone_headband: '#8b6914', stone_fur_hood: '#6b5030',
				// Bronze
				bronze_tunic: '#cd7f32', bronze_armor: '#b8860b',
				bronze_sword: '#daa520', bronze_dagger: '#c0a030',
				bronze_ring: '#cd853f', bronze_amulet: '#d4a040',
				bronze_cap: '#b87333',
				// Iron
				iron_chainmail: '#808080', iron_armor: '#696969',
				iron_longsword: '#a0a0a0', iron_battle_axe: '#888888',
				iron_shield: '#778899', iron_helmet: '#707070', iron_horned_helmet: '#505050',
				// Medieval
				medieval_knight_armor: '#4a4a6a', medieval_royal_robes: '#800020',
				medieval_broadsword: '#c0c0c0', medieval_mace: '#666666', medieval_excalibur: '#e0d060',
				medieval_banner: '#cc2222', medieval_crown: '#ffd700',
				// Renaissance
				renaissance_doublet: '#8b0000', renaissance_scholar_robes: '#2f4f4f',
				renaissance_rapier: '#c0c0c0', renaissance_musket: '#5c4033',
				renaissance_quill: '#daa520', renaissance_hat: '#722f37',
				// Industrial
				industrial_suit: '#36454f', industrial_engineer_coat: '#654321',
				industrial_revolver: '#444', industrial_rifle: '#333',
				industrial_pocket_watch: '#d4af37', industrial_top_hat: '#1a1a1a', industrial_goggles: '#b87333',
				// Modern
				modern_business_suit: '#1c1c3c', modern_tactical_gear: '#2d4a2d',
				modern_pistol: '#333', modern_assault_rifle: '#222',
				modern_smartphone: '#e0e0e0', modern_cap: '#1e3a5f', modern_helmet: '#3a5a3a',
				// Digital
				digital_smart_suit: '#006080', digital_cyber_armor: '#00ced1',
				digital_plasma_pistol: '#00ffff', digital_laser_rifle: '#ff00ff',
				digital_neural_interface: '#7b68ee', digital_vr_headset: '#4169e1', digital_ar_visor: '#00bfff',
				// Space
				space_suit: '#4a0080', space_exo_armor: '#6a0dad',
				space_ion_blaster: '#00ff88', space_antimatter_cannon: '#ff4500',
				space_jetpack: '#ff6347', space_quantum_field: '#9400d3',
				space_helmet: '#6c6c9c', space_commander_helm: '#ffd700',
			}
			return ITEM_COLORS[itemKey] || AGE_COLORS[itemKey?.split('_')[0]] || '#888'
		},
	},
}
</script>

<style scoped>
.pixel-avatar {
	image-rendering: pixelated;
	image-rendering: crisp-edges;
	border-radius: var(--radius-medium);
}
</style>
