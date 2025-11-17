<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'price', 'features', 'active'];
    protected $casts = [
        'features' => 'array',
        'active' => 'boolean',
        'price' => 'decimal:2',
    ];
}
