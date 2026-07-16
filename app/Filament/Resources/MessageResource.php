<?php

namespace App\Filament\Resources;

use App\Models\Message;
use App\Models\Winner;
use App\Services\ActivityLogger;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewMessages() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('winner_id')
                    ->relationship('winner', 'first_name')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search): array {
                        return Winner::where(function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('unique_code', 'like', "%{$search}%");
                        })->limit(50)->get()->mapWithKeys(fn (Winner $w) => [
                            $w->id => "{$w->first_name} {$w->last_name}",
                        ])->toArray();
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->required(),
                Forms\Components\TextInput::make('subject'),
                Forms\Components\Textarea::make('content')
                    ->required(),
                Forms\Components\TextInput::make('sent_by'),
                Forms\Components\Toggle::make('sent_by_admin'),
                Forms\Components\Toggle::make('read')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('winner.first_name')
                    ->label('Winner')
                    ->formatStateUsing(fn ($record) => $record->winner ? "{$record->winner->first_name} {$record->winner->last_name}" : ''),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sent_by')
                    ->searchable(),
                Tables\Columns\IconColumn::make('sent_by_admin')
                    ->boolean(),
                Tables\Columns\IconColumn::make('read')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                TernaryFilter::make('sent_by_admin'),
                TernaryFilter::make('read'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['subject', 'content'];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMessages::route('/'),
            'create' => CreateMessage::route('/create'),
            'edit' => EditMessage::route('/{record}/edit'),
        ];
    }
}

class ListMessages extends \Filament\Resources\Pages\ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}

class CreateMessage extends \Filament\Resources\Pages\CreateRecord
{
    protected static string $resource = MessageResource::class;

    protected function afterCreate(): void
    {
        app(ActivityLogger::class)->log(
            'created',
            'messages',
            null,
            auth()->id(),
            null,
            "Message '{$this->record->subject}' was created"
        );
    }
}

class EditMessage extends \Filament\Resources\Pages\EditRecord
{
    protected static string $resource = MessageResource::class;
}
