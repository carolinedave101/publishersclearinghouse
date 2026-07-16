<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'value'];

    const MAIL_CONFIG_KEY = 'mail_config';

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    public static function getMailConfig(): array
    {
        $raw = static::getValue(static::MAIL_CONFIG_KEY);
        if (!$raw) {
            return [
                'mailer' => 'smtp',
                'resend_api_key' => '',
                'smtp_host' => env('MAIL_HOST', 'smtp.stackmail.com'),
                'smtp_port' => env('MAIL_PORT', 587),
                'smtp_encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'smtp_username' => env('MAIL_USERNAME', ''),
                'smtp_password' => env('MAIL_PASSWORD', ''),
                'from_address' => env('MAIL_FROM_ADDRESS', 'winnersteam@publishersclearing.info'),
                'from_name' => env('MAIL_FROM_NAME', 'Publishers Clearing House'),
                'admin_email' => env('PCH_ADMIN_EMAIL', 'admin@pch.com'),
            ];
        }

        return is_array($raw) ? $raw : (json_decode($raw, true) ?? []);
    }

    public static function setMailConfig(array $config): void
    {
        static::setValue(static::MAIL_CONFIG_KEY, json_encode($config));
    }

    const SITE_CONFIG_KEY = 'site_config';

    public static function getSiteConfig(): array
    {
        $raw = static::getValue(static::SITE_CONFIG_KEY);
        if (!$raw) {
            return [
                'site_name' => 'Publishers Clearing House',
                'site_description' => 'Enter your unique winner code to claim your prize from Publishers Clearing House',
                'logo' => '/logo.png',
                'favicon' => '/favicon.png',
                'footer_text' => 'Publishers Clearing House — Changing lives with prizes since 1967.',
                'footer_tagline' => 'Over $500 Million Awarded to Winners Like You',
            ];
        }
        return is_array($raw) ? $raw : (json_decode($raw, true) ?? []);
    }

    public static function setSiteConfig(array $config): void
    {
        static::setValue(static::SITE_CONFIG_KEY, json_encode($config));
    }

    const WINNER_FEATURES_KEY = 'winner_features';

    public static function getWinnerFeaturesConfig(): array
    {
        $raw = static::getValue(static::WINNER_FEATURES_KEY);
        if (!$raw) {
            return [
                'show_messages' => true,
                'show_documents' => true,
                'show_deposits' => false,
                'show_withdrawals' => false,
                'show_transactions' => false,
                'show_orders' => false,
                'show_dates' => true,
                'show_balance_summary' => true,
                'show_winner_code' => true,
                'show_next_steps' => true,
                'show_quick_actions' => true,
                'show_giveaways' => true,
                'show_games' => true,
                'show_shop' => true,
                'show_memberships' => true,
            ];
        }
        return is_array($raw) ? $raw : (json_decode($raw, true) ?? []);
    }

    public static function setWinnerFeaturesConfig(array $config): void
    {
        static::setValue(static::WINNER_FEATURES_KEY, json_encode($config));
    }
}
