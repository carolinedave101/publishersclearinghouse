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

    public function exportWinnersCsv(): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, [
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'City',
            'State',
            'Zip',
            'Winner Code',
            'Status',
            'Prize Amount',
            'Registered At',
        ]);

        $this->winners()->chunk(500, function ($winners) use ($handle) {
            foreach ($winners as $w) {
                fputcsv($handle, [
                    $w->first_name,
                    $w->last_name,
                    $w->email,
                    $w->phone,
                    $w->city,
                    $w->state,
                    $w->zip,
                    $w->unique_code,
                    $w->status,
                    $w->prize_amount,
                    $w->created_at?->toDateTimeString(),
                ]);
            }
        });

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}