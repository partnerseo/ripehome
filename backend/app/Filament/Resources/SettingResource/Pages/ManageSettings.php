<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Pages\ManageRecords\Concerns\Translatable;

class ManageSettings extends ManageRecords
{
    use Translatable;

    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
