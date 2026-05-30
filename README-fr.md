# 🎴 Cartes Animées

Application éducative destinée aux enfants malentendants, permettant d'associer des mots à des animations visuelles (GIF + vidéo) accompagnées de sons.

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Back-end | Laravel 12 |
| Panels d'administration | Filament 4 |
| Authentification API | Laravel Sanctum |
| Base de données | MySQL |
| Paiement | Stripe |
| Frontend | Vue.js 3 *(à venir)* |
| Mails locaux | Mailpit |

---

## Prérequis

- PHP 8.4+
- Composer
- Node.js + npm
- MySQL
- [Herd](https://herd.laravel.com/) *(recommandé)* ou serveur PHP local
- [Stripe CLI](https://stripe.com/docs/stripe-cli) pour les webhooks en local

---

## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/ton-repo/cartes-animees.git
cd cartes-animees

# 2. Installer les dépendances PHP et JS
composer install
npm install

# 3. Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données dans .env
DB_DATABASE=cartes_animees
DB_USERNAME=root
DB_PASSWORD=

# 5. Configurer Stripe dans .env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# 6. Configurer le mail dans .env (Mailpit en local)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@cartes-animees.fr"
MAIL_FROM_NAME="Cartes Animées"

# 7. Builder les assets
npm run build
```

---

## Initialisation de l'application

La commande `app:init` initialise complètement l'application :
- Remet la base de données à zéro (`migrate:fresh`)
- Nettoie le sandbox Stripe
- Crée le produit et le prix Stripe
- Crée le compte administrateur
- Initialise les paramètres système (taux de commission, prix abonnement)

```bash
# Initialisation seule (production / démo vierge)
php artisan app:init

# Initialisation + données de test (dev)
php artisan app:init --seed
```

> ⚠️ `--seed` effectue de vrais appels à l'API Stripe sandbox. La queue doit tourner pour que les abonnements soient traités.

---

## Démarrage en développement

```bash
composer dev
```

Cette commande lance en parallèle :
- `php artisan serve` — serveur PHP
- `php artisan queue:listen` — worker de queue *(obligatoire pour les notifications et les mails)*
- `php artisan pail` — logs en temps réel
- `npm run dev` — Vite / hot reload

**Dans un terminal séparé**, pour les webhooks Stripe :

```bash
stripe listen --forward-to http://localhost:8000/api/stripe/webhook
```

> Mailpit est accessible sur [http://localhost:8025](http://localhost:8025)

---

## Panels d'administration

| Panel | URL | Rôle |
|-------|-----|------|
| Administrateur | `/admin` | Admin uniquement |
| Orthophoniste | `/therapist` | Orthophoniste + Admin |

---

## API REST

Base URL : `/api`

### Authentification

| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/auth/register` | Inscription parent |
| POST | `/auth/login` | Connexion |
| POST | `/auth/logout` | Déconnexion |
| GET | `/auth/me` | Utilisateur connecté |
| POST | `/auth/password/forgot` | Mot de passe oublié |
| POST | `/auth/password/reset` | Réinitialisation mot de passe |

### Enfants

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/children` | Liste des enfants du parent |
| POST | `/children` | Créer un enfant |
| GET | `/children/{id}` | Détail d'un enfant |
| PUT | `/children/{id}` | Modifier un enfant |
| DELETE | `/children/{id}` | Supprimer un enfant (soft) |

### Séries & Progression

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/children/{id}/series` | Séries débloquées pour un enfant |
| GET | `/children/{id}/series/{seriesId}` | Détail série + cartes |
| POST | `/therapist/patients/{id}/series/{seriesId}` | Débloquer une série |
| PATCH | `/therapist/patients/{id}/series/{seriesId}` | Marquer comme complétée |
| DELETE | `/therapist/patients/{id}/series/{seriesId}` | Retirer une série |

### Orthophoniste

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/therapist/patients` | Liste des patients |
| GET | `/therapist/patients/{id}` | Détail d'un patient |
| POST | `/therapist/invitation-code` | Régénérer le code d'invitation |
| POST | `/children/{id}/therapist` | Affilier un orthophoniste (code invitation) |
| DELETE | `/children/{id}/therapist/{therapistId}` | Retirer un orthophoniste |

### Abonnements

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/children/{id}/subscription` | Abonnement d'un enfant |
| POST | `/children/{id}/subscription` | Créer un abonnement |
| DELETE | `/children/{id}/subscription` | Annuler un abonnement |

### Profil & Notifications

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/profile` | Profil utilisateur |
| PUT | `/profile` | Modifier le profil |
| PATCH | `/profile/password` | Modifier le mot de passe |
| GET | `/notifications` | Liste des notifications |
| PATCH | `/notifications/{id}` | Marquer comme lue |
| PATCH | `/notifications/read-all` | Tout marquer comme lu |

---

## Commandes utiles

```bash
# Lancer les tests Stripe en local
stripe listen --forward-to http://localhost:8000/api/stripe/webhook
stripe trigger invoice.payment_failed
stripe trigger invoice.payment_succeeded
stripe trigger customer.subscription.deleted

# Envoyer les notifications de non-progression
php artisan notifications:no-progress

# Voir les tâches schedulées
php artisan schedule:list

# Lancer le scheduler manuellement
php artisan schedule:run
```

---

## Variables d'environnement importantes

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

## Structure du projet

```
app/
├── Console/Commands/       # Commandes artisan (app:init, notifications:no-progress)
├── Enums/                  # Enums PHP (UserRole, SubscriptionStatus, ...)
├── Filament/
│   ├── Admin/              # Panel administrateur
│   └── Therapist/          # Panel orthophoniste
├── Http/Controllers/Api/   # Controllers API REST
├── Models/                 # Modèles Eloquent
├── Notifications/          # Classes de notification (mail + in-app)
├── Observers/              # Observers Eloquent
└── Services/               # Services (StripeService, StripeTestDataService, ...)
```

---

## Projet académique

PFE — Mastère Expert en Systèmes d'Information (ESI2)
Institut Limayrac — Année 2025-2026
Étudiant : Jean Lautraite | Tuteur technique : Stéphane Blusson
