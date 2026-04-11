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
					<span v-else>🔔</span>
				</div>
				<div class="notification-body">
					<div class="notification-title">{{ notif.title }}</div>
					<div v-if="notif.message" class="notification-message">{{ notif.message }}</div>
				</div>
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
		notifications(newVal) {
			// Auto-dismiss after delay
			if (newVal.length > 0) {
				const latest = newVal[newVal.length - 1]
				const delay = latest.type === 'xp_gain' ? 2000 : 4000
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
	top: 20px;
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
	border-left: 4px solid var(--color-primary);
	min-width: 280px;
	max-width: 400px;
	cursor: pointer;
	pointer-events: auto;
}

.notification-toast.achievement {
	border-left-color: #ff9800;
	background: linear-gradient(135deg, var(--color-main-background), rgba(255, 152, 0, 0.05));
}

.notification-toast.level_up {
	border-left-color: var(--color-success);
	background: linear-gradient(135deg, var(--color-main-background), rgba(70, 186, 97, 0.05));
}

.notification-toast.xp_gain {
	border-left-color: var(--color-primary);
}

.notification-icon {
	font-size: 24px;
	flex-shrink: 0;
}

.notification-title {
	font-weight: 600;
	font-size: var(--font-size-normal);
	color: var(--color-main-text);
}

.notification-message {
	font-size: var(--font-size-small);
	color: var(--color-text-light);
	margin-top: 2px;
}

/* Transitions */
.notification-enter-active {
	transition: all 0.3s ease-out;
}

.notification-leave-active {
	transition: all 0.2s ease-in;
}

.notification-enter {
	opacity: 0;
	transform: translateX(60px);
}

.notification-leave-to {
	opacity: 0;
	transform: translateX(60px);
}
</style>
