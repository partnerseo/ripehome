<?php

namespace App\Filament\Resources\WholesaleOrderResource\Pages;

use App\Filament\Resources\WholesaleOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWholesaleOrders extends ListRecords
{
    protected static string $resource = WholesaleOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Create butonu yok çünkü API'den oluşturulacak
        ];
    }
}





