<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlyerTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'caption',
        'filename',
        'path',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        $relativePath = $this->path ?: ('flyers/'.$this->filename);

        return url('storage/'.ltrim($relativePath, '/'));
    }
}
