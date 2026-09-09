<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class ManageWebsiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Pengaturan Website';

    protected static ?string $title = 'Pengaturan Website';

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['owner', 'admin']) ?? false;
    }

    public function mount(SettingsService $settings): void
    {
        $this->form->fill([
            'site_name' => $settings->siteName(),
            'site_logo_path' => $settings->get('site.logo_path'),
            'site_favicon_path' => $settings->get('site.favicon_path'),
            'theme_preset' => $settings->themePreset(),
            'contact_whatsapp' => $settings->whatsapp(),
            'mail_mailer' => $settings->get('mail.mailer', 'smtp'),
            'mail_host' => $settings->get('mail.host'),
            'mail_port' => $settings->get('mail.port', '587'),
            'mail_username' => $settings->get('mail.username'),
            'mail_password' => null,
            'mail_encryption' => $settings->get('mail.encryption', 'tls'),
            'mail_from_address' => $settings->get('mail.from_address'),
            'mail_from_name' => $settings->get('mail.from_name', $settings->siteName()),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $presetOptions = collect(config('website.presets', []))
            ->mapWithKeys(fn (array $preset, string $key) => [
                $key => ($preset['label'] ?? $key).' — '.($preset['description'] ?? ''),
            ])
            ->all();

        return $schema
            ->components([
                Section::make('Identitas Website')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nama Website')
                            ->required()
                            ->maxLength(120),
                        FileUpload::make('site_logo_path')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->imagePreviewHeight('80'),
                        FileUpload::make('site_favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->imagePreviewHeight('48'),
                    ]),
                Section::make('Preset Tema Storefront')
                    ->schema([
                        Select::make('theme_preset')
                            ->label('Preset')
                            ->options($presetOptions)
                            ->required()
                            ->native(false),
                    ]),
                Section::make('Kontak Support')
                    ->schema([
                        TextInput::make('contact_whatsapp')
                            ->label('Nomor WhatsApp')
                            ->helperText('Format internasional tanpa +, contoh: 6281234567890')
                            ->required()
                            ->regex('/^[0-9]{8,20}$/')
                            ->maxLength(20),
                    ]),
                Section::make('Pengaturan Email')
                    ->schema([
                        Select::make('mail_mailer')
                            ->label('Mailer')
                            ->options([
                                'smtp' => 'SMTP',
                                'log' => 'Log (uji lokal)',
                                'array' => 'Array',
                            ])
                            ->required(),
                        TextInput::make('mail_host')
                            ->label('Host SMTP')
                            ->maxLength(255),
                        TextInput::make('mail_port')
                            ->label('Port')
                            ->numeric()
                            ->default(587),
                        TextInput::make('mail_username')
                            ->label('Username')
                            ->maxLength(255),
                        TextInput::make('mail_password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Kosongkan jika tidak ingin mengubah password yang tersimpan.')
                            ->maxLength(255),
                        Select::make('mail_encryption')
                            ->label('Enkripsi')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                '' => 'Tidak ada',
                            ]),
                        TextInput::make('mail_from_address')
                            ->label('From Address')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('mail_from_name')
                            ->label('From Name')
                            ->maxLength(120),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(SettingsService $settings): void
    {
        $data = $this->form->getState();

        $logo = $data['site_logo_path'] ?? null;
        if (is_array($logo)) {
            $logo = array_values($logo)[0] ?? null;
        }

        $favicon = $data['site_favicon_path'] ?? null;
        if (is_array($favicon)) {
            $favicon = array_values($favicon)[0] ?? null;
        }

        $whatsapp = preg_replace('/\D+/', '', (string) ($data['contact_whatsapp'] ?? '')) ?: null;

        $payload = [
            'site.name' => $data['site_name'] ?? null,
            'site.logo_path' => $logo,
            'site.favicon_path' => $favicon,
            'theme.preset' => $data['theme_preset'] ?? 'emerald',
            'contact.whatsapp' => $whatsapp,
            'whatsapp.support' => $whatsapp,
            'mail.mailer' => $data['mail_mailer'] ?? 'smtp',
            'mail.host' => $data['mail_host'] ?? null,
            'mail.port' => (string) ($data['mail_port'] ?? '587'),
            'mail.username' => $data['mail_username'] ?? null,
            'mail.encryption' => $data['mail_encryption'] ?? 'tls',
            'mail.from_address' => $data['mail_from_address'] ?? null,
            'mail.from_name' => $data['mail_from_name'] ?? null,
        ];

        if (filled($data['mail_password'] ?? null)) {
            $payload['mail.password'] = $data['mail_password'];
        }

        $settings->setMany($payload);

        $settings->applyRuntimeConfig();

        Notification::make()
            ->title('Pengaturan disimpan')
            ->success()
            ->send();
    }

    public function sendTestEmail(SettingsService $settings): void
    {
        $this->save($settings);

        $to = Auth::user()?->email;
        if (! filled($to)) {
            Notification::make()
                ->title('Email pengguna tidak tersedia')
                ->danger()
                ->send();

            return;
        }

        try {
            Mail::raw(
                'Email uji dari '.$settings->siteName().' pada '.now()->toDateTimeString(),
                function ($message) use ($to, $settings): void {
                    $message->to($to)
                        ->subject('Tes SMTP '.$settings->siteName());
                },
            );

            Notification::make()
                ->title('Email uji dikirim ke '.$to)
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal mengirim email uji')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                            Action::make('sendTestEmail')
                                ->label('Kirim Email Uji')
                                ->color('gray')
                                ->action('sendTestEmail'),
                        ]),
                    ]),
            ]);
    }
}
