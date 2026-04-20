<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'service_name',
        'quantity',
        'price',
        'total',
        'image_path',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
