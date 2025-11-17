<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Like;
use App\Models\UserMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MatchingFlowTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_mutual_like_creates_match(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->actingAs($a)->post(route('app.like'), ['to_user_id' => $b->id, 'type' => 'like']);
        $this->actingAs($b)->post(route('app.like'), ['to_user_id' => $a->id, 'type' => 'like']);
        $match = UserMatch::where(function($q) use ($a,$b){
            $q->where('user_id_a',$a->id)->where('user_id_b',$b->id);
        })->orWhere(function($q) use ($a,$b){
            $q->where('user_id_a',$b->id)->where('user_id_b',$a->id);
        })->first();
        $this->assertNotNull($match);
    }
}
