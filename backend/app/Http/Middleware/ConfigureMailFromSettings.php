<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AppSetting;

class ConfigureMailFromSettings
{
    public function handle(Request $request, Closure $next)
    {
        $host = optional(AppSetting::where('key','smtp_host')->first())->value;
        $port = optional(AppSetting::where('key','smtp_port')->first())->value;
        $username = optional(AppSetting::where('key','smtp_username')->first())->value;
        $password = optional(AppSetting::where('key','smtp_password')->first())->value;
        $encryption = optional(AppSetting::where('key','smtp_encryption')->first())->value;
        $fromAddress = optional(AppSetting::where('key','mail_from_address')->first())->value;
        $fromName = optional(AppSetting::where('key','mail_from_name')->first())->value;

        if ($host && $port) {
            config(['mail.default' => 'smtp']);
            config(['mail.mailers.smtp.host' => $host]);
            config(['mail.mailers.smtp.port' => (int) $port]);
            if ($username) config(['mail.mailers.smtp.username' => $username]);
            if ($password) config(['mail.mailers.smtp.password' => $password]);
            if ($encryption) config(['mail.mailers.smtp.encryption' => $encryption]);
        }
        if ($fromAddress) config(['mail.from.address' => $fromAddress]);
        if ($fromName) config(['mail.from.name' => $fromName]);

        return $next($request);
    }
}