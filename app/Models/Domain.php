<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = [
        'user_id',
        'client_name',
        'domain_name',
        'registration_date',
        'expiration_date',
        'auto_renew',
        'status',
        'price',
        'hosting_info',
        'dns_servers',
        'notes',
        'plugins',
        'licenses',
        'maintenance_status',
        'corporate_emails',
        'emails',
        'phone',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'expiration_date' => 'date',
        'auto_renew' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate days until expiration
    public function daysUntilExpiration()
    {
        return (int) now()->diffInDays($this->expiration_date, false);
    }

    // Check if domain is expiring soon (within 30 days)
    public function isExpiringSoon()
    {
        $days = $this->daysUntilExpiration();

        return $days >= 0 && $days <= 30;
    }

    // Check if domain is expired
    public function isExpired()
    {
        return $this->expiration_date->isPast();
    }

    // Scope for active domains
    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    // Scope for expiring soon
    public function scopeExpiringSoon($query)
    {
        return $query->where('expiration_date', '<=', now()->addDays(30))
            ->where('expiration_date', '>=', now());
    }

    // Scope for expired domains
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }

    // Scope for search
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('domain_name', 'like', "%{$search}%")
                ->orWhere('client_name', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }
}
