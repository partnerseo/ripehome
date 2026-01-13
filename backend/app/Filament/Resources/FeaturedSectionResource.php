<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeaturedSectionResource\Pages;
use App\Models\FeaturedSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeaturedSectionResource extends Resource
{
    protected static ?string $model = FeaturedSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Öne Çıkan Bölümler';

    protected static ?string $navigationGroup = 'Anasayfa';

    protected static ?int $navigationSort = 11;

    protected static ?string $modelLabel = 'Öne Çıkan Bölüm';

    protected static ?string $pluralModelLabel = 'Öne Çıkan Bölümler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Açıklama')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('image')
                    ->label('Görsel')
                    ->image()
                    ->directory('featured-sections'),

                Forms\Components\TextInput::make('icon')
                    ->label('İkon (Heroicon adı)')
                    ->placeholder('heroicon-o-star')
                    ->maxLength(255),

                Forms\Components\TextInput::make('link')
                    ->label('Link')
                    ->url()
                    ->maxLength(255),

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
                    ->size(60),

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
            'index' => Pages\ListFeaturedSections::route('/'),
            'create' => Pages\CreateFeaturedSection::route('/create'),
            'edit' => Pages\EditFeaturedSection::route('/{record}/edit'),
        ];
    }
}
