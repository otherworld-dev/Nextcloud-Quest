<template>
	<div class="adventure-page">
		<div class="page-header">
			<h1 class="page-title">Adventure</h1>
			<p class="page-subtitle">Explore procedurally generated worlds as you complete tasks.</p>
		</div>

		<div class="placeholder-card">
			<div class="placeholder-icon">🗺️</div>
			<h2 class="placeholder-title">Coming Soon</h2>
			<p class="placeholder-text">
				The adventure map is being redesigned with new exploration mechanics,
				combat encounters, and treasure discoveries. Check back soon!
			</p>
			<div class="placeholder-stats">
				<div class="placeholder-stat">
					<span class="stat-icon">⚔️</span>
					<span class="stat-label">Combat</span>
				</div>
				<div class="placeholder-stat">
					<span class="stat-icon">💎</span>
					<span class="stat-label">Treasure</span>
				</div>
				<div class="placeholder-stat">
					<span class="stat-icon">🏪</span>
					<span class="stat-label">Shops</span>
				</div>
				<div class="placeholder-stat">
					<span class="stat-icon">👑</span>
					<span class="stat-label">Bosses</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'AdventurePage',
}
</script>

<style scoped>
.adventure-page { padding: 8px 0; }
.page-header { margin-bottom: 24px; }
.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

.placeholder-card {
	text-align: center;
	padding: 60px 24px;
	background: var(--color-main-background);
	border-radius: var(--radius-large);
	box-shadow: var(--shadow-sm);
	max-width: 600px;
	margin: 40px auto;
}

.placeholder-icon { font-size: 80px; margin-bottom: 16px; }
.placeholder-title { font-size: var(--font-size-xxlarge); font-weight: 700; margin: 0 0 12px; color: var(--color-main-text); }
.placeholder-text { font-size: var(--font-size-normal); color: var(--color-text-light); line-height: 1.6; margin-bottom: 32px; }

.placeholder-stats {
	display: flex; justify-content: center; gap: 32px; flex-wrap: wrap;
}
.placeholder-stat { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.stat-icon { font-size: 32px; }
.stat-label { font-size: var(--font-size-small); color: var(--color-text-lighter); font-weight: 500; }
</style>
