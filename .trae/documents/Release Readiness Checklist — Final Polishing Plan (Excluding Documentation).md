## Security & Compliance
- Verify CSRF/XSS protection and sanitize rich content in legal pages and chat attachments
- Enforce authorization policies consistently (banned, unverified, phone-OTP-required, feature gating)
- Add Stripe webhook signature verification and PayPal webhook validation; ensure idempotency on all payment handlers
- GDPR: add data export/delete, consent tracking (terms/privacy acceptance), and account closure flows
- PII/logging review; redact tokens/secrets; configure secure headers and HTTPS-only cookies

## Payments & Monetization
- Subscription lifecycle: cancel/renew/expire, grace periods, proration, and upgrade/downgrade handling
- Gating consistency: Unlimited Swipes, Ad-Free, See Who Liked Me enforced across endpoints/UI
- Receipts/History: user purchase history page and admin reporting filters (type, gateway, status)
- Test/Live mode toggles for Stripe/PayPal; environment checks; error states surfaced to users

## Real-Time Chat & Messaging
- Reconnection/backoff handling for Echo/Firebase; offline queue for outgoing messages
- Read/delivery receipts and typing debounce
- Message attachments: images/GIF uploads with size/type limits; preview; deletion
- Message pagination and retention policy; indexes on conversations/messages

## Video/Audio Calls
- WebRTC: STUN/TURN server configuration; browser compatibility; call teardown/cleanup
- Agora/Zego: secure token generation on backend; channel lifecycle; UI warnings for permissions
- Chime: meeting cleanup (end/delete), device permissions, audio/video toggle controls

## Native Mobile (Capacitor iOS/Android)
- iOS: Info.plist permission strings for Camera/Mic/Location/Push; icons/splash assets; Xcode build setup
- Android: Manifest permissions, FCM setup, AdMob test/live toggles, adaptive icons
- Push: foreground/background handling; notification channels (Android); deep links to chat/match

## Ads & Consent
- Sitewide ad placement controls; Ad-Free enforcement everywhere
- GDPR/CCPA consent for personalized ads; AdMob test IDs in dev; web AdSense safe mode

## Performance & Reliability
- DB indexes: likes(from_user_id,to_user_id), matches(user_id_a,user_id_b), conversations(match_id), messages(conversation_id)
- Pagination for discover/matches/chat list; eager loading to reduce N+1
- Caching for prompts/plans/packs and settings; queue workers for heavy tasks (image processing, push)
- Storage abstraction (local/S3) for photos/voice; CDN configuration option

## Admin Panel Enhancements
- Role management (admin vs moderator); audit logs for actions (ban/warn/approve)
- Legal editor: safe HTML sanitization and preview; settings validation rules
- Payments report: date range filters, export CSV; revenue widgets per period
- Moderation: attachments preview; bulk actions; pagination and search

## Frontend UX & Accessibility
- Onboarding UX polish; error states; progress indicators
- Responsive checks across devices; keyboard navigation and ARIA; color contrast
- i18n scaffolding for strings; RTL support readiness
- Toasts/spinners for actions (like, boost, premium)

## SEO & PWA (Web)
- Meta tags and Open Graph; sitemap/robots
- PWA manifest, icons, and optional offline fallback for marketing/public pages

## Analytics & Observability
- Event tracking (onboarding complete, like/super like, match, purchase)
- Error tracking/log aggregation; performance metrics; feature flags

## CI/CD & Packaging
- Env var validation (fail fast if critical keys missing)
- Build pipelines and test suites execution; versioning/CHANGELOG
- Zip packaging sanity checks: exclude vendor/node_modules; include .env.example with all keys

## Testing (Expand Coverage)
- Payment flows (Stripe intent/checkout, PayPal order/capture, webhooks)
- Chat real-time (Echo/Firebase), reconnection, attachments
- Video provider flows (WebRTC/Agora/Chime)
- Admin actions (ban/warn/approve); verification modes/providers
- Gating/limits (credits consumption, swipe caps, Ad-Free, See Who Liked Me)
