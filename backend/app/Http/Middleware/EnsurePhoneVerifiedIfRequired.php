<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AppSetting;

class EnsurePhoneVerifiedIfRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $required = optional(AppSetting::where('key','require_phone_otp')->first())->value === '1';
        if ($user && $required && empty($user->phone_verified_at) && $request->route()?->getName() !== 'app.otp') {
            return redirect()->route('app.otp');
        }
        return $next($request);
    }
}
