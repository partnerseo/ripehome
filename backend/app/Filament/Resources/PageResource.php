<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Sayfalar';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Sayfa';

    protected static ?string $pluralModelLabel = 'Sayfalar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sayfa Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->helperText('Türkçe başlıktan otomatik oluşturulur'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('İçerik (Diller)')
                    ->icon('heroicon-o-language')
                    ->description('Her dil için sayfa başlığı ve içeriğini girin')
                    ->schema([
                        Forms\Components\Tabs::make('İçerik Dilleri')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('🇹🇷 Türkçe')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_tr')
                                            ->label('Sayfa Başlığı')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                            ),
                                        Forms\Components\RichEditor::make('content_tr')
                                            ->label('İçerik')
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_en')
                                            ->label('Page Title')
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make('content_en')
                                            ->label('Content')
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇸🇦 العربية')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ar')
                                            ->label('عنوان الصفحة')
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make('content_ar')
                                            ->label('المحتوى')
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇷🇺 Русский')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ru')
                                            ->label('Заголовок страницы')
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make('content_ru')
                                            ->label('Содержание')
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇩🇪 Deutsch')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_de')
                                            ->label('Seitentitel')
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make('content_de')
                                            ->label('Inhalt')
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'h2', 'h3', 'link', 'blockquote', 'undo', 'redo']),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\Tabs::make('SEO Dilleri')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('🇹🇷 Türkçe')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title_tr')->label('Meta Başlık')->maxLength(255),
                                        Forms\Components\Textarea::make('meta_description_tr')->label('Meta Açıklama')->rows(3),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title_en')->label('Meta Title')->maxLength(255),
                                        Forms\Components\Textarea::make('meta_description_en')->label('Meta Description')->rows(3),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇸🇦 العربية')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title_ar')->label('عنوان ميتا')->maxLength(255),
                                        Forms\Components\Textarea::make('meta_description_ar')->label('وصف ميتا')->rows(3),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇷🇺 Русский')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title_ru')->label('Мета-заголовок')->maxLength(255),
                                        Forms\Components\Textarea::make('meta_description_ru')->label('Мета-описание')->rows(3),
                                    ]),
                                Forms\Components\Tabs\Tab::make('🇩🇪 Deutsch')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title_de')->label('Meta-Titel')->maxLength(255),
                                        Forms\Components\Textarea::make('meta_description_de')->label('Meta-Beschreibung')->rows(3),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

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
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
