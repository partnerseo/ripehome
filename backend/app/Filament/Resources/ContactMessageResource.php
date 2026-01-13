<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'İletişim Mesajları';

    protected static ?string $navigationGroup = 'Mesajlar';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'Mesaj';

    protected static ?string $pluralModelLabel = 'İletişim Mesajları';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ad Soyad')
                    ->disabled()
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->disabled()
                    ->required(),

                Forms\Components\TextInput::make('subject')
                    ->label('Konu')
                    ->disabled(),

                Forms\Components\Textarea::make('message')
                    ->label('Mesaj')
                    ->disabled()
                    ->rows(5)
                    ->columnSpanFull()
                    ->required(),

                Forms\Components\Toggle::make('is_read')
                    ->label('Okundu')
                    ->required(),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Gönderilme Tarihi')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Konu')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Okunma Durumu')
                    ->placeholder('Tümü')
                    ->trueLabel('Okunanlar')
                    ->falseLabel('Okunmayanlar'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Başlangıç Tarihi'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_read')
                    ->label('Okundu')
                    ->icon('heroicon-o-check')
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => true]))
                    ->visible(fn (ContactMessage $record) => !$record->is_read)
                    ->color('success'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
