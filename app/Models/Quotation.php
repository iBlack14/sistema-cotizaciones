<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_name',
        'client_company',
        'client_ruc',
        'client_phone',
        'client_email',
        'client_address',
        'date',
        'subtotal',
        'igv',
        'total',
        'response_date',
        'follow_up_message',
        'follow_up_note',
        'status',
        'slug',
    ];

    protected static function booted()
    {
        static::creating(function ($quotation) {
            if (empty($quotation->slug)) {
                $quotation->slug = \Illuminate\Support\Str::random(10);
            }
        });

        static::created(function ($quotation) {
            if (str_contains($quotation->slug, '-placeholder-')) {
                // The slug depends on items, but they are created AFTER the quotation.
                // So we'll update it later or just keep a solid random string.
            }
            // For better experience, we can generate a better slug once we have more info.
            $serviceName = $quotation->items->first()->service_name ?? 'servicio';
            $slugBase = \Illuminate\Support\Str::slug($serviceName.'-'.$quotation->id);
            $quotation->slug = $slugBase.'-'.\Illuminate\Support\Str::random(5);
            $quotation->saveQuietly();
        });
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
