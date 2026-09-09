<?php

namespace App\Support;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\Auth;

class Analytics
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function track(string $event, array $payload = [], ?string $sessionId = null): void
    {
        AnalyticsEvent::query()->create([
            'event' => $event,
            'user_id' => Auth::id(),
            'session_id' => $sessionId ?? request()->cookie('cart_token'),
            'payload' => $payload,
        ]);
    }
}
