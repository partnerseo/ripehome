<?php

namespace App\Filament\Resources\ScreeningTemplateResource\Pages;

use App\Filament\Resources\ScreeningTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScreeningTemplate extends EditRecord
{
    protected static string $resource = ScreeningTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
