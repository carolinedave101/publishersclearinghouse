<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class SiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.site-settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->canViewSiteSettings() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::getSiteConfig());
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Branding')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->default('Publishers Clearing House'),
                        Forms\Components\Textarea::make('site_description')
                            ->label('Site Description (Meta)')
                            ->rows(2),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Site Logo')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('settings')
                            ->visibility('public')
                            ->imageEditor()
                            ->helperText('Upload a logo image (max 2MB). Shows in nav & footer.'),
                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()
                            ->maxSize(512)
                            ->disk('public')
                            ->directory('settings')
                            ->visibility('public')
                            ->helperText('Upload a favicon (max 512KB, PNG/ICO preferred).'),
                    ])->columns(2),
                Forms\Components\Section::make('Footer')
                    ->schema([
                        Forms\Components\Textarea::make('footer_text')
                            ->label('Footer Description')
                            ->rows(2),
                        Forms\Components\TextInput::make('footer_tagline')
                            ->label('Footer Tagline'),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $existing = Setting::getSiteConfig();

        foreach (['logo', 'favicon'] as $field) {
            if (!empty($data[$field])) {
                if (is_array($data[$field])) {
                    $data[$field] = Storage::url($data[$field][0] ?? $data[$field]);
                } elseif (is_string($data[$field]) && !str_starts_with($data[$field], '/') && !str_starts_with($data[$field], 'http')) {
                    $data[$field] = Storage::url($data[$field]);
                }
            } else {
                $data[$field] = $existing[$field] ?? null;
            }
        }

        Setting::setSiteConfig($data);

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Site Settings';
    }

    public function getTitle(): string
    {
        return 'Site Settings';
    }
}
