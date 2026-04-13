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
	const isGun = /pistol|rifle|musket|revolver|blaster|cannon/.test(itemKey)
	const isStaff = /spear|staff|quill/.test(itemKey)
	const isAxe = /axe|mace|wrench|club/.test(itemKey)

	if (isGun) {
		px(ctx, 24, 14, 6, 2, '#555')
		px(ctx, 24, 16, 2, 3, '#777')
		px(ctx, 30, 14, 1, 1, '#ff0')
	} else if (isStaff) {
		px(ctx, 25, 4, 1, 18, '#8B4513')
		px(ctx, 24, 3, 3, 2, color)
	} else if (isAxe) {
		px(ctx, 25, 6, 1, 14, '#8B4513')
		px(ctx, 26, 6, 3, 4, color)
		px(ctx, 26, 5, 2, 1, color)
	} else {
		// Sword default
		px(ctx, 25, 4, 1, 14, '#aaa')
		px(ctx, 24, 3, 3, 2, color)
		px(ctx, 23, 17, 5, 1, '#8B4513')
		px(ctx, 25, 18, 1, 3, '#8B4513')
	}
}

function drawHeadgear(ctx, itemKey, color, outline) {
	if (!itemKey) return
	const isCrown = /crown/.test(itemKey)
	const isHood = /hood|headband/.test(itemKey)
	const isVisor = /visor|headset|goggles|cap/.test(itemKey)
	const isHelmet = /helmet|helm|hat/.test(itemKey)

	if (isCrown) {
		px(ctx, 12, 0, 8, 2, '#ffd700')
		px(ctx, 12, -1, 2, 1, '#ffd700'); px(ctx, 15, -2, 2, 1, '#ffd700'); px(ctx, 18, -1, 2, 1, '#ffd700')
		px(ctx, 13, -1, 1, 1, '#e74c3c'); px(ctx, 16, -2, 1, 1, '#3498db'); px(ctx, 19, -1, 1, 1, '#2ecc71')
	} else if (isHood) {
		px(ctx, 11, 0, 10, 4, color)
		px(ctx, 10, 2, 1, 6, color); px(ctx, 21, 2, 1, 6, color)
	} else if (isVisor) {
		px(ctx, 12, 1, 8, 2, color)
		px(ctx, 13, 4, 7, 2, color)
		px(ctx, 14, 5, 5, 1, '#88ccff')
	} else if (isHelmet) {
		px(ctx, 11, -1, 10, 5, color)
		px(ctx, 10, 0, 1, 4, outline); px(ctx, 21, 0, 1, 4, outline)
		px(ctx, 11, -2, 10, 1, outline)
		px(ctx, 15, -2, 2, 1, color)
	}
}

function drawAccessory(ctx, itemKey, color) {
	if (!itemKey) return
	const isShield = /shield|banner/.test(itemKey)
	const isNecklace = /necklace|amulet|ring/.test(itemKey)

	if (isShield) {
		px(ctx, 5, 13, 4, 6, color)
		px(ctx, 4, 14, 1, 4, '#555'); px(ctx, 9, 14, 1, 4, '#555')
		px(ctx, 6, 14, 2, 2, '#ffd700')
	} else if (isNecklace) {
		px(ctx, 14, 10, 1, 2, color); px(ctx, 18, 10, 1, 2, color)
		px(ctx, 15, 11, 3, 1, color)
		px(ctx, 16, 12, 1, 1, '#ffd700')
	} else {
		// Generic — small icon on belt
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
			// Derive color from age in item key
			for (const [age, color] of Object.entries(AGE_COLORS)) {
				if (itemKey.startsWith(age)) return color
			}
			return '#888'
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
