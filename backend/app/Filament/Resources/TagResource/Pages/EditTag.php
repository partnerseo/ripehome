<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $raw = $record->getRawOriginal('name');
        $translations = $raw ? (json_decode($raw, true) ?? []) : [];
        foreach (['tr', 'en', 'ar', 'ru', 'de'] as $locale) {
            $data['name_' . $locale] = $translations[$locale] ?? '';
        }
        unset($data['name']);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
