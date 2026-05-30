<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach (['title', 'content', 'meta_title', 'meta_description'] as $field) {
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
