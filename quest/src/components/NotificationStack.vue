<template>
	<div class="notification-stack">
		<transition-group name="notification" tag="div">
			<div
				v-for="notif in notifications"
				:key="notif.id"
				class="notification-toast"
				:class="notif.type"
				@click="dismiss(notif.id)"
			>
				<div class="notification-icon">
					<span v-if="notif.type === 'achievement'">🏆</span>
					<span v-else-if="notif.type === 'level_up'">🎉</span>
					<span v-else-if="notif.type === 'xp_gain'">✨</span>
					<span v-else-if="notif.type === 'epic_complete'">🏆</span>
					<span v-else>🔔</span>
				</div>
				<div class="notification-body">
					<div class="notification-title">{{ notif.title }}</div>
					<div v-if="notif.message" class="notification-message">{{ notif.message }}</div>
				</div>
				<div class="notification-progress" v-if="notif.type === 'xp_gain'" />
			</div>
		</transition-group>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

export default {
	name: 'NotificationStack',

	computed: {
		...mapState('quest', ['notifications']),
	},

	watch: {
		notifications(newVal, oldVal) {
			if (newVal.length > oldVal.length) {
				const latest = newVal[newVal.length - 1]
				const delay = latest.type === 'xp_gain' ? 2500 : 4000
				setTimeout(() => {
					this.dismissNotification(latest.id)
				}, delay)
			}
		},
	},

	methods: {
		...mapMutations('quest', ['dismissNotification']),

		dismiss(id) {
			this.dismissNotification(id)
		},
	},
}
</script>

<style scoped>
.notification-stack {
	position: fixed;
	top: 60px;
	right: 20px;
	z-index: var(--z-notification);
	display: flex;
	flex-direction: column;
	gap: 8px;
	pointer-events: none;
}

.notification-toast {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 16px;
	background: var(--color-main-background);
	border-radius: var(--radius-large);
	box-shadow: var(--shadow-xl);
	border-left: 4px solid var(--color-primary-element, #0082c9);
	min-width: 260px;
	max-width: 360px;
	cursor: pointer;
	pointer-events: auto;
	position: relative;
	overflow: hidden;
}

.notification-toast.achievement {
	border-left-color: #ff9800;
}

.notification-toast.level_up {
	border-left-color: var(--color-success, #46ba61);
}

.notification-toast.xp_gain {
	border-left-color: var(--color-primary-element, #0082c9);
}

.notification-toast.epic_complete {
	border-left-color: #ff9800;
	background: linear-gradient(135deg, var(--color-main-background), rgba(255, 152, 0, 0.05));
}

.notification-icon {
	font-size: 22px;
	flex-shrink: 0;
}

.notification-body {
	flex: 1;
	min-width: 0;
}

.notification-title {
	font-weight: 700;
	font-size: var(--font-size-normal);
	color: var(--color-main-text);
}

.notification-message {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	margin-top: 1px;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

/* Auto-dismiss progress bar */
.notification-progress {
	position: absolute;
	bottom: 0;
	left: 0;
	height: 3px;
	background: var(--color-primary-element, #0082c9);
	animation: shrink 2.5s linear forwards;
}

@keyframes shrink {
	from { width: 100%; }
	to { width: 0%; }
}

.notification-toast.achievement .notification-progress {
	background: #ff9800;
	animation-duration: 4s;
}

.notification-toast.level_up .notification-progress {
	background: var(--color-success, #46ba61);
	animation-duration: 4s;
}

/* Transitions */
.notification-enter-active {
	transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.notification-leave-active {
	transition: all 0.25s ease-in;
}

.notification-enter {
	opacity: 0;
	transform: translateX(80px) scale(0.8);
}

.notification-leave-to {
	opacity: 0;
	transform: translateX(80px);
}
</style>
