<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'laravel_through_key'
    ];

    public function users()
    {
        return $this->hasManyThrough(
            'App\Models\User', 'App\Models\UserInterest',
            // optional
            'user_id', 'id', 'id', 'interest_id'
        );
    }
}
