<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $products = Product::with(['category'])
                ->where('is_active', true)
                ->orderBy('order')
                ->paginate(12);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Product fetch error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Ürünler yüklenemedi'
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            $product = Product::with(['category'])
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı'
            ], 404);
        }
    }

    public function byCategory($categorySlug)
    {
        try {
            // Kategori bilgisini al
            $category = \App\Models\Category::where('slug', $categorySlug)
                ->where('is_active', true)
                ->firstOrFail();

            // Per page parametresi (default 100, max 1000)
            $perPage = min((int) request()->get('per_page', 100), 1000);

            $products = Product::with(['category'])
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            Log::info("Category products fetched: {$category->name}", [
                'slug' => $categorySlug,
                'total' => $products->total(),
                'per_page' => $perPage,
                'current_page' => $products->currentPage()
            ]);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => ImageHelper::getStorageUrl($category->image),
                ],
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Product by category error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Ürünler yüklenirken hata oluştu',
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}


