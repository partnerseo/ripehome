<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        foreach (['title', 'content', 'meta_title', 'meta_description'] as $field) {
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
