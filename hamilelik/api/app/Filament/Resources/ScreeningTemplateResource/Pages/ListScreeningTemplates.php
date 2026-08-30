<?php

namespace App\Filament\Resources\ScreeningTemplateResource\Pages;

use App\Filament\Resources\ScreeningTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScreeningTemplates extends ListRecords
{
    protected static string $resource = ScreeningTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
