<template>
	<canvas
		ref="canvas"
		class="pixel-avatar"
		:width="size"
		:height="size"
		:style="{ width: size + 'px', height: size + 'px' }"
	/>
</template>

<script>
// 16x16 pixel grid sprites — each cell is a color index
// 0=transparent, 1=skin, 2=hair, 3=clothing, 4=weapon, 5=accessory, 6=headgear, 7=outline

const SKIN_TONES = {
	'1': '#fde7c8', '2': '#f5d0a9', '3': '#e8b88a',
	'4': '#c68c53', '5': '#8d5524', '6': '#4a2c0a',
}

const HAIR_COLORS = {
	black: '#1a1a1a', brown: '#6b3a2a', blonde: '#d4a843',
	red: '#b03a2e', gray: '#7f8c8d', white: '#ecf0f1',
	blue: '#2980b9', green: '#27ae60',
}

const BODY = {
	default: [
		[0,0,0,0,0,7,7,7,7,7,0,0,0,0,0,0],
		[0,0,0,0,7,1,1,1,1,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,1,1,1,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,7,1,7,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,1,1,1,1,7,0,0,0,0,0],
		[0,0,0,0,0,7,1,1,1,7,0,0,0,0,0,0],
		[0,0,0,0,0,0,7,1,7,0,0,0,0,0,0,0],
		[0,0,0,0,7,7,7,7,7,7,7,0,0,0,0,0],
		[0,0,0,7,3,3,3,3,3,3,3,7,0,0,0,0],
		[0,0,0,7,3,3,3,3,3,3,3,7,0,0,0,0],
		[0,0,0,7,3,3,3,3,3,3,3,7,0,0,0,0],
		[0,0,0,7,3,3,3,3,3,3,3,7,0,0,0,0],
		[0,0,0,0,7,1,7,0,7,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,7,0,7,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,7,0,7,1,7,0,0,0,0,0],
		[0,0,0,0,0,7,0,0,0,7,0,0,0,0,0,0],
	],
	broad: [
		[0,0,0,0,0,7,7,7,7,7,0,0,0,0,0,0],
		[0,0,0,0,7,1,1,1,1,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,1,1,1,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,7,1,7,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,1,1,1,1,7,0,0,0,0,0],
		[0,0,0,0,0,7,1,1,1,7,0,0,0,0,0,0],
		[0,0,0,0,0,0,7,1,7,0,0,0,0,0,0,0],
		[0,0,0,7,7,7,7,7,7,7,7,7,0,0,0,0],
		[0,0,7,3,3,3,3,3,3,3,3,3,7,0,0,0],
		[0,0,7,3,3,3,3,3,3,3,3,3,7,0,0,0],
		[0,0,7,3,3,3,3,3,3,3,3,3,7,0,0,0],
		[0,0,0,7,3,3,3,3,3,3,3,7,0,0,0,0],
		[0,0,0,0,7,1,7,0,7,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,7,0,7,1,7,0,0,0,0,0],
		[0,0,0,0,7,1,7,0,7,1,7,0,0,0,0,0],
		[0,0,0,0,0,7,0,0,0,7,0,0,0,0,0,0],
	],
}

const HAIR_STYLES = {
	short: [
		[0,0,0,0,2,2,2,2,2,2,0,0,0,0,0,0],
		[0,0,0,2,2,2,2,2,2,2,2,0,0,0,0,0],
		[0,0,0,2,2,0,0,0,0,0,2,0,0,0,0,0],
	],
	long: [
		[0,0,0,0,2,2,2,2,2,2,0,0,0,0,0,0],
		[0,0,0,2,2,2,2,2,2,2,2,0,0,0,0,0],
		[0,0,0,2,2,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,0,2,0,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,0,2,0,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,0,2,0,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,0,2,0,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,0,2,0,0,0,0,0,0,2,0,0,0,0,0],
	],
	mohawk: [
		[0,0,0,0,0,0,2,2,2,0,0,0,0,0,0,0],
		[0,0,0,0,0,2,2,2,2,2,0,0,0,0,0,0],
		[0,0,0,0,0,2,2,2,2,2,0,0,0,0,0,0],
	],
	bald: [],
	ponytail: [
		[0,0,0,0,2,2,2,2,2,2,0,0,0,0,0,0],
		[0,0,0,2,2,2,2,2,2,2,2,0,0,0,0,0],
		[0,0,0,0,0,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,0,0,0,0,0,0,0,0,0,2,0,0,0,0],
		[0,0,0,0,0,0,0,0,0,0,0,2,0,0,0,0],
		[0,0,0,0,0,0,0,0,0,0,0,2,0,0,0,0],
	],
	curly: [
		[0,0,0,2,0,2,0,2,0,2,0,2,0,0,0,0],
		[0,0,2,0,2,2,2,2,2,2,0,2,0,0,0,0],
		[0,0,2,2,2,0,0,0,0,0,2,2,0,0,0,0],
		[0,0,2,0,0,0,0,0,0,0,0,2,0,0,0,0],
	],
	spiky: [
		[0,0,0,2,0,0,2,0,2,0,0,2,0,0,0,0],
		[0,0,0,0,2,2,2,2,2,2,2,0,0,0,0,0],
		[0,0,0,2,2,2,2,2,2,2,2,2,0,0,0,0],
		[0,0,0,2,2,0,0,0,0,0,2,2,0,0,0,0],
	],
	braided: [
		[0,0,0,0,2,2,2,2,2,2,0,0,0,0,0,0],
		[0,0,0,2,2,2,2,2,2,2,2,0,0,0,0,0],
		[0,0,0,2,2,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,2,0,0,0,0,0,0,0,0,2,0,0,0,0],
		[0,0,0,2,0,0,0,0,0,2,0,0,0,0,0,0],
		[0,0,2,0,0,0,0,0,0,0,2,0,0,0,0,0],
		[0,0,0,2,0,0,0,0,0,2,0,0,0,0,0,0],
	],
}

// Simple weapon shapes (drawn to the right of body at row 8-12)
const WEAPON_TYPES = {
	club: [[0,0,0,0,0,0,0,0,0,0,0,0,4,4,0,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,4,0,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,4,0,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,4,0,0]],
	sword: [[0,0,0,0,0,0,0,0,0,0,0,0,0,4,4,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,4,0,0],[0,0,0,0,0,0,0,0,0,0,0,0,4,4,4,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,4,0,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,4,0,0]],
	gun: [[0,0,0,0,0,0,0,0,0,0,0,4,4,4,4,0],[0,0,0,0,0,0,0,0,0,0,0,4,0,0,0,0],[0,0,0,0,0,0,0,0,0,0,0,4,4,0,0,0]],
	staff: [[0,0,0,0,0,0,0,0,0,0,0,0,0,0,4,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,0,4,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,0,4,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,0,4,0],[0,0,0,0,0,0,0,0,0,0,0,0,0,0,4,0]],
	none: [],
}

// Headgear drawn at top rows
const HEADGEAR_TYPES = {
	helmet: [[0,0,0,7,6,6,6,6,6,6,6,7,0,0,0,0],[0,0,0,6,6,6,6,6,6,6,6,6,0,0,0,0]],
	crown: [[0,0,0,0,6,0,6,0,6,0,6,0,0,0,0,0],[0,0,0,0,6,6,6,6,6,6,6,0,0,0,0,0]],
	hat: [[0,0,0,6,6,6,6,6,6,6,6,6,0,0,0,0],[0,0,0,0,0,6,6,6,6,6,0,0,0,0,0,0]],
	visor: [[0,0,0,0,6,6,6,6,6,6,6,0,0,0,0,0]],
	hood: [[0,0,0,6,6,6,6,6,6,6,6,6,0,0,0,0],[0,0,6,6,0,0,0,0,0,0,0,6,6,0,0,0]],
	none: [],
}

// Map equipment item keys to weapon/headgear visual types
function getWeaponType(itemKey) {
	if (!itemKey) return 'none'
	if (itemKey.includes('club') || itemKey.includes('axe') || itemKey.includes('mace') || itemKey.includes('wrench')) return 'club'
	if (itemKey.includes('sword') || itemKey.includes('rapier') || itemKey.includes('blade') || itemKey.includes('dagger') || itemKey.includes('longsword')) return 'sword'
	if (itemKey.includes('pistol') || itemKey.includes('rifle') || itemKey.includes('musket') || itemKey.includes('revolver') || itemKey.includes('blaster') || itemKey.includes('cannon')) return 'gun'
	if (itemKey.includes('spear') || itemKey.includes('staff') || itemKey.includes('quill')) return 'staff'
	return 'sword'
}

function getHeadgearType(itemKey) {
	if (!itemKey) return 'none'
	if (itemKey.includes('helmet') || itemKey.includes('helm')) return 'helmet'
	if (itemKey.includes('crown')) return 'crown'
	if (itemKey.includes('hat') || itemKey.includes('cap')) return 'hat'
	if (itemKey.includes('visor') || itemKey.includes('headset') || itemKey.includes('goggles')) return 'visor'
	if (itemKey.includes('hood') || itemKey.includes('headband')) return 'hood'
	return 'helmet'
}

// Age-based clothing colors
const AGE_CLOTHING_COLORS = {
	stone: '#8b7355', bronze: '#cd7f32', iron: '#71706e',
	medieval: '#8b4513', renaissance: '#daa520', industrial: '#696969',
	modern: '#2c3e50', digital: '#00ced1', space: '#9370db',
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
		ageKey: { type: String, default: 'stone' },
		size: { type: Number, default: 128 },
	},

	watch: {
		skinTone() { this.render() },
		hairStyle() { this.render() },
		hairColor() { this.render() },
		bodyType() { this.render() },
		equippedWeapon() { this.render() },
		equippedHeadgear() { this.render() },
		ageKey() { this.render() },
	},

	mounted() {
		this.render()
	},

	methods: {
		render() {
			const canvas = this.$refs.canvas
			if (!canvas) return
			const ctx = canvas.getContext('2d')
			const scale = this.size / 16
			ctx.clearRect(0, 0, this.size, this.size)
			ctx.imageSmoothingEnabled = false

			const skinColor = SKIN_TONES[this.skinTone] || SKIN_TONES['3']
			const hairCol = HAIR_COLORS[this.hairColor] || HAIR_COLORS.brown
			const clothingColor = AGE_CLOTHING_COLORS[this.ageKey] || '#8b7355'
			const weaponColor = '#a0a0a0'
			const headgearColor = AGE_CLOTHING_COLORS[this.ageKey] || '#8b7355'
			const outlineColor = '#2c2c2c'

			const colorMap = {
				0: null,
				1: skinColor,
				2: hairCol,
				3: clothingColor,
				4: weaponColor,
				5: '#c0a060',
				6: headgearColor,
				7: outlineColor,
			}

			// Draw body
			const body = BODY[this.bodyType] || BODY.default
			this.drawSprite(ctx, body, colorMap, scale, 0, 0)

			// Draw hair
			const hair = HAIR_STYLES[this.hairStyle] || HAIR_STYLES.short
			if (hair.length > 0) {
				// Hair starts above head, offset depends on style
				const hairOffset = this.hairStyle === 'curly' || this.hairStyle === 'spiky' ? -1 : 0
				this.drawSprite(ctx, hair, colorMap, scale, 0, hairOffset)
			}

			// Draw weapon
			const wType = getWeaponType(this.equippedWeapon)
			const weaponSprite = WEAPON_TYPES[wType] || []
			if (weaponSprite.length > 0) {
				this.drawSprite(ctx, weaponSprite, colorMap, scale, 0, 7)
			}

			// Draw headgear
			const hType = getHeadgearType(this.equippedHeadgear)
			const headgearSprite = HEADGEAR_TYPES[hType] || []
			if (headgearSprite.length > 0) {
				const hOffset = hType === 'crown' ? -1 : -2
				this.drawSprite(ctx, headgearSprite, colorMap, scale, 0, hOffset)
			}
		},

		drawSprite(ctx, sprite, colorMap, scale, offsetX, offsetY) {
			for (let y = 0; y < sprite.length; y++) {
				for (let x = 0; x < sprite[y].length; x++) {
					const colorIdx = sprite[y][x]
					const color = colorMap[colorIdx]
					if (color) {
						ctx.fillStyle = color
						ctx.fillRect((x + offsetX) * scale, (y + offsetY) * scale, scale, scale)
					}
				}
			}
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
