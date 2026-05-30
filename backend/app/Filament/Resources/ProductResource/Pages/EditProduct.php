<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
            $raw = $record->getRawOriginal($field);
            $translations = $raw ? (json_decode($raw, true) ?? []) : [];
            foreach (['tr', 'en', 'ar', 'ru', 'de'] as $locale) {
                $data[$field . '_' . $locale] = $translations[$locale] ?? '';
            }
            unset($data[$field]);
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
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
