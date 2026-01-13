<?php

namespace App\Filament\Resources\FeaturedSectionResource\Pages;

use App\Filament\Resources\FeaturedSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeaturedSection extends EditRecord
{
    protected static string $resource = FeaturedSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
