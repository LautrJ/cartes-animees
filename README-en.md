# 🎴 Cartes Animées

An educational web application for hearing-impaired children, allowing them to associate words with animated visuals (GIF + video) accompanied by sounds.

---

## Tech Stack

| Component | Technology |
|-----------|------------|
| Back-end | Laravel 12 |
| Admin Panels | Filament 4 |
| API Authentication | Laravel Sanctum |
| Database | MySQL |
| Payments | Stripe |
| Frontend | Vue.js 3 *(coming soon)* |
| Local Mail | Mailpit |

---

## Requirements

- PHP 8.4+
- Composer
- Node.js + npm
- MySQL
- [Herd](https://herd.laravel.com/) *(recommended)* or local PHP server
- [Stripe CLI](https://stripe.com/docs/stripe-cli) for local webhooks

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-repo/cartes-animees.git
cd cartes-animees

# 2. Install PHP and JS dependencies
composer install
npm install

# 3. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure the database in .env
DB_DATABASE=cartes_animees
DB_USERNAME=root
DB_PASSWORD=

# 5. Configure Stripe in .env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# 6. Configure mail in .env (Mailpit locally)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@cartes-animees.fr"
MAIL_FROM_NAME="Cartes Animées"

# 7. Build assets
npm run build
```

---

## Application Initialization

The `app:init` command fully initializes the application:
- Resets the database (`migrate:fresh`)
- Clears the Stripe sandbox
- Creates the Stripe product and price
- Creates the administrator account
- Initializes system settings (commission rate, subscription price)

```bash
# Initialization only (production / clean demo)
php artisan app:init

# Initialization + test data (development)
php artisan app:init --seed
```

> ⚠️ `--seed` makes real calls to the Stripe sandbox API. The queue worker must be running for subscriptions to be processed.

---

## Development

```bash
composer dev
```

This command runs in parallel:
- `php artisan serve` — PHP server
- `php artisan queue:listen` — queue worker *(required for notifications and emails)*
- `php artisan pail` — real-time logs
- `npm run dev` — Vite / hot reload

**In a separate terminal**, for Stripe webhooks:

```bash
stripe listen --forward-to http://localhost:8000/api/stripe/webhook
```

> Mailpit is available at [http://localhost:8025](http://localhost:8025)

---

## Admin Panels

| Panel | URL | Role |
|-------|-----|------|
| Administrator | `/admin` | Admin only |
| Speech Therapist | `/therapist` | Therapist + Admin |

---

## REST API

Base URL: `/api`

### Authentication

| Method | Route | Description |
|--------|-------|-------------|
| POST | `/auth/register` | Parent registration |
| POST | `/auth/login` | Login |
| POST | `/auth/logout` | Logout |
| GET | `/auth/me` | Authenticated user |
| POST | `/auth/password/forgot` | Forgot password |
| POST | `/auth/password/reset` | Reset password |

### Children

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/children` | List parent's children |
| POST | `/children` | Create a child |
| GET | `/children/{id}` | Child details |
| PUT | `/children/{id}` | Update a child |
| DELETE | `/children/{id}` | Delete a child (soft) |

### Series & Progression

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/children/{id}/series` | Unlocked series for a child |
| GET | `/children/{id}/series/{seriesId}` | Series detail + cards |
| POST | `/therapist/patients/{id}/series/{seriesId}` | Unlock a series |
| PATCH | `/therapist/patients/{id}/series/{seriesId}` | Mark as completed |
| DELETE | `/therapist/patients/{id}/series/{seriesId}` | Remove a series |

### Therapist

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/therapist/patients` | Patient list |
| GET | `/therapist/patients/{id}` | Patient details |
| POST | `/therapist/invitation-code` | Regenerate invitation code |
| POST | `/children/{id}/therapist` | Link a therapist (invitation code) |
| DELETE | `/children/{id}/therapist/{therapistId}` | Remove a therapist |

### Subscriptions

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/children/{id}/subscription` | Child's subscription |
| POST | `/children/{id}/subscription` | Create a subscription |
| DELETE | `/children/{id}/subscription` | Cancel a subscription |

### Profile & Notifications

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/profile` | User profile |
| PUT | `/profile` | Update profile |
| PATCH | `/profile/password` | Update password |
| GET | `/notifications` | Notification list |
| PATCH | `/notifications/{id}` | Mark as read |
| PATCH | `/notifications/read-all` | Mark all as read |

---

## Useful Commands

```bash
# Test Stripe webhooks locally
stripe listen --forward-to http://localhost:8000/api/stripe/webhook
stripe trigger invoice.payment_failed
stripe trigger invoice.payment_succeeded
stripe trigger customer.subscription.deleted

# Send no-progress notifications
php artisan notifications:no-progress

# List scheduled tasks
php artisan schedule:list

# Run the scheduler manually
php artisan schedule:run
```

---

## Key Environment Variables

```env
APP_URL=http://localhost:8000
APP_FRONTEND_URL=http://localhost:5173

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_PRODUCT_ID=

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

---

## Project Structure

```
app/
├── Console/Commands/       # Artisan commands (app:init, notifications:no-progress)
├── Enums/                  # PHP Enums (UserRole, SubscriptionStatus, ...)
├── Filament/
│   ├── Admin/              # Administrator panel
│   └── Therapist/          # Speech therapist panel
├── Http/Controllers/Api/   # REST API controllers
├── Models/                 # Eloquent models
├── Notifications/          # Notification classes (mail + in-app)
├── Observers/              # Eloquent observers
└── Services/               # Services (StripeService, StripeTestDataService, ...)
```

---

## Academic Project

Final Year Project (PFE) — Mastère Expert en Systèmes d'Information (ESI2)
Institut Limayrac — 2025-2026
Student: Jean Lautraite | Technical Supervisor: Stéphane Blusson
