/**
 * @copyright Copyright (c) 2025 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 *
 * Entry point — detects the active page from initial state and mounts App.vue.
 */

import Vue from 'vue'
import App from './App.vue'
import store from './store'
import { generateFilePath } from '@nextcloud/router'
import { getRequestToken } from '@nextcloud/auth'
import { loadState } from '@nextcloud/initial-state'

// Nextcloud compatibility
__webpack_nonce__ = btoa(getRequestToken())
__webpack_public_path__ = generateFilePath('quest', '', 'js/')

// Global translation helper
Vue.mixin({
	methods: {
		t: window.t || ((app, text) => text),
	},
})

// Load initial state from PHP controller
const initialUser = loadState('quest', 'user', {})
const config = loadState('quest', 'config', {})

// Bootstrap store
store.commit('quest/setUser', initialUser)
store.commit('quest/setActivePage', config.active_page || 'dashboard')

// Load initial data
store.dispatch('quest/loadStats')
store.dispatch('quest/loadAvatar')

// Restore sound preference
import { setSoundEnabled } from './services/audio'
const soundPref = localStorage.getItem('quest-sound-enabled')
if (soundPref !== null) {
	setSoundEnabled(soundPref !== 'false')
}

/**
 * Override Nextcloud's #content layout.
 * NC uses position:fixed, constrained width, overflow:clip, and border-radius
 * on #content. We need full-width for our own sidebar+content layout.
 * Inline styles guarantee highest specificity — no CSS battles.
 */
function overrideNextcloudLayout() {
	const content = document.getElementById('content')
	if (content) {
		content.style.cssText += ';'
			+ 'width: 100% !important;'
			+ 'max-width: 100% !important;'
			+ 'margin: 0 !important;'
			+ 'margin-top: var(--header-height, 50px) !important;'
			+ 'padding: 0 !important;'
			+ 'border-radius: 0 !important;'
			+ 'overflow: auto !important;'
			+ 'height: calc(100vh - var(--header-height, 50px)) !important;'
			+ 'background: var(--color-main-background, #ffffff) !important;'
	}

	const questApp = document.getElementById('quest-app')
	if (questApp) {
		questApp.style.cssText += ';'
			+ 'flex: 1 1 100% !important;'
			+ 'width: 100% !important;'
			+ 'min-width: 0 !important;'
			+ 'min-height: 100% !important;'
	}
}

// Map URL paths to page names
function getPageFromPath(path) {
	const segments = path.replace(/\/+$/, '').split('/')
	const last = segments[segments.length - 1]
	const pages = ['quests', 'achievements', 'character', 'adventure', 'settings']
	if (pages.includes(last)) return last
	return 'dashboard'
}

// Handle browser back/forward
window.addEventListener('popstate', () => {
	store.commit('quest/setActivePage', getPageFromPath(window.location.pathname))
})

// Mount
const mountEl = document.getElementById('quest-app')
if (mountEl) {
	overrideNextcloudLayout()

	new Vue({
		store,
		render: h => h(App),
	}).$mount(mountEl)
}
