<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSliderResource\Pages;
use App\Models\HomeSlider;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeSliderResource extends Resource
{
    protected static ?string $model = HomeSlider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Anasayfa Slider';

    protected static ?string $navigationGroup = 'Anasayfa';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Slider';

    protected static ?string $pluralModelLabel = 'Slider Görselleri';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('subtitle')
                    ->label('Alt Başlık')
                    ->maxLength(255),

                Forms\Components\TextInput::make('button_text')
                    ->label('Buton Metni')
                    ->placeholder('Örn: Keşfet, İncele, Sipariş Ver')
                    ->maxLength(255),

                Forms\Components\Select::make('button_link')
                    ->label('Buton Linki')
                    ->options(function () {
                        $pages = [
                            '/' => 'Ana Sayfa',
                            '/hakkimizda' => 'Hakkımızda',
                            '/iletisim' => 'İletişim',
                            '/toptan-siparis' => 'Toptan Sipariş',
                            '/sss' => 'Sıkça Sorulan Sorular',
                            '/kargo-teslimat' => 'Kargo & Teslimat',
                            '/iade-degisim' => 'İade & Değişim',
                        ];

                        $categories = Category::orderBy('name')
                            ->pluck('name', 'slug')
                            ->mapWithKeys(fn ($name, $slug) => ["/kategori/{$slug}" => $name])
                            ->toArray();

                        return [
                            'Site Sayfaları' => $pages,
                            'Kategoriler' => $categories,
                        ];
                    })
                    ->searchable()
                    ->placeholder('Sayfa veya kategori seçin')
                    ->helperText('Butonun tıklandığında gideceği sayfayı seçin'),

                Forms\Components\FileUpload::make('image')
                    ->label('Slider Görseli')
                    ->image()
                    ->disk('public')
                    ->directory('sliders')
                    ->visibility('public')
                    ->maxSize(20480)
                    ->imageEditor()
                    ->imageEditorAspectRatios(['16:9', '4:3', '21:9'])
                    ->required()
                    ->columnSpanFull(),

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
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel')
                    ->size(100),

                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
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
            'index' => Pages\ListHomeSliders::route('/'),
            'create' => Pages\CreateHomeSlider::route('/create'),
            'edit' => Pages\EditHomeSlider::route('/{record}/edit'),
        ];
    }
}
