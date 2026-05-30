<?php

namespace App\Filament\Resources\HomeSliderResource\Pages;

use App\Filament\Resources\HomeSliderResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditHomeSlider extends EditRecord
{
    use Translatable;

    protected static string $resource = HomeSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
