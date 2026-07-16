<?php

namespace App\Filament\Resources\WinnerResource\RelationManagers;

use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class WinnerDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_type')
                    ->options([
                        'id' => 'Photo ID',
                        'proof_of_address' => 'Proof of Address',
                        'tax_form' => 'Tax Form (W-9/W-2)',
                        'bank_details' => 'Bank Details',
                        'release_form' => 'Prize Release Form',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('custom_type')
                    ->visible(fn (Forms\Get $get) => $get('document_type') === 'other'),
                Forms\Components\Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\RichEditor::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'id' => 'Photo ID',
                        'proof_of_address' => 'Proof of Address',
                        'tax_form' => 'Tax Form',
                        'bank_details' => 'Bank Details',
                        'release_form' => 'Release Form',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('file_name')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'gray',
                        'under_review' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['winner_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->file_path ? Storage::url($record->file_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => (bool) $record->file_path),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                        ]);

                        app(ActivityLogger::class)->log(
                            'document_verified',
                            'documents',
                            (string) $record->id,
                            auth()->id(),
                            null,
                            "Document {$record->document_type} for winner approved"
                        );

                        if ($this->getOwnerRecord()->email) {
                            \App\Helpers\EmailHelper::send(
                                new \App\Mail\WinnerNotification(
                                    $this->getOwnerRecord(),
                                    'Document Approved',
                                    "Good news! Your document \"{$record->document_type}\" has been approved and verified by our team."
                                ),
                                $this->getOwnerRecord()->email,
                                $this->getOwnerRecord()->first_name
                            );
                        }

                        Notification::make()->title('Document approved')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for rejection')
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['reason'],
                        ]);

                        if ($this->getOwnerRecord()->email) {
                            \App\Helpers\EmailHelper::send(
                                new \App\Mail\WinnerNotification(
                                    $this->getOwnerRecord(),
                                    'Document Needs Attention',
                                    "Unfortunately your document \"{$record->document_type}\" was rejected. Reason: {$data['reason']}. Please upload a new copy."
                                ),
                                $this->getOwnerRecord()->email,
                                $this->getOwnerRecord()->first_name
                            );
                        }

                        Notification::make()->title('Document rejected')->danger()->send();
                    }),
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
