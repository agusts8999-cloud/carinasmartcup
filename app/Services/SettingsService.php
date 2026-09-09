<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class SettingsService
{
    public const CACHE_KEY = 'website.settings.all';

    /**
     * @var list<string>
     */
    private const SENSITIVE_KEYS = [
        'mail.password',
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        if (! array_key_exists($key, $all)) {
            return $default;
        }

        return $this->decodeValue($key, $all[$key]);
    }

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            try {
                return Setting::query()
                    ->pluck('value', 'key')
                    ->map(fn ($value) => $value === null ? null : (string) $value)
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public function set(string $key, mixed $value, ?string $group = null): void
    {
        $group ??= $this->guessGroup($key);

        Setting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->encodeValue($key, $value),
                'group' => $group,
            ],
        );

        $this->forgetCache();
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values, ?string $group = null): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $this->encodeValue($key, $value),
                    'group' => $group ?? $this->guessGroup($key),
                ],
            );
        }

        $this->forgetCache();
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function siteName(): string
    {
        return (string) ($this->get('site.name') ?: config('app.name', 'CarinaSmartCup'));
    }

    public function logoUrl(): ?string
    {
        $path = $this->get('site.logo_path');

        return filled($path) ? Storage::disk('public')->url($path) : null;
    }

    public function faviconUrl(): ?string
    {
        $path = $this->get('site.favicon_path');

        return filled($path) ? Storage::disk('public')->url($path) : null;
    }

    public function whatsapp(): string
    {
        $value = $this->get('contact.whatsapp')
            ?: $this->get('whatsapp.support')
            ?: config('carina.whatsapp_support');

        return preg_replace('/\D+/', '', (string) $value) ?: '6281234567890';
    }

    public function themePreset(): string
    {
        $preset = (string) ($this->get('theme.preset') ?: config('website.default_preset', 'emerald'));
        $presets = config('website.presets', []);

        if (array_key_exists($preset, $presets)) {
            return $preset;
        }

        $legacy = config('website.legacy_map', []);
        if (isset($legacy[$preset]) && array_key_exists($legacy[$preset], $presets)) {
            return $legacy[$preset];
        }

        return 'emerald';
    }

    /**
     * @return array<string, string>
     */
    public function themeCssVariables(): array
    {
        $preset = $this->themePreset();

        return config("website.presets.{$preset}.css", config('website.presets.emerald.css', []));
    }

    /**
     * @return array<string, string>
     */
    public function themeStyleAttribute(): array
    {
        return $this->themeCssVariables();
    }

    public function themeStyleString(): string
    {
        $parts = [];

        foreach ($this->themeCssVariables() as $property => $value) {
            $parts[] = $property.': '.$value;
        }

        return implode('; ', $parts);
    }

    /**
     * Apply branding / WhatsApp / SMTP settings to runtime config (no .env write).
     */
    public function applyRuntimeConfig(): void
    {
        $siteName = $this->siteName();
        if ($siteName !== '') {
            Config::set('app.name', $siteName);
        }

        $whatsapp = $this->whatsapp();
        if ($whatsapp !== '') {
            Config::set('carina.whatsapp_support', $whatsapp);
        }

        $mailer = $this->get('mail.mailer');
        $host = $this->get('mail.host');

        if (filled($mailer)) {
            Config::set('mail.default', $mailer);
        }

        if (filled($host)) {
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', (int) ($this->get('mail.port') ?: 587));
            Config::set('mail.mailers.smtp.username', $this->get('mail.username'));

            $password = $this->get('mail.password');
            if (filled($password)) {
                Config::set('mail.mailers.smtp.password', $password);
            }

            $encryption = $this->get('mail.encryption');
            Config::set('mail.mailers.smtp.encryption', $encryption === '' ? null : $encryption);
        }

        $fromAddress = $this->get('mail.from_address');
        if (filled($fromAddress)) {
            Config::set('mail.from.address', $fromAddress);
        }

        $fromName = $this->get('mail.from_name') ?: $siteName;
        if (filled($fromName)) {
            Config::set('mail.from.name', $fromName);
        }
    }

    private function encodeValue(string $key, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $string = (string) $value;

        if (in_array($key, self::SENSITIVE_KEYS, true)) {
            return encrypt($string);
        }

        return $string;
    }

    private function decodeValue(string $key, mixed $value): mixed
    {
        if ($value === null || ! in_array($key, self::SENSITIVE_KEYS, true)) {
            return $value;
        }

        try {
            return decrypt((string) $value);
        } catch (\Throwable) {
            // Legacy plaintext rows written before encryption.
            return (string) $value;
        }
    }

    private function guessGroup(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'site.') => 'branding',
            str_starts_with($key, 'theme.') => 'theme',
            str_starts_with($key, 'contact.') || str_starts_with($key, 'whatsapp.') => 'contact',
            str_starts_with($key, 'mail.') => 'mail',
            str_starts_with($key, 'bank.') => 'payment',
            default => 'general',
        };
    }
}
