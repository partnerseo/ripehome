<?php

namespace App\Filament\Resources\WholesaleOrderResource\Pages;

use App\Filament\Resources\WholesaleOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWholesaleOrder extends EditRecord
{
    protected static string $resource = WholesaleOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}





