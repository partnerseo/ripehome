<?php

namespace App\Filament\Resources\FeaturedProductResource\Pages;

use App\Filament\Resources\FeaturedProductResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditFeaturedProduct extends EditRecord
{
    use Translatable;

    protected static string $resource = FeaturedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
