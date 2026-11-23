# Explore. Travel. Discover.

A travel & tourism website in plain PHP 8.3 (no framework). Visitors can browse and search destinations, check the live
weather and map for each one, book trips and manage their bookings. Admins get a dashboard for destinations, bookings
and contact messages.

## Features

- **Destinations**: stored in the `destinations` table. `/destinations` has search (city or country), a region
  filter, a max-price filter and sorting. The header search bar goes there.
- **Destination pages** (`/destinations/{slug}`): description, current weather and a 5-day forecast from
  [Open-Meteo](https://open-meteo.com/) (no API key), a Leaflet + OpenStreetMap map with a marker, and a booking box.
- **Bookings**:
  - Both the home page "Book now" form and the box on each destination page save to the `bookings` table. The total
    is price × guests, calculated on the server.
  - You must be logged in to book. Guests are sent to log in and then brought back to the same page, with their
    details kept.
  - Dates are validated: no past arrivals, and the leaving date must be after the arrival date.
  - **My trips** (`/my-trips`) lists your bookings and lets you cancel upcoming ones. Users can only see and cancel
    their own bookings.
- **Accounts**:
  - Register, log in and log out.
  - Forgot password: reset links are single-use, expire after 30 minutes and are stored hashed.
  - Login and forgot-password errors are generic, so they don't reveal which emails are registered.
- **Admin dashboard** (`/admin`, `role = 'admin'` only):
  - stats: pending bookings, confirmed revenue, destinations, users, unread messages
  - add, edit and delete destinations, with validation
  - list bookings with a status filter, and change a booking's status (the guest is emailed)
  - read contact-form messages
- **Contact form**: messages are saved to the `messages` table, and also emailed to `CONTACT_TO` when SMTP is set up.

### Security

- Passwords use `password_hash` / `password_verify`.
- **Sessions are stored in the database** (`sessions` table), so they work on serverless hosting.
  - Cookies are `HttpOnly`, `SameSite=Lax` and `Secure` on HTTPS.
  - The session ID is regenerated on login and logout, and stored hashed.
- **CSRF tokens** on every POST form, checked centrally by the router.
- PDO prepared statements everywhere, and all output is escaped.
- Post-login redirects only go to paths on this site (no open redirects).
- Email is always sent *from* `MAIL_FROM`. A visitor's address is only used as Reply-To.
- All configuration comes from environment variables. Nothing secret is in the code.

## Project layout

```
api/index.php        front controller (every dynamic request goes through it)
router.php           router for `php -S` locally (same rules as vercel.json)
src/                 app code: Router, Database, DbSessionHandler, Auth, Csrf, Mailer, models, controllers
src/routes.php       all URLs
views/               PHP templates (layout, pages, admin)
db/                  schema.mysql.sql, schema.sqlite.sql, seed destinations
bin/migrate.php      create tables (and --seed sample destinations)
bin/make-admin.php   promote a user to admin
css/ js/ images/     static files (css/style.css is the original theme; new styles are in pages.css / admin.css)
```

## Run it locally

You need PHP 8.1+ with `pdo_sqlite` (or `pdo_mysql`) and [Composer](https://getcomposer.org/).
On macOS, run `brew install php composer`.

```bash
composer install
cp .env.example .env          # defaults to SQLite: nothing else to set up
php bin/migrate.php --seed    # creates storage/database.sqlite with 11 destinations
php -S localhost:8000 router.php
```

Open http://localhost:8000, register an account, then make yourself admin:

```bash
php bin/make-admin.php you@example.com
```

Without SMTP settings the site still works. With `APP_ENV=local`, the password-reset link is shown on screen
instead of being emailed. Contact messages are always saved, so you can read them in `/admin/messages`.

To use MySQL locally instead, set `DB_DRIVER=mysql` and the `DB_*` values in `.env`, then run the migration again.

## Deploy to Vercel (with a free hosted MySQL)

### 1. Create a MySQL database

Pick one:

- **TiDB Cloud Serverless** (MySQL-compatible, free tier): https://tidbcloud.com
  1. Create a *Serverless* cluster.
  2. Click **Connect**, choose "General" and generate a password.
  3. Note the host (`gateway01.<region>.prod.aws.tidbcloud.com`), the port (`4000`) and the user (`xxxxxxxx.root`).
     The database is `test`, or create your own.
  4. TLS is required. The standard system CA bundle works, so leave `DB_SSL_CA` empty.
- **Aiven for MySQL** (free plan): https://aiven.io
  1. Create a free MySQL service.
  2. From the overview page, note the host, port, user (`avnadmin`), password and database (`defaultdb`).
  3. Aiven uses its own CA. Download **CA certificate** (`ca.pem`) and paste its whole contents into `DB_SSL_CA`.

### 2. Create the tables in the hosted database

Run the migration from your computer, pointing it at the hosted database. Either put the values in `.env` or pass
them inline:

```bash
DB_DRIVER=mysql DB_HOST=... DB_PORT=4000 DB_DATABASE=test DB_USERNAME=... DB_PASSWORD=... DB_SSL=true \
  php bin/migrate.php --seed
```

(For Aiven, also add `DB_SSL_CA=/path/to/ca.pem`.)

### 3. Import the repo in Vercel

1. Go to https://vercel.com/new and import this GitHub repository.
2. **Framework preset: Other.** Leave the build and output settings empty. `vercel.json` defines everything: it uses
   the community PHP runtime `vercel-php@0.7.3` (PHP 8.3) for `api/index.php` and serves only `/css`, `/js` and
   `/images` as static files. `src/`, `views/`, `db/` and `.env` can never be downloaded.
3. **Turn on Git LFS**: in *Project → Settings → Git*, enable **Git Large File Storage (LFS)**. The hero videos
   (`images/*.mp4`) are stored in LFS. Without this setting the videos are deployed as tiny pointer files and won't
   play. Redeploy after turning it on.
4. Add the environment variables (*Project → Settings → Environment Variables*):

   | Variable | Example | Notes |
   |---|---|---|
   | `APP_ENV` | `production` | never `local` on a public site |
   | `APP_URL` | `https://your-project.vercel.app` | no trailing slash; used in email links |
   | `DB_DRIVER` | `mysql` | |
   | `DB_HOST` | `gateway01.eu-central-1.prod.aws.tidbcloud.com` | |
   | `DB_PORT` | `4000` (TiDB) / Aiven's port | |
   | `DB_DATABASE` | `test` / `defaultdb` | |
   | `DB_USERNAME` | `xxxxxxxx.root` / `avnadmin` | |
   | `DB_PASSWORD` | … | |
   | `DB_SSL` | `true` | TLS to the database |
   | `DB_SSL_CA` | *(Aiven only)* contents of `ca.pem` | paste the PEM text |
   | `SMTP_HOST` | `smtp.gmail.com` | optional: leave empty to disable email |
   | `SMTP_PORT` | `587` | |
   | `SMTP_ENCRYPTION` | `tls` | `tls` for 587, `ssl` for 465 |
   | `SMTP_USERNAME` | `you@gmail.com` | |
   | `SMTP_PASSWORD` | Gmail *app password* | Google Account → Security → App passwords |
   | `MAIL_FROM` | `you@gmail.com` | must be an address your SMTP provider lets you send from |
   | `MAIL_FROM_NAME` | `Travel` | |
   | `CONTACT_TO` | `you@example.com` | where contact-form messages are emailed |

5. Deploy.

### 4. Make yourself admin

Register on the live site, then run the CLI script from your computer against the hosted database (same `DB_*`
values as in step 2):

```bash
DB_DRIVER=mysql DB_HOST=... DB_PORT=... DB_DATABASE=... DB_USERNAME=... DB_PASSWORD=... DB_SSL=true \
  php bin/make-admin.php you@example.com
```

Refresh the site: the admin icon appears in the header, and `/admin` opens the dashboard.

## Notes

- Sessions live in the `sessions` table and old ones are cleaned up automatically. Resetting a password logs that
  account out everywhere.
- Deleting a destination keeps its bookings: each booking stores the destination name and price at booking time.
- Destination images can be a path in this repo (`images/p-1.jpg`) or any `https://` URL.
