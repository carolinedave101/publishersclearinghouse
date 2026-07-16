<?php

namespace App\Services;

use App\Helpers\EmailHelper;
use App\Mail\WinnerNotification;
use App\Models\Winner;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function sendWinnerNotification(Winner $winner): void
    {
        if (!$winner->email) {
            return;
        }

        try {
            EmailHelper::send(
                new WinnerNotification($winner),
                $winner->email,
                $winner->first_name
            );
        } catch (\Exception $e) {
            Log::error('Failed to send winner email: ' . $e->getMessage());
        }
    }
}
