<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\WeekContentResource\Pages;
use App\Models\WeekContent;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WeekContentResource extends Resource
{
    protected static ?string $model = WeekContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Hafta içerikleri';

    protected static ?string $modelLabel = 'hafta içeriği';

    protected static ?string $pluralModelLabel = 'hafta içerikleri';

    protected static ?int $navigationSort = 1;

    /** Kaç hafta hâlâ yayına hazır değil — panelde görünen ilk sayı bu olsun. */
    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::query()->where('status', '!=', WeekContent::STATUS_PUBLISHED)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Hafta')
                ->columns(3)
                ->schema([
                    TextInput::make('week')
                        ->label('Hafta')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(42)
                        ->required(),
                    Select::make('locale')
                        ->label('Dil')
                        ->options(['tr' => 'Türkçe', 'en' => 'English'])
                        ->default('tr')
                        ->required(),
                    TextInput::make('baby_size_label')
                        ->label('Boyut karşılaştırması')
                        ->placeholder('mısır koçanı'),
                ]),

            Section::make('İçerik')
                ->description('Metinler özgün yazılmalıdır. Başka bir kaynaktan kopyalanan cümle telif ihlalidir.')
                ->schema([
                    TextInput::make('baby_length_mm')->label('Boy (mm)')->numeric(),
                    TextInput::make('baby_weight_g')->label('Ağırlık (g)')->numeric(),
                    Textarea::make('baby_body')->label('Bebekte neler oluyor')->rows(4),
                    Textarea::make('mother_body')->label('Annede neler oluyor')->rows(4),
                    Textarea::make('tips_body')->label('Bu hafta ipuçları')->rows(4),
                ])
                ->columns(2),

            Section::make('Tıbbi gözden geçirme')
                ->description('Gözden geçiren kişi ve tarih girilmeden içerik yayına alınamaz.')
                ->schema([
                    Placeholder::make('uyari')
                        ->hiddenLabel()
                        ->content('Yayındaki bir metni değiştirirseniz önceki onay o metni kapsamaz; kayıt otomatik olarak taslağa döner ve yeniden gözden geçirilmesi gerekir.'),
                    Select::make('status')
                        ->label('Durum')
                        ->options([
                            WeekContent::STATUS_DRAFT => 'Taslak',
                            WeekContent::STATUS_IN_REVIEW => 'Gözden geçiriliyor',
                            WeekContent::STATUS_PUBLISHED => 'Yayında',
                        ])
                        ->default(WeekContent::STATUS_DRAFT)
                        ->live()
                        ->required(),
                    TextInput::make('reviewed_by')
                        ->label('Gözden geçiren')
                        ->placeholder('Dr. Ad Soyad, Kadın Hastalıkları ve Doğum')
                        // Model de aynı kuralı uygular; buradaki doğrulama
                        // kullanıcıya 500 yerine anlaşılır bir mesaj verir.
                        ->required(fn ($get): bool => $get('status') === WeekContent::STATUS_PUBLISHED),
                    DatePicker::make('reviewed_at')
                        ->label('Gözden geçirme tarihi')
                        ->required(fn ($get): bool => $get('status') === WeekContent::STATUS_PUBLISHED),
                    Textarea::make('review_note')->label('Gözden geçirme notu')->rows(2),
                    Repeater::make('source_refs')
                        ->label('Dayanak kaynaklar')
                        ->schema([
                            TextInput::make('label')->label('Kaynak')->required(),
                            TextInput::make('url')->label('Bağlantı')->url(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Kaynak ekle'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('week')
            ->columns([
                TextColumn::make('week')->label('Hafta')->sortable(),
                TextColumn::make('locale')->label('Dil')->badge(),
                TextColumn::make('baby_size_label')->label('Boyut')->limit(24),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        WeekContent::STATUS_PUBLISHED => 'Yayında',
                        WeekContent::STATUS_IN_REVIEW => 'Gözden geçiriliyor',
                        default => 'Taslak',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        WeekContent::STATUS_PUBLISHED => 'success',
                        WeekContent::STATUS_IN_REVIEW => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('reviewed_by')->label('Gözden geçiren')->placeholder('—')->limit(30),
                TextColumn::make('reviewed_at')->label('Onay tarihi')->date('d.m.Y')->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Durum')->options([
                    WeekContent::STATUS_DRAFT => 'Taslak',
                    WeekContent::STATUS_IN_REVIEW => 'Gözden geçiriliyor',
                    WeekContent::STATUS_PUBLISHED => 'Yayında',
                ]),
                SelectFilter::make('locale')->label('Dil')->options(['tr' => 'Türkçe', 'en' => 'English']),
            ])
            ->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeekContents::route('/'),
            'create' => Pages\CreateWeekContent::route('/create'),
            'edit' => Pages\EditWeekContent::route('/{record}/edit'),
        ];
    }
}
