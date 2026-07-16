<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'purpose',
        'type',
        'description',
        'instructions',
        'config',
        'logo',
        'barcode',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getPurposesAttribute(): array
    {
        return array_filter(explode(',', $this->purpose));
    }

    public function setPurposesAttribute(array $purposes): void
    {
        $this->attributes['purpose'] = implode(',', array_unique(array_filter($purposes)));
    }

    public function hasPurpose(string $purpose): bool
    {
        return in_array($purpose, $this->purposes, true);
    }

    public function getPurposeLabelsAttribute(): array
    {
        $labels = self::getPurposeOptions();
        return array_map(fn ($p) => $labels[$p] ?? ucfirst($p), $this->purposes);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDeposits($query)
    {
        return $query->where(function ($q) {
            $q->where('purpose', 'like', '%deposit%');
        });
    }

    public function scopeForWithdrawals($query)
    {
        return $query->where(function ($q) {
            $q->where('purpose', 'like', '%withdrawal%');
        });
    }

    public function scopeForShop($query)
    {
        return $query->where(function ($q) {
            $q->where('purpose', 'like', '%shop%');
        });
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public static function getPurposeOptions(): array
    {
        return [
            'deposit' => 'Deposits',
            'withdrawal' => 'Withdrawals',
            'shop' => 'Shop',
        ];
    }

    public static function getPurposeBadge(string $purpose): string
    {
        return self::getPurposeOptions()[$purpose] ?? ucfirst($purpose);
    }
}
