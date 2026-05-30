<?php

namespace App\Filament\Resources\HomeSliderResource\Pages;

use App\Filament\Resources\HomeSliderResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateHomeSlider extends CreateRecord
{
    use Translatable;

    protected static string $resource = HomeSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
