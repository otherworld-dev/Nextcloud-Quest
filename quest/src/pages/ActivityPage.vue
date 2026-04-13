<template>
	<div class="activity-page">
		<div class="page-header">
			<h1 class="page-title">Activity</h1>
			<p class="page-subtitle">Your complete quest history.</p>
		</div>

		<!-- Filter tabs -->
		<div class="filter-tabs">
			<button
				v-for="f in filters"
				:key="f.value"
				class="filter-btn"
				:class="{ active: filter === f.value }"
				@click="filter = f.value"
			>{{ f.label }}</button>
		</div>

		<!-- Loading -->
		<div v-if="loading" class="loading-state">
			<div class="spinner" />
		</div>

		<!-- Timeline -->
		<div v-else-if="filteredEvents.length > 0" class="timeline">
			<div
				v-for="(event, idx) in filteredEvents"
				:key="idx"
				class="timeline-item"
				:class="event.type"
			>
				<div class="timeline-icon">
					<span v-if="event.icon === 'task'">✅</span>
					<span v-else-if="event.icon === 'battle'">⚔️</span>
					<span v-else-if="event.icon === 'boss'">💀</span>
					<span v-else-if="event.icon === 'treasure'">📦</span>
					<span v-else-if="event.icon === 'event'">📜</span>
					<span v-else-if="event.icon === 'achievement'">🏆</span>
					<span v-else-if="event.icon === 'challenge'">🎯</span>
					<span v-else>📋</span>
				</div>
				<div class="timeline-body">
					<span class="timeline-title">{{ event.title }}</span>
					<span class="timeline-detail">{{ event.detail }}</span>
				</div>
				<span class="timeline-time">{{ formatTime(event.timestamp) }}</span>
			</div>
		</div>

		<!-- Empty -->
		<div v-else class="empty-state">
			<div class="empty-icon">📋</div>
			<div class="empty-title">No activity yet</div>
			<div class="empty-desc">Complete tasks to start building your history!</div>
		</div>

		<!-- Load more -->
		<div v-if="hasMore && !loading" class="load-more">
			<button class="btn" @click="loadMore">Load More</button>
		</div>
	</div>
</template>

<script>
import api from '../services/api'

export default {
	name: 'ActivityPage',

	data() {
		return {
			events: [],
			loading: false,
			filter: 'all',
			offset: 0,
			total: 0,
			filters: [
				{ value: 'all', label: 'All' },
				{ value: 'task', label: 'Tasks' },
				{ value: 'journey', label: 'Journey' },
				{ value: 'achievement', label: 'Achievements' },
				{ value: 'challenge', label: 'Challenges' },
			],
		}
	},

	computed: {
		filteredEvents() {
			if (this.filter === 'all') return this.events
			return this.events.filter(e => e.type === this.filter)
		},

		hasMore() {
			return this.events.length < this.total
		},
	},

	mounted() {
		this.loadFeed()
	},

	methods: {
		async loadFeed() {
			this.loading = true
			try {
				const response = await api.getActivityFeed(50, 0)
				if (response.status === 'success') {
					this.events = response.data || []
					this.total = response.total || 0
					this.offset = this.events.length
				}
			} catch (e) {
				console.error('Failed to load activity:', e)
			} finally {
				this.loading = false
			}
		},

		async loadMore() {
			try {
				const response = await api.getActivityFeed(30, this.offset)
				if (response.status === 'success') {
					this.events.push(...(response.data || []))
					this.offset = this.events.length
				}
			} catch (e) {
				console.error('Failed to load more:', e)
			}
		},

		formatTime(dateStr) {
			if (!dateStr) return ''
			const d = new Date(dateStr)
			const now = new Date()
			const diff = now - d
			if (diff < 60000) return 'Just now'
			if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago'
			if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago'
			if (diff < 172800000) return 'Yesterday'
			return d.toLocaleDateString()
		},
	},
}
</script>

<style scoped>
.activity-page { padding: 16px 8px; max-width: 800px; }

.page-header { margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border); }
.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

/* Filters */
.filter-tabs { display: flex; gap: 4px; margin-bottom: 20px; }
.filter-btn {
	padding: 6px 14px; border: 1px solid var(--color-border); border-radius: 20px;
	background: var(--color-main-background); color: var(--color-main-text);
	cursor: pointer; font-size: var(--font-size-small); transition: all var(--transition-fast);
}
.filter-btn.active { background: var(--color-primary-element, #0082c9); color: white; border-color: transparent; }

/* Timeline */
.timeline { display: flex; flex-direction: column; gap: 2px; }

.timeline-item {
	display: flex; align-items: center; gap: 12px; padding: 10px 14px;
	background: var(--color-main-background); border: 1px solid var(--color-border);
	border-radius: var(--radius-medium); transition: background var(--transition-fast);
}
.timeline-item:hover { background: var(--color-background-hover); }

/* Type borders */
.timeline-item.task { border-left: 3px solid var(--quest-success); }
.timeline-item.journey { border-left: 3px solid #ff9800; }
.timeline-item.achievement { border-left: 3px solid #9c27b0; }
.timeline-item.challenge { border-left: 3px solid var(--color-primary-element, #0082c9); }

.timeline-icon { font-size: 18px; flex-shrink: 0; width: 24px; text-align: center; }

.timeline-body { flex: 1; min-width: 0; }
.timeline-title {
	display: block; font-size: var(--font-size-small); font-weight: 600; color: var(--color-main-text);
	white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.timeline-detail {
	display: block; font-size: 12px; color: var(--color-text-light);
}

.timeline-time { font-size: 11px; color: var(--color-text-light); flex-shrink: 0; white-space: nowrap; }

/* Load more */
.load-more { text-align: center; margin-top: 16px; }
.btn {
	padding: 8px 20px; border: 1px solid var(--color-border); border-radius: var(--radius-medium);
	background: var(--color-main-background); color: var(--color-main-text); cursor: pointer;
}
.btn:hover { background: var(--color-background-hover); }

/* Empty/loading */
.empty-state { text-align: center; padding: 48px 20px; }
.empty-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
.empty-title { font-size: var(--font-size-large); font-weight: 600; color: var(--color-main-text); margin-bottom: 6px; }
.empty-desc { color: var(--color-text-light); }
.loading-state { text-align: center; padding: 48px; }
.spinner {
	width: 32px; height: 32px; border: 3px solid var(--color-border);
	border-top-color: var(--color-primary-element, #0082c9); border-radius: 50%;
	animation: spin 0.8s linear infinite; margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
