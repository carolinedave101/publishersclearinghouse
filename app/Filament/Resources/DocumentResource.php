<?php

namespace App\Filament\Resources;

use App\Models\Document;
use App\Models\Winner;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewDocuments() ?? false;
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
                Forms\Components\Select::make('document_type')
                    ->options([
                        'government_id' => 'Government Issued ID',
                        'proof_of_address' => 'Proof of Address',
                        'tax_form_w9' => 'Tax Form (W-9)',
                        'bank_information' => 'Bank Information',
                        'signed_agreement' => 'Signed Agreement',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('custom_type')
                    ->visible(fn (callable $get) => $get('document_type') === 'other'),
                Forms\Components\Section::make('Uploaded File')
                    ->schema([
                        Forms\Components\TextInput::make('file_name')
                            ->label('Filename')
                            ->disabled(),
                        Forms\Components\TextInput::make('file_size')
                            ->label('Size')
                            ->formatStateUsing(fn ($state) => $state ? round($state / 1024, 1) . ' KB' : '')
                            ->disabled(),
                        Forms\Components\TextInput::make('mime_type')
                            ->disabled(),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('download')
                                ->label('Download File')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn ($record) => $record && $record->file_path ? Storage::url($record->file_path) : '#')
                                ->openUrlInNewTab()
                                ->visible(fn ($record) => $record && $record->file_path),
                        ]),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Verification')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'requested' => 'Requested',
                                'submitted' => 'Submitted',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $state === 'verified' ? $set('verified_at', now()) : null),
                        Forms\Components\RichEditor::make('admin_notes')
                            ->label('Admin Notes'),
                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('verified_at')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('winner.first_name')
                    ->label('Winner')
                    ->formatStateUsing(fn ($record) => $record->winner ? "{$record->winner->first_name} {$record->winner->last_name}" : ''),
                Tables\Columns\TextColumn::make('document_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'government_id' => 'Government Issued ID',
                        'proof_of_address' => 'Proof of Address',
                        'tax_form_w9' => 'Tax Form (W-9)',
                        'bank_information' => 'Bank Information',
                        'signed_agreement' => 'Signed Agreement',
                        'other' => 'Other',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('File')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state ? round($state / 1024, 1) . ' KB' : '')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'requested' => 'gray',
                        'submitted' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}

class ListDocuments extends \Filament\Resources\Pages\ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}

class CreateDocument extends \Filament\Resources\Pages\CreateRecord
{
    protected static string $resource = DocumentResource::class;
}

class EditDocument extends \Filament\Resources\Pages\EditRecord
{
    protected static string $resource = DocumentResource::class;
}
