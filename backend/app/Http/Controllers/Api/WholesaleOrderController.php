<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WholesaleOrder;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WholesaleOrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:50',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'tax_office' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:50',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string',
                'additional_notes' => 'nullable|string',
            ], [
                'company_name.required' => 'Şirket adı zorunludur',
                'contact_person.required' => 'Yetkili kişi adı zorunludur',
                'email.required' => 'E-posta zorunludur',
                'email.email' => 'Geçerli bir e-posta giriniz',
                'phone.required' => 'Telefon numarası zorunludur',
                'items.required' => 'En az bir ürün seçmelisiniz',
                'items.min' => 'En az bir ürün seçmelisiniz',
                'items.*.product_id.required' => 'Ürün seçimi zorunludur',
                'items.*.product_id.exists' => 'Seçilen ürün bulunamadı',
                'items.*.quantity.required' => 'Adet bilgisi zorunludur',
                'items.*.quantity.min' => 'Adet en az 1 olmalıdır',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen tüm zorunlu alanları doldurunuz',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ürün bilgilerini zenginleştir
            $enrichedItems = collect($request->items)->map(function ($item) {
                $product = Product::find($item['product_id']);
                
                // İlk görseli al
                $productImage = null;
                if ($product->images) {
                    if (is_array($product->images)) {
                        $productImage = $product->images[0] ?? null;
                    } else {
                        $images = json_decode($product->images, true);
                        $productImage = $images[0] ?? null;
                    }
                }

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_image' => $productImage,
                    'quantity' => (int) $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ];
            })->toArray();

            $order = WholesaleOrder::create([
                'company_name' => $request->company_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address ?? null,
                'city' => $request->city ?? null,
                'tax_office' => $request->tax_office ?? null,
                'tax_number' => $request->tax_number ?? null,
                'items' => $enrichedItems,
                'additional_notes' => $request->additional_notes ?? null,
                'status' => 'pending',
            ]);

            // Telegram bildirimi gönder
            try {
                $telegramService = new TelegramService();
                $telegramService->sendNewOrderNotification($order);
            } catch (\Exception $e) {
                Log::warning('Telegram bildirimi gönderilemedi: ' . $e->getMessage());
                // Bildirim hatası sipariş oluşumunu engellemez
            }

            // Email bildirimi gönderilebilir (opsiyonel)
            // Mail::to('admin@example.com')->send(new NewWholesaleOrder($order));

            return response()->json([
                'success' => true,
                'message' => 'Toptan sipariş talebiniz başarıyla alındı. En kısa sürede size dönüş yapacağız.',
                'data' => [
                    'order_id' => $order->id,
                    'company_name' => $order->company_name,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Wholesale order error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Sipariş oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.',
            ], 500);
        }
    }
}





