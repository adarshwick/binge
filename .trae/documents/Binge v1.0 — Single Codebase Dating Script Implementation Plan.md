## Monorepo Layout
- Structure: `/backend` (Laravel 11), `/mobile` (Capacitor wrappers), `/documentation.html` (install/build guide)
- Frontend: Inertia.js (React) + Vite + TailwindCSS; single responsive app served by Laravel
- Auth: Laravel Sanctum (SPA session/cookie shared for web and Capacitor webview)
- Real-time: Laravel Reverb + Laravel Echo (fallback: Pusher)
- Native bridge: Capacitor plugins for Camera, Geolocation, Push Notifications, AdMob

## Backend (Laravel)
- Install Laravel 11, configure Sanctum for SPA auth and CSRF protection
- Inertia React scaffolding with SSR disabled (client-only), Vite build pipeline
- Storage: local public disk for user images; queued jobs for image processing and push notifications
- Filament admin panel setup with role-based access (admin vs. user)
- Settings system: key-value config for Monetization Mode, API keys, feature toggles, legal pages content

## Data Model
- `users`: auth fields (name, email, dob, password, phone_verified), roles, status (active/banned)
- `profiles`: user_id, bio, gender, preferences, onboarding_completed, location (lat/lng)
- `profile_photos`: user_id, path, order, verified (for selfie verification)
- `profile_prompts`: admin-defined prompts; `user_prompt_answers`: user_id, prompt_id, answer
- `likes`: from_user_id, to_user_id, type (like/super_like/pass), created_at
- `matches`: user_id_a, user_id_b, created_at (derive from mutual likes)
- `conversations`: match_id; `messages`: conversation_id, sender_id, type (text/image/gif/voice), content, delivered_at/read_at
- `verification_queue`: user_id, selfie_photo_id, status (pending/approved/rejected)
- `reports`: reporter_id, reported_user_id, reason, status (queued/warned/banned)
- Monetization: `subscription_plans`, `subscription_plan_features` (plan-feature mapping), `user_subscriptions` (status, period), `credit_packs`, `user_credit_ledger` (balance changes and consumption events)
- Payments/Ads/API keys: `app_settings` (grouped keys), plus `feature_toggles`

## Admin Panel (Filament)
- Dashboard: cards for total users, new users today, total revenue, active subscriptions
- Users: list/search/edit/ban; view profile, photos, prompts, credit balance, subscription
- Verification: queue view with selfie; Approve/Reject actions
- Reports: queue view; actions Warn/Ban with audit trail
- Monetization:
  - Master switch: Free, Premium, Credits, Hybrid → drives frontend visibility/gating
  - Subscriptions: CRUD plans with price and checkbox features (Unlimited Swipes, See Who Liked Me, Ad-Free)
  - Credits: CRUD packs; set per-feature prices (Super Like, Boost)
  - Payments: Stripe/PayPal API keys; test/live mode flags
  - Ads: AdMob ID (mobile) and AdSense snippet (web)
- App Settings:
  - Feature toggles: Video Chat, Voice Notes, Require Phone (OTP) Verification
  - API keys: Reverb/Pusher, Agora/ZegoCloud, Google Maps, SMS Gateway
- Content Mgmt: CRUD for Profile Prompts; WYSIWYG editors for `/privacy` and `/terms`

## Frontend (Inertia React)
- Main App Shell: responsive layout
  - Mobile (<768px): bottom tab bar (Discover, Matches, Chat, Profile)
  - Desktop (>=768px): left sidebar with same nav; bottom bar hidden
- Public & Auth pages:
  - Landing (`/`): brand, app store badges, Login/Launch Web App button
  - Login (`/login`): email/password, Forgot Password, Google sign-in button, link to register
  - Register (`/register`): name, email, DOB, password/confirm; link to login
  - Legal (`/privacy`, `/terms`): content loaded from Admin settings (WYSIWYG)
- Onboarding Wizard:
  - Routes: `/onboarding/start`, `/onboarding/photos`, `/onboarding/profile`, `/onboarding/filters`
  - Photos: 2–9 photos; use Capacitor Camera for take/choose on mobile; drag/drop on web
  - Profile: bio + pick 3 prompts, answer inline
  - Filters: initial discovery filters (Age, Distance, Gender)
- Core App pages:
  - Discover (`/app/discover`): swipe card stack with buttons (Pass, Super Like, Like, Boost); implement touch/mouse gestures without heavy deps
  - Matches (`/app/matches`): responsive grid of mutual matches
  - Chat List (`/app/chat`): mobile full-screen list; web 2-column list + placeholder right pane
  - Active Chat (`/app/chat/{match_id}`): mobile replaces list with back; web loads into right pane; bubbles, input with GIF/Image/Voice, Video Call icon
  - User Profile (`/app/profile`): view own profile; buttons Edit Profile, Settings
  - Edit Profile (`/app/profile/edit`): form for photos, bio, prompts
  - Go Premium (`/app/premium`): dynamic render of subscription plans and/or credit packs incl. features based on Admin settings
- Real-time & Notifications:
  - Laravel Echo connects to Reverb; private channels for conversations and match events
  - Capacitor PushNotifications: register on login; handle incoming to update Chat/Matches
- Ads Integration:
  - Web: render admin-provided AdSense snippet in designated slots when ads enabled
  - Mobile: Capacitor AdMob: show/hide banner or interstitial per admin config

## Native Bridge (Capacitor)
- Project init in `/mobile`; configure `capacitor.config` with appId, appName, server URL
- Plugins: `@capacitor/camera`, `@capacitor/geolocation`, `@capacitor/push-notifications`; AdMob plugin
- Permissions:
  - iOS: add Camera, Photo Library, Location, Push usage descriptions in Info.plist
  - Android: add required permissions in AndroidManifest; set FCM for push
- Sync/build: `npm run build` then `npx cap sync`; ensure web assets are produced by Vite

## Matching & Discovery Logic
- Like/Pass/Super Like actions update `likes`; mutual like creates `matches`
- Discovery feed respects filters (age, distance, gender) and excludes passed/blocked users
- Boost elevates profile visibility temporarily; decrement credits if applicable

## Monetization & Gating
- Master switch drives UI visibility and enforcement:
  - Free: show ads; premium features hidden
  - Premium: show plans; enforce features via subscription status
  - Credits: show credit packs; consume credits for Super Like/Boost
  - Hybrid: enable both subscriptions and credits
- Payments: client creates intent; server validates and records subscription/credit purchases

## Security & Compliance
- Sanitize/uploads; image size/type validation; store with signed URLs
- Rate limit swipes/actions; CSRF protection; authorization policies for admin actions
- GDPR-friendly: delete account, export data; terms/privacy managed via Admin

## Testing & QA
- Seeders: demo users, prompts, plans, packs
- Unit tests: matching logic, credit consumption, subscription gating
- Feature tests: onboarding, auth, chat events
- Frontend smoke tests: render key pages; manual responsive checks

## Build & Deliverables
- `/backend`: full Laravel project (no `vendor`), with `.env.example` and install scripts
- `/mobile`: Capacitor project with `capacitor.config`, `ios` and `android` shells
- `documentation.html`: install prerequisites, backend setup, admin config, mobile build/sync steps

## Milestones
1. Scaffold Laravel + Inertia + Tailwind + Sanctum
2. Filament admin base + settings framework
3. Auth + public pages + legal content
4. Onboarding (photos/camera, profile/prompts, filters/geolocation)
5. Discover swipe + matching logic
6. Matches grid + chat list + active chat (Echo/Reverb)
7. Monetization (plans, credits, master switch) + payments config
8. Ads integration (web/mobile) + feature toggles
9. Capacitor mobile wrappers (permissions, push registration)
10. QA, seeders, documentation, packaging