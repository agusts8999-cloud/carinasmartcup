<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Stub dispatcher — logs outbound notifications without sending to external channels.
     *
     * @param  array<string, mixed>  $payload
     */
    public function send(
        Model $notifiable,
        string $channel,
        string $event,
        array $payload = [],
    ): NotificationLog {
        return NotificationLog::query()->create([
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'channel' => $channel,
            'event' => $event,
            'payload' => $payload,
            'sent_at' => now(),
            'status' => 'sent',
        ]);
    }
}
