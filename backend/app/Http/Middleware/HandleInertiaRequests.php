<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\AppSetting;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'features' => $request->user() ? [
                    'hasActiveSubscription' => (bool) $request->user()->activeSubscription(),
                    'hasAdFree' => $request->user()->hasFeature('Ad-Free'),
                    'hasUnlimitedSwipes' => $request->user()->hasFeature('Unlimited Swipes'),
                    'hasSeeWhoLikedMe' => $request->user()->hasFeature('See Who Liked Me'),
                ] : [],
                'creditBalance' => $request->user() ? $request->user()->creditBalance() : 0,
            ],
            'settings' => [
                'monetization_mode' => optional(AppSetting::where('key', 'monetization_mode')->first())->value,
                'ads_web_snippet' => optional(AppSetting::where('key', 'ads_web_snippet')->first())->value,
                'feature_video_chat' => optional(AppSetting::where('key', 'feature_video_chat')->first())->value === '1',
                'feature_voice_notes' => optional(AppSetting::where('key', 'feature_voice_notes')->first())->value === '1',
                'require_phone_otp' => optional(AppSetting::where('key', 'require_phone_otp')->first())->value === '1',
                'admob_banner_id' => optional(AppSetting::where('key', 'admob_banner_id')->first())->value,
                'chat_provider' => optional(AppSetting::where('key', 'chat_provider')->first())->value,
                'firebase_api_key' => optional(AppSetting::where('key', 'firebase_api_key')->first())->value,
                'firebase_project_id' => optional(AppSetting::where('key', 'firebase_project_id')->first())->value,
                'firebase_app_id' => optional(AppSetting::where('key', 'firebase_app_id')->first())->value,
                'verification_mode' => optional(AppSetting::where('key', 'verification_mode')->first())->value,
                'verification_provider' => optional(AppSetting::where('key', 'verification_provider')->first())->value,
                'video_provider' => optional(AppSetting::where('key', 'video_provider')->first())->value,
                'webrtc_signaling' => optional(AppSetting::where('key', 'webrtc_signaling')->first())->value,
                'webrtc_ice_servers' => optional(AppSetting::where('key', 'webrtc_ice_servers')->first())->value,
                'webrtc_stun_url' => optional(AppSetting::where('key', 'webrtc_stun_url')->first())->value,
                'webrtc_turn_url' => optional(AppSetting::where('key', 'webrtc_turn_url')->first())->value,
                'webrtc_turn_username' => optional(AppSetting::where('key', 'webrtc_turn_username')->first())->value,
                'webrtc_turn_password' => optional(AppSetting::where('key', 'webrtc_turn_password')->first())->value,
                'seo_title' => optional(AppSetting::where('key', 'seo_title')->first())->value,
                'seo_description' => optional(AppSetting::where('key', 'seo_description')->first())->value,
                'analytics_snippet' => optional(AppSetting::where('key', 'analytics_snippet')->first())->value,
                'price_super_like' => optional(AppSetting::where('key', 'price_super_like')->first())->value,
                'price_boost' => optional(AppSetting::where('key', 'price_boost')->first())->value,
            ],
        ];
    }
}
