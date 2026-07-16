<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpinAndWin extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'rules',
        'image',
        'is_active',
        'sort_order',
        'max_spins_per_day',
        'cooldown_minutes',
        'requires_login',
        'success_message',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'requires_login' => 'boolean',
            'max_spins_per_day' => 'integer',
            'cooldown_minutes' => 'integer',
        ];
    }

    public function segments(): HasMany
    {
        return $this->hasMany(SpinWheelSegment::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(SpinResult::class);
    }

    public function activeSegments(): HasMany
    {
        return $this->segments()->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
