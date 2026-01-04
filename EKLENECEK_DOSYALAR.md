# 📁 Eklemeniz Gereken Dosyalar

## ✅ SADECE 2 İŞLEM!

Aslında backend'de **sadece 1 yeni dosya ekleyip, 1 düzenleme** yapacaksınız!

---

## 📄 1. TelegramService.php (YENİ DOSYA)

### Konum:
```
api/app/Services/TelegramService.php
```

### cPanel'de Nasıl Yapılır:

1. **File Manager → api/app/** klasörüne git
2. **"Services"** klasörü var mı kontrol et
   - **Yoksa:** "+ Folder" ile **Services** klasörü oluştur
3. **Services/** klasörüne gir
4. **"+ File"** → İsim: **TelegramService.php**
5. Dosyaya sağ tık → **Edit**
6. Aşağıdaki kodu **KOPYALA-YAPIŞTIR:**

```php
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
            
            $data = [
                'chat_id' => $this->chatId,
                'text' => $message,
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
```

7. **Save Changes**

✅ **TAMAM!** TelegramService.php eklendi!

---

## 📄 2. WholesaleOrderController.php Kontrolü

### Konum:
```
api/app/Http/Controllers/Api/WholesaleOrderController.php
```

### Kontrol Edin:

1. **File Manager → api/app/Http/Controllers/Api/** klasörüne git
2. **WholesaleOrderController.php** dosyasını aç
3. **Satır 8** civarında şunu arayın:

```php
use App\Services\TelegramService;
```

4. **Satır 93-100** civarında şunu arayın:

```php
// Telegram bildirimi gönder
try {
    $telegramService = new TelegramService();
    $telegramService->sendNewOrderNotification($order);
} catch (\Exception $e) {
    Log::warning('Telegram bildirimi gönderilemedi: ' . $e->getMessage());
}
```

### Varsa:
✅ **Mükemmel!** Hiçbir şey yapmanıza gerek yok!

### Yoksa:
**Satır 91'den sonra** (sipariş oluşturulduktan sonra) şu kodu ekleyin:

```php
// Telegram bildirimi gönder
try {
    $telegramService = new TelegramService();
    $telegramService->sendNewOrderNotification($order);
} catch (\Exception $e) {
    Log::warning('Telegram bildirimi gönderilemedi: ' . $e->getMessage());
}
```

---

## ⚙️ 3. .env Dosyası Güncellemesi

### Konum:
```
api/.env
```

### Nasıl Yapılır:

1. **File Manager → api/** klasörüne git
2. **.env** dosyasını bul ve **Edit** ile aç
3. **En alta** şu satırları ekle:

```env
# Telegram Bot Bildirimleri
TELEGRAM_BOT_TOKEN=8099911715:AAGhw02TJkpF843tNd1w7v9w01i9433gF-U
TELEGRAM_CHAT_ID=8363052797
```

4. **Save Changes**

✅ **TAMAM!** .env güncellendi!

---

## 🎉 Tamamlandı!

Artık yeni toptan sipariş geldiğinde Telegram'a bildirim gidecek!

### Test İçin:

1. Siteye gidin: `https://ripehome.com.tr/toptan-siparis`
2. Formu doldurun
3. Gönder
4. Telegram'a bildirim gelmeli! 🎉

---

## 🔧 Sorun Giderme

### Telegram bildirimi gelmiyor?

**Kontrol:**
1. `.env` dosyasında `TELEGRAM_BOT_TOKEN` ve `TELEGRAM_CHAT_ID` doğru mu?
2. `TelegramService.php` dosyası `api/app/Services/` altında mı?
3. `WholesaleOrderController.php` içinde Telegram kodu var mı?

**Log Kontrol:**
- File Manager → `api/storage/logs/laravel.log`
- En altta hata var mı?

### "Class 'App\Services\TelegramService' not found" hatası

**Çözüm:** Cache temizleyin
- SSH varsa: `php artisan config:clear`
- SSH yoksa: Bir sonraki sipariş denemesinde otomatik düzelir

---

**İyi satışlar! 🚀**

