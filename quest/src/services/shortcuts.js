/**
 * Keyboard shortcuts for Quest navigation and actions.
 * Registered once in main.js, listens globally.
 */

export function registerShortcuts(store) {
	const pages = {
		d: 'dashboard',
		q: 'quests',
		a: 'achievements',
		c: 'character',
		j: 'adventure',
		l: 'leaderboard',
		f: 'activity',
		s: 'settings',
	}

	const pageUrls = {
		dashboard: '/index.php/apps/quest/',
		quests: '/index.php/apps/quest/quests',
		achievements: '/index.php/apps/quest/achievements',
		character: '/index.php/apps/quest/character',
		adventure: '/index.php/apps/quest/adventure',
		leaderboard: '/index.php/apps/quest/leaderboard',
		activity: '/index.php/apps/quest/activity',
		settings: '/index.php/apps/quest/settings',
	}

	document.addEventListener('keydown', (e) => {
		// Don't trigger when typing in inputs
		if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return
		if (e.target.isContentEditable) return

		// ? — show shortcut help
		if (e.key === '?' && !e.ctrlKey && !e.altKey) {
			e.preventDefault()
			toggleHelp()
			return
		}

		// Alt+key — navigate to page
		if (e.altKey && !e.ctrlKey && !e.shiftKey) {
			const page = pages[e.key.toLowerCase()]
			if (page) {
				e.preventDefault()
				store.commit('quest/setActivePage', page)
				window.history.pushState({}, '', pageUrls[page])
			}
		}
	})
}

let helpVisible = false
let helpEl = null

function toggleHelp() {
	if (helpVisible && helpEl) {
		helpEl.remove()
		helpEl = null
		helpVisible = false
		return
	}

	helpEl = document.createElement('div')
	helpEl.innerHTML = `
		<div style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center" onclick="this.remove()">
			<div style="background:var(--color-main-background,#1a1a1a);border:1px solid var(--color-border,#333);border-radius:12px;padding:28px 32px;max-width:420px;color:var(--color-main-text,#eee)" onclick="event.stopPropagation()">
				<h2 style="margin:0 0 16px;font-size:20px;font-weight:700">Keyboard Shortcuts</h2>
				<div style="display:grid;grid-template-columns:80px 1fr;gap:6px 16px;font-size:14px">
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+D</kbd><span>Dashboard</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+Q</kbd><span>Quests</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+A</kbd><span>Achievements</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+C</kbd><span>Character</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+J</kbd><span>Adventure (Journey)</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+L</kbd><span>Leaderboard</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+F</kbd><span>Activity Feed</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">Alt+S</kbd><span>Settings</span>
					<kbd style="background:var(--color-background-dark,#333);padding:2px 8px;border-radius:4px;text-align:center;font-family:monospace">?</kbd><span>This help</span>
				</div>
				<p style="margin:16px 0 0;font-size:12px;color:var(--color-text-light,#999)">Press ? or click outside to close</p>
			</div>
		</div>
	`
	document.body.appendChild(helpEl)
	helpVisible = true

	// Close on escape
	const close = (e) => {
		if (e.key === 'Escape') {
			helpEl?.remove()
			helpEl = null
			helpVisible = false
			document.removeEventListener('keydown', close)
		}
	}
	document.addEventListener('keydown', close)
}
