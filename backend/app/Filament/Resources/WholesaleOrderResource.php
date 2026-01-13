<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WholesaleOrderResource\Pages;
use App\Models\WholesaleOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class WholesaleOrderResource extends Resource
{
    protected static ?string $model = WholesaleOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Toptan Siparişler';

    protected static ?string $navigationGroup = 'Siparişler';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Toptan Sipariş';

    protected static ?string $pluralModelLabel = 'Toptan Siparişler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Şirket Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Şirket Adı')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('contact_person')
                            ->label('Yetkili Kişi')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('city')
                            ->label('Şehir')
                            ->disabled(),

                        Forms\Components\Textarea::make('address')
                            ->label('Adres')
                            ->disabled()
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('tax_office')
                            ->label('Vergi Dairesi')
                            ->disabled(),

                        Forms\Components\TextInput::make('tax_number')
                            ->label('Vergi Numarası')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Sipariş Detayları')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Ürünler')
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('Ürün Adı')
                                    ->disabled(),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Adet')
                                    ->disabled(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Notlar')
                                    ->disabled()
                                    ->rows(2),

                                Forms\Components\FileUpload::make('product_image')
                                    ->label('Ürün Görseli')
                                    ->image()
                                    ->disabled()
                                    ->visibility('private'),
                            ])
                            ->columns(2)
                            ->disabled()
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('additional_notes')
                            ->label('Ek Notlar (Müşteri)')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Yönetim')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Yeni Talep',
                                'reviewing' => 'İnceleniyor',
                                'approved' => 'Onaylandı',
                                'rejected' => 'Reddedildi',
                                'completed' => 'Tamamlandı',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('İnceleme Tarihi')
                            ->native(false),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notları')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Bu notlar müşteriye gösterilmez.'),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Talep Tarihi')
                            ->disabled(),
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('company_name')
                    ->label('Şirket Adı')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Yetkili Kişi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_items')
                    ->label('Ürün Çeşidi')
                    ->getStateUsing(fn (WholesaleOrder $record) => count($record->items))
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Toplam Adet')
                    ->getStateUsing(fn (WholesaleOrder $record) => collect($record->items)->sum('quantity'))
                    ->sortable()
                    ->alignCenter()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Yeni Talep',
                        'reviewing' => 'İnceleniyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        'completed' => 'Tamamlandı',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Talep Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('İnceleme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Yeni Talep',
                        'reviewing' => 'İnceleniyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        'completed' => 'Tamamlandı',
                    ])
                    ->multiple(),

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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('approve')
                        ->label('Onayla')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (WholesaleOrder $record) => $record->status !== 'approved')
                        ->requiresConfirmation()
                        ->action(function (WholesaleOrder $record) {
                            $record->update([
                                'status' => 'approved',
                                'reviewed_at' => now(),
                            ]);
                        }),

                    Tables\Actions\Action::make('reject')
                        ->label('Reddet')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (WholesaleOrder $record) => !in_array($record->status, ['rejected', 'completed']))
                        ->requiresConfirmation()
                        ->action(function (WholesaleOrder $record) {
                            $record->update([
                                'status' => 'rejected',
                                'reviewed_at' => now(),
                            ]);
                        }),

                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_reviewing')
                        ->label('İnceleniyor Olarak İşaretle')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->action(fn ($records) => $records->each->update(['status' => 'reviewing'])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWholesaleOrders::route('/'),
            'view' => Pages\ViewWholesaleOrder::route('/{record}'),
            'edit' => Pages\EditWholesaleOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Siparişler yalnızca API'den oluşturulabilir
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}





