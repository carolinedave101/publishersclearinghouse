<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiveawayEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'giveaway_id',
        'first_name',
        'last_name',
        'email',
        'is_winner',
    ];

    protected function casts(): array
    {
        return [
            'is_winner' => 'boolean',
        ];
    }

    public function giveaway()
    {
        return $this->belongsTo(Giveaway::class);
    }
}
