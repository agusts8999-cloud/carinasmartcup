<?php

use App\Filament\Pages\ManageWebsiteSettings;
use App\Models\User;
use App\Services\SettingsService;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WebsiteSettingsSeeder;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('persists branding theme and whatsapp via SettingsService', function () {
    $this->seed(WebsiteSettingsSeeder::class);

    $settings = app(SettingsService::class);
    $settings->setMany([
        'site.name' => 'Toko Gelas Uji',
        'theme.preset' => 'ocean',
        'contact.whatsapp' => '6281999888777',
        'whatsapp.support' => '6281999888777',
    ]);

    expect($settings->siteName())->toBe('Toko Gelas Uji')
        ->and($settings->themePreset())->toBe('ocean')
        ->and($settings->whatsapp())->toBe('6281999888777')
        ->and($settings->themeStyleString())->toContain('--brand-600: #0891b2');
});

it('renders storefront with site name whatsapp and theme preset', function () {
    $this->seed(WebsiteSettingsSeeder::class);

    $settings = app(SettingsService::class);
    $settings->setMany([
        'site.name' => 'Carina Brand Test',
        'theme.preset' => 'rose',
        'contact.whatsapp' => '6281555123456',
        'whatsapp.support' => '6281555123456',
    ]);
    $settings->applyRuntimeConfig();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Carina Brand Test', false)
        ->assertSee('data-theme="rose"', false)
        ->assertSee('wa.me/6281555123456', false)
        ->assertSee('--brand-600: #e11d48', false);
});

it('shows Dashboard Admin link for admin role but not for customer', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(WebsiteSettingsSeeder::class);

    $admin = User::factory()->create(['email' => 'panel-admin@example.test']);
    $admin->assignRole('admin');

    $customer = User::factory()->create(['email' => 'shopper@example.test']);
    $customer->assignRole('customer');

    $this->actingAs($admin)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Dashboard Admin', false)
        ->assertSee(url('/admin'), false);

    $this->actingAs($customer)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('Dashboard Admin', false);
});

it('can assign Spatie roles to a user', function () {
    $this->seed(RoleSeeder::class);

    $user = User::factory()->create();
    $user->syncRoles(['admin', 'cs']);

    expect($user->fresh()->hasRole('admin'))->toBeTrue()
        ->and($user->fresh()->hasRole('cs'))->toBeTrue()
        ->and($user->canAccessPanel(Filament\Facades\Filament::getPanel('admin')))->toBeTrue();
});

it('applies smtp settings to runtime mail config', function () {
    $this->seed(WebsiteSettingsSeeder::class);

    Mail::fake();

    $settings = app(SettingsService::class);
    $settings->setMany([
        'mail.mailer' => 'smtp',
        'mail.host' => 'smtp.example.test',
        'mail.port' => '2525',
        'mail.username' => 'mailer@example.test',
        'mail.password' => 'secret-pass',
        'mail.encryption' => 'tls',
        'mail.from_address' => 'noreply@example.test',
        'mail.from_name' => 'Carina Mail Test',
    ]);
    $settings->applyRuntimeConfig();

    expect(config('mail.default'))->toBe('smtp')
        ->and(config('mail.mailers.smtp.host'))->toBe('smtp.example.test')
        ->and(config('mail.mailers.smtp.port'))->toBe(2525)
        ->and(config('mail.mailers.smtp.username'))->toBe('mailer@example.test')
        ->and(config('mail.mailers.smtp.password'))->toBe('secret-pass')
        ->and(config('mail.mailers.smtp.encryption'))->toBe('tls')
        ->and(config('mail.from.address'))->toBe('noreply@example.test')
        ->and(config('mail.from.name'))->toBe('Carina Mail Test');

    $raw = \App\Models\Setting::query()->where('key', 'mail.password')->value('value');
    expect($raw)->not->toBe('secret-pass')
        ->and($settings->get('mail.password'))->toBe('secret-pass');
});

it('preserves smtp password when not included in a later setMany', function () {
    $this->seed(WebsiteSettingsSeeder::class);

    $settings = app(SettingsService::class);
    $settings->set('mail.password', 'keep-me-secret');

    $settings->setMany([
        'mail.host' => 'smtp.keep.test',
        'mail.port' => '587',
    ]);

    expect($settings->get('mail.password'))->toBe('keep-me-secret')
        ->and($settings->get('mail.host'))->toBe('smtp.keep.test');
});

it('shows color mode toggle on the storefront', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Ganti mode gelap/terang', false)
        ->assertSee('color-mode', false);
});

it('shows branding theme whatsapp and smtp sections on settings page', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(WebsiteSettingsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(ManageWebsiteSettings::class)
        ->assertOk()
        ->assertSee('Nama Website')
        ->assertSee('Logo')
        ->assertSee('Favicon')
        ->assertSee('Preset Tema Storefront')
        ->assertSee('Template Warna')
        ->assertSee('Emerald')
        ->assertSee('Ocean')
        ->assertSee('Rose')
        ->assertSee('Nomor WhatsApp')
        ->assertSee('Host SMTP')
        ->assertSee('Simpan')
        ->assertSee('Kirim Email Uji');
});
