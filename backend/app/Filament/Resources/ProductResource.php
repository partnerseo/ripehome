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
                Forms\Components\Tabs::make('Ürün')
                    ->tabs([
                        // TAB 1 - Genel Bilgiler
                        Forms\Components\Tabs\Tab::make('Genel Bilgiler')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        // Sol panel (2/3)
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Section::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('URL Slug')
                                                            ->placeholder('otomatik-olusturulur')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique(Product::class, 'slug', ignoreRecord: true)
                                                            ->prefixIcon('heroicon-o-link')
                                                            ->helperText('URL adresinde kullanılacak kısa isim'),

                                                        Forms\Components\Select::make('category_id')
                                                            ->label('Kategori')
                                                            ->placeholder('Kategori seçin')
                                                            ->relationship('category', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->required()
                                                            ->prefixIcon('heroicon-o-folder')
                                                            ->createOptionForm([
                                                                Forms\Components\TextInput::make('name')
                                                                    ->label('Kategori Adı (TR)')
                                                                    ->required()
                                                                    ->live(onBlur: true)
                                                                    ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                                                        $set('slug', Str::slug($state))
                                                                    ),
                                                                Forms\Components\TextInput::make('slug')
                                                                    ->label('Slug')
                                                                    ->required(),
                                                            ]),

                                                        Forms\Components\TextInput::make('sku')
                                                            ->label('Ürün Kodu (SKU)')
                                                            ->placeholder('Örn: RH-00111')
                                                            ->maxLength(255)
                                                            ->prefixIcon('heroicon-o-hashtag'),
                                                    ])->columns(2),

                                                // Dil sekmeleri - Ad ve Açıklamalar
                                                Forms\Components\Section::make('İçerik (Diller)')
                                                    ->icon('heroicon-o-language')
                                                    ->description('Her dil için ürün adı ve açıklamasını girin')
                                                    ->schema([
                                                        Forms\Components\Tabs::make('İçerik Dilleri')
                                                            ->tabs([
                                                                Forms\Components\Tabs\Tab::make('🇹🇷 Türkçe')
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('name_tr')
                                                                            ->label('Ürün Adı')
                                                                            ->required()
                                                                            ->maxLength(255)
                                                                            ->live(onBlur: true)
                                                                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                                                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                                                            )
                                                                            ->placeholder('Örn: Premium Pamuk Nevresim Takımı'),
                                                                        Forms\Components\Textarea::make('short_description_tr')
                                                                            ->label('Kısa Açıklama')
                                                                            ->rows(2)
                                                                            ->maxLength(300)
                                                                            ->placeholder('Ürün listelerinde görünecek kısa özet...'),
                                                                        Forms\Components\RichEditor::make('description_tr')
                                                                            ->label('Detaylı Açıklama')
                                                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo'])
                                                                            ->placeholder('Ürünün tüm özelliklerini, kumaş bilgisini ve bakım talimatlarını buraya yazın...'),
                                                                    ]),
                                                                Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('name_en')
                                                                            ->label('Product Name')
                                                                            ->maxLength(255)
                                                                            ->placeholder('e.g. Premium Cotton Duvet Set'),
                                                                        Forms\Components\Textarea::make('short_description_en')
                                                                            ->label('Short Description')
                                                                            ->rows(2)
                                                                            ->maxLength(300),
                                                                        Forms\Components\RichEditor::make('description_en')
                                                                            ->label('Full Description')
                                                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                                                    ]),
                                                                Forms\Components\Tabs\Tab::make('🇸🇦 العربية')
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('name_ar')
                                                                            ->label('اسم المنتج')
                                                                            ->maxLength(255),
                                                                        Forms\Components\Textarea::make('short_description_ar')
                                                                            ->label('وصف قصير')
                                                                            ->rows(2)
                                                                            ->maxLength(300),
                                                                        Forms\Components\RichEditor::make('description_ar')
                                                                            ->label('الوصف الكامل')
                                                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                                                    ]),
                                                                Forms\Components\Tabs\Tab::make('🇷🇺 Русский')
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('name_ru')
                                                                            ->label('Название продукта')
                                                                            ->maxLength(255),
                                                                        Forms\Components\Textarea::make('short_description_ru')
                                                                            ->label('Краткое описание')
                                                                            ->rows(2)
                                                                            ->maxLength(300),
                                                                        Forms\Components\RichEditor::make('description_ru')
                                                                            ->label('Полное описание')
                                                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                                                    ]),
                                                                Forms\Components\Tabs\Tab::make('🇩🇪 Deutsch')
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('name_de')
                                                                            ->label('Produktname')
                                                                            ->maxLength(255),
                                                                        Forms\Components\Textarea::make('short_description_de')
                                                                            ->label('Kurzbeschreibung')
                                                                            ->rows(2)
                                                                            ->maxLength(300),
                                                                        Forms\Components\RichEditor::make('description_de')
                                                                            ->label('Vollständige Beschreibung')
                                                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                                                    ]),
                                                            ])
                                                            ->columnSpanFull(),
                                                    ]),
                                            ])->columnSpan(2),

                                        // Sağ panel (1/3)
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Section::make('Fiyat & Stok')
                                                    ->icon('heroicon-o-cube')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('price')
                                                            ->label('Birim Fiyat (USD)')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->placeholder('Boş = kategori fiyatı')
                                                            ->helperText(fn ($record) => $record?->category?->price
                                                                ? 'Kategori fiyatı: $' . number_format($record->category->price, 2)
                                                                : 'Kategori fiyatı tanımlı değil'
                                                            ),

                                                        Forms\Components\TextInput::make('stock')
                                                            ->label('Stok Adedi')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->prefixIcon('heroicon-o-cube')
                                                            ->suffix('adet'),

                                                        Forms\Components\TextInput::make('min_order')
                                                            ->label('Min. Sipariş')
                                                            ->numeric()
                                                            ->default(100)
                                                            ->minValue(1)
                                                            ->suffix('adet'),

                                                        Forms\Components\TextInput::make('production_time')
                                                            ->label('Üretim Süresi')
                                                            ->default('2-3 hafta')
                                                            ->placeholder('Örn: 2-3 hafta')
                                                            ->prefixIcon('heroicon-o-clock'),
                                                    ]),

                                                Forms\Components\Section::make('Durum')
                                                    ->icon('heroicon-o-adjustments-horizontal')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('is_active')
                                                            ->label('Yayında')
                                                            ->helperText('Kapalıyken ürün sitede görünmez')
                                                            ->default(true)
                                                            ->onColor('success')
                                                            ->offColor('danger')
                                                            ->onIcon('heroicon-m-check')
                                                            ->offIcon('heroicon-m-x-mark'),

                                                        Forms\Components\Toggle::make('is_featured')
                                                            ->label('Öne Çıkan')
                                                            ->helperText('Ana sayfada öne çıkan olarak gösterilir')
                                                            ->default(false)
                                                            ->onColor('warning')
                                                            ->onIcon('heroicon-m-star'),

                                                        Forms\Components\TextInput::make('order')
                                                            ->label('Sıralama')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->prefixIcon('heroicon-o-arrows-up-down')
                                                            ->helperText('Küçük sayı önce görünür'),
                                                    ]),
                                            ])->columnSpan(1),
                                    ]),
                            ]),

                        // TAB 2 - Görseller
                        Forms\Components\Tabs\Tab::make('Görseller')
                            ->icon('heroicon-o-photo')
                            ->badge(fn (?Product $record): ?string =>
                                $record?->images ? count($record->images) : null
                            )
                            ->schema([
                                Forms\Components\Section::make('Ürün Görselleri')
                                    ->description('Yüksek kaliteli ürün fotoğrafları yükleyin. İlk görsel kapak görseli olarak kullanılır.')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        Forms\Components\FileUpload::make('images')
                                            ->label('')
                                            ->image()
                                            ->disk('public')
                                            ->multiple()
                                            ->directory('products')
                                            ->visibility('public')
                                            ->maxSize(51200)
                                            ->maxFiles(20)
                                            ->reorderable()
                                            ->appendFiles()
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '1:1',
                                                '4:3',
                                                '3:4',
                                                '16:9',
                                            ])
                                            ->imagePreviewHeight(200)
                                            ->panelLayout('grid')
                                            ->openable()
                                            ->downloadable()
                                            ->helperText('Maks 20 görsel, her biri en fazla 50MB. Sürükleyerek sıralayabilirsiniz.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 3 - Özellikler & Etiketler
                        Forms\Components\Tabs\Tab::make('Özellikler')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Forms\Components\Section::make('Ürün Özellikleri')
                                    ->description('Ürünün öne çıkan özelliklerini ekleyin')
                                    ->icon('heroicon-o-list-bullet')
                                    ->schema([
                                        Forms\Components\Repeater::make('features')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\TextInput::make('icon')
                                                    ->label('İkon')
                                                    ->placeholder('heroicon-o-star'),

                                                Forms\Components\TextInput::make('title')
                                                    ->label('Başlık')
                                                    ->placeholder('Örn: %100 Pamuk')
                                                    ->required(),

                                                Forms\Components\Textarea::make('description')
                                                    ->label('Açıklama')
                                                    ->placeholder('Kısa açıklama...')
                                                    ->rows(2),
                                            ])
                                            ->columns(3)
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderable()
                                            ->defaultItems(0)
                                            ->addActionLabel('Özellik Ekle')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Etiketler')
                                    ->icon('heroicon-o-tag')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('tags')
                                            ->label('')
                                            ->relationship('tags', 'name')
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 4 - SEO
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Section::make('Arama Motoru Optimizasyonu')
                                    ->description('Google ve diğer arama motorlarında nasıl görüneceğini ayarlayın')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->schema([
                                        Forms\Components\Tabs::make('SEO Dilleri')
                                            ->tabs([
                                                Forms\Components\Tabs\Tab::make('🇹🇷 Türkçe')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('meta_title_tr')
                                                            ->label('Sayfa Başlığı')
                                                            ->maxLength(70)
                                                            ->helperText(fn (?string $state): string => ($state ? strlen($state) : 0) . '/70 karakter'),
                                                        Forms\Components\Textarea::make('meta_description_tr')
                                                            ->label('Sayfa Açıklaması')
                                                            ->maxLength(160)
                                                            ->rows(3)
                                                            ->helperText(fn (?string $state): string => ($state ? strlen($state) : 0) . '/160 karakter'),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('meta_title_en')
                                                            ->label('Page Title')
                                                            ->maxLength(70),
                                                        Forms\Components\Textarea::make('meta_description_en')
                                                            ->label('Page Description')
                                                            ->maxLength(160)
                                                            ->rows(3),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇸🇦 العربية')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('meta_title_ar')
                                                            ->label('عنوان الصفحة')
                                                            ->maxLength(70),
                                                        Forms\Components\Textarea::make('meta_description_ar')
                                                            ->label('وصف الصفحة')
                                                            ->maxLength(160)
                                                            ->rows(3),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇷🇺 Русский')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('meta_title_ru')
                                                            ->label('Заголовок страницы')
                                                            ->maxLength(70),
                                                        Forms\Components\Textarea::make('meta_description_ru')
                                                            ->label('Описание страницы')
                                                            ->maxLength(160)
                                                            ->rows(3),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇩🇪 Deutsch')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('meta_title_de')
                                                            ->label('Seitentitel')
                                                            ->maxLength(70),
                                                        Forms\Components\Textarea::make('meta_description_de')
                                                            ->label('Seitenbeschreibung')
                                                            ->maxLength(160)
                                                            ->rows(3),
                                                    ]),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->contained(false)
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(45)
                    ->stacked()
                    ->limit(1)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=U&background=8B7355&color=fff&size=45'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->description(fn (Product $record): string => $record->sku ? 'SKU: ' . $record->sku : ''),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->getStateUsing(fn ($record) => $record->price ?? $record->category?->price)
                    ->formatStateUsing(fn ($state, $record) => $state
                        ? ('$' . number_format((float)$state, 2) . ($record->price ? '' : ' *'))
                        : '—')
                    ->tooltip(fn ($record) => !$record->price && $record->category?->price
                        ? '* Kategori fiyatından alınıyor'
                        : null)
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->suffix(' adet'),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Yayında')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable()
                    ->alignCenter(),

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
                    ->preload()
                    ->placeholder('Tüm Kategoriler'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Yayında')
                    ->falseLabel('Taslak'),

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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->label('Düzenle'),
                    Tables\Actions\DeleteAction::make()->label('Sil'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Seçilenleri Sil'),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->emptyStateHeading('Henüz ürün eklenmemiş')
            ->emptyStateDescription('İlk ürününüzü ekleyerek kataloğunuzu oluşturun')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->striped();
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
