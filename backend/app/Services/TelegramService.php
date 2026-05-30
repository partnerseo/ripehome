<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
    }

    /**
     * Telegram'a mesaj gönder
     */
    public function sendMessage(string $message, array $options = [])
    {
        if (!$this->botToken || !$this->chatId) {
            Log::warning('Telegram bot token veya chat ID tanımlanmamış');
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            
            // Hangi siteden geldiği net olsun (iki site aynı bot/chat kullanıyor)
            $siteTag = "🟢 <b>RIPEHOME.COM.TR</b>\n\n";

            $data = [
                'chat_id' => $this->chatId,
                'text' => $siteTag . $message,
                'parse_mode' => $options['parse_mode'] ?? 'HTML',
                'disable_web_page_preview' => $options['disable_preview'] ?? false,
            ];

            $response = Http::post($url, $data);

            if ($response->successful()) {
                Log::info('Telegram mesajı başarıyla gönderildi');
                return true;
            } else {
                Log::error('Telegram mesaj gönderimi başarısız', [
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram mesaj hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Yeni toptan sipariş bildirimi gönder
     */
    public function sendNewOrderNotification($order)
    {
        // Sipariş ürünlerini formatla
        $items = collect($order->items)->map(function ($item) {
            return "• {$item['product_name']} - {$item['quantity']} adet";
        })->join("\n");

        // Toplam ürün sayısı
        $totalQuantity = collect($order->items)->sum('quantity');
        $itemCount = count($order->items);

        // Mesaj oluştur
        $message = "🔔 <b>YENİ TOPTAN SİPARİŞ!</b>\n\n";
        $message .= "🏢 <b>Şirket:</b> {$order->company_name}\n";
        $message .= "👤 <b>Yetkili:</b> {$order->contact_person}\n";
        $message .= "📧 <b>Email:</b> {$order->email}\n";
        $message .= "📱 <b>Telefon:</b> {$order->phone}\n";
        
        if ($order->city) {
            $message .= "📍 <b>Şehir:</b> {$order->city}\n";
        }
        
        if ($order->tax_number) {
            $message .= "🏦 <b>Vergi No:</b> {$order->tax_number}\n";
        }

        $message .= "\n🛍️ <b>Sipariş Detayları:</b>\n{$items}\n";
        $message .= "\n💰 <b>Toplam:</b> {$totalQuantity} ürün ({$itemCount} farklı ürün)\n";

        if ($order->additional_notes) {
            $message .= "\n📝 <b>Not:</b> {$order->additional_notes}\n";
        }

        $message .= "\n🔗 <a href='" . env('APP_URL') . "/admin/wholesale-orders/{$order->id}'>Detaylı İncele</a>";

        return $this->sendMessage($message);
    }

    /**
     * Test mesajı gönder
     */
    public function sendTestMessage()
    {
        $message = "✅ <b>Telegram Botu Aktif!</b>\n\n";
        $message .= "🎉 Ripe Home Sipariş Bildirim Sistemi başarıyla kuruldu.\n";
        $message .= "📱 Yeni siparişler bu kanala bildirilecektir.";

        return $this->sendMessage($message);
    }
}

