<?php

namespace App\Notifications;

use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class WinnerClaimedNotification extends Notification
{
    use Queueable;

    public function __construct(public Winner $winner) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'winner_id' => $this->winner->id,
            'message' => "{$this->winner->first_name} {$this->winner->last_name} claimed their \${$this->winner->prize_amount} prize!",
            'url' => url("/admin/winners/{$this->winner->id}/edit"),
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'winner_id' => $this->winner->id,
            'message' => "{$this->winner->first_name} {$this->winner->last_name} claimed their \${$this->winner->prize_amount} prize!",
            'url' => url("/admin/winners/{$this->winner->id}/edit"),
        ];
    }
}
