<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\UserMatch;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('match.{matchId}', function ($user, $matchId) {
    $match = UserMatch::find($matchId);
    if (!$match) return false;
    return $user->id === $match->user_id_a || $user->id === $match->user_id_b;
});