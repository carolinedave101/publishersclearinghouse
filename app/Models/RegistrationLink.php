<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegistrationLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    public function registrationsCount(): int
    {
        return $this->winners()->count();
    }
}