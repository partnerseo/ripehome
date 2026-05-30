<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->get('locale', 'tr');
        $items = Testimonial::where('is_active', true)
            ->orderBy('order_index')
            ->get()
            ->map(fn($t) => [
                'id'             => $t->id,
                'customer_name'  => $t->contact_person,
                'company'        => $t->company_name,
                'position'       => $t->position,
                'rating'         => $t->rating,
                'comment'        => $locale === 'tr' ? $t->content
                                 : ($t->{'content_' . $locale} ?: $t->content),
                'avatar'         => $t->avatar,
                'company_logo'   => $t->company_logo,
            ]);

        return response()->json($items);
    }
}
