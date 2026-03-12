<?php

namespace App\Filament\Widgets;

use App\Models\LoginLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LoginLogsWidget extends BaseWidget
{
    protected static ?string $heading = 'Son Giriş Kayıtları';
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LoginLog::query()
                    ->with('user')
                    ->latest('logged_in_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->icon('heroicon-o-user')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Konum')
                    ->getStateUsing(fn ($record) =>
                        $record->city && $record->country
                            ? "{$record->city}, {$record->country}"
                            : ($record->country ?? '-')
                    )
                    ->icon('heroicon-o-map-pin'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Tarayıcı')
                    ->getStateUsing(function ($record) {
                        $ua = $record->user_agent ?? '';
                        if (str_contains($ua, 'Chrome')) return 'Chrome';
                        if (str_contains($ua, 'Firefox')) return 'Firefox';
                        if (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) return 'Safari';
                        if (str_contains($ua, 'Edge')) return 'Edge';
                        return mb_substr($ua, 0, 30) . '...';
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Chrome' => 'blue',
                        'Firefox' => 'orange',
                        'Safari' => 'gray',
                        'Edge' => 'green',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('logged_in_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->logged_in_at?->format('d.m.Y H:i:s')),
            ])
            ->paginated(false)
            ->defaultSort('logged_in_at', 'desc');
    }
}
