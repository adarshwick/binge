<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPromptAnswer extends Model
{
    protected $fillable = ['user_id','prompt_id','answer'];
}
