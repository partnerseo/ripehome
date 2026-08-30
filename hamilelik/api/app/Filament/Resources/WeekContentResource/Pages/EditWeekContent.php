<?php

namespace App\Filament\Resources\WeekContentResource\Pages;

use App\Filament\Resources\WeekContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeekContent extends EditRecord
{
    protected static string $resource = WeekContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
