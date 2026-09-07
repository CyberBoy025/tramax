# PROJECT_UI_DESIGN_SYSTEM — Tramax Entertainment

**Status:** Draft v1 — synthesized from two supplied visual references. No production codebase exists yet (the engineering roadmap is at Phase 0/1), so this document is the *specification* to build against once Phase 2 (UI/UX Design) starts — see [README.md](README.md).

**Assumption flagged:** the two reference images (an agency site called "Creatix" and a studio site referencing "Fluxora") are treated here as Tramax's chosen visual mood references, not as a literal brand handoff — no Tramax logo, wordmark, or approved hex values have been supplied. Colors below are a **proposed synthesized palette**, marked accordingly, pending confirmation from Tramax. This resolves the deferral noted in the proposal (§7): "premium, modern music-industry visual direction" is now being defined, per the instruction to treat later-supplied UI/UX material as authoritative once it arrives.

---

## Phase 1 — Design Analysis

### Reference 01 — "Creatix" (creative-agency style)

| Aspect | Observation |
|---|---|
| Visual personality | Bold, energetic, youthful creative-agency. Approachable rather than corporate. |
| Density | Moderate — many sections, but each has generous internal breathing room. |
| Layout | A single rounded "hero card" (nav + headline + portrait + CTA) floats inset from the viewport edge on a light ground; every section below it is full-bleed near-black. |
| Signature layout device | Extreme corner radius (superellipse-like, ~32–40px) on the hero container; pill-shaped floating navbar sits inside it. |
| Typography | Bold, rounded/geometric sans for headings, tight tracking, large scale contrast to body copy. Numbered list markers ("01", "02"…) used as eyebrow labels. |
| Color | Near-black sections (`~#0E0F0C`) alternating with an off-white hero card (`~#F5F4EF`); a single vivid chartreuse/lime accent (`~#B6FF3C`) on CTAs, tags, and the footer fill. |
| Imagery | Duotone / black-and-white treatment on supporting photography, tying images into the mono-plus-accent palette. |
| Effects | No drop shadows — separation comes from flat color contrast, not elevation. Icons are simple line-art plus decorative sparkle/star-burst accents flanking the headline. |
| Components seen | Pill nav, pill buttons, numbered service list with arrow affordances, stat strip (4-up), duotone photo pair, team grid (2×3 cards), testimonial carousel, tag marquee, big color-block footer. |

### Reference 02 — "Fluxora" (cinematic tech-studio style)

| Aspect | Observation |
|---|---|
| Visual personality | Cinematic, dramatic, editorial — a single striking portrait carries the whole hero, more "product launch" than "friendly agency." |
| Density | Tighter and more compressed than Reference 01 — content sits close to frame edges. |
| Layout | Full-bleed hero photo with a thick color-bar "frame" running down both page edges; stat chips and a partner-logo row are overlaid directly onto the photo rather than placed in a separate section. |
| Signature layout device | The vertical frame bars — a persistent poster/gatefold-sleeve motif; asymmetric two-column "About" grid below the hero rather than a centered layout. |
| Typography | Headline mixes a bold grotesque sans with an italic serif accent word in the same sentence — an expressive pairing device. Small letter-spaced "kicker" label with a square bullet precedes section headings. |
| Color | Near-black base; the entire hero photo carries a saturated warm red-orange duotone (`~#E63A1E` → amber `~#FFB020` highlights), which the frame bars echo as a literal accent — the photography *is* the brand color statement. |
| Imagery | Duotone applied to essentially every image as a system-wide rule, not a one-off treatment. |
| Effects | Minimal/sharp radius on the photo and frame bars; small-to-medium radius on stat chips and the one pill CTA button. No shadows — separation via value contrast and the frame device. |
| Components seen | Full-bleed hero with overlay stats, partner-logo row, kicker + heading pattern, portrait + stat-pair asymmetric grid, small circular social icons, pill CTA, tag marquee, closing two-line tagline. |

---

## Phase 5 — Design Decision Matrix

Neither reference is used wholesale. Each category below states which reference wins, or how the two are merged, and why — judged against what Tramax's site actually needs to do: showcase artists dramatically (favors Reference 02) while staying approachable and readable across a large public catalogue and a dense admin/artist-portal system (favors Reference 01).

| Category | Reference 01 | Reference 02 | Final decision |
|---|---|---|---|
| Headline typography | Bold rounded grotesk | Grotesk + italic serif accent word | **Hybrid** — rounded grotesk as the base voice; borrow the serif-italic accent-word technique to carry one emphasized word per headline (fits "Discover. **Develop.** Promote." directly) |
| Navigation | Floating pill nav in a rounded card | Plain bar over the hero image | **Reference 01** — reads as more welcoming for a public fan/artist-facing site |
| Hero structure | Portrait in a light card + stat strip below | Full-bleed dramatic portrait, frame bars, overlaid stats | **Reference 02, adapted** — full-bleed rotating-artist hero, but stats move to a dedicated strip (Ref 01) rather than crowding the photo, since Tramax's hero subject rotates (featured artist/release) rather than staying fixed |
| Color system | Near-black + lime accent | Near-black + red-orange accent | **Neither literally** — same dark-neutral-plus-one-saturated-accent *structure*, new synthesized accent (see below); actual hue pending Tramax brand sign-off |
| Stat presentation | Large breathing stat blocks | Small dense chips overlaid on image | **Hybrid** — breathing blocks in the dedicated stats section; compact chip style reserved for stats overlaid on artist/hero photography |
| Photography treatment | Duotone on supporting images only | Duotone as the entire system's color signature | **Reference 02 principle, Reference 01 restraint** — duotone is a system rule for editorial/story imagery, but artist photography itself stays true-color so fans recognize the artist |
| Radius | Extreme superellipse on containers, pill on buttons | Small/sharp on frames, pill on the one CTA | **Hybrid, scaled down** — pill radius for buttons/nav/tags from both; container radius kept moderate (extreme radius reads "SaaS app," not "record label") |
| Frame/border device | None | Vertical color-bar page frame | **Reference 02, optional** — reserved for editorial moments (a release page, an event poster page), not the global site chrome, since it would fight the density of catalogue and admin screens |
| Section rhythm | Generous | Tight/editorial | **Reference 01** for the public site (readability across long catalogue/news pages); tighter rhythm reserved for the admin platform and artist portal where information density matters more |
| Iconography | Line-art + decorative sparkle/star accents | Minimal functional glyphs only | **Hybrid** — functional line icons throughout; sparkle/star accents kept as a rare "featured" marker, not a constant motif |

---

## 1. Design Philosophy

**"Studio Stage."** The interface behaves like a stage: a deep, near-black ground recedes so a single accent color and full-bleed artist photography can command attention, while a rounded, confident typographic voice keeps the brand feeling like a modern record label rather than a corporate portal. Public pages breathe; the admin platform and artist portal tighten the same system for density without introducing a second visual language.

## 2. Design Principles

1. **One dark stage, one accent** — near-black is the default ground everywhere; the accent color is spent deliberately, never sprinkled.
2. **Photography leads** — artist and event imagery is the primary visual content; UI chrome stays quiet around it.
3. **Legible at a glance and at length** — generous rhythm on public pages; the same tokens tighten, not change, for dense operational screens.
4. **One emphasized word, not a shouting headline** — the serif-italic accent technique is used sparingly, on a single word per hero, never on full sentences.
5. **Consistent component geometry** — one radius scale, one shadow rule, applied the same way whether it's a release card or an admin table row.
6. **Real photography stays true-color** — duotone is a system effect for editorial moments, never applied to an artist's own likeness.
7. **Accessible by default** — every token pairing is chosen to clear WCAG AA contrast against its intended background.
8. **Seven roles, one system** — Super Admin through Partner (per the platform's RBAC model) see the same components with different data, not different UIs.

## 3. Color Tokens

Values marked **[proposed]** are a synthesized starting palette in the spirit of both references — not an approved Tramax brand color. Confirm with Tramax before Phase 2 sign-off.

| Token | Value | Usage |
|---|---|---|
| `color/bg/base` | `#0D0E0C` [proposed] | Default page background — the "stage" |
| `color/bg/raised` | `#171815` [proposed] | Cards, panels, nav bar fill |
| `color/bg/sunken` | `#0A0B09` [proposed] | Footer, code/data blocks |
| `color/bg/inverse` | `#F6F4EC` [proposed] | Light hero card variant (Ref 01 pattern), light-mode admin surfaces |
| `color/accent/primary` | `#D9542E` [proposed] | Synthesized warm ember accent — sits between Ref 02's red-orange and a more restrained tone; primary CTAs, active nav state, key stat numbers |
| `color/accent/on-accent` | `#0D0E0C` [proposed] | Text/icon color placed on top of the accent |
| `color/accent/secondary` | `#C7E86B` [proposed] | Secondary accent (a muted echo of Ref 01's lime) — used only for success/positive states and small featured tags, never competing with the primary accent |
| `color/text/primary` | `#F3F1E9` [proposed] | Headings and primary body text on dark ground |
| `color/text/secondary` | `#A6A79C` [proposed] | Supporting copy, captions, metadata |
| `color/text/muted` | `#6E6F65` [proposed] | Disabled text, placeholder text |
| `color/text/on-light` | `#111210` [proposed] | Text on `bg/inverse` |
| `color/border/default` | `#2A2B26` [proposed] | Card and input borders on dark ground |
| `color/border/strong` | `#3E4038` [proposed] | Focus rings, active dividers |
| `color/state/success` | `#7FBE5A` | Positive states (e.g. "Distributed", "Paid" statuses) |
| `color/state/warning` | `#E0A83C` | Pending states (e.g. "Processing", "Under Review") |
| `color/state/error` | `#D9483B` | Errors, destructive actions, "Rejected" statuses |
| `color/state/info` | `#5B9BD1` | Informational banners, tooltips |

**Duotone image treatment** (Reference 02 principle, applied only to editorial/story photography, never artist portraits): overlay `color/accent/primary` at ~30% multiply over a grayscale base image.

## 4. Typography Tokens

| Role | Family | Weight | Size (desktop) | Size (mobile) | Line-height | Tracking | Notes |
|---|---|---|---|---|---|---|---|
| Display | Rounded geometric sans (e.g. Cabinet Grotesk / General Sans class) | 700 | 64px | 36px | 1.05 | −0.01em | Landing hero only |
| H1 | Same as Display | 700 | 48px | 30px | 1.1 | −0.01em | One word per H1 may switch to Accent Serif italic (see below) |
| H2 | Same | 600 | 34px | 24px | 1.15 | normal | Section headings |
| H3 | Same | 600 | 24px | 20px | 1.2 | normal | Card titles, sub-sections |
| H4 | Same | 600 | 18px | 16px | 1.3 | normal | List/table group headers |
| Accent Serif | Serif italic (e.g. Fraunces Italic / Newsreader Italic) | 500 | matches surrounding heading | — | 1.1 | normal | Reference 02's single-word emphasis device — used at most once per hero/headline |
| Body Large | Body sans (e.g. Inter / Public Sans) | 400 | 18px | 16px | 1.6 | normal | Intro paragraphs |
| Body | Same | 400 | 15px | 14px | 1.6 | normal | Default running text |
| Body Small | Same | 400 | 13px | 13px | 1.5 | normal | Secondary/meta text |
| Caption | Same | 500 | 12px | 12px | 1.4 | 0.02em | Timestamps, image credits |
| Label | Same | 600 | 11px | 11px | 1.3 | 0.08em, uppercase | Eyebrow/kicker labels (Ref 02 pattern), form field labels |
| Button | Same | 600 | 14px | 14px | 1 | 0.01em | All button text |
| Numeric/Stat | A tabular-figure sans or mono (e.g. IBM Plex Mono) | 600 | matches H1–H3 per context | — | 1 | normal | Stat blocks, royalty figures, table numbers — always tabular-nums |

## 5. Spacing Tokens

Scale (px): `4, 8, 12, 16, 24, 32, 48, 64, 96, 128`

| Token | Value | Usage |
|---|---|---|
| `space/xs` | 4 | Icon-to-label gaps |
| `space/sm` | 8 | Tight inline gaps, badge padding |
| `space/md` | 16 | Default component internal padding |
| `space/lg` | 24 | Card padding, form field gaps |
| `space/xl` | 32 | Gaps between related components |
| `space/2xl` | 48 | Gaps between sub-sections |
| `space/3xl` | 64 | Section vertical padding (public site, per Reference 01's generous rhythm) |
| `space/4xl` | 96 | Major section separation on the homepage/landing pages |
| `space/5xl` | 128 | Hero top/bottom breathing room only |

**Density override for Admin Platform & Artist Portal:** halve `space/2xl` through `space/4xl` (24 / 32 / 48) to match Reference 02's tighter, data-dense rhythm — the token *names* stay the same, only the operational-surface theme redefines their values.

## 6. Layout Tokens

| Token | Value |
|---|---|
| `layout/container-max` | 1280px |
| `layout/container-padding` | `space/lg` mobile → `space/2xl` desktop |
| `layout/grid-columns` | 12 |
| `layout/grid-gutter` | `space/lg` |
| `breakpoint/mobile` | 0–639px |
| `breakpoint/tablet` | 640–1023px |
| `breakpoint/desktop` | 1024px+ |
| `layout/hero-frame` (optional, Reference 02 device) | 20px solid color-bar on left/right edges — reserved for editorial pages (a release/event page), never the global site chrome or the admin platform |

## 7. Border & Radius Tokens

| Token | Value | Usage |
|---|---|---|
| `radius/sm` | 6px | Inputs, small chips, table cells |
| `radius/md` | 12px | Cards, modals |
| `radius/lg` | 20px | Hero card / large feature containers (scaled down from Reference 01's extreme radius to stay "label," not "SaaS app") |
| `radius/pill` | 999px | Buttons, nav bar, tags, badges (from both references) |
| `border/hairline` | 1px solid `color/border/default` | Default card/input border |
| `border/focus` | 2px solid `color/accent/primary` | Focus ring, per accessibility rules below |

## 8. Shadow & Elevation Tokens

Both references favor **flat separation via color contrast over drop shadows** — this system follows that discipline rather than introducing skeuomorphic elevation.

| Token | Value | Usage |
|---|---|---|
| `elevation/0` | none | Default cards on the dark ground — separation via `border/hairline` and background-value contrast only |
| `elevation/1` | `0 1px 2px rgba(0,0,0,0.24)` | Dropdown menus, popovers only |
| `elevation/2` | `0 8px 24px rgba(0,0,0,0.32)` | Modals, dialogs only |
| `motion/fast` | 120ms ease-out | Hover state transitions |
| `motion/normal` | 200ms ease-out | Menu open/close, tab switches |
| `motion/slow` | 320ms ease-in-out | Page-section reveal (respect `prefers-reduced-motion`) |

## 9. Iconography Rules

- Base icon set: simple, functional line-art, 1.5px stroke, from both references' line-icon language (arrows, chevrons, social glyphs).
- Decorative sparkle/star-burst accents (Reference 01) are reserved for a single "Featured" marker — e.g. a featured artist or release — never repeated as ambient decoration across a page.
- Icons take color from `color/text/secondary` by default, switching to `color/accent/primary` only on hover/active/selected states.
- Minimum touch target 40×40px for any icon-only interactive control.

## 10. Image & Illustration Rules

- **Artist and event photography:** always true-color, never duotoned — fans must recognize the artist as-is.
- **Editorial/story imagery** (About page, News features, section dividers): optional duotone overlay using `color/accent/primary` at ~30% multiply, per Reference 02's system-wide photography treatment.
- Every image ships with a defined aspect ratio (no layout shift): artist profile 1:1, release artwork 1:1, event/hero banner 16:9 or 21:9, news thumbnail 4:3.
- All images require `alt` text describing the subject (artist name, release title, event name) — never decorative-only alt text for content images.

## 11. Component Specifications

### Navbar
Pill-shaped, `radius/pill`, sits inset within `space/lg` of the viewport edge on the homepage hero (Reference 01), collapses to a standard full-width bar with the same token set on inner pages and the admin platform. Height 64px desktop / 56px mobile. Background `color/bg/raised` at ~85% opacity with backdrop blur. Active link uses `color/accent/primary` underline, 2px, `motion/fast`.

### Primary Button
`radius/pill`, height 44px, horizontal padding `space/lg`, `Button` type token, background `color/accent/primary`, text `color/accent/on-accent`. Hover: background darkens 8%. Active: darkens 14%. Focus: `border/focus` ring offset 2px. Disabled: 40% opacity, no pointer events.

### Secondary/Outline Button
Same geometry as Primary; transparent background, `border/hairline` in `color/border/strong`, text `color/text/primary`. Hover: background `color/bg/raised`.

### Card (Artist / Release / Event / News)
`radius/md`, `border/hairline`, background `color/bg/raised`, internal padding `space/lg`. Image fills the top at its defined aspect ratio with no radius mismatch (image corners match card corners). Title uses `H4`; metadata row uses `Caption` in `color/text/secondary`. Hover: border color shifts to `color/border/strong`, no shadow, per the flat-elevation rule.

### Stat Block (dedicated stats section — Reference 01 style)
No card chrome; number in `Numeric/Stat` at H1/H2 scale in `color/text/primary`, label beneath in `Label` token, `color/text/secondary`. Grid of 3–4 across desktop, 2 across mobile, `space/xl` gap.

### Stat Chip (overlaid on hero imagery — Reference 02 style)
Compact card, `radius/sm`, background `color/bg/base` at 70% opacity with backdrop blur, padding `space/sm` `space/md`, number in `Numeric/Stat` at `H4` scale, label in `Caption`. Used only over photography, never on plain backgrounds.

### Status Badge (release status, licensing status, royalty payment status)
`radius/pill`, padding `space/xs` `space/sm`, `Caption` weight 600. Color pairs from the semantic state tokens: Success/Warning/Error/Info backgrounds at 16% opacity with full-opacity text of the same hue — never the raw saturated fill, to keep it legible on both the public site and admin tables.

### Form Input
Height 44px, `radius/sm`, `border/hairline`, background `color/bg/base`. Label above in `Label` token. Focus: `border/focus`. Error: border becomes `color/state/error`, helper text below in `Body Small`/`color/state/error` explaining exactly what to fix.

### Data Table (Admin Platform)
Row height 48px, `border/hairline` between rows only (no vertical rules), header row in `Label` token uppercase, `color/text/secondary`, sticky on scroll. Numeric columns right-aligned with `Numeric/Stat`/tabular figures. Row hover: `color/bg/raised`.

### Kicker / Eyebrow Label (Reference 02 pattern)
Small square or dash bullet + `Label` token text, precedes every section `H2` sitewide — a single consistent device rather than the varied intro treatments seen in either reference alone.

## 12. Responsive Design Rules

| Breakpoint | Navigation | Hero | Grids |
|---|---|---|---|
| **Desktop** (1024px+) | Full pill navbar, all items visible | Full-bleed hero, stat strip 4-up | Cards 3–4 per row |
| **Tablet** (640–1023px) | Pill navbar collapses secondary items into a menu | Hero retains full-bleed treatment, stat strip 2-up | Cards 2 per row |
| **Mobile** (0–639px) | Navbar becomes a full-width bar with a hamburger menu; the floating pill-in-card pattern is dropped in favor of a standard top bar (it doesn't survive small viewports cleanly in either reference) | Hero image crops to a taller portrait ratio, headline and CTA stack vertically, stat chips move below the image rather than overlaid | Cards stack 1 per row; admin tables switch to a stacked key/value card per row instead of a horizontal table |

No horizontal scrolling anywhere except a data table explicitly wrapped in its own scroll container with a visible scroll affordance.

## 13. Interaction States

Every interactive component must define all of: `Default, Hover, Focus, Active, Selected, Disabled, Loading, Error, Success`.

- **Focus** is always a visible 2px `color/accent/primary` ring with 2px offset — never removed, never color-only (also shifts border weight so it reads without color).
- **Loading** replaces label text with a spinner sized to the component's icon slot; the component keeps its committed width so surrounding layout doesn't shift.
- **Disabled** is 40% opacity plus `cursor: not-allowed`, never color-only.
- **Selected** (e.g. active nav item, chosen role tab) uses `color/accent/primary` as an underline or left-border accent, not a full background fill, to avoid competing with primary CTAs.

## 14. Accessibility Rules

- All text/background pairings above are chosen to clear **WCAG AA** (4.5:1 body text, 3:1 large text/UI components) — verify `color/text/secondary` on `color/bg/raised` specifically once final accent hex is confirmed, as it's the tightest pairing in the set.
- Every interactive element reachable and operable by keyboard alone, in a logical tab order matching visual order.
- Minimum touch target 40×40px (icon buttons) / 44px height (buttons, inputs).
- Form fields always have a visible `<label>`, not placeholder-only text.
- Status badges and semantic colors always pair color with a text label or icon — never color alone to convey meaning (important for royalty/rights statuses).
- Respect `prefers-reduced-motion`: disable `motion/slow` section reveals, keep only instant or `motion/fast` state changes.
- Semantic HTML landmarks (`nav`, `main`, `header`, `footer`) on every page template; heading levels never skip a level.

## Phase 7 — Design Tokens (CSS custom properties)

```css
:root {
  /* Color */
  --color-bg-base: #0D0E0C;
  --color-bg-raised: #171815;
  --color-bg-sunken: #0A0B09;
  --color-bg-inverse: #F6F4EC;
  --color-accent-primary: #D9542E;
  --color-accent-on-accent: #0D0E0C;
  --color-accent-secondary: #C7E86B;
  --color-text-primary: #F3F1E9;
  --color-text-secondary: #A6A79C;
  --color-text-muted: #6E6F65;
  --color-text-on-light: #111210;
  --color-border-default: #2A2B26;
  --color-border-strong: #3E4038;
  --color-state-success: #7FBE5A;
  --color-state-warning: #E0A83C;
  --color-state-error: #D9483B;
  --color-state-info: #5B9BD1;

  /* Spacing */
  --space-xs: 4px; --space-sm: 8px; --space-md: 16px; --space-lg: 24px;
  --space-xl: 32px; --space-2xl: 48px; --space-3xl: 64px; --space-4xl: 96px; --space-5xl: 128px;

  /* Radius */
  --radius-sm: 6px; --radius-md: 12px; --radius-lg: 20px; --radius-pill: 999px;

  /* Elevation */
  --elevation-1: 0 1px 2px rgba(0,0,0,0.24);
  --elevation-2: 0 8px 24px rgba(0,0,0,0.32);

  /* Motion */
  --motion-fast: 120ms ease-out;
  --motion-normal: 200ms ease-out;
  --motion-slow: 320ms ease-in-out;

  /* Layout */
  --layout-container-max: 1280px;
  --breakpoint-tablet: 640px;
  --breakpoint-desktop: 1024px;
}

/* Admin Platform & Artist Portal density override */
[data-surface="operational"] {
  --space-2xl: 24px;
  --space-3xl: 32px;
  --space-4xl: 48px;
}
```

## Phase 8/9 — Existing Project Integration

No frontend codebase exists yet — per the [engineering roadmap](README.md), this design system is the Phase 2 (UI/UX Design) input, to be applied when Phase 3 (Frontend Development) begins. When that codebase is scaffolded (React/Next.js, per the proposal's §8 recommendation), these tokens translate directly into either CSS custom properties (above) or an equivalent Tailwind theme extension — whichever the frontend team chooses in Phase 1's technical planning. No component code is fabricated here against a non-existent app; this document is the spec that implementation is checked against once real screens exist.

## Design QA Checklist (for Phase 2/3 review, not yet applicable)

**Visual:** [ ] Typography matches this scale · [ ] Colors match approved tokens (pending Tramax brand sign-off) · [ ] Spacing follows the defined scale · [ ] Radius consistent per component type · [ ] No drop shadows outside `elevation/1`–`2` · [ ] Public-site rhythm vs. operational-surface density correctly applied

**UX:** [ ] Navigation collapses correctly per breakpoint · [ ] All interactive elements visibly interactive · [ ] Status always paired with a label, not color alone

**Responsive:** [ ] No horizontal overflow · [ ] Hero degrades per the mobile rule above · [ ] Admin tables convert to stacked cards on mobile

**Accessibility:** [ ] AA contrast verified once final accent hex is set · [ ] Full keyboard operability · [ ] Visible focus ring on every interactive element · [ ] Reduced-motion respected

---

### Open items requiring Tramax input before this leaves "draft"

1. **Confirm or replace the proposed accent/neutral hex values** — nothing here is an approved brand color yet.
2. **Confirm the Display/Body typefaces** — the families named above are placeholders in the right *style class* (rounded geometric sans; serif-italic accent), not a licensing commitment.
3. **Confirm whether the Reference 02 "frame bar" device is wanted anywhere** (e.g. release/event pages) or dropped entirely.
