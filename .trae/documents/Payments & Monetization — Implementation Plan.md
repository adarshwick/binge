## Goals
- Make payments robust and production-ready: signature-verified webhooks, idempotency, lifecycle management, consistent gating, user/admin UIs, test/live modes, and receipts.

## Stripe — Webhooks & Checkout
1. Add `STRIPE_WEBHOOK_SECRET` to `.env.example` and App Settings; read from env or settings
2. Update `PaymentWebhookController@stripe`:
   - Verify signature using `\\Stripe\\Webhook::constructEvent($payload, $sigHeader, $secret)`
   - Implement idempotency: create table `processed_events(id, gateway, created_at)` and skip duplicates
   - Map events to actions:
     - `payment_intent.succeeded` → log payment, activate subscription or add credits by metadata
     - `charge.refunded` → log refund and set subscription `canceled` or debit credits
3. Stripe Checkout/Intent
   - Keep existing endpoints; add error handling and validate types/IDs; include `mode` toggle (test/live)

## PayPal — Orders & Webhooks
1. Add `PAYPAL_WEBHOOK_ID` and test/live mode
2. Update `PaymentWebhookController@paypal`:
   - Verify transmission via PayPal Webhook Verification API (`v1/notifications/verify-webhook-signature`)
   - Idempotency via `processed_events` table
   - Map COMPLETED captures to payments and lifecycle updates; handle REFUNDED
3. Capture endpoint: add error handling and return structured result to client

## Subscription Lifecycle
1. Model/UserSubscription
   - Add transitions: `activate`, `cancel`, `expire`, `renew`
   - Add `grace_ends_at` field for grace period; optional `auto_renew` flag
2. Controllers
   - `SubscriptionController`: endpoints for `POST /app/subscription/cancel`, `POST /app/subscription/renew`, `POST /app/subscription/change-plan`
   - Validate gating; audit logs for actions
3. Scheduler/Jobs
   - Nightly job to expire past `ends_at` (respect grace period)

## Consistent Feature Gating
1. Enforce Unlimited Swipes, Ad-Free, See Who Liked Me across endpoints/UI
   - Review all routes (like, discover, matches, premium, shell) and ensure helpers are used
2. Add a small middleware `EnsurePremiumFeature` for specific routes if needed

## Purchase History & Receipts
1. User Billing page `/app/billing`
   - List payments (type, gateway, amount, status, created_at) and subscriptions (plan, status, start/end)
   - Download receipt (render HTML/PDF from Payment model)
2. Admin Payments report
   - Add filters by `type`, `gateway`, `status`, date range; CSV export

## Test/Live Modes & Settings
1. Add `stripe_mode`, `paypal_mode` in App Settings (test|live)
2. Premium page UI badges for mode; disable purchase when keys missing; user-friendly errors
3. Ensure environment selection when creating Checkout/Orders

## Error Handling & UX
1. Premium page
   - Toasts for errors/success; loader on Stripe/PayPal buttons
   - Auto-refresh credit/subscription status on return (success/canceled)
2. Webhooks
   - Log failures; alert admin (optional) and retry policy

## Idempotency & Indexes
1. Migration for `processed_events` (event_id UNIQUE)
2. Add/verify DB indexes: payments(user_id,type,status), subscriptions(user_id,status,ends_at)

## Tests
1. Stripe: intent/checkout → webhook activates subscription/adds credits
2. PayPal: order/approve/capture → webhook activates subscription/adds credits
3. Lifecycle: cancel/renew/expire; grace period
4. Gating: Unlimited Swipes/Ad-Free/See Who Liked Me across endpoints

## Rollout Steps
1. Implement migrations (processed_events, subscription fields)
2. Update controllers (webhooks, premium, new lifecycle endpoints)
3. Create new user Billing page and admin filters/export
4. Add mode toggles and validations in App Settings forms
5. Add tests and run suite; fix any regressions
6. Manual end-to-end testing with Stripe/PayPal test environments