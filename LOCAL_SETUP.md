# Local Development Setup — XAMPP

How to run Tramax locally against XAMPP (Apache + MariaDB). Tramax is a single Laravel application — Blade views, served entirely by Apache/PHP — so there's no separate frontend dev server to run alongside it; Node/npm are only used to compile front-end assets (Laravel Mix). Written for this machine's XAMPP install at `C:\xampp`; adjust paths if yours differs.

## 1. One-time setup

### 1.1 PHP & Composer on PATH

XAMPP's PHP (`C:\xampp\php`) has been added to your **User** PATH, and Composer is installed as `C:\xampp\php\composer.phar` with a `composer.bat` wrapper alongside it — so `php` and `composer` work from any new terminal (PowerShell, Git Bash, cmd). **Open a new terminal window** for this to take effect if you had one open already.

Verify:

```powershell
php -v
composer --version
```

### 1.2 Database

A `tramax` database already exists in the local MariaDB instance (`utf8mb4_unicode_ci`), matching `backend/.env`'s `DB_DATABASE=tramax`. XAMPP's MySQL/MariaDB defaults to user `root` with no password — fine for local dev, never used in production.

### 1.3 Apache virtual host

`C:\xampp\apache\conf\extra\httpd-vhosts.conf` now defines two vhosts:

- A default catch-all so `http://localhost/` still serves `C:\xampp\htdocs` (the XAMPP dashboard and any other local projects there) — required once any named vhost exists, or Apache would otherwise serve the first vhost for every request.
- `tramax.local`, `DocumentRoot` pointed at `C:\xampp\htdocs\tramax\backend\public` — the project lives directly under `htdocs` (moved there so it's exactly where XAMPP expects local projects, rather than the earlier setup which served it from an path outside `htdocs`).

### 1.4 Hosts file — the one step that needs your own admin action

Apache is already serving `tramax.local` correctly, but Windows won't resolve that hostname to your machine until it's in the hosts file, which needs administrator rights I don't have from here. Add this line yourself:

1. Open Notepad **as Administrator** (right-click → Run as administrator).
2. Open `C:\Windows\System32\drivers\etc\hosts`.
3. Add this line, save:
   ```
   127.0.0.1   tramax.local
   ```

Or, from an **elevated** PowerShell:

```powershell
Add-Content -Path "$env:SystemRoot\System32\drivers\etc\hosts" -Value "127.0.0.1`ttramax.local"
```

Once that's saved, `http://tramax.local/` in a browser hits the Laravel backend directly.

## 2. Starting the stack

### 2.1 Apache & MariaDB

Easiest: open the **XAMPP Control Panel** (`C:\xampp\xampp-control.exe`) and click **Start** next to Apache and MySQL. (For this session, I started both directly as background processes rather than through the Control Panel GUI — functionally identical, but the Control Panel is the normal day-to-day way to do this.)

### 2.2 Backend (Laravel)

```powershell
cd tramax\backend
php artisan key:generate    # first time only
php artisan migrate         # first time, and after new migrations
```

Then visit `http://tramax.local/`.

### 2.3 Front-end assets (Laravel Mix)

Also unrelated to XAMPP, but doesn't need its own server — it just watches `resources/js`/`resources/sass` and rewrites `public/js/app.js` / `public/css/app.css` on save. Apache serves those compiled files directly, same as any other static asset:

```powershell
cd tramax\backend
npm run watch
```

Or `npm run build` for a one-off compile (no watching) — needed once after a fresh clone, before `http://tramax.local/` will have any styling at all.

## 3. Day-to-day

| Task | Command |
|---|---|
| New migration | `php artisan make:migration create_x_table` (in `backend/`) |
| Run migrations | `php artisan migrate` |
| New model | `php artisan make:model X -mcr` |
| Tinker (REPL) | `php artisan tinker` |
| Install a PHP package | `composer require vendor/package` |
| Install a JS package | `npm install package` (in `backend/`) |
| Rebuild front-end assets once | `npm run build` (in `backend/`) |

## 4. Notes specific to this machine

- Composer's default `--prefer-dist` (zip downloads) failed repeatedly here with `Permission denied` writing to `vendor/composer/tmp-*.zip` — a known Windows issue, almost always antivirus real-time scanning locking the file mid-write. The backend was installed with `composer install --prefer-source` instead, which clones each package via git and sidesteps the issue entirely. If you hit the same error installing new packages later, add `--prefer-source`, or add a Windows Defender (or your AV's) exclusion for this project folder and `composer.phar`'s cache dir (`composer config -g cache-dir` to find it) to use normal `--prefer-dist` speed.
