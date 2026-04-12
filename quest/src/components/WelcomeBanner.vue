<template>
	<div v-if="visible" class="welcome-banner">
		<button class="welcome-close" @click="dismiss">&times;</button>
		<div class="welcome-header">
			<div class="welcome-icon">&#x1F3AE;</div>
			<div>
				<h2 class="welcome-title">Welcome to Quest!</h2>
				<p class="welcome-desc">Turn your task management into an RPG adventure. Complete tasks, earn XP, level up, and unlock achievements.</p>
			</div>
		</div>

		<div class="welcome-steps">
			<div class="step" :class="{ done: completedTasks > 0 }">
				<div class="step-num">{{ completedTasks > 0 ? '&#x2713;' : '1' }}</div>
				<div class="step-body">
					<div class="step-title">Complete your first task</div>
					<div class="step-desc">Tick off a task from your task lists below to earn XP.</div>
				</div>
			</div>
			<div class="step" :class="{ done: currentStreak > 0 }">
				<div class="step-num">{{ currentStreak > 0 ? '&#x2713;' : '2' }}</div>
				<div class="step-body">
					<div class="step-title">Start a streak</div>
					<div class="step-desc">Complete tasks on consecutive days to build your streak multiplier.</div>
				</div>
			</div>
			<div class="step" :class="{ done: unlockedCount > 0 }">
				<div class="step-num">{{ unlockedCount > 0 ? '&#x2713;' : '3' }}</div>
				<div class="step-body">
					<div class="step-title">Unlock an achievement</div>
					<div class="step-desc">There are 946 achievements across 19 categories. Your first one is just a task away!</div>
				</div>
			</div>
			<div class="step" :class="{ done: level > 1 }">
				<div class="step-num">{{ level > 1 ? '&#x2713;' : '4' }}</div>
				<div class="step-body">
					<div class="step-title">Level up</div>
					<div class="step-desc">Earn enough XP to reach Level 2 and unlock your rank title.</div>
				</div>
			</div>
		</div>

		<div v-if="allDone" class="welcome-complete">
			<span>&#x1F389;</span> You've completed the basics! Keep going to unlock new ages, equipment, and rare achievements.
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex'

export default {
	name: 'WelcomeBanner',

	data() {
		return {
			dismissed: false,
		}
	},

	computed: {
		...mapState('quest', ['stats']),

		completedTasks() {
			return this.stats.total_tasks || 0
		},

		currentStreak() {
			return this.stats.streak.current_streak || 0
		},

		unlockedCount() {
			return this.stats.achievements.unlocked || 0
		},

		level() {
			return this.stats.level.level || 1
		},

		allDone() {
			return this.completedTasks > 0 && this.currentStreak > 0 && this.unlockedCount > 0 && this.level > 1
		},

		visible() {
			if (this.dismissed) return false
			// Show until user has reached level 5 or dismissed
			return this.level < 5
		},
	},

	methods: {
		dismiss() {
			this.dismissed = true
		},
	},
}
</script>

<style scoped>
.welcome-banner {
	background: linear-gradient(135deg, var(--color-primary-element, #0082c9), color-mix(in srgb, var(--color-primary-element, #0082c9) 70%, black));
	border-radius: var(--radius-large);
	padding: 24px 28px;
	margin-bottom: 24px;
	color: white;
	position: relative;
}

.welcome-close {
	position: absolute;
	top: 12px;
	right: 16px;
	background: none;
	border: none;
	color: rgba(255, 255, 255, 0.6);
	font-size: 20px;
	cursor: pointer;
}

.welcome-close:hover {
	color: white;
}

.welcome-header {
	display: flex;
	align-items: flex-start;
	gap: 16px;
	margin-bottom: 20px;
}

.welcome-icon {
	font-size: 36px;
	flex-shrink: 0;
}

.welcome-title {
	font-size: var(--font-size-xxlarge);
	font-weight: 700;
	margin: 0 0 4px;
}

.welcome-desc {
	font-size: var(--font-size-normal);
	opacity: 0.85;
	margin: 0;
	line-height: 1.4;
}

.welcome-steps {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 12px;
}

.step {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	padding: 12px;
	background: rgba(255, 255, 255, 0.1);
	border-radius: var(--radius-medium);
	transition: background var(--transition-fast);
}

.step.done {
	background: rgba(255, 255, 255, 0.2);
}

.step-num {
	width: 28px;
	height: 28px;
	border-radius: 50%;
	background: rgba(255, 255, 255, 0.2);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 13px;
	font-weight: 700;
	flex-shrink: 0;
}

.step.done .step-num {
	background: rgba(70, 186, 97, 0.8);
}

.step-title {
	font-size: var(--font-size-small);
	font-weight: 600;
	margin-bottom: 2px;
}

.step-desc {
	font-size: 12px;
	opacity: 0.75;
	line-height: 1.3;
}

.welcome-complete {
	margin-top: 16px;
	padding: 10px 14px;
	background: rgba(70, 186, 97, 0.2);
	border-radius: var(--radius-medium);
	font-size: var(--font-size-small);
	font-weight: 500;
}

@media (max-width: 900px) {
	.welcome-steps {
		grid-template-columns: repeat(2, 1fr);
	}
}

@media (max-width: 600px) {
	.welcome-steps {
		grid-template-columns: 1fr;
	}
}
</style>
