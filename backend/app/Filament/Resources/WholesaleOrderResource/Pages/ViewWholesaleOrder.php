<?php

namespace App\Filament\Resources\WholesaleOrderResource\Pages;

use App\Filament\Resources\WholesaleOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWholesaleOrder extends ViewRecord
{
    protected static string $resource = WholesaleOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}





