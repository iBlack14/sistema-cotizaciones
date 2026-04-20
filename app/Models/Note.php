<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'user_id',
        'cliente',
        'telefono',
        'llamar',
        'servicio',
        'descripcion',
        'collapsed',
        'pin_side',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
