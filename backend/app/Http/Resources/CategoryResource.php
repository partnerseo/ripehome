<?php

namespace App\Http\Resources;

use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    private function localizedSlug(): string
    {
        $loc = app()->getLocale();
        if ($loc !== 'tr' && !empty($this->{'slug_' . $loc})) {
            return $this->{'slug_' . $loc};
        }
        return $this->slug;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->localizedSlug(),
            'description' => $this->description,
            'image' => ImageHelper::getStorageUrl($this->image),
            'order' => $this->order,
            'products_count' => $this->products_count ?? 0,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}






