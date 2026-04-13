# Plan v001 — Pixel Art Avatar Builder

## Context

The character system has 66 items across 9 ages and 4 equipment slots, but the avatar is just initials in a circle. This plan adds a programmatically generated pixel art avatar with customizable appearance and equipment-based sprite layers.

## Approach: Option A — Programmatic Sprite Generation

Generate simple pixel art sprites using code (canvas/SVG pixel grids). Single-color silhouettes with basic shapes — not hand-drawn pixel art, but functional and shippable now.

## Character Customization Options

**Base appearance (chosen by user, editable anytime):**
- Skin tone: 6 options (light → dark)
- Hair style: 8 options (short, long, mohawk, bald, ponytail, curly, spiky, braided)
- Hair color: 8 options (black, brown, blonde, red, gray, white, blue, green)
- Body type: 2 options (default, broad)

**Equipment layers (driven by equipped items):**
- Clothing layer — changes based on equipped clothing item
- Weapon layer — rendered to side based on equipped weapon
- Accessory layer — overlay based on equipped accessory
- Headgear layer — on top based on equipped headgear

**Age theming:**
- Art style/palette shifts per age (rougher in Stone, sleek in Space)
- Base character identity (skin, hair) carries through all ages

## Layer Composition

Bottom to top:
1. Body (skin tone + body type)
2. Hair (style + color)
3. Clothing (equipped item or age default)
4. Weapon (equipped or none)
5. Accessory (equipped or none)
6. Headgear (equipped or none)

Rendered at 64x64 native, displayed at various sizes (128px character page, 48px sidebar, 32px inline).

## Implementation

### Backend Changes

**Modify `ncquest_users` table** — add columns:
- `avatar_skin_tone` (string, default '3')
- `avatar_hair_style` (string, default 'short')
- `avatar_hair_color` (string, default 'brown')
- `avatar_body_type` (string, default 'default')

**New migration**: `Version1019Date20260413120000.php`

**Modify CharacterService** — add `getAvatarConfig()` and `updateAvatarConfig()` methods

**Modify CharacterController** — add `GET /api/character/avatar` and `PUT /api/character/avatar` endpoints

### Frontend: PixelAvatar Component

**New file**: `src/components/PixelAvatar.vue`

A Vue component that renders the avatar using an HTML5 `<canvas>` element:
- Props: `skinTone`, `hairStyle`, `hairColor`, `bodyType`, `equipment` (object with slot keys), `size` (32/48/64/128)
- Draws each layer as colored pixel blocks on the canvas
- Pixel grid approach: define each sprite as a 2D array of color indices
- Equipment sprites defined per-item (or per-age-default)

**Sprite data format:**
```javascript
const BODY_SPRITES = {
  default: [
    [0,0,0,1,1,0,0,0],  // row 0 (top)
    [0,0,1,1,1,1,0,0],  // row 1
    // ... 16x16 or 12x12 grid
  ]
}
```
Colors mapped from indices: 0=transparent, 1=skin, 2=hair, 3=clothing, etc.

### Frontend: Avatar Customizer

**Modify**: `src/pages/CharacterPage.vue`

Add an "Appearance" section above the equipment:
- Skin tone picker (6 colored circles)
- Hair style picker (small previews of each style)
- Hair color picker (8 colored circles)
- Body type toggle
- Live preview updates as user picks options
- Save button calls API

### Integration Points

**Sidebar** (`AppSidebar.vue`): Replace initials circle with `<PixelAvatar>` component at 48px
**Dashboard**: Show avatar in welcome banner
**Character page**: Large 128px avatar with customization controls
**Achievements/notifications**: Optional small avatar displays

## Sprite Design

Keep it minimal — 12x12 or 16x16 pixel grid per layer:
- Body: head circle (3px wide) + torso rectangle + limbs
- Hair: extends above/around head based on style
- Clothing: colored overlay on torso area
- Weapon: small shape to the right of body
- Headgear: shape above head
- Accessory: shape near neck/chest area

Color palettes per age give visual progression without redrawing sprites.

## Files to Create/Modify

- `lib/Migration/Version1019Date20260413120000.php` — avatar columns
- `src/components/PixelAvatar.vue` — canvas renderer + sprite data
- `src/pages/CharacterPage.vue` — appearance customizer UI
- `src/components/AppSidebar.vue` — use PixelAvatar instead of initials
- `lib/Service/CharacterService.php` — avatar config methods
- `lib/Controller/CharacterController.php` — avatar API endpoints
- `src/services/api.js` — avatar API methods
- `src/store/modules/quest.js` — avatar state

## Verification

1. Navigate to Character page → see pixel avatar
2. Change skin tone → avatar updates live
3. Change hair → avatar updates
4. Equip item → corresponding layer changes on avatar
5. Sidebar shows small avatar
6. Save appearance → persists across page reload

Created: 2026-04-13T00:03:31Z
