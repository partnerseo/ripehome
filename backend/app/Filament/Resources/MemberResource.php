<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use App\Services\TokenService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Üyeler';

    protected static ?string $navigationGroup = 'Üyelik';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Üye';

    protected static ?string $pluralModelLabel = 'Üyeler';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kişisel Bilgiler')
                ->schema([
                    Forms\Components\TextInput::make('ad')
                        ->label('Ad')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('soyad')
                        ->label('Soyad')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('firma')
                        ->label('Firma')
                        ->maxLength(200),

                    Forms\Components\TextInput::make('telefon')
                        ->label('Telefon')
                        ->required()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('ulke')
                        ->label('Ülke Kodu')
                        ->required()
                        ->maxLength(2)
                        ->default('TR'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Durum')
                ->schema([
                    Forms\Components\Select::make('durum')
                        ->label('Durum')
                        ->options([
                            'beklemede' => 'Beklemede',
                            'onaylandi' => 'Onaylandı',
                            'pasif'     => 'Pasif',
                        ])
                        ->required(),

                    Forms\Components\DateTimePicker::make('banned_at')
                        ->label('Engelleme Tarihi')
                        ->nullable(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->getStateUsing(fn ($record) => "{$record->ad} {$record->soyad}")
                    ->searchable(['ad', 'soyad'])
                    ->sortable('ad'),

                Tables\Columns\TextColumn::make('firma')
                    ->label('Firma')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('telefon')
                    ->label('Telefon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ulke')
                    ->label('Ülke')
                    ->badge()
                    ->width(60),

                Tables\Columns\BadgeColumn::make('durum')
                    ->label('Durum')
                    ->colors([
                        'warning' => 'beklemede',
                        'success' => 'onaylandi',
                        'danger'  => 'pasif',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'beklemede' => 'Beklemede',
                        'onaylandi' => 'Onaylandı',
                        'pasif'     => 'Pasif',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('durum')
                    ->label('Durum')
                    ->options([
                        'beklemede' => 'Beklemede',
                        'onaylandi' => 'Onaylandı',
                        'pasif'     => 'Pasif',
                    ]),

                Tables\Filters\SelectFilter::make('ulke')
                    ->label('Ülke'),
            ])
            ->actions([
                Tables\Actions\Action::make('onayla')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->durum === 'beklemede')
                    ->action(function ($record) {
                        $record->update(['durum' => 'onaylandi']);
                        Notification::make()->title('Üye onaylandı.')->success()->send();
                    }),

                Tables\Actions\Action::make('pasife_al')
                    ->label('Pasife Al')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->durum === 'onaylandi')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['durum' => 'pasif']);
                        app(TokenService::class)->revokeAll($record);
                        Notification::make()->title('Üye pasife alındı, tüm tokenlar iptal edildi.')->warning()->send();
                    }),

                Tables\Actions\Action::make('token_iptal')
                    ->label('Oturumları Kapat')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(TokenService::class)->revokeAll($record);
                        Notification::make()->title('Tüm aktif oturumlar kapatıldı.')->warning()->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('toplu_onayla')
                    ->label('Seçilileri Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records) {
                        $records->each(fn ($r) => $r->update(['durum' => 'onaylandi']));
                        Notification::make()->title('Üyeler onaylandı.')->success()->send();
                    }),

                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit'   => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('durum', 'beklemede')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }
}
