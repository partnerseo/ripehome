<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\FeaturedProduct;
use Illuminate\Support\Facades\Log;

class FeaturedProductController extends Controller
{
    public function index()
    {
        try {
            $products = FeaturedProduct::where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'category_label' => $product->category_label,
                        'title' => $product->title,
                        'description' => $product->description,
                        'image' => ImageHelper::getStorageUrl($product->image),
                        'tags' => $product->tags,
                        'button_text' => $product->button_text,
                        'button_link' => $product->button_link,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Featured products error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Öne çıkan ürünler yüklenirken hata oluştu'
            ], 500);
        }
    }
}











