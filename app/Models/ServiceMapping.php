<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceMapping extends Model
{
    protected $fillable = ['service_name', 'service_image_id', 'order'];

    public function serviceImage()
    {
        return $this->belongsTo(ServiceImage::class);
    }
}
