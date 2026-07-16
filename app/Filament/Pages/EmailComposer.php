<?php

namespace App\Filament\Pages;

use App\Helpers\EmailHelper;
use App\Mail\WinnerNotification;
use App\Models\Winner;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class EmailComposer extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 15;

    protected static string $view = 'filament.pages.email-composer';

    public static function canAccess(): bool
    {
        return auth()->user()?->canViewWinners() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Select Winner')
                    ->schema([
                        Forms\Components\Select::make('winner_id')
                            ->label('Choose Winner')
                            ->searchable()
                            ->options(function (): array {
                                return Winner::orderBy('is_demo', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(function (Winner $w) {
                                        $email = $w->email ?: 'no email';
                                        $demo = $w->is_demo ? ' ⭐' : '';
                                        return [$w->id => "{$w->first_name} {$w->last_name} ({$email} — {$w->unique_code}){$demo}"];
                                    })->toArray();
                            })
                            ->getSearchResultsUsing(function (string $search): array {
                                return Winner::where(function (Builder $q) use ($search) {
                                    $q->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%")
                                      ->orWhere('unique_code', 'like', "%{$search}%");
                                })->orderBy('is_demo', 'desc')->orderBy('created_at', 'desc')
                                ->limit(50)->get()->mapWithKeys(function (Winner $w) {
                                    $email = $w->email ?: 'no email';
                                    $demo = $w->is_demo ? ' ⭐' : '';
                                    return [$w->id => "{$w->first_name} {$w->last_name} ({$email} — {$w->unique_code}){$demo}"];
                                })->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $w = Winner::find($value);
                                if (!$w) return '';
                                $email = $w->email ?? 'no email';
                                return "{$w->first_name} {$w->last_name} ({$email} — {$w->unique_code})";
                            })
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => $this->updateWinnerInfo($state, $set))
                            ->required(),
                    ]),
                Forms\Components\Section::make('Winner Info')
                    ->schema([
                        Forms\Components\Placeholder::make('winner_name')
                            ->label('Name')
                            ->content(fn (Forms\Get $get) => $get('winner_name') ?: '—'),
                        Forms\Components\Placeholder::make('winner_email')
                            ->label('Email')
                            ->content(fn (Forms\Get $get) => $get('winner_email') ?: '—'),
                        Forms\Components\Placeholder::make('winner_code')
                            ->label('Winner Code')
                            ->content(fn (Forms\Get $get) => $get('winner_code') ?: '—'),
                    ])
                    ->columns(3)
                    ->visible(fn (Forms\Get $get) => (bool) $get('winner_id')),
                Forms\Components\Section::make('Email Content')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter email subject...'),
                        Forms\Components\RichEditor::make('message')
                            ->label('Message')
                            ->required()
                            ->placeholder('Write your message here...')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'bulletList', 'orderedList',
                                'blockquote', 'link',
                            ])
                            ->grow(),
                    ])
                    ->visible(fn (Forms\Get $get) => (bool) $get('winner_id')),
            ]);
    }

    protected function updateWinnerInfo($winnerId, Forms\Set $set): void
    {
        if (!$winnerId) {
            $set('winner_name', null);
            $set('winner_email', null);
            $set('winner_code', null);
            return;
        }
        $winner = Winner::find($winnerId);
        if ($winner) {
            $set('winner_name', "{$winner->first_name} {$winner->last_name}");
            $set('winner_email', $winner->email ?: 'No email on file');
            $set('winner_code', $winner->unique_code);
        }
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $winner = Winner::find($data['winner_id']);
        if (!$winner) {
            Notification::make()->title('Winner not found')->danger()->send();
            return;
        }

        if (empty($winner->email)) {
            Notification::make()
                ->title('No email address')
                ->body("{$winner->first_name} {$winner->last_name} does not have an email address on file.")
                ->danger()
                ->send();
            return;
        }

        EmailHelper::send(
            new WinnerNotification($winner, $data['subject'], $data['message']),
            $winner->email,
            $winner->first_name
        );

        app(ActivityLogger::class)->log(
            'admin_email',
            'winners',
            (string) $winner->id,
            auth()->id(),
            null,
            "Admin emailed {$winner->first_name} {$winner->last_name}: {$data['subject']}"
        );

        Notification::make()
            ->title('Email sent')
            ->body("Email sent to {$winner->first_name} {$winner->last_name} ({$winner->email})")
            ->success()
            ->send();

        $this->form->fill();
    }

    public static function getNavigationLabel(): string
    {
        return 'Send Email';
    }

    public function getTitle(): string
    {
        return 'Send Email to Winner';
    }
}
