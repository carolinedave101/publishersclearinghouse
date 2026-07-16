<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserMessageResource\Pages;
use App\Models\UserMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserMessageResource extends Resource
{
    protected static ?string $model = UserMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewUserMessages() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Compose Message')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Recipient')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->visibleOn('create'),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->rows(6),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('direction')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'admin_to_user' ? 'Sent' : 'Received')
                    ->color(fn (string $state): string => $state === 'admin_to_user' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('direction')
                    ->options([
                        'admin_to_user' => 'Sent to Users',
                        'user_to_admin' => 'Received from Users',
                    ]),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('subject')
                            ->disabled(),
                        Forms\Components\Textarea::make('message')
                            ->disabled()
                            ->rows(6),
                    ])
                    ->mutateRecordDataUsing(function (array $data, UserMessage $record): array {
                        if ($record->direction === 'admin_to_user' && !$record->is_read) {
                            $record->update(['is_read' => true, 'read_at' => now()]);
                        }
                        return $data;
                    }),
                Tables\Actions\Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->modalHeading('Reply to Message')
                    ->modalWidth('lg')
                    ->form([
                        Forms\Components\Hidden::make('user_id'),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->rows(6)
                            ->label('Your Reply'),
                    ])
                    ->action(function (array $data, UserMessage $record): void {
                        $reply = \App\Models\UserMessage::create([
                            'user_id' => $record->user_id,
                            'admin_id' => auth()->id(),
                            'subject' => $data['subject'],
                            'message' => $data['message'],
                            'direction' => 'admin_to_user',
                            'is_read' => false,
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Reply sent')
                            ->body("Your reply to {$record->user->name} has been sent.")
                            ->success()
                            ->send();
                    })
                    ->mountUsing(function (Forms\Form $form, UserMessage $record): void {
                        $form->fill([
                            'user_id' => $record->user_id,
                            'subject' => 'Re: ' . $record->subject,
                        ]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Message')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['admin_id'] = auth()->id();
                        $data['direction'] = 'admin_to_user';
                        return $data;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserMessages::route('/'),
            'create' => Pages\CreateUserMessage::route('/create'),
        ];
    }
}
