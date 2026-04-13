<template>
	<div class="leaderboard-page">
		<div class="page-header">
			<h1 class="page-title">Leaderboard</h1>
			<p class="page-subtitle">See how you rank against other adventurers.</p>
		</div>

		<!-- Sort tabs -->
		<div class="sort-tabs">
			<button
				v-for="s in sortOptions"
				:key="s.value"
				class="sort-tab"
				:class="{ active: sortBy === s.value }"
				@click="changeSort(s.value)"
			>{{ s.label }}</button>
		</div>

		<!-- Your rank -->
		<div class="your-rank" v-if="userRank">
			<div class="rank-number">#{{ userRank }}</div>
			<div class="rank-text">Your Rank</div>
			<div class="rank-sub">out of {{ totalUsers }} adventurers</div>
		</div>

		<!-- Loading -->
		<div v-if="loadingBoard" class="loading-state">
			<div class="spinner" />
		</div>

		<!-- Leaderboard table -->
		<div v-else-if="leaderboard.length > 0" class="board">
			<div
				v-for="(player, index) in leaderboard"
				:key="player.user_id"
				class="board-row"
				:class="{
					'is-you': player.user_id === user.uid,
					'top-1': index === 0,
					'top-2': index === 1,
					'top-3': index === 2,
				}"
			>
				<div class="board-rank">
					<span v-if="index === 0" class="medal">🥇</span>
					<span v-else-if="index === 1" class="medal">🥈</span>
					<span v-else-if="index === 2" class="medal">🥉</span>
					<span v-else class="rank-num">{{ index + 1 }}</span>
				</div>
				<div class="board-user">
					<span class="user-name">{{ player.user_id === user.uid ? 'You' : player.user_id }}</span>
					<span class="user-title">{{ player.rank_title }}</span>
				</div>
				<div class="board-stats">
					<div class="stat-pill">
						<span class="stat-label">Lv</span>
						<span class="stat-val">{{ player.level }}</span>
					</div>
					<div class="stat-pill">
						<span class="stat-label">XP</span>
						<span class="stat-val">{{ formatNum(player.lifetime_xp) }}</span>
					</div>
					<div class="stat-pill">
						<span class="stat-label">Streak</span>
						<span class="stat-val">{{ player.current_streak }}d</span>
					</div>
					<div class="stat-pill">
						<span class="stat-label">Tasks</span>
						<span class="stat-val">{{ formatNum(player.total_tasks) }}</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Empty -->
		<div v-else class="empty-state">
			<div class="empty-icon">🏆</div>
			<div class="empty-title">No adventurers yet</div>
			<div class="empty-desc">Complete some tasks to appear on the leaderboard!</div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex'
import api from '../services/api'

export default {
	name: 'LeaderboardPage',

	data() {
		return {
			sortBy: 'lifetime_xp',
			leaderboard: [],
			userRank: null,
			totalUsers: 0,
			loadingBoard: false,
			sortOptions: [
				{ value: 'lifetime_xp', label: 'Total XP' },
				{ value: 'level', label: 'Level' },
				{ value: 'current_streak', label: 'Streak' },
				{ value: 'total_tasks_completed', label: 'Tasks' },
			],
		}
	},

	computed: {
		...mapState('quest', ['user']),
	},

	mounted() {
		this.loadBoard()
	},

	methods: {
		async loadBoard() {
			this.loadingBoard = true
			try {
				const response = await api.getLeaderboard(this.sortBy, 50, 0)
				if (response.status === 'success') {
					this.leaderboard = response.data.leaderboard || []
					this.userRank = response.data.user_rank
					this.totalUsers = response.data.total_users
				}
			} catch (e) {
				console.error('Failed to load leaderboard:', e)
			} finally {
				this.loadingBoard = false
			}
		},

		changeSort(val) {
			this.sortBy = val
			this.loadBoard()
		},

		formatNum(n) {
			if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M'
			if (n >= 1000) return (n / 1000).toFixed(1) + 'K'
			return n
		},
	},
}
</script>

<style scoped>
.leaderboard-page { padding: 16px 8px; max-width: 800px; }

.page-header { margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border); }
.page-title { font-size: var(--font-size-huge); font-weight: 700; color: var(--color-main-text); margin: 0 0 4px; }
.page-subtitle { font-size: var(--font-size-normal); color: var(--color-text-light); margin: 0; }

/* Sort tabs */
.sort-tabs { display: flex; gap: 4px; margin-bottom: 20px; }
.sort-tab {
	padding: 6px 16px; border: 1px solid var(--color-border); border-radius: 20px;
	background: var(--color-main-background); color: var(--color-main-text);
	cursor: pointer; font-size: var(--font-size-small); transition: all var(--transition-fast);
}
.sort-tab.active { background: var(--color-primary-element, #0082c9); color: white; border-color: transparent; }

/* Your rank */
.your-rank {
	text-align: center; padding: 20px;
	background: var(--color-main-background); border: 1px solid var(--color-border);
	border-radius: var(--radius-large); margin-bottom: 20px;
}
.rank-number { font-size: 48px; font-weight: 700; color: var(--color-primary-element, #0082c9); }
.rank-text { font-size: var(--font-size-large); font-weight: 600; color: var(--color-main-text); }
.rank-sub { font-size: var(--font-size-small); color: var(--color-text-light); margin-top: 4px; }

/* Board */
.board { display: flex; flex-direction: column; gap: 4px; }

.board-row {
	display: flex; align-items: center; gap: 12px; padding: 12px 16px;
	background: var(--color-main-background); border: 1px solid var(--color-border);
	border-radius: var(--radius-medium); transition: background var(--transition-fast);
}
.board-row:hover { background: var(--color-background-hover); }
.board-row.is-you { border-color: var(--color-primary-element, #0082c9); background: rgba(0, 130, 201, 0.05); }
.board-row.top-1 { border-left: 3px solid #ffd700; }
.board-row.top-2 { border-left: 3px solid #c0c0c0; }
.board-row.top-3 { border-left: 3px solid #cd7f32; }

.board-rank { width: 40px; text-align: center; flex-shrink: 0; }
.medal { font-size: 24px; }
.rank-num { font-size: var(--font-size-large); font-weight: 700; color: var(--color-text-light); }

.board-user { flex: 1; min-width: 0; }
.user-name { display: block; font-size: var(--font-size-normal); font-weight: 600; color: var(--color-main-text); }
.user-title { display: block; font-size: 12px; color: var(--color-text-light); }

.board-stats { display: flex; gap: 8px; flex-shrink: 0; }
.stat-pill {
	display: flex; flex-direction: column; align-items: center;
	padding: 4px 10px; background: var(--color-background-hover); border-radius: var(--radius-small);
	min-width: 50px;
}
.stat-label { font-size: 10px; color: var(--color-text-light); text-transform: uppercase; }
.stat-val { font-size: var(--font-size-small); font-weight: 700; color: var(--color-main-text); }

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

@media (max-width: 600px) {
	.board-stats { display: none; }
}
</style>
