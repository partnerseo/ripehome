<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ScreeningTemplateResource\Pages;
use App\Models\ScreeningTemplate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScreeningTemplateResource extends Resource
{
    protected static ?string $model = ScreeningTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Tetkik takvimi';

    protected static ?string $modelLabel = 'tetkik';

    protected static ?string $pluralModelLabel = 'tetkikler';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::query()->where('status', '!=', ScreeningTemplate::STATUS_PUBLISHED)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Tetkik')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kod')
                        ->helperText('Kalıcı kimlik; randevu üretimi buna bağlanır, sonradan değiştirmeyin.')
                        ->required(),
                    TextInput::make('name')->label('Ad')->required(),
                    Select::make('category')->label('Kategori')->options([
                        'usg' => 'Ultrason',
                        'lab' => 'Laboratuvar',
                        'vaccine' => 'Aşı / immünglobulin',
                        'visit' => 'Muayene',
                    ])->required(),
                    Toggle::make('is_optional')->label('Tercihe bağlı'),
                    Textarea::make('description')->label('Açıklama')->rows(3)->columnSpanFull(),
                ]),

            Section::make('Zaman aralığı')
                ->description('Yanlış bir hafta, kaçırılmış bir tarama demektir.')
                ->columns(3)
                ->schema([
                    TextInput::make('week_start')->label('Başlangıç haftası')->numeric()->minValue(1)->maxValue(42)->required(),
                    TextInput::make('week_end')->label('Bitiş haftası')->numeric()->minValue(1)->maxValue(42)->required()
                        ->rule('gte:week_start'),
                    TextInput::make('sort')->label('Sıra')->numeric()->default(0),
                ]),

            Section::make('Kapsam')
                ->columns(2)
                ->schema([
                    Select::make('country')->label('Ülke')->options(['TR' => 'Türkiye'])->default('TR')->required(),
                    Select::make('locale')->label('Dil')->options(['tr' => 'Türkçe', 'en' => 'English'])->default('tr')->required(),
                ]),

            Section::make('Tıbbi gözden geçirme')
                ->description('Gözden geçiren kişi ve tarih girilmeden tetkik yayına alınamaz.')
                ->columns(2)
                ->schema([
                    Placeholder::make('uyari')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->content('Yayındaki bir tetkiğin adı, haftası veya açıklaması değiştirilirse önceki onay geçersiz sayılır ve kayıt taslağa döner.'),
                    Select::make('status')->label('Durum')->options([
                        ScreeningTemplate::STATUS_DRAFT => 'Taslak',
                        ScreeningTemplate::STATUS_IN_REVIEW => 'Gözden geçiriliyor',
                        ScreeningTemplate::STATUS_PUBLISHED => 'Yayında',
                    ])->default(ScreeningTemplate::STATUS_DRAFT)->live()->required(),
                    TextInput::make('reviewed_by')->label('Gözden geçiren')
                        ->placeholder('Dr. Ad Soyad, Kadın Hastalıkları ve Doğum')
                        ->required(fn ($get): bool => $get('status') === ScreeningTemplate::STATUS_PUBLISHED),
                    DatePicker::make('reviewed_at')->label('Gözden geçirme tarihi')
                        ->required(fn ($get): bool => $get('status') === ScreeningTemplate::STATUS_PUBLISHED),
                    Textarea::make('review_note')->label('Gözden geçirme notu')->rows(2),
                    Repeater::make('source_refs')
                        ->label('Dayanak kaynaklar')
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('label')->label('Kaynak')->required(),
                            TextInput::make('url')->label('Bağlantı')->url(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Kaynak ekle'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label('Tetkik')->searchable()->limit(36),
                TextColumn::make('category')->label('Kategori')->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'usg' => 'Ultrason',
                        'lab' => 'Laboratuvar',
                        'vaccine' => 'Aşı',
                        default => 'Muayene',
                    }),
                TextColumn::make('week_start')->label('Hafta')
                    ->formatStateUsing(fn ($state, ScreeningTemplate $record): string => $record->week_start === $record->week_end
                        ? "{$record->week_start}"
                        : "{$record->week_start}–{$record->week_end}"),
                IconColumn::make('is_optional')->label('Tercihe bağlı')->boolean(),
                TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        ScreeningTemplate::STATUS_PUBLISHED => 'Yayında',
                        ScreeningTemplate::STATUS_IN_REVIEW => 'Gözden geçiriliyor',
                        default => 'Taslak',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        ScreeningTemplate::STATUS_PUBLISHED => 'success',
                        ScreeningTemplate::STATUS_IN_REVIEW => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('reviewed_by')->label('Gözden geçiren')->placeholder('—')->limit(28),
            ])
            ->filters([
                SelectFilter::make('status')->label('Durum')->options([
                    ScreeningTemplate::STATUS_DRAFT => 'Taslak',
                    ScreeningTemplate::STATUS_IN_REVIEW => 'Gözden geçiriliyor',
                    ScreeningTemplate::STATUS_PUBLISHED => 'Yayında',
                ]),
            ])
            ->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScreeningTemplates::route('/'),
            'create' => Pages\CreateScreeningTemplate::route('/create'),
            'edit' => Pages\EditScreeningTemplate::route('/{record}/edit'),
        ];
    }
}
