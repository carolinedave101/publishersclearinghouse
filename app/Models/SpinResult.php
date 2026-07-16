<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'spin_and_win_id',
        'spin_wheel_segment_id',
        'user_id',
        'winner_name',
        'winner_email',
        'prize_label',
        'prize_type',
        'prize_value',
        'is_claimed',
        'claimed_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'prize_value' => 'decimal:2',
            'is_claimed' => 'boolean',
            'claimed_at' => 'datetime',
        ];
    }

    public function spinAndWin(): BelongsTo
    {
        return $this->belongsTo(SpinAndWin::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(SpinWheelSegment::class, 'spin_wheel_segment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeClaimed($query)
    {
        return $query->where('is_claimed', true);
    }

    public function scopeUnclaimed($query)
    {
        return $query->where('is_claimed', false);
    }
}
