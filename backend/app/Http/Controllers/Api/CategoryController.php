<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('order')
                ->get();

            // Debug için log
            Log::info('Categories API called', [
                'count' => $categories->count(),
                'first_three' => $categories->take(3)->map(function($cat) {
                    return [
                        'name' => $cat->name,
                        'products_count' => $cat->products_count
                    ];
                })
            ]);

            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories)
            ]);
        } catch (\Exception $e) {
            Log::error('Category fetch error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Kategoriler yüklenemedi'
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            $category = Category::where('slug', $slug)
                ->where('is_active', true)
                ->withCount('products')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı'
            ], 404);
        }
    }
}


