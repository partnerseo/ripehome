<?php

namespace App\Filament\Resources\HomeSliderResource\Pages;

use App\Filament\Resources\HomeSliderResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;

class ListHomeSliders extends ListRecords
{
    protected static string $resource = HomeSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
