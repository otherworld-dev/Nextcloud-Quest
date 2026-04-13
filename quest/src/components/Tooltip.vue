<template>
	<span class="tooltip-wrap" @mouseenter="onEnter" @mouseleave="show = false">
		<slot />
		<span v-if="show" class="tooltip-box" ref="tip" :style="tipStyle">{{ text }}</span>
	</span>
</template>

<script>
export default {
	name: 'Tooltip',
	props: {
		text: { type: String, required: true },
		position: { type: String, default: 'bottom' },
	},
	data() {
		return { show: false, tipStyle: {} }
	},
	methods: {
		onEnter(e) {
			this.show = true
			this.$nextTick(() => {
				const wrap = this.$el.getBoundingClientRect()
				const style = { position: 'fixed', zIndex: 9999 }
				const pos = this.position

				if (pos === 'right') {
					style.left = (wrap.right + 8) + 'px'
					style.top = (wrap.top + wrap.height / 2) + 'px'
					style.transform = 'translateY(-50%)'
				} else if (pos === 'left') {
					style.right = (window.innerWidth - wrap.left + 8) + 'px'
					style.top = (wrap.top + wrap.height / 2) + 'px'
					style.transform = 'translateY(-50%)'
				} else if (pos === 'top') {
					style.left = (wrap.left + wrap.width / 2) + 'px'
					style.bottom = (window.innerHeight - wrap.top + 6) + 'px'
					style.transform = 'translateX(-50%)'
				} else {
					style.left = (wrap.left + wrap.width / 2) + 'px'
					style.top = (wrap.bottom + 6) + 'px'
					style.transform = 'translateX(-50%)'
				}

				this.tipStyle = style
			})
		},
	},
}
</script>

<style scoped>
.tooltip-wrap {
	position: relative;
	display: block;
	cursor: help;
}

.tooltip-box {
	padding: 8px 12px;
	background: rgba(0, 0, 0, 0.85);
	color: #fff;
	font-size: 12px;
	font-weight: 400;
	line-height: 1.4;
	border-radius: 6px;
	pointer-events: none;
	max-width: 260px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
</style>
