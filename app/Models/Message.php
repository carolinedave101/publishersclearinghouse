<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'winner_id',
        'subject',
        'content',
        'sent_by',
        'sent_by_admin',
        'read',
    ];

    protected function casts(): array
    {
        return [
            'sent_by_admin' => 'boolean',
            'read' => 'boolean',
        ];
    }

    public function winner()
    {
        return $this->belongsTo(Winner::class);
    }
}
