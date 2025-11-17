<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserSubscription;
use App\Models\UserCreditLedger;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function activeSubscription()
    {
        return UserSubscription::where('user_id', $this->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })->first();
    }

    public function hasFeature(string $feature): bool
    {
        $sub = $this->activeSubscription();
        if (! $sub) return false;
        $plan = \App\Models\SubscriptionPlan::find($sub->subscription_plan_id);
        return in_array($feature, $plan->features ?? []);
    }

    public function creditBalance(): int
    {
        return (int) UserCreditLedger::where('user_id', $this->id)->sum('change');
    }
}
