<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AppSetting;
use App\Models\Verification;

class EnsureVerificationIfRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $mode = optional(AppSetting::where('key','verification_mode')->first())->value;
        $provider = optional(AppSetting::where('key','verification_provider')->first())->value;
        $user = $request->user();
        if ($user && $mode === 'mandatory') {
            $isVerified = !empty($user->verified_at);
            if (! $isVerified) {
                $pending = Verification::where('user_id', $user->id)->where('status','pending')->exists();
                if (! $pending) {
                    return redirect()->route('onboarding.photos');
                }
            }
        }
        return $next($request);
    }
}
