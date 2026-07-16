<?php

namespace App\Filament\Resources\WinnerResource\RelationManagers;

use App\Helpers\EmailHelper;
use App\Mail\WinnerNotification;
use App\Models\Message;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WinnerMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Messages & Activity';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(4),
                Forms\Components\Toggle::make('read')
                    ->label('Read by winner'),
                Forms\Components\Toggle::make('sent_by_admin')
                    ->label('Sent by admin'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('content')
                    ->limit(50),
                Tables\Columns\IconColumn::make('sent_by_admin')
                    ->label('From')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope')
                    ->falseIcon('heroicon-o-user')
                    ->tooltip(fn ($state) => $state ? 'From Admin' : 'From Winner'),
                Tables\Columns\IconColumn::make('read')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Note')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['winner_id'] = $this->getOwnerRecord()->id;
                        $data['sent_by'] = 'admin';
                        $data['sent_by_admin'] = true;

                        return $data;
                    }),
                Tables\Actions\Action::make('reply')
                    ->label('Reply via Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function (array $data): void {
                        $winner = $this->getOwnerRecord();

                        if ($winner->email) {
                            EmailHelper::send(
                                new WinnerNotification($winner, $data['subject'], $data['content']),
                                $winner->email,
                                $winner->first_name
                            );
                        }

                        Message::create([
                            'winner_id' => $winner->id,
                            'subject' => $data['subject'],
                            'content' => $data['content'],
                            'sent_by' => 'admin',
                            'sent_by_admin' => true,
                            'read' => false,
                        ]);

                        app(ActivityLogger::class)->log(
                            'admin_reply',
                            'messages',
                            null,
                            auth()->id(),
                            null,
                            "Admin replied to {$winner->first_name} {$winner->last_name}: {$data['subject']}"
                        );

                        Notification::make()
                            ->title('Reply sent')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
