<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredefinedMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'title',
        'content',
        'type',
        'usage',
        'is_active',
        'is_favorite',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_favorite' => 'boolean',
    ];

    /**
     * Scope para mensajes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para mensajes de email
     */
    public function scopeForEmail($query)
    {
        return $query->whereIn('type', ['email', 'both']);
    }

    /**
     * Scope para mensajes de WhatsApp
     */
    public function scopeForWhatsApp($query)
    {
        return $query->whereIn('type', ['whatsapp', 'both']);
    }

    /**
     * Scope ordenado
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('number');
    }

    /**
     * Obtener el badge de tipo
     */
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'email' => 'bg-blue-100 text-blue-800',
            'whatsapp' => 'bg-green-100 text-green-800',
            'both' => 'bg-purple-100 text-purple-800',
        ];

        return $badges[$this->type] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Obtener el ícono del tipo
     */
    public function getTypeIconAttribute()
    {
        $icons = [
            'email' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
            'whatsapp' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>',
            'both' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>',
        ];

        return $icons[$this->type] ?? '';
    }
}
