<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSliderResource\Pages;
use App\Models\HomeSlider;
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
                    ->maxLength(255),

                Forms\Components\TextInput::make('button_link')
                    ->label('Buton Linki')
                    ->url()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('image')
                    ->label('Slider Görseli')
                    ->image()
                    ->directory('sliders')
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
