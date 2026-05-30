<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $translations = [];
        foreach (['tr', 'en', 'ar', 'ru', 'de'] as $locale) {
            $key = 'name_' . $locale;
            if (!empty($data[$key])) {
                $translations[$locale] = $data[$key];
            }
            unset($data[$key]);
        }
        $data['name'] = $translations ?: null;
        return $data;
    }
}
