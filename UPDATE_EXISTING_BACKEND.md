# 🔄 Mevcut Backend'i Güncelleme Rehberi

## ✅ Senaryo: Backend zaten kurulu

Eğer backend zaten çalışıyorsa, **sadece güncellemeler yapacağız**!

---

## 🚀 Çok Basit 3 Adım (10 dakika)

### ✅ ADIM 1: Frontend Güncelle (5 dakika)

1. **cPanel → File Manager → public_html/**
2. **Eski dosyaları yedekle** (opsiyonel):
   - `index.html` → `index.html.old`
   - `assets/` → `assets_old/`

3. **Yeni dist/ dosyalarını yükle:**
   - `dist/index.html` → `public_html/index.html`
   - `dist/assets/` → `public_html/assets/`
   - `dist/yikamatalimati.pdf` → `public_html/yikamatalimati.pdf`

✅ **Sonuç:** Frontend güncellendi!

---

### ✅ ADIM 2: Backend'e Yeni Dosyalar Ekle (3 dakika)

Backend'iniz nerede? (Örnek: `public_html/api/`)

#### A. TelegramService.php Ekle

1. **File Manager → api/app/Services/** klasörüne git
   - Eğer `Services` klasörü yoksa oluştur
2. **+ File** → İsim: `TelegramService.php`
3. **Edit** ile aç

**İçeriği:**
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $chatId;
    protected $apiUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function sendMessage(string $message): bool
    {
        if (!$this->token || !$this->chatId) {
            Log::warning('Telegram bot token or chat ID is not set.');
            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                Log::info('Telegram message sent successfully.');
                return true;
            } else {
                Log::error('Failed to send Telegram message: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending Telegram message: ' . $e->getMessage());
            return false;
        }
    }

    public function sendNewOrderNotification(array $orderData): bool
    {
        $itemsText = collect($orderData['items'])->map(function ($item) {
            return "• {$item['product_name']} - {$item['quantity']} adet";
        })->implode("\n");

        $totalItems = collect($orderData['items'])->sum('quantity');
        $uniqueProducts = collect($orderData['items'])->count();

        $message = "<b>🔔 YENİ TOPTAN SİPARİŞ!</b>\n\n" .
                   "🏢 <b>Şirket:</b> " . ($orderData['company_name'] ?? 'N/A') . "\n" .
                   "👤 <b>Yetkili:</b> " . ($orderData['contact_person'] ?? 'N/A') . "\n" .
                   "📧 <b>Email:</b> " . ($orderData['email'] ?? 'N/A') . "\n" .
                   "📱 <b>Telefon:</b> " . ($orderData['phone'] ?? 'N/A') . "\n" .
                   "📍 <b>Şehir:</b> " . ($orderData['city'] ?? 'N/A') . "\n\n" .
                   "🛍️ <b>Sipariş Detayları:</b>\n" . $itemsText . "\n\n" .
                   "💰 <b>Toplam:</b> {$totalItems} ürün ({$uniqueProducts} farklı ürün)\n\n" .
                   "📝 <b>Not:</b> " . ($orderData['additional_notes'] ?? 'Yok');

        return $this->sendMessage($message);
    }
}
```

4. **Save Changes**

#### B. WholesaleOrderController.php Güncelle

1. **File Manager → api/app/Http/Controllers/Api/WholesaleOrderController.php**
2. **Edit** ile aç
3. `store` metodunu bul (yaklaşık satır 20-50 arası)
4. Sipariş oluşturulduktan sonra şu satırları ekle:

```php
// Telegram bildirimi gönder
$telegramService = new \App\Services\TelegramService();
$telegramService->sendNewOrderNotification([
    'order_id' => $order->id,
    'company_name' => $order->company_name,
    'contact_person' => $order->contact_person,
    'email' => $order->email,
    'phone' => $order->phone,
    'city' => $order->city,
    'items' => $order->items,
    'additional_notes' => $order->additional_notes,
]);
```

5. **Save Changes**

---

### ✅ ADIM 3: .env'ye Telegram Ekle (2 dakika)

1. **File Manager → api/.env**
2. **Edit** ile aç
3. **En alta** şu satırları ekle:

```env
# Telegram Bot
TELEGRAM_BOT_TOKEN=8099911715:AAGhw02TJkpF843tNd1w7v9w01i9433gF-U
TELEGRAM_CHAT_ID=8363052797
```

4. **Save Changes**

---

### ✅ ADIM 4: Cache Temizle (1 dakika)

Eğer **SSH/Terminal** varsa:

```bash
cd ~/public_html/api
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

Eğer **SSH yoksa:**
- Göz ardı edin, otomatik temizlenecek

---

## ✅ TEST ET!

### 1. Frontend Test
```
https://ripehome.com.tr
```

✅ **Görmeli:**
- Kategoriler görünmeli
- WhatsApp butonu sağ altta
- Yıkama talimatı butonu footer'da
- URL'ler `/kategori/...` olmalı (eski `/category/` değil)

### 2. Backend API Test
```
https://ripehome.com.tr/api/api/categories
```

✅ **JSON dönmeli**

### 3. Admin Panel Test
```
https://ripehome.com.tr/admin
```

✅ **Giriş yapabilmeli**

### 4. Telegram Test

Toptan sipariş formu doldur ve gönder:
- Telegram'a bildirim gelmeli!

---

## 🔧 Sorun Giderme

### Frontend kategorileri göstermiyor

**Sorun:** API URL yanlış

**Çözüm:**
1. Tarayıcıda **F12** → **Console**
2. API isteği nereye gidiyor?
3. Doğru URL: `https://ripehome.com.tr/api/api/...`

### Telegram bildirimi gelmiyor

**Kontrol:**
1. `.env` dosyasında `TELEGRAM_BOT_TOKEN` ve `TELEGRAM_CHAT_ID` var mı?
2. `TelegramService.php` dosyası `app/Services/` altında mı?
3. `WholesaleOrderController.php`'de Telegram kodu eklendi mi?

**Log kontrol:**
- File Manager → `api/storage/logs/laravel.log`

---

## 🎉 Tamamlandı!

Sadece **10 dakikada** site güncellendi!

**Yeni Özellikler:**
- ✅ WhatsApp & Telefon butonları
- ✅ Yıkama talimatı PDF
- ✅ Telegram sipariş bildirimleri
- ✅ URL yapısı: `/kategori/`
- ✅ Kategori header görselleri

**İyi satışlar! 🚀**

