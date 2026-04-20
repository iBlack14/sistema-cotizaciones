<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomMessage extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'content',
        'type',
        'domain_name',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
