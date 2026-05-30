<?php

namespace App\Filament\Resources\FeaturedSectionResource\Pages;

use App\Filament\Resources\FeaturedSectionResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateFeaturedSection extends CreateRecord
{
    use Translatable;

    protected static string $resource = FeaturedSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
