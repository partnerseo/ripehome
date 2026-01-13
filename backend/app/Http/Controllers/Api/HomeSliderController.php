<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use Illuminate\Support\Facades\Log;

class HomeSliderController extends Controller
{
    public function index()
    {
        try {
            $sliders = HomeSlider::where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($slider) {
                    return [
                        'id' => $slider->id,
                        'title' => $slider->title,
                        'subtitle' => $slider->subtitle,
                        'button_text' => $slider->button_text,
                        'button_link' => $slider->button_link,
                        'image' => ImageHelper::getStorageUrl($slider->image),
                        'order' => $slider->order,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sliders
            ]);
        } catch (\Exception $e) {
            Log::error('Home slider fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Slider görselleri yüklenirken hata oluştu'
            ], 500);
        }
    }
}












