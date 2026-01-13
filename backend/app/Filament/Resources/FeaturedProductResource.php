<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeaturedProductResource\Pages;
use App\Models\FeaturedProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeaturedProductResource extends Resource
{
    protected static ?string $model = FeaturedProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Öne Çıkan Ürünler';

    protected static ?string $navigationGroup = 'Anasayfa';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Öne Çıkan Ürün';

    protected static ?string $pluralModelLabel = 'Öne Çıkan Ürünler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('category_label')
                            ->label('Üst Etiket')
                            ->placeholder('Örn: PREMIUM SPA DENEYİMİ')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Örn: Özel Dokuma Waffle Bornoz'),

                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Görsel')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Ana Görsel')
                            ->image()
                            ->directory('featured-products')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Etiketler ve Buton')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiketler')
                            ->placeholder('Örn: %100 Pamuk, Yüksek Emicilik')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('button_text')
                            ->label('Buton Metni')
                            ->default('Detayları Gör')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('button_link')
                            ->label('Buton Linki')
                            ->required()
                            ->placeholder('/products/waffle-bornoz')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ayarlar')
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel')
                    ->size(80),

                Tables\Columns\TextColumn::make('category_label')
                    ->label('Üst Etiket')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

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
            'index' => Pages\ListFeaturedProducts::route('/'),
            'create' => Pages\CreateFeaturedProduct::route('/create'),
            'edit' => Pages\EditFeaturedProduct::route('/{record}/edit'),
        ];
    }
}












