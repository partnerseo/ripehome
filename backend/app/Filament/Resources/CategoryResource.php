<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Kategoriler';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategoriler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Diller')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('🇹🇷 Türkçe')
                            ->schema([
                                Forms\Components\TextInput::make('name_tr')
                                    ->label('Kategori Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                    ),
                                Forms\Components\Textarea::make('description_tr')
                                    ->label('Açıklama')
                                    ->rows(3),
                            ]),
                        Forms\Components\Tabs\Tab::make('🇬🇧 English')
                            ->schema([
                                Forms\Components\TextInput::make('name_en')
                                    ->label('Category Name')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description_en')
                                    ->label('Description')
                                    ->rows(3),
                            ]),
                        Forms\Components\Tabs\Tab::make('🇸🇦 العربية')
                            ->schema([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label('اسم الفئة')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description_ar')
                                    ->label('الوصف')
                                    ->rows(3),
                            ]),
                        Forms\Components\Tabs\Tab::make('🇷🇺 Русский')
                            ->schema([
                                Forms\Components\TextInput::make('name_ru')
                                    ->label('Название категории')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description_ru')
                                    ->label('Описание')
                                    ->rows(3),
                            ]),
                        Forms\Components\Tabs\Tab::make('🇩🇪 Deutsch')
                            ->schema([
                                Forms\Components\TextInput::make('name_de')
                                    ->label('Kategoriename')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description_de')
                                    ->label('Beschreibung')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->readOnly(),

                Forms\Components\FileUpload::make('image')
                    ->label('Görsel')
                    ->image()
                    ->disk('public')
                    ->directory('categories')
                    ->visibility('public')
                    ->maxSize(10240)
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1', '4:3', '16:9'])
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->label('Kategori Fiyatı (USD)')
                    ->numeric()
                    ->prefix('$')
                    ->placeholder('0.00')
                    ->helperText('Bu fiyat, ürünlere özel fiyat girilmemişse otomatik uygulanır.'),

                Forms\Components\TextInput::make('order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kategori Adı')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün Sayısı')
                    ->counts('products')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Kategori Fiyatı')
                    ->formatStateUsing(fn ($state) => $state ? '$' . number_format((float)$state, 2) : '—')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif Olanlar')
                    ->falseLabel('Pasif Olanlar'),
            ])
            ->actions([
                Tables\Actions\Action::make('fiyat_uygula')
                    ->label('Fiyatı Ürünlere Uygula')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Fiyatı Tüm Ürünlere Uygula')
                    ->modalDescription(fn ($record) => "\"{$record->name}\" kategorisindeki tüm urunlerin fiyati "
                        . ($record->price ? '$' . number_format((float)$record->price, 2) : '(tanimli degil)')
                        . " olarak guncellenecek. Emin misiniz?")
                    ->visible(fn ($record) => $record->price !== null)
                    ->action(function ($record) {
                        $count = $record->products()->update(['price' => $record->price]);
                        Notification::make()
                            ->title("{$count} urune \${$record->price} fiyati uygulandi.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
