<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MailSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.mail-settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->canViewMailSettings() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::getMailConfig());
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Mail Driver')
                    ->description('Choose how emails are sent from your application')
                    ->schema([
                        Forms\Components\Select::make('mailer')
                            ->label('Mail Driver')
                            ->options([
                                'smtp' => 'SMTP Server',
                                'resend' => 'Resend API',
                                'log' => 'Log to File (testing)',
                            ])
                            ->default(config('mail.default', 'smtp'))
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('mailer', $state))
                            ->required(),
                    ]),
                Forms\Components\Section::make('Resend Configuration')
                    ->visible(fn (Forms\Get $get) => $get('mailer') === 'resend')
                    ->schema([
                        Forms\Components\TextInput::make('resend_api_key')
                            ->label('Resend API Key')
                            ->password()
                            ->revealable(),
                    ]),
                Forms\Components\Section::make('SMTP Configuration')
                    ->description('Configure SMTP settings for webmail (Gmail, Outlook, cPanel, etc.) — used when Mail Driver is set to SMTP Server')
                    ->schema([
                        Forms\Components\Select::make('smtp_preset')
                            ->label('Provider Preset')
                            ->helperText('Select a provider to auto-fill SMTP settings, or choose Manual Configuration to enter your own')
                            ->options([
                                'custom' => 'Manual Configuration',
                                'stackmail' => 'StackMail',
                                'gmail' => 'Gmail',
                                'outlook' => 'Outlook / Hotmail',
                                'yahoo' => 'Yahoo Mail',
                                'cpanel' => 'cPanel Webmail',
                                'zoho' => 'Zoho Mail',
                            ])
                            ->default('custom')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $presets = [
                                    'stackmail' => ['smtp.stackmail.com', 587, 'tls'],
                                    'gmail' => ['smtp.gmail.com', 587, 'tls'],
                                    'outlook' => ['smtp-mail.outlook.com', 587, 'tls'],
                                    'yahoo' => ['smtp.mail.yahoo.com', 465, 'ssl'],
                                    'cpanel' => ['mail.', 465, 'ssl'],
                                    'zoho' => ['smtp.zoho.com', 587, 'tls'],
                                ];
                                if ($state !== 'custom' && isset($presets[$state])) {
                                    [$host, $port, $encryption] = $presets[$state];
                                    $set('smtp_host', $host);
                                    $set('smtp_port', $port);
                                    $set('smtp_encryption', $encryption);
                                }
                            }),
                        Forms\Components\TextInput::make('smtp_host')
                            ->label('SMTP Host')
                            ->helperText(fn (Forms\Get $get) => $get('smtp_preset') === 'cpanel' ? 'Replace with your domain: mail.yourdomain.com' : '')
                            ->default('smtp.gmail.com'),
                        Forms\Components\TextInput::make('smtp_port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->default(587),
                        Forms\Components\Select::make('smtp_encryption')
                            ->label('Encryption')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                '' => 'None',
                            ])
                            ->default('tls'),
                        Forms\Components\TextInput::make('smtp_username')
                            ->label('SMTP Username')
                            ->helperText('Usually your full email address'),
                        Forms\Components\TextInput::make('smtp_password')
                            ->label('SMTP Password')
                            ->password()
                            ->revealable(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Sender Information')
                    ->schema([
                        Forms\Components\TextInput::make('from_address')
                            ->label('From Email Address')
                            ->email()
                            ->default(config('mail.from.address', 'winnersteam@publishersclearing.info'))
                            ->required(),
                        Forms\Components\TextInput::make('from_name')
                            ->label('From Name')
                            ->default(config('mail.from.name', 'Publishers Clearing House'))
                            ->required(),
                        Forms\Components\TextInput::make('admin_email')
                            ->label('Admin Notification Email')
                            ->email()
                            ->default(env('PCH_ADMIN_EMAIL', 'admin@pch.com'))
                            ->required()
                            ->helperText('All admin notifications will be sent to this address'),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setMailConfig($data);

        Notification::make()
            ->title('Mail settings saved')
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Mail Settings';
    }

    public function getTitle(): string
    {
        return 'Mail Settings';
    }
}
