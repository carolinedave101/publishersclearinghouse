<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpinWheelSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'spin_and_win_id',
        'label',
        'color',
        'prize_type',
        'prize_value',
        'prize_description',
        'weight',
        'is_jackpot',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'prize_value' => 'decimal:2',
            'weight' => 'integer',
            'is_jackpot' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function spinAndWin(): BelongsTo
    {
        return $this->belongsTo(SpinAndWin::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(SpinResult::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeJackpots($query)
    {
        return $query->where('is_jackpot', true);
    }
}
