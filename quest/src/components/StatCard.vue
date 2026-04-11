<template>
	<div class="stat-card" :class="variant">
		<div class="stat-card-icon">
			<slot name="icon">{{ icon }}</slot>
		</div>
		<div class="stat-card-label">{{ label }}</div>
		<div class="stat-card-value">{{ formattedValue }}</div>
		<div v-if="subtitle" class="stat-card-subtitle">{{ subtitle }}</div>
	</div>
</template>

<script>
export default {
	name: 'StatCard',

	props: {
		icon: { type: String, default: '' },
		label: { type: String, required: true },
		value: { type: [String, Number], default: 0 },
		subtitle: { type: String, default: '' },
		variant: { type: String, default: '' },
	},

	computed: {
		formattedValue() {
			if (typeof this.value === 'number' && this.value >= 1000) {
				return new Intl.NumberFormat().format(this.value)
			}
			return this.value
		},
	},
}
</script>

<style scoped>
.stat-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 20px 16px;
	text-align: center;
	transition: transform var(--transition-fast), box-shadow var(--transition-fast), border-color var(--transition-fast);
	position: relative;
	overflow: hidden;
}

.stat-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 3px;
	background: linear-gradient(90deg, var(--color-primary), var(--color-primary-dark, #005a8c));
	opacity: 0;
	transition: opacity var(--transition-fast);
}

.stat-card:hover {
	transform: translateY(-3px);
	box-shadow: var(--shadow-lg);
	border-color: var(--color-primary);
}

.stat-card:hover::before {
	opacity: 1;
}

.stat-card-icon {
	font-size: 32px;
	margin-bottom: 10px;
	line-height: 1;
}

.stat-card-label {
	font-size: 11px;
	color: var(--color-text-light);
	margin-bottom: 6px;
	text-transform: uppercase;
	letter-spacing: 1px;
	font-weight: 600;
}

.stat-card-value {
	font-size: 28px;
	font-weight: 700;
	color: var(--color-main-text);
	line-height: 1.1;
}

.stat-card-subtitle {
	font-size: var(--font-size-small);
	color: var(--color-text-lighter);
	margin-top: 6px;
}
</style>
