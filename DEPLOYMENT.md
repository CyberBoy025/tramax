# Staging Deployment — cPanel Shared Hosting

How to deploy Tramax to staging on a cPanel account with PHP, MySQL, and SSH/Terminal access.

Tramax is a single Laravel application — the admin platform, artist portal, and public marketing site are all server-rendered Blade views served by PHP. There is **no separate frontend app and no persistent Node.js process in production**: Node/npm are needed only once, at build time, to compile front-end assets (Laravel Mix) into plain `.css`/`.js` files that Apache/PHP then serve like any other static file.

This is a manual runbook, not an automated pipeline — I have no access to your hosting account, so every step here is something you run yourself over SSH or click in cPanel. Re-run the "Deploying an update" section at the bottom whenever you want to push new commits to staging.

---

## 0. Before you start

- [ ] Confirm PHP 8.2+ is available (`composer.json` requires `^8.2`). Check cPanel's **MultiPHP Manager**.
- [ ] Confirm Node.js 18+ is reachable over SSH, just to run the one-time/per-deploy asset build (`npm run build`) — it does not need to stay running afterward. If your host doesn't have `node`/`npm` on the system PATH, cPanel's **Setup Node.js App** page still has an "Enter to the virtual environment" command that puts a Node binary on PATH for your SSH session without you needing to actually create a persistent Node "app" there.
- [ ] Have the repo URL handy: `https://github.com/CyberBoy025/tramax.git`. If the repo is private, generate a GitHub [personal access token](https://github.com/settings/tokens) (classic, `repo` scope) first — you'll clone with `https://<token>@github.com/CyberBoy025/tramax.git` instead of the plain URL.
- [ ] SSH into the account once to confirm access: `ssh <cpanel_user>@<your_server_host>`.

---

## 1. Create the subdomain

In cPanel → **Domains** (or **Subdomains** on older cPanel themes):

1. Create `staging.tramaxentertainment.com`. When it asks for a document root, **do not accept the default** — you'll repoint it in step 3.1 once the code exists, since Laravel's document root must be `backend/public`, not the repo root (everything else — `.env`, `app/`, `vendor/` — must never be web-accessible).

Only one subdomain is needed now — the admin platform (`/admin`), artist portal (`/portal`), and public site (`/`) are all the same Laravel app. The Sanctum token API (`/api/v1/*`) lives on this same domain too, for any external client that wants it (a mobile app, etc.) — it no longer needs a separate `api-staging` subdomain to talk to a separately-hosted frontend, since there is no separately-hosted frontend anymore.

---

## 2. Create the database

cPanel → **MySQL® Databases**:

1. Create a database, e.g. `<cpanel_user>_tramax_staging`.
2. Create a database user with a strong generated password, and **save that password** — you'll need it in step 3.
3. Add the user to the database with **All Privileges**.

---

## 3. Deploy the application

SSH in, then:

```bash
cd ~
git clone https://github.com/CyberBoy025/tramax.git tramax
cd tramax/backend
```

### 3.1 Point the subdomain at `backend/public`

Back in cPanel → **Domains**, edit `staging.tramaxentertainment.com` and set its document root to:

```
/home/<cpanel_user>/tramax/backend/public
```

(Exact path depends on your account — confirm your home directory with `pwd` after `cd ~`.)

### 3.2 Select PHP 8.2+ for the subdomain

cPanel → **MultiPHP Manager** → select `staging.tramaxentertainment.com` → set to PHP 8.2 or later.

Confirm the SSH `php` binary matches — `php -v`. If it doesn't (shared hosts sometimes default SSH to an older PHP), use the versioned binary cPanel provides instead, e.g. `php82`, for every command below (`php82 artisan ...`, `php82 /usr/local/bin/composer install`, etc.) — check `ls /usr/local/bin/ | grep php`, or your host's docs, for the exact name.

### 3.3 Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

If `composer` isn't on PATH over SSH, try `php composer.phar install --no-dev --optimize-autoloader`, or ask your host how Composer is invoked on their system.

### 3.4 Configure the environment

```bash
cp .env.example .env
```

Edit `.env` (via `nano .env`, `vi .env`, or cPanel's File Manager) and set:

```
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.tramaxentertainment.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<cpanel_user>_tramax_staging
DB_USERNAME=<the database user from step 2>
DB_PASSWORD=<that user's password>
```

`APP_DEBUG=false` is not optional — with it `true`, unhandled errors dump full stack traces (file paths, query text) to anyone who hits a broken endpoint. Leave the rest (`SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`, `MAIL_MAILER=log`) as `.env.example` already has them — no Redis or external mail service needed for staging.

`CORS_ALLOWED_ORIGINS` only matters if some *other* site or app calls `/api/v1/*` directly from a browser — the Blade admin/portal/public UI is served same-origin and never hits CORS at all. Leave it unset unless you have a specific external API consumer in mind.

Then:

```bash
php artisan key:generate
```

### 3.5 Migrate and seed

```bash
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
```

`RoleSeeder` is required — every user row has a `role_id` foreign key, so the app can't function without the seven role rows existing.

You need at least one real Super Administrator account to sign in with. Either:

- Seed the full demo dataset for quick testing (`php artisan db:seed --class=UserSeeder --force`) — **but every demo account uses the password `password`**, so only do this if staging isn't reachable by anyone outside the team, and change it before showing anyone real.
- Or create one real admin account directly:
  ```bash
  php artisan tinker
  ```
  ```php
  \App\Models\User::create([
      'name' => 'Your Name',
      'email' => 'you@tramaxentertainment.com',
      'password' => 'a-real-password-here',
      'role_id' => \App\Models\Role::query()->where('name', \App\Models\Role::SUPER_ADMIN)->value('id'),
      'status' => 'Active',
  ]);
  ```

### 3.6 Storage link and permissions

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

The media upload pipeline writes into `storage/app/public`; without the symlink, uploaded images 404 even though the upload itself succeeds.

### 3.7 Build front-end assets (Laravel Mix)

Still inside `tramax/backend`:

```bash
npm ci
npm run build
```

This is the only thing Node.js is used for. It compiles `resources/js/app.js` and `resources/sass/app.scss` into `public/js/app.js` and `public/css/app.css`, which Apache then serves as plain static files — nothing Node-related needs to keep running afterward, and there's no cPanel "Setup Node.js App" entry to create for this project at all.

### 3.8 Smoke-test the app

```bash
curl -s https://staging.tramaxentertainment.com/
curl -s https://staging.tramaxentertainment.com/api/v1/artists
```

The first should return the public homepage's HTML; the second should return `{"data":[]}` (empty is correct — nothing's been created yet) rather than an error page. Then visit `https://staging.tramaxentertainment.com/admin/login` in a browser and sign in with the account from step 3.5.

---

## 4. SSL

cPanel almost always offers free AutoSSL (Let's Encrypt) — cPanel → **SSL/TLS Status** → select the subdomain → **Run AutoSSL**. Do this before sharing the staging link with anyone.

---

## Deploying an update

Once the above is done once, pushing new commits to staging is much shorter:

```bash
cd ~/tramax/backend
git pull origin main

composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:clear
```

Nothing needs restarting afterward — PHP is invoked fresh per request by the web server (not a long-running process), and the asset build just overwrites the static files in `public/js` and `public/css`.

---

## What staging does *not* have yet

- Automated deploys — every update above is manual. A GitHub Actions SSH-deploy workflow is a reasonable follow-up once this manual path is confirmed working.
- A CDN/S3 media disk — uploads work (§ the media upload pipeline) but live on this server's local disk, not yet behind a CDN. `FILESYSTEM_DISK=s3` in `config/filesystems.php` is ready for that when it's time; today it stays `local`.
