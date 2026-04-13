/**
 * Quest Audio Service — synthesized sound effects using Web Audio API.
 * No external audio files needed.
 */

let audioCtx = null
let enabled = true

function getCtx() {
	if (!audioCtx) {
		try {
			audioCtx = new (window.AudioContext || window.webkitAudioContext)()
		} catch (e) {
			return null
		}
	}
	return audioCtx
}

function playTone(freq, duration = 0.15, type = 'sine', volume = 0.3) {
	if (!enabled) return
	const ctx = getCtx()
	if (!ctx) return

	const osc = ctx.createOscillator()
	const gain = ctx.createGain()
	osc.connect(gain)
	gain.connect(ctx.destination)

	osc.type = type
	osc.frequency.setValueAtTime(freq, ctx.currentTime)
	gain.gain.setValueAtTime(volume, ctx.currentTime)
	gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration)

	osc.start(ctx.currentTime)
	osc.stop(ctx.currentTime + duration)
}

function playChord(freqs, duration = 0.2, type = 'sine', volume = 0.15) {
	freqs.forEach(f => playTone(f, duration, type, volume))
}

function playSequence(notes, interval = 0.1) {
	notes.forEach(([freq, dur, type], i) => {
		setTimeout(() => playTone(freq, dur || 0.15, type || 'sine', 0.25), i * interval * 1000)
	})
}

// ─── Sound Effects ────────────────────────────────────────

export function soundTaskComplete() {
	// Quick ascending ping
	playSequence([[523, 0.08], [659, 0.08], [784, 0.12]], 0.06)
}

export function soundXPGain() {
	// Soft chime
	playTone(880, 0.15, 'sine', 0.2)
}

export function soundLevelUp() {
	// Triumphant ascending fanfare
	playSequence([
		[523, 0.12], [659, 0.12], [784, 0.12], [1047, 0.25],
	], 0.1)
	setTimeout(() => playChord([1047, 1319, 1568], 0.4, 'sine', 0.12), 400)
}

export function soundAchievement() {
	// Sparkle sound — high pitched descending
	playSequence([
		[1200, 0.08], [1400, 0.08], [1600, 0.08], [1800, 0.12],
	], 0.05)
}

export function soundBattleWin() {
	// Victory stab
	playSequence([[440, 0.1], [554, 0.1], [659, 0.15]], 0.08)
	setTimeout(() => playChord([659, 831, 988], 0.3, 'sine', 0.1), 250)
}

export function soundBattleLose() {
	// Descending sad tones
	playSequence([[400, 0.15, 'triangle'], [300, 0.15, 'triangle'], [200, 0.25, 'triangle']], 0.12)
}

export function soundTreasure() {
	// Coin/chest sparkle
	playSequence([
		[1047, 0.06], [1319, 0.06], [1568, 0.06], [2093, 0.06], [1568, 0.06], [2093, 0.1],
	], 0.04)
}

export function soundBossDefeat() {
	// Epic victory
	playSequence([
		[440, 0.1], [554, 0.1], [659, 0.1], [880, 0.15],
	], 0.08)
	setTimeout(() => {
		playChord([880, 1109, 1319], 0.3, 'sine', 0.12)
		setTimeout(() => playChord([1047, 1319, 1568], 0.5, 'sine', 0.1), 300)
	}, 350)
}

export function soundChallengeComplete() {
	// Upbeat ding-ding
	playSequence([[784, 0.1], [988, 0.1], [1175, 0.15]], 0.08)
}

export function soundEpicComplete() {
	// Grand fanfare
	playSequence([
		[523, 0.12], [659, 0.12], [784, 0.12], [1047, 0.2],
	], 0.1)
	setTimeout(() => playChord([1047, 1319, 1568], 0.5, 'sine', 0.15), 450)
}

export function soundEvent() {
	// Mysterious tone
	playChord([440, 554, 659], 0.25, 'sine', 0.1)
}

// ─── Settings ────────────────────────────────────────────

export function setSoundEnabled(val) {
	enabled = !!val
}

export function isSoundEnabled() {
	return enabled
}
