<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCreditLedger extends Model
{
    protected $fillable = ['user_id','change','reason','meta'];
    protected $casts = ['meta' => 'array'];
}
