<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'content',
        'type',
        'priority',
        'status',
        'recipients',
        'recipient_type',
        'scheduled_at',
        'sent_at',
        'metadata',
        'is_hidden',
        'hidden_at',
        'message_number',
    ];

    protected $casts = [
        'recipients' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'hidden_at' => 'datetime',
        'is_hidden' => 'boolean',
    ];

    /**
     * Relación con el usuario que creó el mensaje
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para mensajes programados
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now())
            ->where('status', 'pending');
    }

    /**
     * Scope para mensajes enviados
     */
    public function scopeSent($query)
    {
        return $query->whereIn('status', ['sent', 'delivered']);
    }

    /**
     * Scope para borradores
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope para búsqueda
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Obtener el número de destinatarios
     */
    public function getRecipientsCountAttribute()
    {
        return is_array($this->recipients) ? count($this->recipients) : 0;
    }

    /**
     * Verificar si el mensaje está programado
     */
    public function isScheduled()
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture() && $this->status === 'pending';
    }

    /**
     * Verificar si el mensaje fue enviado
     */
    public function isSent()
    {
        return in_array($this->status, ['sent', 'delivered']);
    }

    /**
     * Scope para mensajes visibles (no ocultos)
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    /**
     * Scope para mensajes ocultos
     */
    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }

    /**
     * Scope para mensajes recientes (últimos 30 días, visibles)
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->visible()
            ->where('created_at', '>=', now()->subDays($days))
            ->latest();
    }

    /**
     * Obtener el badge de estado
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'sent' => 'bg-green-100 text-green-800',
            'delivered' => 'bg-blue-100 text-blue-800',
            'failed' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Obtener el badge de prioridad
     */
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => 'bg-gray-100 text-gray-600',
            'normal' => 'bg-blue-100 text-blue-600',
            'high' => 'bg-orange-100 text-orange-600',
            'urgent' => 'bg-red-100 text-red-600',
        ];

        return $badges[$this->priority] ?? 'bg-blue-100 text-blue-600';
    }

    /**
     * Ocultar mensaje (soft hide)
     */
    public function hide()
    {
        $this->update([
            'is_hidden' => true,
            'hidden_at' => now(),
        ]);
    }

    /**
     * Mostrar mensaje (unhide)
     */
    public function unhide()
    {
        $this->update([
            'is_hidden' => false,
            'hidden_at' => null,
        ]);
    }

    /**
     * Asignar número de mensaje automáticamente
     */
    public function assignMessageNumber()
    {
        if (! $this->message_number) {
            $lastNumber = static::where('user_id', $this->user_id)
                ->whereNotNull('message_number')
                ->max('message_number') ?? 0;

            $this->update(['message_number' => $lastNumber + 1]);
        }
    }

    /**
     * Boot del modelo para asignar número automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            $message->assignMessageNumber();
        });
    }
}
