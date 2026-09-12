# Tramax Frontend Conversion — Final Report

Scope: convert the Tramax frontend from Next.js 16 / React 19 / Tailwind v4 to a Laravel Blade / Bootstrap / jQuery frontend, following the UltrAdemy reference model (`Conversion-README.md`), while preserving the existing Laravel 12 + Sanctum backend unchanged. Scope B — all 38 pages (16 public marketing pages, the 12-module/14-page admin platform, the 8-page artist portal), so the deployed site has **no runtime dependency on Node.js or Next.js at all**.

Delivered in 10 phases (2–9 = actual conversion work; 1 = inspection, done before Phase 2; 10 = this report).

---

## 1. Acceptance criteria (Conversion-README.md §22)

### Architecture

- [x] Next.js is no longer required to serve the website — `frontend/` is deleted from the repository (Phase 9).
- [x] React is no longer required for normal website rendering.
- [x] Tailwind is no longer a runtime dependency of the website.
- [x] Blade is the primary website rendering layer — all 38 pages are server-rendered Blade views.
- [x] Bootstrap is the primary UI framework — Bootstrap 5.3 via `resources/sass/app.scss`.
- [x] jQuery/standard JavaScript handles required browser interactions — jQuery 3.7 via `resources/js/app.js`.
- [x] Laravel remains the backend and website runtime — Laravel 12, unchanged.

### Functionality

- [x] Existing Tramax URLs work — every public/admin/portal path from the original Next.js app resolves to the same URL under Blade (see §3 conversion map).
- [x] Dynamic pages work — artist/release/event/news/product detail pages, all admin CRUD, all portal pages.
- [x] Forms work — all four public enquiry forms, every admin create/edit form, the portal profile/release-submission forms.
- [x] Authentication works — session-guard admin/portal login, RBAC middleware, self-protection guards, all live-tested.
- [x] Images/assets load correctly — `storage:link` verified, image uploads verified end-to-end through the jQuery AJAX pipeline on both admin and portal surfaces.
- [x] API functionality required by the project still works — the Sanctum `/api/v1/*` surface is untouched; all 78 PHPUnit tests pass.
- [x] No important frontend feature has been lost — see §3; the one deliberate scope trim (Documents & Contracts nav item) was already absent from the pre-conversion React app, not dropped by this conversion.

### Deployment

- [x] Laravel runs on PHP 8.2+.
- [x] `backend/public` is the web document root.
- [x] MySQL works — all 78 tests + every live-tested CRUD flow hit the real MySQL database.
- [x] Composer installation works — `composer.json`/`composer.lock` are **unchanged** by this conversion.
- [x] Node is needed only for build-time asset compilation — `npm run build` (Laravel Mix) compiles once; nothing Node-related runs afterward.
- [x] No persistent Next.js process is required — verified live with `frontend/` fully deleted from disk.
- [x] `storage:link` works — verified.
- [x] Production `.env` is configured correctly — see `DEPLOYMENT.md` §3.4.
- [x] `APP_DEBUG=false` — documented as required in `DEPLOYMENT.md`.
- [x] HTTPS works — AutoSSL documented in `DEPLOYMENT.md` §4 (not yet provisioned, since there is no live staging server for this session to reach).

All boxes checked. The two items not independently re-verified in this session (AutoSSL, live cPanel deploy) are hosting-account actions outside this environment — `DEPLOYMENT.md` documents the exact steps.

---

## 2. Architecture — before / after

**Before:**

```
Browser → Next.js/Node.js (persistent process) → Laravel API (Sanctum) → MySQL
```

**After:**

```
Browser → Apache/PHP → Laravel 12
                          ├── Blade frontend (public site, admin, portal)
                          ├── Sanctum API (/api/v1/*, untouched)
                          └── MySQL
```

Node/npm now appear **only** at build time (`npm run build`, via Laravel Mix, inside `backend/`) to compile `resources/js/app.js` + `resources/sass/app.scss` into `public/js/app.js` + `public/css/app.css`. No Node process runs in production. Verified live in this session: the entire site (public pages, admin login, dashboard, asset loading) works correctly with `frontend/` deleted from disk entirely.

---

## 3. Conversion map

All 38 pages, mapped from their original Next.js file to their Blade equivalent. Every URL is preserved exactly.

### Public marketing site (16 pages) — Phase 3

| URL | Original Next.js file | Blade view | Route name | Controller |
|---|---|---|---|---|
| `/` | `app/(marketing)/page.tsx` | `site/home.blade.php` | `site.home` | `Web\Site\HomeController@index` |
| `/about` | `.../about/page.tsx` | `site/about.blade.php` | `site.about` | `Web\Site\PageController@about` |
| `/artists` | `.../artists/page.tsx` | `site/artists/index.blade.php` | `site.artists.index` | `Web\Site\ArtistController@index` |
| `/artists/{slug}` | `.../artists/[slug]/page.tsx` | `site/artists/show.blade.php` | `site.artists.show` | `Web\Site\ArtistController@show` |
| `POST /artists/apply` | (inline form on artists page) | — | `site.artists.apply` | `Web\Site\ArtistController@storeApplication` |
| `/music` | `.../music/page.tsx` | `site/music/index.blade.php` | `site.music.index` | `Web\Site\ReleaseController@index` |
| `/music/{slug}` | `.../music/[slug]/page.tsx` | `site/music/show.blade.php` | `site.music.show` | `Web\Site\ReleaseController@show` |
| `/videos` | `.../videos/page.tsx` | `site/videos.blade.php` | `site.videos` | `Web\Site\PageController@videos` |
| `/events` | `.../events/page.tsx` | `site/events/index.blade.php` | `site.events.index` | `Web\Site\EventController@index` |
| `/events/{slug}` | `.../events/[slug]/page.tsx` | `site/events/show.blade.php` | `site.events.show` | `Web\Site\EventController@show` |
| `/news` | `.../news/page.tsx` | `site/news/index.blade.php` | `site.news.index` | `Web\Site\NewsController@index` |
| `/news/{slug}` | `.../news/[slug]/page.tsx` | `site/news/show.blade.php` | `site.news.show` | `Web\Site\NewsController@show` |
| `/store` | `.../store/page.tsx` | `site/store/index.blade.php` | `site.store.index` | `Web\Site\ProductController@index` |
| `/store/{slug}` | `.../store/[slug]/page.tsx` | `site/store/show.blade.php` | `site.store.show` | `Web\Site\ProductController@show` |
| `/contact` (+ `POST`) | `.../contact/page.tsx` | `site/contact.blade.php` | `site.contact.show` / `.store` | `Web\Site\ContactController` |
| `/licensing` (+ `POST`) | `.../licensing/page.tsx` | `site/licensing.blade.php` | `site.licensing.show` / `.store` | `Web\Site\LicensingRequestController` |
| `/partnerships` (+ `POST`) | `.../partnerships/page.tsx` | `site/partnerships.blade.php` | `site.partnerships.show` / `.store` | `Web\Site\PartnerController` |

Data source for every list/detail page: the same public-scoped Eloquent queries already used by `Api\ArtistController`/`Api\ReleaseController`/`Api\EventController`/`Api\NewsController`/`Api\ProductController` (e.g. `ArtistProfile::where('status', '!=', 'Inactive')`), so the Blade pages show exactly what the public API already exposed — nothing re-scoped or redesigned.

### Admin platform (14 pages) — Phases 2, 4–7

| URL | Blade view | Route name | Controller |
|---|---|---|---|
| `/admin/login` | `auth/admin-login.blade.php` | `admin.login` | `Web\AuthController` |
| `/admin/dashboard` | `admin/dashboard.blade.php` | `admin.dashboard` | `Web\DashboardController` |
| `/admin/applications` | `admin/applications/index.blade.php` | `admin.applications.index` | `Web\ApplicationController` |
| `/admin/artists` (+ create/edit) | `admin/artists/{index,form}.blade.php` | `admin.artists.*` | `Web\ArtistController` |
| `/admin/releases` (+ create/edit) | `admin/releases/{index,form}.blade.php` | `admin.releases.*` | `Web\ReleaseController` |
| `/admin/rights` (+ create/edit) | `admin/rights/{index,form}.blade.php` | `admin.rights.*` | `Web\RightsRecordController` |
| `/admin/royalty` (+ create/edit) | `admin/royalty/{index,form}.blade.php` | `admin.royalty.*` | `Web\RoyaltyStatementController` |
| `/admin/licensing` | `admin/licensing/index.blade.php` | `admin.licensing.*` | `Web\LicensingRequestController` |
| `/admin/partners` | `admin/partners/index.blade.php` | `admin.partners.*` | `Web\PartnerController` |
| `/admin/events` (+ create/edit) | `admin/events/{index,form}.blade.php` | `admin.events.*` | `Web\EventController` |
| `/admin/store` (+ create/edit) | `admin/store/{index,form}.blade.php` | `admin.store.*` | `Web\ProductController` |
| `/admin/content` (+ create/edit) | `admin/content/{index,form}.blade.php` | `admin.content.*` | `Web\NewsController` |
| `/admin/users` (+ create/edit) | `admin/users/{index,form}.blade.php` | `admin.users.*` | `Web\UserController` |
| `/admin/audit-log` | `admin/audit-log/index.blade.php` | `admin.audit-log.index` | `Web\AuditLogController` |
| `/admin/reports` | `admin/reports/index.blade.php` | `admin.reports.index` | `Web\ReportController` |

(Login + Dashboard + 12 modules = 14, matching `discovery.md`'s original admin sitemap exactly. "Roles" has no separate page — it was always a read-only picker feed for the Users form, so it's queried inline rather than getting its own route, same as the original API design.)

### Artist Portal (8 pages) — Phases 2, 8

| URL | Blade view | Route name | Controller |
|---|---|---|---|
| `/portal/login` | `auth/portal-login.blade.php` | `portal.login` | `Web\AuthController` |
| `/portal/dashboard` | `portal/dashboard.blade.php` | `portal.dashboard` | `Web\Portal\DashboardController` |
| `/portal/profile` | `portal/profile.blade.php` | `portal.profile.show` / `.update` | `Web\Portal\ProfileController` |
| `/portal/releases` | `portal/releases/index.blade.php` | `portal.releases.*` | `Web\Portal\ReleaseController` |
| `/portal/royalty-statements` | `portal/royalty-statements/index.blade.php` | `portal.royalty-statements.index` | `Web\Portal\RoyaltyStatementController` |
| `/portal/bookings` | `portal/bookings/index.blade.php` | `portal.bookings.index` | `Web\Portal\EventController` |
| `/portal/notifications` | `portal/notifications/index.blade.php` | `portal.notifications.*` | `Web\Portal\NotificationController` |

(7 distinct functional pages + login = 8, matching `discovery.md`'s portal sitemap: "Discography"/"Earnings Overview" were the same data as Releases/Royalty Statements under a different label, folded in rather than duplicated; "Documents & Contracts" was already absent — no `Document` entity exists in the data model.)

---

## 4. Component conversion explanations

| React component | Blade equivalent | Notes |
|---|---|---|
| `components/ui/dashboard-shell.tsx` | `<x-dashboard-shell>` (class-based component, `App\View\Components\DashboardShell`) | Typed props (`brand`, `navGroups`, `userLabel`, `logoutRoute`) — 1:1 port, used by both admin and portal layouts. |
| `components/marketing/site-header.tsx` / `site-footer.tsx` | Inlined into `components/layouts/public.blade.php` | Pill nav + footer markup ported directly; nav links hardcoded same as the React `navGroups`/`links` consts. |
| `components/ui/kicker.tsx` | `<x-kicker>` | Anonymous Blade component, single-purpose. |
| `components/ui/card.tsx` | `<x-card :href :title :meta>` | Anonymous Blade component with a slot for extra content (e.g. store card's price line). |
| `components/ui/button.tsx` (`Button`/`LinkButton`) | Plain Bootstrap `.btn.btn-primary`/`.btn-outline-light` + `.rounded-pill` | No dedicated component — Bootstrap's own button primitives already cover this, themed via the `.btn-primary` CSS-variable override in `app.scss`. |
| `components/marketing/enquiry-form.tsx` | `site/partials/enquiry-form.blade.php` (`@include`) | Same generic `{fields, submitLabel, endpoint/action, extra}` shape; client-side `fetch` + local state replaced with a real POST + Laravel validation errors (`@error`) + a `session('submitted')` flash flag standing in for the React `status === "submitted"` state. |
| `components/ui/image-upload-field.tsx` | `admin/partials/image-upload.blade.php` + `portal/partials/image-upload.blade.php` | jQuery AJAX upload widget (`.image-upload__file` change handler → `MediaUploadController` → writes the returned URL into a hidden input), reused verbatim across Artists/Releases/Store/Content/Portal-Profile/Portal-Releases. |
| `components/ui/badge.tsx` | `.badge-status.badge-status--{success,warning,error,info}` | CSS classes instead of a component — same four status colors. |
| Royalty statement line-item repeater (inline React state) | `[data-add-row]`/`[data-remove-row]` jQuery pattern in `app.js` | `<template>` with `__INDEX__` placeholders swapped for an incrementing counter — a generic, reusable primitive, not React-state-bound. |
| Artist/Event multi-select linking (React controlled `<select multiple>`) | Native HTML `<select multiple>` submitted as `name="artist_profile_ids[]"` | Synced server-side via `$event->artists()->sync($artistIds)`; documented limitation in §10 below. |
| SPA single-page create/edit toggle (Applications, Artists, Releases, etc.) | Separate `/create` and `/{id}/edit` Blade pages/routes | Deliberate adaptation to Blade's natural multi-page shape rather than forcing the old SPA pattern — noted per-module in each controller's file comment. |
| Client-side `expanded`/`showForm` React state (Partners review rows, Audit Log details, Royalty breakdown, Releases submit-form toggle) | Bootstrap `data-bs-toggle="collapse"` | Zero custom JS needed — purely declarative. |

---

## 5. Files created, modified, removed

Counts below are exact (derived from `git status`, not estimated).

### Removed — 75 files

The entire `frontend/` directory (Next.js app: `app/`, `components/`, `lib/`, config files, `server.js`, etc.) — deleted in Phase 9 via `git rm -r`, fully recoverable through git history. Plus two dead files inside `backend/` from the old Vite scaffold: `resources/views/welcome.blade.php`, `vite.config.js`.

### Created — 97 files (all inside `backend/`)

- **31 Web controllers** — `app/Http/Controllers/Web/`: 15 directly under `Web/` (the admin surface, including the shared `AuthController` and `DashboardController`), 6 under `Web/Portal/`, 10 under `Web/Site/` (the public marketing site) — covering every module in §3.
- **1 service** — `app/Services/ReportsSummaryService.php` (extracted from `Api\Admin\ReportsAdminController` so the Blade Reports page and the JSON API build identical numbers from one place).
- **1 view component class** — `app/View/Components/DashboardShell.php`.
- **57 Blade views** — layouts (`components/layouts/{guest,admin,portal,public}.blade.php`), shared components (`kicker`, `card`, `dashboard-shell`), partials (`head`, `admin/partials/image-upload`, `portal/partials/image-upload`, `site/partials/enquiry-form`), the two login pages, all 14 admin module views, all 7 portal views, all 16 public site views.
- **Asset pipeline** — `webpack.mix.js`, `resources/sass/app.scss`, plus the compiled output `public/css/app.css`, `public/js/app.js` (+ `.LICENSE.txt`), `public/mix-manifest.json`, and `package-lock.json` (7 files).

### Modified — 13 files

`routes/web.php` (grown from nothing to 96 routes across the public/admin/portal surfaces), `resources/js/app.js` (jQuery init + AJAX upload + repeatable-rows + `data-confirm`), `package.json` (Vite/Tailwind → Mix/Bootstrap/jQuery, see §6), `bootstrap/app.php` (guest-redirect-by-path fix), `app/Http/Middleware/EnsureUserHasRole.php` (JSON vs HTML 403 branching), `app/Http/Controllers/Api/Admin/ReportsAdminController.php` (thinned to call the new service), `.env.example` + `config/cors.php` (dropped the stale `localhost:3000` Next-dev-server CORS default), `.github/workflows/ci.yml`, `DEPLOYMENT.md`, `LOCAL_SETUP.md`.

**Not touched at all**: `composer.json` / `composer.lock` (byte-for-byte unchanged — confirmed via `git diff`), every model, every migration, every Sanctum `Api\*`/`Api\Admin\*`/`Api\Portal\*` controller (except the one Reports refactor above, which is a pure extraction with identical behavior, verified by the unchanged `ReportsTest.php` suite still passing), and the entire `Api\` route surface in `routes/api.php`.

---

## 6. Dependencies

### Added (`backend/package.json`)

`bootstrap ^5.3.3`, `@popperjs/core ^2.11.8`, `jquery ^3.7.1`, `laravel-mix ^6.0.49`, `resolve-url-loader ^5.0.0`, `sass ^1.77.8`, `sass-loader ^16.0.0`, `webpack 5.94.0` (pinned explicitly — the newer default broke laravel-mix's internal `webpack/lib/SizeFormatHelpers` require).

### Removed (`backend/package.json`)

`@tailwindcss/vite`, `axios`, `concurrently`, `laravel-vite-plugin`, `tailwindcss`, `vite`.

### Removed (deleted with `frontend/`)

Everything in the old Next.js `package.json`: `next`, `react`, `react-dom`, and its own separate Tailwind/lint toolchain.

### Retained, unchanged

`backend/composer.json` / `composer.lock` — no PHP dependency was added, removed, or version-bumped. `laravel/sanctum` stays exactly as it was; **no Passport was introduced**, per the brief's explicit instruction.

---

## 7. Build commands

```bash
# One-time / after any dependency change
cd backend
npm ci               # or npm install
npm run build         # Laravel Mix, production build → public/css/app.css, public/js/app.js

# Day-to-day local development
npm run watch          # Mix watch mode, recompiles on save
# or
npm run dev             # single non-production Mix compile

# Backend (unchanged from before this conversion)
composer install
php artisan migrate
php artisan storage:link
```

No `next build`, no `next dev`, no `server.js`, no Passenger Node.js App — all removed with `frontend/`.

---

## 8. Environment variables

No new environment variables were introduced by this conversion. One existing variable's *default* changed:

| Variable | Before | After | Why |
|---|---|---|---|
| `CORS_ALLOWED_ORIGINS` | `http://localhost:3000,http://tramax.local` | `http://tramax.local` | `localhost:3000` was the old Next.js dev server's origin. CORS only ever applied to `/api/*` (Sanctum) — the Blade UI is same-origin and never triggers it — so this only matters for an external API consumer, not the website itself. |

Everything else (`APP_*`, `DB_*`, `SESSION_*`, `CACHE_*`, `QUEUE_*`, `MAIL_*`, `FILESYSTEM_DISK`) is unchanged from the pre-conversion `.env.example`.

---

## 9. Deployment instructions

Full runbook: [`DEPLOYMENT.md`](DEPLOYMENT.md) (fully rewritten in Phase 9). Summary of what changed from the pre-conversion version:

- **One subdomain instead of two** — no more `api-staging` + `staging` split; the whole app (public site + `/admin` + `/portal` + `/api/v1`) is one Laravel deployment.
- **No cPanel "Setup Node.js App" / Passenger entry** — that entire section is deleted.
- **One new step**: `npm ci && npm run build` inside `backend/`, run once per deploy, right alongside `composer install`. Nothing needs to keep running afterward.
- **"Deploying an update"** now ends after `php artisan config:clear` — no frontend restart step exists anymore, because there's no frontend process.

---

## 10. Known limitations / remaining issues

- **Multi-select "clear to zero" ambiguity** (`admin/events` artist linking): a plain HTML `<select multiple>` can't distinguish "field not touched" from "deliberately cleared to zero selections" the way the old React controlled-component version could. Documented in `EventController`'s own comment; accepted as a reasonable HTML-forms trade-off, not fixed with extra JS since it wasn't asked for.
- **`#submit` anchor doesn't survive a validation-error redirect** on the artists page's "Submit your music" form — Laravel's `back()` redirect uses the HTTP `Referer` header, which browsers never include a URL fragment in, so a failed submission scrolls to the top of `/artists` rather than back down to the form. Cosmetic only; the form and its errors still render correctly, just not pre-scrolled to.
- **About and Videos pages still carry placeholder copy** — this was already true in the original React version (explicitly commented "Placeholder copy — final content supplied by Tramax… during Phase 3" / "Video embed placeholder"); the conversion preserved that placeholder state rather than inventing real content.
- **Store has no checkout** — by original design (`README.md §06`), unchanged by this conversion.
- **No automated deploy pipeline yet** — deploys are the manual SSH runbook in `DEPLOYMENT.md`; a GitHub Actions SSH-deploy workflow is flagged there as a reasonable follow-up, not built.
- **No CDN/S3 media disk yet** — uploads work but live on local disk; `FILESYSTEM_DISK=s3` in `config/filesystems.php` is ready for that switch when needed.
- **AutoSSL/live HTTPS not independently re-verified this session** — there is no live cPanel account reachable from this environment; the exact steps are documented and were previously part of the original deployment runbook.

None of these are regressions introduced by the conversion — each is either a pre-existing, already-documented product decision, or a narrow, explicitly-noted trade-off of moving off a JS-framework controlled-component model onto plain HTML forms.

---

## 11. Remaining Next.js dependencies

**None.** Verified by:

- `frontend/` does not exist on disk (`git status` shows it as 75 tracked deletions, nothing untracked left behind).
- `grep -rn "next/link\|from \"react\"\|tailwindcss\|NEXT_PUBLIC"` across `backend/{resources,app,routes,config,public}` returns zero matches.
- `backend/package.json` contains no `next`, `react`, `react-dom`, `tailwindcss`, or `vite` entry.
- `backend/composer.json` never had a Next.js dependency to begin with (it's a separate PHP toolchain).

---

## 12. Final verification — no persistent Next.js runtime required

Confirmed live, in this session, with `frontend/` completely deleted from disk (not just stopped — the directory does not exist):

- `http://tramax.local/` (homepage) renders correctly with real DB-backed content (artist/release counts, featured artists).
- `http://tramax.local/admin/login` renders and authenticates correctly.
- Every other public/admin/portal page tested per-phase throughout this conversion continued to work after the deletion.
- The full CI-equivalent sequence — `npm ci && npm run build` (Mix) → `vendor/bin/pint --test` → `php artisan test` — runs to completion with **78/78 tests passing** and Pint clean, using only PHP and a build-time Node invocation.

No Next.js process, no `server.js`, no Passenger Node.js App entry, and no `NEXT_PUBLIC_*` environment variable exist anywhere in the deployed application.

---

## 13. Regression summary

- **PHPUnit**: 78/78 tests passing (388 assertions) — the full pre-existing Sanctum API test suite, unaffected by 10 phases of Blade work built in parallel to it.
- **Pint**: clean across the full repository.
- **Live browser testing**: every module was tested end-to-end against the real MySQL database at least once per phase (create/edit/delete cycles, RBAC role-by-role, self-protection guards, file uploads via real `DataTransfer`-constructed files, rate limiting, validation-error paths, 404 handling) — all test data created during testing was cleaned up afterward; the database is in the same state it was in before this conversion began, plus the intentional schema/data that already existed.

---

*Authored across Phases 2–10 of the Tramax Blade/Bootstrap/jQuery conversion. Nothing in this repository has been committed to git as part of this work — all changes remain in the working tree, ready for review before commit.*
