# Staging Deployment — cPanel Shared Hosting

How to deploy Tramax to staging on a cPanel account with PHP, MySQL, SSH/Terminal access, and Node.js App (Passenger) support.

- Backend (Laravel API): `https://api-staging.tramaxentertainment.com`
- Frontend (Next.js): `https://staging.tramaxentertainment.com`

This is a manual runbook, not an automated pipeline — I have no access to your hosting account, so every step here is something you run yourself over SSH or click in cPanel. Re-run the "Deploying an update" section at the bottom whenever you want to push new commits to staging.

---

## 0. Before you start

- [ ] Confirm PHP 8.2+ is available (`composer.json` requires `^8.2`). Check cPanel's **MultiPHP Manager**.
- [ ] Confirm your cPanel plan's Node.js version is 18.18+ (ideally 20 or later) — check **Setup Node.js App**'s version dropdown.
- [ ] Have the repo URL handy: `https://github.com/CyberBoy025/tramax.git`. If the repo is private, generate a GitHub [personal access token](https://github.com/settings/tokens) (classic, `repo` scope) first — you'll clone with `https://<token>@github.com/CyberBoy025/tramax.git` instead of the plain URL.
- [ ] SSH into the account once to confirm access: `ssh <cpanel_user>@<your_server_host>`.

---

## 1. Create the two subdomains

In cPanel → **Domains** (or **Subdomains** on older cPanel themes):

1. Create `api-staging.tramaxentertainment.com`. When it asks for a document root, **do not accept the default** — you'll repoint it in step 3 once the code exists, since Laravel's document root must be `backend/public`, not the repo root (everything else — `.env`, `app/`, `vendor/` — must never be web-accessible).
2. Create `staging.tramaxentertainment.com`. Its document root doesn't matter — the Node.js App in step 5 serves this domain directly via Passenger, bypassing the static document root entirely.

---

## 2. Create the database

cPanel → **MySQL® Databases**:

1. Create a database, e.g. `<cpanel_user>_tramax_staging`.
2. Create a database user with a strong generated password, and **save that password** — you'll need it in step 3.
3. Add the user to the database with **All Privileges**.

---

## 3. Deploy the backend (Laravel)

SSH in, then:

```bash
cd ~
git clone https://github.com/CyberBoy025/tramax.git tramax
cd tramax/backend
```

### 3.1 Point the subdomain at `backend/public`

Back in cPanel → **Domains**, edit `api-staging.tramaxentertainment.com` and set its document root to:

```
/home/<cpanel_user>/tramax/backend/public
```

(Exact path depends on your account — confirm your home directory with `pwd` after `cd ~`.)

### 3.2 Select PHP 8.2+ for the subdomain

cPanel → **MultiPHP Manager** → select `api-staging.tramaxentertainment.com` → set to PHP 8.2 or later.

Confirm the SSH `php` binary matches — `php -v`. If it doesn't (shared hosts sometimes default SSH to an older PHP), use the versioned binary cPanel provides instead, e.g. `php82`, for every command below (`php82 artisan ...`, `php82 /usr/local/bin/composer install`, etc.) — check `ls /usr/local/bin/ | grep php`, or your host's docs, for the exact name.

### 3.3 Install dependencies

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
APP_URL=https://api-staging.tramaxentertainment.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<cpanel_user>_tramax_staging
DB_USERNAME=<the database user from step 2>
DB_PASSWORD=<that user's password>

CORS_ALLOWED_ORIGINS=https://staging.tramaxentertainment.com
```

`APP_DEBUG=false` is not optional — with it `true`, unhandled errors dump full stack traces (file paths, query text) to anyone who hits a broken endpoint. Leave the rest (`SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`, `MAIL_MAILER=log`) as `.env.example` already has them — no Redis or external mail service needed for staging.

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

### 3.7 Smoke-test the backend

```bash
curl -s https://api-staging.tramaxentertainment.com/api/v1/artists
```

Should return `{"data":[]}` (empty is correct — nothing's been created yet) rather than an error page.

---

## 4. Deploy the frontend (Next.js) — build first

Still over SSH:

```bash
cd ~/tramax/frontend
```

### 4.1 Set the API URL before building

This matters more than it sounds: `NEXT_PUBLIC_API_URL` gets baked into the JavaScript bundle **at build time**, not read at runtime. Setting it in cPanel's Node.js App environment-variables panel *after* building has no effect — the build has to see it first.

```bash
echo "NEXT_PUBLIC_API_URL=https://api-staging.tramaxentertainment.com/api/v1" > .env.production
```

### 4.2 Install and build

cPanel's Node.js App (set up next, in section 5) gives you a "Run NPM Install" button, but you can also do this now directly if the account already has a usable Node on PATH:

```bash
npm ci
npm run build
```

If `npm`/`node` aren't on PATH over plain SSH, do steps 5.1–5.2 first (which creates the Node virtual environment and shows you the `source .../activate` command), then come back and run `npm ci && npm run build` from inside that activated environment.

---

## 5. Deploy the frontend — Passenger app

cPanel → **Setup Node.js App** → **Create Application**:

- **Node.js version**: highest available (20+).
- **Application mode**: Production.
- **Application root**: `tramax/frontend` (relative to your home directory).
- **Application URL**: `staging.tramaxentertainment.com`.
- **Application startup file**: `server.js`.

`server.js` (already in the repo) exists specifically for this — Passenger requires a file that calls `.listen()` directly; it has no way to run `next start` or an npm script itself. It wraps Next.js's programmatic API and listens on whatever port Passenger assigns via `process.env.PORT`. Verified locally against a real production build before this was written.

Environment variables (in the same cPanel screen, under the app's settings): add `NEXT_PUBLIC_API_URL` here too, matching `.env.production` from step 4.1 — belt-and-suspenders in case this specific cPanel version treats it differently. Redundant, not harmful.

Steps 4.1–4.2 already installed and built the app; in the Node.js App panel, click **Restart** to launch (or relaunch) the Passenger process against that build.

### 5.1 Smoke-test the frontend

Visit `https://staging.tramaxentertainment.com/` in a browser. Then `https://staging.tramaxentertainment.com/admin/login` and sign in with the account from step 3.5 — confirms the frontend is actually reaching the backend across the two subdomains (this is what the CORS config in `config/cors.php` and the `CORS_ALLOWED_ORIGINS` env var from step 3.4 exist for).

---

## 6. SSL

cPanel almost always offers free AutoSSL (Let's Encrypt) — cPanel → **SSL/TLS Status** → select both subdomains → **Run AutoSSL**. Do this before sharing the staging link with anyone; without it, browsers will hard-block the Bearer-token login flow as mixed content if either side loads over plain HTTP while the other is HTTPS.

---

## Deploying an update

Once the above is done once, pushing new commits to staging is much shorter:

```bash
cd ~/tramax
git pull origin main

cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear

cd ../frontend
npm ci
npm run build
```

Then in cPanel → **Setup Node.js App**, click **Restart** on the frontend app — the backend needs no restart (PHP is invoked fresh per request by the web server, not a long-running process like the frontend).

---

## What staging does *not* have yet

- Automated deploys — every update above is manual. A GitHub Actions SSH-deploy workflow is a reasonable follow-up once this manual path is confirmed working.
- A CDN/S3 media disk — uploads work (§ the media upload pipeline) but live on this server's local disk, not yet behind a CDN. `FILESYSTEM_DISK=s3` in `config/filesystems.php` is ready for that when it's time; today it stays `local`.
