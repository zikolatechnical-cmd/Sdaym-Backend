<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
## Sdaym Backend

Sdaym connects a Salla merchant store to a merchant dashboard and Flutter
clients. It receives store events through signed Salla webhooks, stores special
offers, and sends a Firebase Cloud Messaging notification when a new offer is
created.

### Implemented features

- Salla merchant authorization and encrypted OAuth-token storage.
- Signed, idempotent processing of Salla webhook deliveries.
- Persistence of created and updated Salla special offers.
- Firebase topic subscription for Android, iOS, and web devices.
- One FCM topic notification for each newly created special offer.
- Merchant dashboard authentication using JWT access and refresh tokens.
- A forced-password-change flag for accounts created from Salla authorization.
- Queued merchant credential emails using the configured Laravel mailer.
- Repeatable demo seed data and an Apidog/Postman collection.

### Event flow

1. Salla sends `app.store.authorize` after the app is authorized. The queued
   job fetches the real merchant profile, stores encrypted Salla tokens, creates
   a dashboard user when needed, and queues the credentials email.
2. A Flutter or web client calls `POST /api/devices`. The token is stored in
   encrypted form and subscribed to the merchant's FCM topic.
3. Salla sends `specialoffer.created` or `specialoffer.updated`. The endpoint
   verifies `X-Salla-Signature`, stores a receipt, and queues processing.
4. The worker upserts the offer. Only `specialoffer.created` sends an FCM
   notification, and duplicate webhook deliveries do not repeat the work.

### Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
php artisan queue:work --tries=5
```

Configure these values in `.env`:

```dotenv
QUEUE_CONNECTION=database
SALLA_WEBHOOK_SECRET=the-exact-secret-from-salla-partners
SALLA_USER_INFO_URL=https://accounts.salla.sa/oauth2/user/info
FIREBASE_CREDENTIALS=storage/app/firebase_credentials.json
```

The Firebase path must point to a private Admin SDK service-account JSON file.
Do not commit that file. To deliver credential emails through Gmail, configure
Laravel SMTP with `smtp.gmail.com`, port `587`, TLS, the Gmail address, and a
Google App Password.

### Salla Partners configuration

Install the same Salla app on the demo store being tested. In the app's
Webhooks/Notifications settings, set the public HTTPS callback to:

```text
https://your-public-domain.example/api/webhooks/salla
```

Subscribe the app to these events:

- `app.store.authorize`
- `specialoffer.created`
- `specialoffer.updated`

The webhook secret in Salla Partners must exactly match
`SALLA_WEBHOOK_SECRET`. A local-only URL such as `localhost` is not reachable
from Salla; use a stable public domain or an HTTPS tunnel while developing.
When using a temporary tunnel, update the Salla callback whenever its URL
changes.

### Queue requirement

Webhook processing and credential email delivery are asynchronous. With
`QUEUE_CONNECTION=database`, keep this command running:

```bash
php artisan queue:work --tries=5
```

The webhook endpoint returning `202 Accepted` only confirms receipt. The offer
will appear in `special_offers` after the queue worker processes its job. Use
`php artisan queue:failed` to inspect failures.

### API endpoints

| Method | Endpoint | Authentication | Description |
| --- | --- | --- | --- |
| `POST` | `/api/auth/login` | Public | Validates email/password and returns a JWT. |
| `GET` | `/api/auth/me` | Bearer JWT | Returns the user and linked merchant. |
| `POST` | `/api/auth/refresh` | Bearer JWT | Replaces the current JWT with a new token. |
| `POST` | `/api/auth/logout` | Bearer JWT | Invalidates the current JWT. |
| `POST` | `/api/auth/change-password` | Bearer JWT | Changes the password and clears the forced-change flag. |
| `POST` | `/api/devices` | Public | Stores an FCM token and subscribes it to the store topic. |
| `GET` | `/api/stores/{store_key}/offers` | Public | Returns the store's offers with pagination. |
| `POST` | `/api/webhooks/salla` | Salla signature | Receives and queues supported Salla events. |

The `store_key` is the merchant's public UUID, not its internal database ID or
Salla merchant ID. Notification data includes `type`, `offer_id`, `store_key`,
and `event`.

### Demo data

Run the demo seeder with:

```bash
php artisan db:seed
```

It creates one dashboard user, one merchant, and two offers. The seeder uses
`updateOrCreate`, so it can be run repeatedly without duplicating these rows.

```text
Email:     test@example.com
Password:  12345678
Store key: 123e4567-e89b-12d3-a456-426614174000
```

These credentials are for local/demo use only. The seeded merchant has a fake
Salla ID and is not automatically linked to a real Salla demo store. End-to-end
webhook testing requires installing the app on the Salla demo store so the real
merchant ID is stored through the authorization flow.

### Apidog collection

Import [`docs/Sdaym-Backend.postman_collection.json`](docs/Sdaym-Backend.postman_collection.json)
into Apidog using the **Postman** source format. Set `baseUrl` to the local or
public API URL. A successful Login request automatically saves `token` and
`storeKey` for subsequent requests.

### Verification commands

```bash
php artisan test
vendor/bin/pint --test
php artisan route:list --path=api
php artisan queue:failed
```
