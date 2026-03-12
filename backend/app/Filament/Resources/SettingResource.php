<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use App\Models\Category;
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

                        Forms\Components\Tabs\Tab::make('Marka Hikayesi')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                Forms\Components\TextInput::make('brand_title')
                                    ->label('Başlık')
                                    ->placeholder('Doğallık, Kalite ve Şıklık Ev Tekstilinde Buluşuyor')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('brand_subtitle')
                                    ->label('Alt Başlık')
                                    ->placeholder('Pamuk, keten ve muslinin zamansız dokusu evinizde.')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('brand_description')
                                    ->label('Açıklama')
                                    ->placeholder('Her ürün, doğal liflerin benzersiz dokusunu...')
                                    ->rows(4)
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('brand_image')
                                    ->label('Arka Plan Görseli')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->maxSize(20480)
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['16:9', '4:3'])
                                    ->helperText('Geniş, yüksek çözünürlüklü bir görsel kullanın (1920x1080 önerilir)'),

                                Forms\Components\TextInput::make('brand_button_text')
                                    ->label('Buton Yazısı')
                                    ->placeholder('Marka Hikayemizi Keşfet')
                                    ->maxLength(100),

                                Forms\Components\Select::make('brand_button_link')
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
