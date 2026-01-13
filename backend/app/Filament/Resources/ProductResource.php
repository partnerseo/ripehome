<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Ürünler';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Ürün';

    protected static ?string $pluralModelLabel = 'Ürünler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ürün Adı')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->readOnly(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Görseller')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Ürün Görselleri')
                            ->image()
                            ->multiple()
                            ->directory('products')
                            ->maxFiles(10)
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('İlişkiler')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Kategori Adı')
                                    ->required(),
                            ]),

                        Forms\Components\CheckboxList::make('tags')
                            ->label('Etiketler')
                            ->relationship('tags', 'name')
                            ->columns(2)
                            ->gridDirection('row'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Özellikler')
                    ->schema([
                        Forms\Components\Repeater::make('features')
                            ->label('Ürün Özellikleri')
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->label('İkon (Heroicon adı)')
                                    ->placeholder('heroicon-o-star'),

                                Forms\Components\TextInput::make('title')
                                    ->label('Başlık')
                                    ->required(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Açıklama')
                                    ->rows(2),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Başlık')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Açıklama')
                            ->rows(3),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Forms\Components\Section::make('Yayın Ayarları')
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

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Öne Çıkan')
                            ->default(false)
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Görsel')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->getStateUsing(fn ($record) => $record->images[0] ?? null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Etiketler')
                    ->badge()
                    ->separator(',')
                    ->limit(2),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif Olanlar')
                    ->falseLabel('Pasif Olanlar'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Öne Çıkan')
                    ->placeholder('Tümü')
                    ->trueLabel('Öne Çıkanlar')
                    ->falseLabel('Normal'),

                Tables\Filters\SelectFilter::make('tags')
                    ->label('Etiket')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
