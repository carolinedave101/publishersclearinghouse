<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaignRecipient extends Model
{
    use HasFactory;
    protected $fillable = [
        'campaign_id',
        'winner_id',
        'email',
        'first_name',
        'body_variant_used',
        'status',
        'sent_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'body_variant_used' => 'integer',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Winner::class, 'winner_id');
    }
}
