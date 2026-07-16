<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WinnerFeatures extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.winner-features';

    public static function canAccess(): bool
    {
        return auth()->user()?->canViewWinnerFeatures() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::getWinnerFeaturesConfig());
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Feature Sections')
                    ->description('Toggle entire sections on/off for winners')
                    ->schema([
                        Forms\Components\Toggle::make('show_messages')
                            ->label('Messages')
                            ->helperText('Show/hide the Messages section and send-message form on the dashboard'),
                        Forms\Components\Toggle::make('show_documents')
                            ->label('Documents')
                            ->helperText('Show/hide the Documents section and upload form on the dashboard'),
                        Forms\Components\Toggle::make('show_deposits')
                            ->label('Deposits')
                            ->helperText('Show/hide the Make a Deposit page, form, and history'),
                        Forms\Components\Toggle::make('show_withdrawals')
                            ->label('Withdrawals')
                            ->helperText('Show/hide the Withdraw Funds page, form, and history'),
                        Forms\Components\Toggle::make('show_transactions')
                            ->label('Transaction History')
                            ->helperText('Show/hide the Transaction History page and ledger'),
                        Forms\Components\Toggle::make('show_orders')
                            ->label('My Orders')
                            ->helperText('Show/hide the My Orders page in the winner dashboard nav'),
                    ])->columns(2),

                Forms\Components\Section::make('Data Visibility')
                    ->description('Control what data elements are visible across winner pages')
                    ->schema([
                        Forms\Components\Toggle::make('show_dates')
                            ->label('Dates')
                            ->helperText('Show/hide all dates (created_at, approved_at, completed_at, etc.) across all winner views'),
                        Forms\Components\Toggle::make('show_balance_summary')
                            ->label('Balance Summary Cards')
                            ->helperText('Show/hide the prize amount, total deposits, total withdrawn, and available balance cards'),
                        Forms\Components\Toggle::make('show_winner_code')
                            ->label('Winner Code')
                            ->helperText('Show/hide the winner unique code display and copy button on the dashboard'),
                        Forms\Components\Toggle::make('show_next_steps')
                            ->label('Next Steps')
                            ->helperText('Show/hide the Next Steps callout on the dashboard'),
                        Forms\Components\Toggle::make('show_quick_actions')
                            ->label('Quick Action Cards')
                            ->helperText('Show/hide the Quick Actions grid on the dashboard'),
                    ])->columns(2),

                Forms\Components\Section::make('External Links')
                    ->description('Show/hide links to external/public features in winner navigation')
                    ->schema([
                        Forms\Components\Toggle::make('show_giveaways')
                            ->label('Giveaways Link')
                            ->helperText('Show/hide the Giveaways link in winner navigation and dashboard'),
                        Forms\Components\Toggle::make('show_games')
                            ->label('Games / Spin & Win Link')
                            ->helperText('Show/hide the Spin & Win link in winner navigation and dashboard'),
                        Forms\Components\Toggle::make('show_shop')
                            ->label('Shop Link')
                            ->helperText('Show/hide the Shop link in winner navigation and dashboard'),
                        Forms\Components\Toggle::make('show_memberships')
                            ->label('Memberships Link')
                            ->helperText('Show/hide the Memberships link in winner navigation and dashboard'),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setWinnerFeaturesConfig($data);

        Notification::make()
            ->title('Winner features saved')
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Winner Features';
    }

    public function getTitle(): string
    {
        return 'Winner Features';
    }
}
