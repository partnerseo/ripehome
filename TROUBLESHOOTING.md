# 🚨 Backend Sorun Giderme (Troubleshooting)

## Backend Çalışmıyor veya ERR_TIMED_OUT Hatası

### 1. Backend Çalışıyor mu Kontrol Et

Terminal'de:
```bash
cd backend
php artisan serve
```

**Beklenen Çıktı:**
```
Starting Laravel development server: http://127.0.0.1:8000
[Fri Oct 17 18:17:00 2025] PHP 8.2.0 Development Server (http://127.0.0.1:8000) started
```

### 2. Test Endpoint'leri

Terminal'de test et:
```bash
# Backend çalışıyor mu?
curl http://localhost:8000/test

# API çalışıyor mu?
curl http://localhost:8000/api/ping

# Database bağlantısı var mı?
curl http://localhost:8000/api/test-db

# Kategoriler API
curl http://localhost:8000/api/categories
```

**Tüm sonuçlar 200 OK dönmeli.**

### 3. Cache Temizle

```bash
cd backend

# Tüm cache'leri temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Composer autoload'ı yenile
composer dump-autoload

# Sunucuyu yeniden başlat
php artisan serve
```

### 4. Port Çakışması Kontrolü

8000 portu kullanılıyor olabilir:

```bash
# 8000 portunu kim kullanıyor?
lsof -i :8000

# Eğer başka bir process varsa, onu sonlandır:
kill -9 [PID]

# Ya da farklı port kullan:
php artisan serve --port=8001
```

Farklı port kullanıyorsan, frontend `.env` dosyasını güncelle:
```
VITE_API_URL=http://localhost:8001/api
```

### 5. .env Dosyası Kontrolü

`backend/.env` dosyası olduğundan emin ol:
```bash
cd backend
ls -la .env
```

Yoksa `.env.example`'dan kopyala:
```bash
cp .env.example .env
php artisan key:generate
```

**.env içeriği kontrol et:**
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:... (php artisan key:generate ile oluşturulmuş olmalı)
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 6. Database Bağlantısı Kontrolü

```bash
cd backend
php artisan tinker
```

Tinker'da:
```php
DB::connection()->getPdo();
DB::table('categories')->count();
exit
```

Hata alıyorsan:
- MySQL çalışıyor mu kontrol et
- `.env` dosyasındaki DB bilgileri doğru mu?

### 7. PHP Versiyonu Kontrolü

```bash
php -v
```

**PHP 8.1+ olmalı.** Eski versiyon kullanıyorsan güncelle.

### 8. Permissions (İzinler)

```bash
cd backend
chmod -R 775 storage bootstrap/cache
```

### 9. Log Dosyası Kontrolü

Hata loglarını kontrol et:
```bash
cd backend
tail -50 storage/logs/laravel.log
```

### 10. Background Process Kontrolü

Eğer backend arka planda çalıştırıldıysa:

```bash
# Tüm php artisan serve process'lerini bul
ps aux | grep "php artisan serve"

# Hepsini sonlandır
pkill -f "php artisan serve"

# Tekrar başlat
cd backend
php artisan serve
```

---

## Frontend Sorunları

### Frontend Çalışmıyor

```bash
cd /Users/ahmetalkan/Downloads/ripehome

# Node modüllerini temizle ve yenile
rm -rf node_modules
npm install

# Sunucuyu başlat
npm run dev
```

### API Çağrıları Çalışmıyor

`.env` dosyasını kontrol et:
```env
VITE_API_URL=http://localhost:8000/api
```

Tarayıcıda F12 → Console → Network:
- API çağrıları yapılıyor mu?
- 404, 500 gibi hatalar var mı?
- CORS hatası var mı?

### CORS Hatası

`backend/config/cors.php`:
```php
'allowed_origins' => ['*'], // Development için tüm originlere izin ver
```

---

## Hızlı Kontrol Listesi

✅ **Backend çalışıyor mu?** → `curl http://localhost:8000/test`
✅ **API çalışıyor mu?** → `curl http://localhost:8000/api/ping`
✅ **Database bağlantısı var mı?** → `curl http://localhost:8000/api/test-db`
✅ **Frontend çalışıyor mu?** → `http://localhost:5177/ripehome/`
✅ **Port çakışması var mı?** → `lsof -i :8000` ve `lsof -i :5173`

---

## Yardım

Sorun devam ediyorsa:
1. `storage/logs/laravel.log` dosyasını kontrol et
2. Tarayıcı Console (F12) hatalarını kontrol et
3. Network sekmesinde API yanıtlarını incele





