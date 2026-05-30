<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        foreach (['name', 'description'] as $field) {
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
