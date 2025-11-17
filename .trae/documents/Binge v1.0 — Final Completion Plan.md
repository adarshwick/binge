## Overview
Complete remaining features across backend, admin, frontend, native mobile, monetization, real-time, ads, security and QA to reach CodeCanyon-ready status.

## Backend Features
- Implement profile saving endpoints (photos upload, bio, prompts) and tie uploads to `profile_photos` with ordering and soft delete
- Finish onboarding persistence for prompts and photos; store to verification queue for selfies
- Boost prioritization logic: add discovery ranking that elevates boosted users and sorts by distance/recency
- Add swipe-card endpoints for pass/like/super like with server validation (rate limiting, banned checks, daily cap)
- Add REST endpoints for “See Who Liked Me” pagination, credit balance retrieval and subscription status
- Payment integration layer: Stripe PaymentIntent for subscriptions/credits and PayPal Orders API; webhooks to confirm and update `user_subscriptions`/`user_credit_ledgers`/`payments`
- Push tokens storage: persist device FCM/APNs tokens per user and broadcast on match/message events

## Admin Panel Enhancements
- Legal content editors: Filament WYSIWYG pages for `/privacy` and `/terms` using RichEditor and preview
- API keys management pages: Pusher/Reverb, Agora/Zego, Google Maps, SMS Gateway, FCM server key
- Monetization settings page: feature prices (Super Like, Boost), daily swipe limit, AdMob IDs per platform, AdSense snippet
- Purchases reporting: Filament table for `payments` with filters by type/gateway/status
- Moderation: reports detail view with evidence attachments; verification review with selfie preview (public storage)
- Dashboard: add revenue and active subscriptions metrics

## Frontend Completion
- Swipe card UI: implement touch/mouse gestures (stack, animations), optimistic updates
- Discover ranking UI feedback (boost badge), filter controls panel for quick changes
- Edit Profile page: file uploader (multiple), reorder, delete; live preview of prompts and bio
- Liked Me: pagination and CTA to purchase if gated; show feature upsell banner
- Premium page: show current credit balance, subscription status, feature list highlighting owned features
- Legal pages: render admin-managed HTML content with safe sanitization

## Real-time & Push
- Echo channels for match-created events and typing indicators
- Push notifications: server emits FCM messages on new match/message; client registers and handles foreground/back notifications

## Native Mobile
- iOS Capacitor platform project; configure Info.plist permissions (Camera, Location, Push), APNs setup
- AdMob placement: banner slots in Discover/Matches/Chat as configured; toggle show/hide from Admin
- Use Capacitor Geolocation on app load to refresh location; permissions prompt UX

## Video/Voice Features
- Integrate Agora/Zego SDKs for video call button in chat; guard by toggles and keys
- Voice notes: record/upload/playback flow; storage and mime validation; gating by toggle

## Monetization & Gating
- Enforce Unlimited Swipes/Ad-Free/See Who Liked Me consistently in UI and endpoints
- Credits UI: show balance globally (header or profile), consumption receipts
- Subscriptions UI: show active plan, expiry, upgrade/downgrade flows

## Ads Refinement
- Web AdSense placement map: shell and page-level slots; respect Ad-Free and Admin visibility
- Mobile AdMob: mediation config stub; test IDs in dev mode from Admin

## Security & QA
- Rate limiting on actions (likes, messages, uploads)
- Authorization policies: prevent banned or unverified users from actions
- Validation & sanitization for rich content
- Unit/integration tests for matching logic, credit consumption, subscription gating, payments webhooks

## Documentation & Packaging
- Generate `documentation.html` with prerequisites, backend install, admin config, mobile build/sync, payment keys, push setup
- Package deliverables: `/backend` without `vendor`; `/mobile` Capacitor project; ensure `.env.example` complete with keys
- Optional seeders: demo content and users; commands to reset demo data

## Milestones
1. Profile & onboarding persistence + verification selfie upload
2. Swipe stack UI + discovery ranking + boost priority
3. Payments: Stripe/PayPal flows + webhooks + admin payments reporting
4. Push notifications: token storage + FCM server integration + client handlers
5. iOS Capacitor + mobile permissions + AdMob placements
6. Video/voice features with toggles and provider keys
7. Monetization/gating enforcement across UI & endpoints; credit balance UI
8. Admin legal editors, API keys, advanced settings; dashboard revenue/subscriptions
9. Security (rate limits, policies) + tests
10. Documentation & packaging for CodeCanyon
