<?php

namespace App\Models;

use App\Helpers\EmailHelper;
use App\Mail\DepositConfirmation;
use App\Mail\OrderConfirmation;
use App\Mail\WithdrawalStatusNotification;
use App\Mail\WinnerNotification;
use App\Models\EmailCampaignRecipient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Winner extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'date_of_birth',
        'gender',
        'prize_amount',
        'prize_description',
        'email',
        'source',
        'registration_link_id',
        'password',
        'unique_code',
        'is_claimed',
        'claimed_at',
        'is_active',
        'status',
        'next_steps',
        'admin_notes',
        'features',
        'is_demo',
        'demographics',
    ];

    protected function casts(): array
    {
        return [
            'is_claimed' => 'boolean',
            'is_active' => 'boolean',
            'prize_amount' => 'decimal:2',
            'claimed_at' => 'datetime',
            'date_of_birth' => 'date',
            'features' => 'array',
            'demographics' => 'array',
            'password' => 'hashed',
        ];
    }

    public function routeNotificationForMail(): ?string
    {
        return $this->email;
    }

    public function registrationLink(): BelongsTo
    {
        return $this->belongsTo(RegistrationLink::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function emailCampaignRecipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class, 'winner_id');
    }

    public function sentCampaignRecipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class, 'winner_id')->where('status', 'sent');
    }

    public function getCampaignsSentCountAttribute(): int
    {
        return $this->sentCampaignRecipients()->count();
    }

    public function getCampaignHistoryAttribute(): array
    {
        return $this->sentCampaignRecipients()
            ->with('campaign')
            ->get()
            ->map(fn ($r) => [
                'campaign' => $r->campaign?->name ?? 'Unknown',
                'sent_at' => $r->sent_at,
            ])
            ->toArray();
    }

    public function getAvailableBalanceAttribute(): float
    {
        $totalDeposits = $this->deposits()->where('status', 'approved')->sum('amount');
        $totalWithdrawals = $this->withdrawals()->whereIn('status', ['approved', 'completed'])->sum('amount');
        return max(0, $this->prize_amount + $totalDeposits - $totalWithdrawals);
    }

    public function getEffectiveFeatures(): array
    {
        $global = \App\Models\Setting::getWinnerFeaturesConfig();
        $overrides = $this->features ?? [];

        return array_merge($global, $overrides);
    }

    public function getPrizeAmountFormattedAttribute(): string
    {
        return '$' . number_format($this->prize_amount, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeClaimed($query)
    {
        return $query->where('is_claimed', true);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function sendDepositConfirmation(Deposit $deposit): void
    {
        if (!$this->email) return;
        EmailHelper::send(
            new DepositConfirmation($deposit, $this),
            $this->email,
            $this->first_name
        );
    }

    public function sendWithdrawalStatus(Withdrawal $withdrawal): void
    {
        if (!$this->email) return;
        EmailHelper::send(
            new WithdrawalStatusNotification($withdrawal, $this),
            $this->email,
            $this->first_name
        );
    }

    public function sendOrderConfirmation(ShopOrder $order): void
    {
        if (!$this->email) return;
        EmailHelper::send(
            new OrderConfirmation($order),
            $this->email,
            $this->first_name
        );
    }

    public function sendNotification(string $subject, string $body): void
    {
        if (!$this->email) return;
        EmailHelper::send(
            new WinnerNotification($this, $subject, $body),
            $this->email,
            $this->first_name
        );
    }
}
