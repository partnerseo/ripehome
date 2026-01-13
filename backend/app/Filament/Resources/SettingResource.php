<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static ?string $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 100;

    protected static ?string $modelLabel = 'Ayarlar';

    protected static ?string $pluralModelLabel = 'Site Ayarları';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Ayarlar')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Genel')
                            ->schema([
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('settings'),

                                Forms\Components\FileUpload::make('favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->directory('settings'),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefon')
                                    ->tel()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('E-posta')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('address')
                                    ->label('Adres')
                                    ->rows(3),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Sosyal Medya')
                            ->schema([
                                Forms\Components\TextInput::make('facebook')
                                    ->label('Facebook')
                                    ->url()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('twitter')
                                    ->label('Twitter (X)')
                                    ->url()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('linkedin')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Footer')
                            ->schema([
                                Forms\Components\Textarea::make('footer_text')
                                    ->label('Footer Metni')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSettings::route('/'),
        ];
    }
}
