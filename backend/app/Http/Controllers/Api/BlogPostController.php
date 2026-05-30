<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    private function translate(BlogPost $post, string $field, string $locale): string
    {
        $localized = $locale !== 'tr' ? ($post->{$field . '_' . $locale} ?? null) : null;
        return $localized ?: ($post->{$field} ?? '');
    }

    public function index(Request $request)
    {
        $locale = $request->get('locale', 'tr');
        $posts = BlogPost::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'title'        => $this->translate($p, 'title', $locale),
                'slug'         => $this->translate($p, 'slug', $locale),
                'excerpt'      => $this->translate($p, 'excerpt', $locale),
                'cover_image'  => $p->image,
                'published_at' => $p->published_at?->toDateString(),
            ]);

        return response()->json($posts);
    }

    public function show(Request $request, string $slug)
    {
        $locale = $request->get('locale', 'tr');
        $post = BlogPost::slug($slug)->where('is_published', true)->firstOrFail();

        return response()->json([
            'id'           => $post->id,
            'title'        => $this->translate($post, 'title', $locale),
            'slug'         => $this->translate($post, 'slug', $locale),
            'content'      => $this->translate($post, 'content', $locale),
            'excerpt'      => $this->translate($post, 'excerpt', $locale),
            'cover_image'  => $post->image,
            'published_at' => $post->published_at?->toDateString(),
        ]);
    }
}
