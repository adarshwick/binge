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

## Launch Readiness Plan

### Configuration
- Run migrations and seeds: `php artisan migrate --force` and `php artisan db:seed --class=InitialSetupSeeder`
- Fill App Settings: payments (`stripe_*`, `paypal_*`), mail (`smtp_*`, `mail_from_*`), chat/video providers, verification mode/provider, monetization/ads, ICE servers (JSON or STUN/TURN), SEO/analytics
- Shared settings available via Inertia props (ICE/SEO/analytics)

### Security
- Global headers: CSP, HSTS (HTTPS), nosniff, clickjacking, permissions
- Middleware: phone OTP gating, ban enforcement, mandatory verification
- Review CSP console; allow required origins for Stripe/PayPal/WebRTC/Chime/Agora/Firebase

### Payments
- Stripe: set keys and webhook secret; test Checkout/Intents and webhooks (idempotent)
- PayPal: set client/secret/webhook id; test Orders/Capture and webhooks
- User Billing page and Filament Payments filters

### Email
- Configure SMTP in App Settings; verify email verification and password reset
- `User` implements `MustVerifyEmail`

### Chat
- Text/image/voice; typing indicator; read receipts and delivery status
- Pagination with Load more; unread badges update in real time
- Throttling applied to media uploads

### Video
- Providers: WebRTC/Chime/Agora toggle in settings
- ICE servers: JSON list or STUN/TURN fields; verify call setup and media

### Onboarding & Verification
- Photo uploads (camera/gallery) stored under `profile_photos`; server-generated thumbnails
- Selfie verification: AWS Rekognition auto-approve when enabled; mandatory/optional gating via settings

### Discover & Matching
- Age/distance filters with Haversine; boost-first sorting
- Thumbnails with lazy loading; like/super like/match creation; push notifications

### Mobile
- Capacitor push token registration; server-side FCM notifications
- Location updates to influence Discovery distance; AdMob banner support

### Admin
- Filament resources: Users, Verifications, Reports, Payments, Plans, Credit Packs, Prompts, App Settings
- App Settings table filters: by group and key contains; dashboard stats widget

### PWA/SEO/Analytics
- `manifest.webmanifest` and `service-worker.js` with registration in layouts
- SEO title/description and analytics snippet via App Settings
- `robots.txt` included; optionally add `sitemap.xml`

### Performance
- Short TTL caching for Discover results and chat list; real-time badge updates via broadcasts
- Consider DB indexes for messages/likes/payments

### Testing
- Add tests for payment webhooks, chat messaging/broadcasts, verification/OTP middleware
- Manual smoke: registration, onboarding, discovery, likes/matches, chat (media), video, purchases, billing, admin moderation

### Deployment
- Cache config/routes/views: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
- Public storage link: `php artisan storage:link`
- Serve over HTTPS to enable HSTS; monitor logs and alerts
