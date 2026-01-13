<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function show($slug)
    {
        try {
            $page = Page::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new PageResource($page)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sayfa bulunamadı'
            ], 404);
        }
    }
}













