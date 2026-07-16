<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'customer_email',
        'address',
        'city',
        'state',
        'zip',
        'items',
        'total',
        'status',
        'payment_method',
        'payment_details',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'total' => 'decimal:2',
            'payment_details' => 'array',
        ];
    }
}
