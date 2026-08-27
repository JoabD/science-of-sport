# Science of Sport - Event Directory

A small Laravel app for a fictional non-profit ("Science of Sport") to list
and manage their fundraising events. Built as a single landing page: no
dashboard, no separate admin area - login, registration, account settings
and event management all happen on `/` through Bootstrap modals, with the
event table loaded and paginated over AJAX.

This started from the Laravel Breeze starter kit, which is why some of the
auth scaffolding (password reset, email verification pages) still looks
like stock Breeze/Tailwind while the rest of the app uses Bootstrap.

## Stack

- Laravel 13 / PHP 8.3
- MySQL
- Bootstrap 5 (CDN) for the UI, vanilla JS (no framework) for the AJAX bits
- Vite for bundling `resources/css` and `resources/js`
- PHPUnit for tests

## Requirements

- PHP >= 8.3 with the usual extensions (pdo_mysql, mbstring, etc)
- Composer
- Node.js >= 18 + npm
- MySQL (or edit `.env` to point at whatever DB you have)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Set your DB connection in `.env` (defaults to mysql on 127.0.0.1, db name
`science_of_sport`), then:

```bash
php artisan migrate --seed
npm install
npm run build      # or `npm run dev` while working on the front-end
php artisan serve
```

Open `http://127.0.0.1:8000`.

### Seeded accounts

`RoleAndUserSeeder` creates one user per role so you can test both sides
without registering a new account:

| Role  | Email                       | Password      |
|-------|------------------------------|----------------|
| admin | admin@sciencesport.org       | password123    |
| user  | user@sciencesport.org        | password123    |

`PostSeeder` also drops in a handful of sample events (including the Golf
Classic one with its full pricing table) so the event list isn't empty on
first load.

## Roles

There are 2 roles, stored in a `roles` table and referenced from `users.role_id`
(see `app/Models/Role.php` for why it's a table and not just an enum column).

- **admin** - can create, edit and delete events, and see event details.
- **user** (regular, logged in) - can browse events and see event details,
  can't create/edit/delete.
- **guest** (not logged in) - can browse the event list only. Trying to view
  details or create an event shows a toast asking to log in.

Who's allowed to do what lives in one place: `app/Policies/PostPolicy.php`.
Both the Blade views (`@can(...)`) and the FormRequests
(`StorePostRequest`/`UpdatePostRequest`) check against that same policy, so
there's no risk of the UI hiding a button while the route stays open to
anyone - I made that mistake early on (the "Create Event" button was
hidden for non-admins but the route itself had no server-side check) and
fixed it once I noticed.

## How the "everything on one page" thing works

- `GET /` renders the landing page shell (`posts/show.blade.php`), the
  event table itself is filled in by JS hitting `GET /api/events`
  (paginated, no full reload).
- Login, register and the account settings (profile/password/delete) are
  Bootstrap modals rendered on that same page
  (`resources/views/partials/auth-modals.blade.php` and
  `.../profile-modal.blade.php`), they still POST to normal Laravel routes,
  nothing fancy/SPA-framework-y going on.
- If a form fails validation (wrong password, duplicate email, etc) Laravel
  redirects back to `/` as usual. So the right modal doesn't just vanish
  after that redirect, each form carries a hidden `form` field
  (`login`, `register`, `profile-info`, `create-event`, ...) and the layout
  reads it back via `old('form')` (or a `open_modal` session flash for
  redirects that aren't validation failures) to decide which modal to
  re-open on page load. See the `$openModal` bit at the top of
  `layouts/public.blade.php` and `reopenModalAfterRedirect()` in
  `resources/js/public.js`.
- The "Edit" button on an event reuses the same modal/form as
  "Create New Event" - JS fetches the event over `GET /api/posts/{id}/packages`,
  fills the form and swaps its `action`/method to the update route. It resets
  back to "create mode" when the modal is closed.
- "Delete" is a plain HTML form per row (method DELETE, spoofed) with a
  `confirm()` in `onsubmit`, submitted normally, no AJAX for that one.

## Architecture

Trying to keep controllers thin here on purpose:

```
Route -> FormRequest (validate + authorize) -> Controller (orchestrates) -> Service (does the work)
                                                     |
                                                     v
                                                  Policy (who's allowed to do what)
```

- `app/Http/Controllers/PostController.php` - only calls into the service
  and returns a redirect/JSON response. No business logic.
- `app/Http/Requests/StorePostRequest.php` / `UpdatePostRequest.php` -
  validation rules + `authorize()` (checks `PostPolicy` under the hood).
- `app/Policies/PostPolicy.php` - single source of truth for "is this user
  allowed to create/update/delete events" (currently: only admins, via the
  `before()` hook).
- `app/services/PostService.php` - the actual create/update/delete logic,
  wrapped in DB transactions where more than one table gets written to.

## Data model

```
User --belongsTo--> Role
User --hasMany----> Post   (the events)
Post --hasMany----> PostPackage   (pricing tiers, e.g. "Title Sponsor - $15,000")
```

## Tests

```bash
php artisan test
```

Covers auth (login/register/logout), profile updates, password reset/update,
email verification and password confirmation. Ran with an in-memory SQLite
DB (see `phpunit.xml`), doesn't touch your real dev database.

## Known rough edges

- `app/services/` is lowercase while its namespace is `App\Services` - works
  fine on Windows (case-insensitive filesystem), would need renaming to
  `Services/` before deploying to a case-sensitive filesystem like most
  Linux hosts.
- Email verification wiring exists (routes, views) but `User` doesn't
  implement `MustVerifyEmail`, so it's effectively unused right now.
