<?php

namespace App\Http\Resources;

use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Features'ı güvenli şekilde parse et
        $features = $this->features;
        if (is_string($features)) {
            $features = json_decode($features, true) ?? [];
        } elseif (!is_array($features)) {
            $features = [];
        }

        // Images'ı güvenli şekilde parse et
        $images = $this->images;
        if (is_string($images)) {
            $images = json_decode($images, true) ?? [];
        } elseif (!is_array($images)) {
            $images = [];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'images' => !empty($images) ? ImageHelper::getStorageUrls($images) : [],
            'category' => new CategoryResource($this->whenLoaded('category')),
            'features' => $features,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'is_featured' => $this->is_featured,
            'order' => $this->order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}


