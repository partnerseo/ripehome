<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityLogResource\Pages;
use App\Models\SecurityLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class SecurityLogResource extends Resource
{
    protected static ?string $model = SecurityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'Güvenlik Logları';

    protected static ?string $navigationGroup = 'Üyelik';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Güvenlik Logu';

    protected static ?string $pluralModelLabel = 'Güvenlik Logları';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('olay')
                    ->label('Olay')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'login'         => 'success',
                        'registered'    => 'info',
                        'failed_otp'    => 'warning',
                        'otp_locked'    => 'danger',
                        'token_revoked' => 'warning',
                        'banned'        => 'danger',
                        'logout'        => 'gray',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'login'         => 'Giriş',
                        'registered'    => 'Kayıt',
                        'failed_otp'    => 'Hatalı OTP',
                        'otp_locked'    => 'OTP Kilitli',
                        'token_revoked' => 'Token İptal',
                        'banned'        => 'Engellendi',
                        'logout'        => 'Çıkış',
                        default         => $state,
                    }),

                Tables\Columns\TextColumn::make('member.ad')
                    ->label('Üye')
                    ->getStateUsing(fn ($record) => $record->member
                        ? "{$record->member->ad} {$record->member->soyad}"
                        : '-')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('ip')
                    ->label('IP Adresi')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('olay')
                    ->label('Olay Tipi')
                    ->options([
                        'login'         => 'Giriş',
                        'registered'    => 'Kayıt',
                        'failed_otp'    => 'Hatalı OTP',
                        'otp_locked'    => 'OTP Kilitli',
                        'token_revoked' => 'Token İptal',
                        'banned'        => 'Engellendi',
                        'logout'        => 'Çıkış',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSecurityLogs::route('/'),
        ];
    }
}
