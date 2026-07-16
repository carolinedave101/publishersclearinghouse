<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'winner_id',
        'type',
        'amount',
        'fee',
        'net_amount',
        'payment_method',
        'reference_type',
        'reference_id',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
        ];
    }

    public function winner()
    {
        return $this->belongsTo(Winner::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
