<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'prize',
        'prize_value',
        'image',
        'starts_at',
        'ends_at',
        'max_entries',
        'status',
        'color',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'prize_value' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function entries()
    {
        return $this->hasMany(GiveawayEntry::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getEntryCountAttribute(): int
    {
        return $this->entries()->count();
    }
}
