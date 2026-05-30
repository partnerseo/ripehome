<?php

namespace App\Filament\Resources\FeaturedProductResource\Pages;

use App\Filament\Resources\FeaturedProductResource;
use Filament\Actions;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateFeaturedProduct extends CreateRecord
{
    use Translatable;

    protected static string $resource = FeaturedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
