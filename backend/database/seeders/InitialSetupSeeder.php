<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;
use App\Models\SubscriptionPlan;
use App\Models\CreditPack;
use App\Models\ProfilePrompt;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@binge.local')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@binge.local',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        AppSetting::updateOrCreate(['key' => 'monetization_mode'], ['value' => 'hybrid', 'group' => 'monetization']);
        AppSetting::updateOrCreate(['key' => 'legal_privacy'], ['value' => '<p>Privacy content managed via Admin.</p>', 'group' => 'legal']);
        AppSetting::updateOrCreate(['key' => 'legal_terms'], ['value' => '<p>Terms content managed via Admin.</p>', 'group' => 'legal']);
        AppSetting::updateOrCreate(['key' => 'ads_web_snippet'], ['value' => '', 'group' => 'ads']);
        AppSetting::updateOrCreate(['key' => 'admob_banner_id'], ['value' => '', 'group' => 'ads']);
        AppSetting::updateOrCreate(['key' => 'fcm_server_key'], ['value' => '', 'group' => 'push']);
        AppSetting::updateOrCreate(['key' => 'seo_title'], ['value' => 'Binge Dating', 'group' => 'seo']);
        AppSetting::updateOrCreate(['key' => 'seo_description'], ['value' => 'Meet, match, and chat with people nearby.', 'group' => 'seo']);
        AppSetting::updateOrCreate(['key' => 'analytics_snippet'], ['value' => '', 'group' => 'analytics']);
        AppSetting::updateOrCreate(['key' => 'video_provider'], ['value' => 'agora', 'group' => 'video']);
        AppSetting::updateOrCreate(['key' => 'agora_app_id'], ['value' => '', 'group' => 'video']);
        AppSetting::updateOrCreate(['key' => 'agora_token'], ['value' => '', 'group' => 'video']);
        AppSetting::updateOrCreate(['key' => 'webrtc_signaling'], ['value' => 'reverb', 'group' => 'video']);
        AppSetting::updateOrCreate(['key' => 'chat_provider'], ['value' => 'reverb', 'group' => 'chat']);
        AppSetting::updateOrCreate(['key' => 'firebase_api_key'], ['value' => '', 'group' => 'chat']);
        AppSetting::updateOrCreate(['key' => 'firebase_project_id'], ['value' => '', 'group' => 'chat']);
        AppSetting::updateOrCreate(['key' => 'firebase_app_id'], ['value' => '', 'group' => 'chat']);
        AppSetting::updateOrCreate(['key' => 'verification_mode'], ['value' => 'optional', 'group' => 'verification']); // off|optional|mandatory
        AppSetting::updateOrCreate(['key' => 'verification_provider'], ['value' => 'manual', 'group' => 'verification']); // manual|aws
        AppSetting::updateOrCreate(['key' => 'aws_access_key_id'], ['value' => '', 'group' => 'aws']);
        AppSetting::updateOrCreate(['key' => 'aws_secret_access_key'], ['value' => '', 'group' => 'aws']);
        AppSetting::updateOrCreate(['key' => 'aws_region'], ['value' => 'us-east-1', 'group' => 'aws']);
        AppSetting::updateOrCreate(['key' => 'feature_video_chat'], ['value' => '0', 'group' => 'features']);
        AppSetting::updateOrCreate(['key' => 'feature_voice_notes'], ['value' => '0', 'group' => 'features']);
        AppSetting::updateOrCreate(['key' => 'require_phone_otp'], ['value' => '0', 'group' => 'features']);
        AppSetting::updateOrCreate(['key' => 'stripe_key'], ['value' => '', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'stripe_secret'], ['value' => '', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'paypal_client_id'], ['value' => '', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'paypal_secret'], ['value' => '', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'stripe_webhook_secret'], ['value' => '', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'paypal_webhook_id'], ['value' => '', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'stripe_mode'], ['value' => 'test', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'paypal_mode'], ['value' => 'test', 'group' => 'payments']);
        AppSetting::updateOrCreate(['key' => 'price_super_like'], ['value' => '10', 'group' => 'pricing']);
        AppSetting::updateOrCreate(['key' => 'price_boost'], ['value' => '50', 'group' => 'pricing']);
        AppSetting::updateOrCreate(['key' => 'daily_swipe_limit'], ['value' => '100', 'group' => 'limits']);

        AppSetting::updateOrCreate(['key' => 'smtp_host'], ['value' => '', 'group' => 'mail']);
        AppSetting::updateOrCreate(['key' => 'smtp_port'], ['value' => '587', 'group' => 'mail']);
        AppSetting::updateOrCreate(['key' => 'smtp_username'], ['value' => '', 'group' => 'mail']);
        AppSetting::updateOrCreate(['key' => 'smtp_password'], ['value' => '', 'group' => 'mail']);
        AppSetting::updateOrCreate(['key' => 'smtp_encryption'], ['value' => 'tls', 'group' => 'mail']);
        AppSetting::updateOrCreate(['key' => 'mail_from_address'], ['value' => 'no-reply@binge.local', 'group' => 'mail']);
        AppSetting::updateOrCreate(['key' => 'mail_from_name'], ['value' => 'Binge', 'group' => 'mail']);

        if (!SubscriptionPlan::count()) {
            SubscriptionPlan::create(['name' => 'Gold', 'price' => 9.99, 'features' => ['Unlimited Swipes', 'See Who Liked Me', 'Ad-Free']]);
            SubscriptionPlan::create(['name' => 'Platinum', 'price' => 19.99, 'features' => ['Priority Boosts', 'Premium Badge', 'All Gold features']]);
        }
        if (!CreditPack::count()) {
            CreditPack::create(['name' => '100 Credits', 'credits' => 100, 'price' => 5]);
            CreditPack::create(['name' => '500 Credits', 'credits' => 500, 'price' => 20]);
        }
        if (!ProfilePrompt::count()) {
            foreach ([
                'My simple pleasures are...',
                'The best way to win me over is...',
                'Two truths and a lie...',
                'My ideal weekend...',
            ] as $text) {
                ProfilePrompt::create(['text' => $text]);
            }
        }
    }
}