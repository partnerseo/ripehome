<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach (['name', 'description'] as $field) {
            $translations = [];
            foreach (['tr', 'en', 'ar', 'ru', 'de'] as $locale) {
                $key = $field . '_' . $locale;
                if (!empty($data[$key])) {
                    $translations[$locale] = $data[$key];
                }
                unset($data[$key]);
            }
            $data[$field] = $translations ?: null;
        }
        return $data;
    }
}
