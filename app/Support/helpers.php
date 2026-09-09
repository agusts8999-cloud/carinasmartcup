<?php

use App\Services\SettingsService;

if (! function_exists('website')) {
    function website(): SettingsService
    {
        return app(SettingsService::class);
    }
}
