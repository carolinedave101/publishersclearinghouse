<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Mail\AdminEmail;
use App\Helpers\EmailHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->hidden(fn (string $operation): bool => $operation === 'edit')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                Forms\Components\Select::make('role')
                    ->options(\App\Models\User::roleOptions())
                    ->default('user')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $defaults = [];
                        if ($state === 'admin') {
                            $defaults = \App\Models\User::allPermissionKeys();
                        } elseif ($state === 'manager') {
                            $defaults = [
                                \App\Models\User::PERM_VIEW_WINNERS,
                                \App\Models\User::PERM_VIEW_DEPOSITS,
                                \App\Models\User::PERM_VIEW_WITHDRAWALS,
                                \App\Models\User::PERM_VIEW_TRANSACTIONS,
                                \App\Models\User::PERM_VIEW_MESSAGES,
                                \App\Models\User::PERM_VIEW_DOCUMENTS,
                                \App\Models\User::PERM_VIEW_USER_MESSAGES,
                                \App\Models\User::PERM_VIEW_PAYMENT_METHODS,
                                \App\Models\User::PERM_VIEW_SHOP_PRODUCTS,
                                \App\Models\User::PERM_VIEW_SHOP_ORDERS,
                                \App\Models\User::PERM_VIEW_MEMBERSHIP_TIERS,
                                \App\Models\User::PERM_VIEW_MEMBERSHIP_SUBSCRIPTIONS,
                                \App\Models\User::PERM_VIEW_SPIN_AND_WIN,
                                \App\Models\User::PERM_VIEW_SPIN_RESULTS,
                                \App\Models\User::PERM_VIEW_GIVEAWAYS,
                                \App\Models\User::PERM_VIEW_PAGES,
                                \App\Models\User::PERM_VIEW_SETTINGS,
                                \App\Models\User::PERM_VIEW_ACTIVITY_LOG,
                                \App\Models\User::PERM_VIEW_MAIL_SETTINGS,
                                \App\Models\User::PERM_VIEW_SITE_SETTINGS,
                                \App\Models\User::PERM_VIEW_WINNER_FEATURES,
                            ];
                        } elseif ($state === 'support') {
                            $defaults = [
                                \App\Models\User::PERM_VIEW_WINNERS,
                                \App\Models\User::PERM_VIEW_DEPOSITS,
                                \App\Models\User::PERM_VIEW_WITHDRAWALS,
                                \App\Models\User::PERM_VIEW_TRANSACTIONS,
                                \App\Models\User::PERM_VIEW_MESSAGES,
                                \App\Models\User::PERM_VIEW_DOCUMENTS,
                                \App\Models\User::PERM_VIEW_USER_MESSAGES,
                            ];
                        }
                        $set('permissions', $defaults);
                    }),
                Forms\Components\Section::make('Custom Permissions')
                    ->description('Override the role defaults by checking/unchecking individual permissions')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->options(\App\Models\User::allPermissions())
                            ->columns(3)
                            ->afterStateHydrated(fn (Forms\Components\CheckboxList $component, $state, $record) =>
                                $component->state($record?->grantedPermissions() ?? [])),
                    ]),
                Forms\Components\Toggle::make('is_super_admin')
                    ->label('Super Admin')
                    ->helperText('Super admins have unrestricted access and can manage other users'),
                Forms\Components\Toggle::make('is_admin')
                    ->label('Admin Access')
                    ->helperText('Grants access to the admin panel'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => \App\Models\User::roleOptions()[$state] ?? ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'primary',
                        'manager' => 'warning',
                        'support' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions')
                    ->label('Permissions')
                    ->state(fn ($record) => collect($record->grantedPermissions())->map(fn ($p) => \App\Models\User::allPermissions()[$p] ?? $p)->join(', '))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_super_admin')
                    ->label('Super Admin')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('sendEmail')
                    ->label('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function (User $record, array $data): void {
                        if (empty($record->email)) {
                            Notification::make()
                                ->title('No email address')
                                ->body('This user does not have an email address.')
                                ->danger()
                                ->send();
                            return;
                        }

                        EmailHelper::send(
                            new AdminEmail($data['subject'], $data['message'], $record->name),
                            $record->email,
                            $record->name
                        );

                        \App\Models\UserMessage::create([
                            'user_id' => $record->id,
                            'admin_id' => auth()->id(),
                            'subject' => $data['subject'],
                            'message' => $data['message'],
                            'direction' => 'admin_to_user',
                            'is_read' => false,
                        ]);

                        Notification::make()
                            ->title('Email sent')
                            ->body("Email sent to {$record->email}.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('emailAll')
                    ->label('Email Selected')
                    ->icon('heroicon-o-envelope')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                        $sent = 0;
                        foreach ($records as $record) {
                            if (empty($record->email)) {
                                continue;
                            }
                            EmailHelper::send(
                                new AdminEmail($data['subject'], $data['message'], $record->name),
                                $record->email,
                                $record->name
                            );
                            $sent++;
                        }

                        Notification::make()
                            ->title('Emails queued')
                            ->body("{$sent} email(s) sent to selected users.")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}

class ListUsers extends \Filament\Resources\Pages\ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}

class CreateUser extends \Filament\Resources\Pages\CreateRecord
{
    protected static string $resource = UserResource::class;
}

class EditUser extends \Filament\Resources\Pages\EditRecord
{
    protected static string $resource = UserResource::class;
}
