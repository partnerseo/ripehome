<?php

namespace App\Filament\Resources\FeaturedSectionResource\Pages;

use App\Filament\Resources\FeaturedSectionResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditFeaturedSection extends EditRecord
{
    use Translatable;

    protected static string $resource = FeaturedSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
