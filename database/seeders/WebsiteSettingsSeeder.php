<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class WebsiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site.name', 'value' => config('app.name', 'CarinaSmartCup'), 'group' => 'branding'],
            ['key' => 'site.logo_path', 'value' => null, 'group' => 'branding'],
            ['key' => 'site.favicon_path', 'value' => null, 'group' => 'branding'],
            ['key' => 'theme.preset', 'value' => config('website.default_preset', 'emerald'), 'group' => 'theme'],
            ['key' => 'contact.whatsapp', 'value' => config('carina.whatsapp_support'), 'group' => 'contact'],
            ['key' => 'whatsapp.support', 'value' => config('carina.whatsapp_support'), 'group' => 'contact'],
            ['key' => 'mail.mailer', 'value' => config('mail.default', 'smtp'), 'group' => 'mail'],
            ['key' => 'mail.host', 'value' => config('mail.mailers.smtp.host'), 'group' => 'mail'],
            ['key' => 'mail.port', 'value' => (string) config('mail.mailers.smtp.port', 587), 'group' => 'mail'],
            ['key' => 'mail.username', 'value' => config('mail.mailers.smtp.username'), 'group' => 'mail'],
            ['key' => 'mail.password', 'value' => null, 'group' => 'mail'],
            ['key' => 'mail.encryption', 'value' => config('mail.mailers.smtp.encryption', 'tls'), 'group' => 'mail'],
            ['key' => 'mail.from_address', 'value' => config('mail.from.address'), 'group' => 'mail'],
            ['key' => 'mail.from_name', 'value' => config('mail.from.name', config('app.name')), 'group' => 'mail'],
            ['key' => 'bank.name', 'value' => config('carina.bank.name'), 'group' => 'payment'],
            ['key' => 'bank.account_number', 'value' => config('carina.bank.account_number'), 'group' => 'payment'],
            ['key' => 'bank.account_name', 'value' => config('carina.bank.account_name'), 'group' => 'payment'],
        ];

        foreach ($defaults as $setting) {
            Setting::query()->firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                ],
            );
        }

        app(SettingsService::class)->forgetCache();
    }
}
