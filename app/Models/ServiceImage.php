<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    protected $fillable = ['name', 'filename', 'path', 'is_active', 'order'];

    public function serviceMappings()
    {
        return $this->hasMany(ServiceMapping::class);
    }

    public function getUrlAttribute()
    {
        $relativePath = $this->path ?: ('service_images/'.$this->filename);

        return url('storage/'.ltrim($relativePath, '/'));
    }
}
