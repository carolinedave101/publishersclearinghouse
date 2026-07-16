<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    public static function send(mixed $mailable, string $to, ?string $name = null): void
    {
        try {
            Mail::to($to)->queue($mailable);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
        }
    }

    public static function sendAdmin(mixed $mailable): void
    {
        $adminEmail = config('mail.admin_address', env('PCH_ADMIN_EMAIL', 'admin@pch.com'));
        if ($adminEmail) {
            static::send($mailable, $adminEmail);
        }
    }
}
