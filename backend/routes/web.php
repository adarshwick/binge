<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\AppSetting;

Route::get('/', function () {
    return Inertia::render('Landing');
});
Route::post('/webhooks/stripe', [\App\Http\Controllers\PaymentWebhookController::class, 'stripe'])->name('webhooks.stripe');
Route::post('/webhooks/paypal', [\App\Http\Controllers\PaymentWebhookController::class, 'paypal'])->name('webhooks.paypal');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && ! $user->onboarding_completed) {
        return redirect()->route('onboarding.start');
    }
    return redirect()->route('app.discover');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/privacy', function () {
    $content = optional(AppSetting::where('key', 'legal_privacy')->first())->value;
    return Inertia::render('Legal', ['type' => 'privacy', 'content' => $content]);
})->name('privacy');

Route::get('/terms', function () {
    $content = optional(AppSetting::where('key', 'legal_terms')->first())->value;
    return Inertia::render('Legal', ['type' => 'terms', 'content' => $content]);
})->name('terms');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/app/discover', [\App\Http\Controllers\DiscoverController::class, 'index'])->name('app.discover');
    Route::post('/app/like', [\App\Http\Controllers\LikeController::class, 'store'])->middleware('throttle:60,1')->name('app.like');
    Route::get('/app/liked-me', [\App\Http\Controllers\LikeController::class, 'likedMe'])->name('app.liked_me');
    Route::post('/app/boost', [\App\Http\Controllers\BoostController::class, 'apply'])->middleware('throttle:20,1')->name('app.boost');
    Route::get('/app/matches', [\App\Http\Controllers\MatchesController::class, 'index'])->name('app.matches');
    Route::post('/app/match/{user_id}/unmatch', [\App\Http\Controllers\MatchesController::class, 'unmatch'])->name('app.match.unmatch');
    Route::get('/app/user/{user_id}', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('app.user');
    Route::get('/app/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('app.chat');
    Route::get('/app/chat/{match_id}', [\App\Http\Controllers\ChatController::class, 'show'])->name('app.chat.show');
    Route::get('/app/chat/{match_id}/messages', [\App\Http\Controllers\ChatController::class, 'messages'])->name('app.chat.messages');
    Route::post('/app/chat/{match_id}/message', [\App\Http\Controllers\ChatController::class, 'send'])->middleware('throttle:60,1')->name('app.chat.send');
    Route::post('/app/chat/{match_id}/typing', [\App\Http\Controllers\ChatController::class, 'typing'])->middleware('throttle:120,1')->name('app.chat.typing');
    Route::post('/app/chat/{match_id}/voice', [\App\Http\Controllers\ChatController::class, 'uploadVoice'])->middleware('throttle:20,1')->name('app.chat.voice');
    Route::post('/app/chat/{match_id}/image', [\App\Http\Controllers\ChatController::class, 'uploadImage'])->middleware('throttle:30,1')->name('app.chat.image');
    Route::post('/app/video/{match_id}/start', [\App\Http\Controllers\VideoController::class, 'start'])->name('app.video.start');
    Route::post('/app/chime/{match_id}/join', [\App\Http\Controllers\VideoController::class, 'chimeJoin'])->name('app.chime.join');
    Route::post('/app/webrtc/{match_id}/offer', [\App\Http\Controllers\WebRTCController::class, 'offer'])->name('app.webrtc.offer');
    Route::post('/app/webrtc/{match_id}/answer', [\App\Http\Controllers\WebRTCController::class, 'answer'])->name('app.webrtc.answer');
    Route::get('/app/webrtc/{match_id}/poll', [\App\Http\Controllers\WebRTCController::class, 'poll'])->name('app.webrtc.poll');
    Route::post('/app/push/register', [\App\Http\Controllers\PushController::class, 'register'])->name('app.push.register');
    Route::post('/app/location', [\App\Http\Controllers\LocationController::class, 'update'])->name('app.location.update');
    Route::post('/app/block', [\App\Http\Controllers\BlockController::class, 'store'])->name('app.block');
    Route::delete('/app/block/{user_id}', [\App\Http\Controllers\BlockController::class, 'destroy'])->name('app.block.remove');
    Route::post('/app/report', [\App\Http\Controllers\ReportController::class, 'store'])->name('app.report');
    Route::get('/app/profile', function () {
        $photos = \App\Models\ProfilePhoto::where('user_id', auth()->id())->orderBy('order')->get()->map(fn($p)=>['id'=>$p->id,'url'=>\Illuminate\Support\Facades\Storage::url($p->path)]);
        return Inertia::render('App/Profile', ['photos' => $photos]);
    })->name('app.profile');
    Route::get('/app/profile/edit', function () {
        $photos = \App\Models\ProfilePhoto::where('user_id', auth()->id())->orderBy('order')->get()->map(fn($p)=>['id'=>$p->id,'url'=>\Illuminate\Support\Facades\Storage::url($p->path)]);
        return Inertia::render('App/ProfileEdit', ['photos' => $photos]);
    })->name('app.profile.edit');
    Route::post('/app/profile/bio', [\App\Http\Controllers\ProfileController::class, 'updateBio'])->name('app.profile.bio');
    Route::get('/app/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('app.billing');
    Route::post('/app/profile/photos', [\App\Http\Controllers\ProfileMediaController::class, 'upload'])->middleware('throttle:20,1')->name('app.profile.photos.upload');
    Route::patch('/app/profile/photos/order', [\App\Http\Controllers\ProfileMediaController::class, 'reorder'])->name('app.profile.photos.order');
    Route::delete('/app/profile/photos/{id}', [\App\Http\Controllers\ProfileMediaController::class, 'delete'])->middleware('throttle:20,1')->name('app.profile.photos.delete');

    Route::get('/onboarding/start', function () {
        return Inertia::render('Onboarding/Start');
    })->name('onboarding.start');
    Route::get('/onboarding/photos', function () {
        return Inertia::render('Onboarding/Photos');
    })->name('onboarding.photos');
    Route::get('/onboarding/profile', function () {
        $prompts = \App\Models\ProfilePrompt::where('active', true)->get(['id','text']);
        return Inertia::render('Onboarding/Profile', ['prompts' => $prompts]);
    })->name('onboarding.profile');
    Route::get('/onboarding/filters', function () {
        return Inertia::render('Onboarding/Filters');
    })->name('onboarding.filters');
    Route::post('/onboarding/complete', [\App\Http\Controllers\OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::post('/onboarding/selfie', [\App\Http\Controllers\OnboardingController::class, 'selfie'])->middleware('throttle:10,1')->name('onboarding.selfie');
    Route::post('/verification/rekognition', [\App\Http\Controllers\RekognitionController::class, 'compare'])->middleware('throttle:10,1')->name('verification.rekognition');
    Route::get('/app/otp', [\App\Http\Controllers\OtpController::class, 'show'])->name('app.otp');
    Route::post('/app/otp/send', [\App\Http\Controllers\OtpController::class, 'send'])->name('app.otp.send');
    Route::post('/app/otp/verify', [\App\Http\Controllers\OtpController::class, 'verify'])->name('app.otp.verify');

    Route::get('/app/premium', function () {
        $mode = optional(AppSetting::where('key', 'monetization_mode')->first())->value ?? 'free';
        $plans = \App\Models\SubscriptionPlan::where('active', true)->get()->map(fn($p) => [
            'name' => $p->name,
            'price' => '$' . number_format($p->price, 2) . '/mo',
            'features' => $p->features ?? [],
            'id' => $p->id,
        ]);
        $packs = \App\Models\CreditPack::where('active', true)->get()->map(fn($c) => [
            'name' => $c->name,
            'price' => '$' . number_format($c->price, 2),
            'credits' => $c->credits,
            'id' => $c->id,
        ]);
        $sub = \App\Models\UserSubscription::where('user_id', auth()->id())
            ->where('status','active')
            ->where(function($q){ $q->whereNull('ends_at')->orWhere('ends_at','>', now()); })
            ->latest()->first();
        $currentPlan = $sub ? \App\Models\SubscriptionPlan::find($sub->subscription_plan_id) : null;
        $subscription = $sub ? [
            'plan' => $currentPlan?->name,
            'ends_at' => $sub->ends_at,
        ] : null;
        return Inertia::render('App/Premium', [
            'mode' => $mode,
            'plans' => $plans,
            'creditPacks' => $packs,
            'subscription' => $subscription,
        ]);
    })->name('app.premium');
    Route::post('/app/premium/subscription', [\App\Http\Controllers\PremiumController::class, 'subscribe'])->name('app.premium.subscribe');
    Route::post('/app/premium/credits', [\App\Http\Controllers\PremiumController::class, 'buyCredits'])->name('app.premium.credits');
    Route::post('/app/premium/stripe-intent', [\App\Http\Controllers\PremiumController::class, 'stripeIntent'])->name('app.premium.stripe_intent');
    Route::post('/app/premium/paypal-order', [\App\Http\Controllers\PremiumController::class, 'paypalOrder'])->name('app.premium.paypal_order');
    Route::post('/app/premium/stripe-checkout', [\App\Http\Controllers\PremiumController::class, 'stripeCheckout'])->name('app.premium.stripe_checkout');
    Route::post('/app/premium/paypal-capture', [\App\Http\Controllers\PremiumController::class, 'paypalCapture'])->name('app.premium.paypal_capture');
    Route::post('/app/subscription/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('app.subscription.cancel');
    Route::post('/app/subscription/renew', [\App\Http\Controllers\SubscriptionController::class, 'renew'])->name('app.subscription.renew');
    Route::post('/app/subscription/change', [\App\Http\Controllers\SubscriptionController::class, 'changePlan'])->name('app.subscription.change');
});

require __DIR__.'/auth.php';
