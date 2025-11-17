## Overview
Deliver a launch-ready build by completing security, payments, gating, UX, performance, admin controls, mobile readiness, and test coverage.

## Security & Compliance
- Add CSRF/XSS sanitization for legal content and chat attachments
- Enforce policies: banned, verification, OTP, feature gating via middleware/helpers
- Verify Stripe signatures (env + setting), PayPal webhook verification; idempotency table in place
- GDPR: add endpoints/views for data export/delete; track terms/privacy acceptance; account closure
- Secure headers, HTTPS-only cookies; PII log review

## Payments & Monetization
- Subscription lifecycle: cancel/renew/expire, grace periods, proration and plan change handling
- Consistent gating for Unlimited Swipes, Ad-Free, See Who Liked Me across endpoints/UI
- Purchase history UI; receipt rendering (HTML/PDF) and admin CSV export
- Test/Live toggles surfaced in Premium; disable flows when keys missing; user-friendly errors

## Real-time Chat
- Reconnection/backoff (Echo/Firebase); offline queue for outgoing messages
- Read/delivery receipts; typing debounce
- Attachments: image/GIF upload limits, preview, deletion; message pagination

## Video/Audio Calls
- WebRTC: STUN/TURN servers; teardown and cleanup; browser compatibility checks
- Agora: backend secure token generation; join/leave lifecycle; permissions prompts
- Chime: meeting cleanup; device toggles; audio/video controls
- Admin setting: `video_provider` (webrtc/chime/agora), keys and regions configured

## Mobile (Capacitor iOS/Android)
- iOS Info.plist permissions for Camera/Mic/Location/Push; icons/splash; Xcode build stubs
- Android Manifest permissions; notification channels; AdMob test IDs; adaptive icons
- Push handling (foreground/background); deep link to chat/match

## Ads & Consent
- Sitewide ad placement map; Ad-Free enforcement
- GDPR/CCPA consent flow for personalized ads; test IDs in dev; AdSense safe mode

## Performance & Reliability
- DB indexes (added), pagination/eager loading in discover/matches/chat
- Cache prompts/plans/packs, settings; queue heavy tasks (image processing, push)
- Storage abstraction S3 option and CDN config

## Admin Panel
- Roles (admin/moderator); audit logs for moderation/payment actions
- Legal editor sanitization and preview; settings validation
- Payments report: filters (type/gateway/status/date), CSV export
- Moderation: attachment preview; bulk actions; pagination/search polish

## Frontend UX & Accessibility
- Onboarding UX polish (progress, errors)
- Responsive QA; ARIA and keyboard navigation; color contrast
- i18n scaffolding and RTL readiness
- Toasts/spinners on like/boost/premium flows

## SEO & PWA
- Meta tags/Open Graph; sitemap/robots
- PWA manifest/icons; offline fallback for public pages

## Analytics & Observability
- Event tracking (onboarding complete, like/super like, match, purchase)
- Error tracking/log aggregation; performance metrics; feature flags

## CI/CD & Packaging
- Env var validation (fail fast); build/test pipelines; versioning/CHANGELOG stub
- Zip packaging: exclude vendor/node_modules; include `.env.example` with all keys; verify assets

## Tests
- Expand coverage: Stripe/PayPal flows, lifecycle transitions, gating consistency, real-time chat, video providers, admin actions

## Execution Order
1. Security: sanitization, policies, headers/cookies
2. Payments: lifecycle endpoints/UI and receipts; admin CSV export
3. Gating consistency across UI/endpoints
4. Chat reliability: reconnection, receipts, attachments, pagination
5. Video providers: STUN/TURN, token generation, cleanup; admin configs
6. Mobile readiness: iOS/Android permissions, icons, push handling
7. Ads/Consent and PWA/SEO
8. Admin roles/audit logs; legal editor validation
9. Performance/caching/queues/storage
10. Analytics/observability
11. CI/CD and packaging
12. Tests and QA across flows