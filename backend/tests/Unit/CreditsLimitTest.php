<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AppSetting;
use App\Models\UserCreditLedger;
use App\Models\Like;

class CreditsLimitTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_super_like_requires_credits(): void
    {
        AppSetting::create(['key' => 'price_super_like', 'value' => '10']);
        $a = User::factory()->create();
        $b = User::factory()->create();
        $resp = $this->actingAs($a)->post(route('app.like'), ['to_user_id' => $b->id, 'type' => 'super_like']);
        $resp->assertStatus(402);
        UserCreditLedger::create(['user_id' => $a->id, 'change' => 20, 'reason' => 'purchase']);
        $resp2 = $this->actingAs($a)->post(route('app.like'), ['to_user_id' => $b->id, 'type' => 'super_like']);
        $resp2->assertStatus(200);
    }

    public function test_daily_swipe_limit_enforced(): void
    {
        AppSetting::create(['key' => 'daily_swipe_limit', 'value' => '2']);
        $a = User::factory()->create();
        $targets = User::factory(3)->create();
        foreach ($targets as $t) {
            $this->actingAs($a)->post(route('app.like'), ['to_user_id' => $t->id, 'type' => 'like']);
        }
        $count = Like::where('from_user_id', $a->id)->whereDate('created_at', now()->toDateString())->count();
        $this->assertEquals(2, $count);
    }
}
