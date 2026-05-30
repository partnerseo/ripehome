<?php

namespace App\Filament\Resources\FeaturedSectionResource\Pages;

use App\Filament\Resources\FeaturedSectionResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;

class ListFeaturedSections extends ListRecords
{
    protected static string $resource = FeaturedSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
