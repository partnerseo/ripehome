<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\FeaturedSection;
use Illuminate\Support\Facades\Log;

class FeaturedSectionController extends Controller
{
    public function index()
    {
        try {
            $sections = FeaturedSection::where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'title' => $section->title,
                        'description' => $section->description,
                        'image' => ImageHelper::getStorageUrl($section->image),
                        'icon' => $section->icon,
                        'link' => $section->link,
                        'order' => $section->order,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sections
            ]);
        } catch (\Exception $e) {
            Log::error('Featured section fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Öne çıkan bölümler yüklenirken hata oluştu'
            ], 500);
        }
    }
}












