<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePrompt extends Model
{
    protected $fillable = ['text', 'active'];
    protected $casts = [
        'active' => 'boolean',
    ];
}
