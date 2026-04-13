<template>
	<div class="epic-card" :class="[epic.tier, { completed: epic.status === 'completed' }]" :style="{ '--epic-color': epic.color || '#0082c9' }">
		<div class="epic-header">
			<span class="epic-emoji">{{ epic.emoji || '\uD83D\uDCDC' }}</span>
			<div class="epic-title-group">
				<h3 class="epic-title">{{ epic.title }}</h3>
				<span class="tier-badge" :class="epic.tier">{{ epic.tier }}</span>
			</div>
			<div class="epic-actions" v-if="!epic.completed_at">
				<button class="action-btn" @click.stop="$emit('edit', epic)" title="Edit">&#x270F;&#xFE0F;</button>
				<button class="action-btn danger" @click.stop="$emit('delete', epic)" title="Delete">&times;</button>
			</div>
		</div>

		<p v-if="epic.description" class="epic-desc">{{ epic.description }}</p>

		<div class="epic-progress">
			<div class="progress-bar">
				<div class="progress-fill" :style="{ width: epic.progress_percentage + '%' }" />
			</div>
			<div class="progress-stats">
				<span>{{ epic.completed_tasks }}/{{ epic.total_tasks }} tasks</span>
				<span>{{ Math.round(epic.progress_percentage) }}%</span>
			</div>
		</div>

		<div class="epic-xp">
			<span class="xp-earned">{{ epic.total_xp_earned }} XP earned</span>
			<span v-if="epic.bonus_xp_awarded" class="xp-bonus">+{{ epic.bonus_xp_awarded }} bonus</span>
		</div>

		<!-- Task list (expandable) -->
		<div v-if="expanded && epic.tasks" class="epic-tasks">
			<div v-for="task in epic.tasks" :key="task.id" class="epic-task" :class="{ done: task.is_completed }">
				<span class="task-check">{{ task.is_completed ? '\u2705' : '\u2B1C' }}</span>
				<span class="task-name">{{ task.task_title || 'Untitled' }}</span>
				<span v-if="task.xp_earned" class="task-xp">+{{ task.xp_earned }}</span>
			</div>
		</div>

		<button class="expand-btn" @click.stop="expanded = !expanded">
			{{ expanded ? 'Hide tasks' : 'Show tasks (' + epic.total_tasks + ')' }}
		</button>

		<div v-if="epic.status === 'completed'" class="complete-badge">COMPLETE</div>
	</div>
</template>

<script>
export default {
	name: 'EpicCard',
	props: {
		epic: { type: Object, required: true },
	},
	data() {
		return { expanded: false }
	},
}
</script>

<style scoped>
.epic-card {
	background: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--radius-large);
	padding: 20px;
	border-top: 4px solid var(--epic-color);
	transition: box-shadow var(--transition-fast);
	position: relative;
}

.epic-card:hover { box-shadow: var(--shadow-md); }

.epic-card.completed { opacity: 0.75; }
.epic-card.rare { border-top-color: #2196f3; }
.epic-card.epic { border-top-color: #9c27b0; }
.epic-card.legendary { border-top-color: #ff9800; }

.epic-header {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	margin-bottom: 8px;
}

.epic-emoji { font-size: 28px; flex-shrink: 0; }

.epic-title-group { flex: 1; min-width: 0; }
.epic-title { font-size: var(--font-size-large); font-weight: 700; color: var(--color-main-text); margin: 0; }

.tier-badge {
	display: inline-block;
	font-size: 9px;
	padding: 2px 8px;
	border-radius: 10px;
	font-weight: 700;
	text-transform: uppercase;
	color: white;
	margin-top: 4px;
}
.tier-badge.common { background: #9e9e9e; }
.tier-badge.uncommon { background: #4caf50; }
.tier-badge.rare { background: #2196f3; }
.tier-badge.epic { background: #9c27b0; }
.tier-badge.legendary { background: #ff9800; }

.epic-actions { display: flex; gap: 4px; flex-shrink: 0; }
.action-btn {
	background: none; border: none; cursor: pointer; font-size: 14px;
	color: var(--color-text-light); padding: 2px 6px; border-radius: var(--radius-small);
}
.action-btn:hover { background: var(--color-background-hover); }
.action-btn.danger:hover { color: var(--quest-error); }

.epic-desc {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	margin: 0 0 12px;
	line-height: 1.4;
}

.epic-progress { margin-bottom: 8px; }
.progress-bar {
	height: 8px;
	background: var(--color-background-dark);
	border-radius: 4px;
	overflow: hidden;
	margin-bottom: 4px;
}
.progress-fill {
	height: 100%;
	border-radius: 4px;
	background: var(--epic-color);
	transition: width var(--transition-slow);
}
.progress-stats {
	display: flex;
	justify-content: space-between;
	font-size: 12px;
	color: var(--color-text-light);
}

.epic-xp {
	display: flex;
	gap: 8px;
	font-size: var(--font-size-small);
	margin-bottom: 8px;
}
.xp-earned { color: var(--color-text-light); }
.xp-bonus { color: var(--quest-success); font-weight: 600; }

.epic-tasks {
	border-top: 1px solid var(--color-border);
	padding-top: 8px;
	margin-bottom: 4px;
}

.epic-task {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 4px 0;
	font-size: var(--font-size-small);
}
.epic-task.done { opacity: 0.5; }
.epic-task.done .task-name { text-decoration: line-through; }
.task-check { font-size: 14px; }
.task-name { flex: 1; color: var(--color-main-text); }
.task-xp { color: var(--quest-success); font-weight: 600; font-size: 12px; }

.expand-btn {
	display: block;
	width: 100%;
	padding: 6px;
	border: none;
	background: var(--color-background-hover);
	border-radius: var(--radius-small);
	cursor: pointer;
	font-size: 12px;
	color: var(--color-text-light);
	text-align: center;
}
.expand-btn:hover { background: var(--color-background-dark); }

.complete-badge {
	position: absolute;
	top: 12px;
	right: 12px;
	background: var(--quest-success);
	color: white;
	font-size: 10px;
	font-weight: 700;
	padding: 3px 10px;
	border-radius: 10px;
	text-transform: uppercase;
}
</style>
