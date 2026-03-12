# 🔍 Görsel Path Sorunları - Troubleshooting Guide

## Problem: Production'da görseller görünmüyor

### 1️⃣ Backend Kontrolleri

#### A. Storage Link Kontrolü
```bash
# Backend klasöründe
cd backend

# Storage link var mı?
ls -la public/storage

# Beklenen çıktı:
# lrwxr-xr-x  1 user  group  XX XXX XX XX:XX public/storage -> /path/to/backend/storage/app/public

# Eğer link yoksa veya kırıksa:
php artisan storage:link --force
```

#### B. .env Dosyası Kontrolü
```bash
# Backend .env dosyasını kontrol et
cat .env | grep -E "APP_URL|ASSET_URL"

# ✅ Doğru:
# APP_URL=https://ripehome.com.tr
# ASSET_URL=https://ripehome.com.tr

# ❌ Yanlış:
# APP_URL=http://localhost:8000
# APP_URL=http://localhost  (port eksik)
# ASSET_URL yoksa (opsiyonel ama önerilir)
```

#### C. Dosya İzinleri
```bash
# Storage klasörü izinleri
chmod -R 775 storage
chmod -R 755 storage/app/public

# Public storage izinleri
chmod -R 755 public/storage

# Web server kullanıcısına sahiplik ver (gerekirse)
# chown -R www-data:www-data storage
# chown -R www-data:www-data public/storage
```

#### D. Database Path Kontrolü
```bash
# SQLite için
sqlite3 database/database.sqlite "SELECT id, name, image FROM categories LIMIT 3;"

# MySQL için
mysql -u username -p database_name -e "SELECT id, name, image FROM categories LIMIT 3;"

# ✅ Beklenen format:
# products/kategori-adi/dosya.jpg
# categories/dosya.jpg
# sliders/dosya.jpg

# ❌ Yanlış format:
# /storage/products/... (başında / olmamalı)
# http://localhost:8000/storage/... (tam URL olmamalı)
# C:\xampp\htdocs\... (Windows path olmamalı)
```

#### E. ImageHelper Testi
```bash
# Laravel Tinker ile test
php artisan tinker

# Test kodu:
>>> use App\Helpers\ImageHelper;
>>> $path = "products/test/image.jpg";
>>> $url = ImageHelper::getStorageUrl($path);
>>> echo $url;

# ✅ Beklenen:
# https://ripehome.com.tr/storage/products/test/image.jpg

# Eğer yanlış çıktı alıyorsanız:
>>> config('app.url');
>>> config('app.asset_url');
# Bu değerleri kontrol edin
```

#### F. API Response Kontrolü
```bash
# Terminal'den API'yi test et
curl https://ripehome.com.tr/api/categories | jq '.data[0].image'

# ✅ Beklenen:
# "https://ripehome.com.tr/storage/products/kategori/image.jpg"

# ❌ Yanlış:
# "http://localhost:8000/storage/..."
# "/storage/..." (domain eksik)
# "products/..." (storage prefix eksik)
```

#### G. Storage Dosyası Varlık Kontrolü
```bash
# Backend klasöründe
ls -la storage/app/public/products/

# Klasör yapısı:
# storage/app/public/
#   ├── categories/
#   ├── products/
#   │   ├── kategori-1/
#   │   │   ├── urun1.jpg
#   │   │   └── urun2.jpg
#   │   └── kategori-2/
#   ├── sliders/
#   └── settings/

# Eğer dosyalar farklı yerdeyse:
# 1. Doğru konuma taşıyın
# 2. Veya database path'lerini güncelleyin
```

---

### 2️⃣ Frontend Kontrolleri

#### A. .env Dosyası
```bash
# Frontend root klasöründe
cat .env

# ✅ Doğru:
# VITE_API_URL=https://ripehome.com.tr/api

# ❌ Yanlış:
# VITE_API_URL=http://localhost:8000/api
# VITE_API_URL=https://ripehome.com.tr (son /api eksik)
```

#### B. Build Kontrolü
```bash
# Frontend'i yeniden build et
npm run build

# Build çıktısını kontrol et
ls -la dist/

# public_html'e kopyala (gerekirse)
```

#### C. Browser Console Kontrolü
```
1. Tarayıcıda F12 ile Developer Tools'u aç
2. Console tab'inde hataları kontrol et
3. Network tab'inde API isteklerini kontrol et

✅ Başarılı API isteği:
GET https://ripehome.com.tr/api/categories
Status: 200 OK
Response: {"success":true,"data":[...]}

❌ Başarısız durum:
- Status: 404 (API endpoint'i bulunamıyor)
- Status: 500 (Backend hatası)
- CORS error (CORS ayarları yanlış)
- Mixed content error (HTTP/HTTPS karışık kullanım)
```

---

### 3️⃣ Server Konfigürasyonu

#### A. .htaccess Kontrolü (public_html root)
```apache
# /storage/ ile başlayan istekleri backend'e yönlendir
RewriteCond %{REQUEST_URI} ^/storage/
RewriteRule ^(.*)$ backend/public/$1 [L,QSA]

# Bu satırlar MUTLAKA olmalı
```

#### B. Apache mod_rewrite Kontrolü
```bash
# Apache modules kontrol
apache2ctl -M | grep rewrite

# Beklenen: rewrite_module (shared)

# Eğer aktif değilse:
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### C. .htaccess İzinleri
```bash
# .htaccess dosyaları okunabilir mi?
ls -la public_html/.htaccess
ls -la public_html/backend/public/.htaccess

# ✅ Minimum izinler:
# -rw-r--r-- (644)

# AllowOverride ayarı (Apache config)
# /etc/apache2/sites-available/your-site.conf içinde:
# <Directory /var/www/html/public_html>
#     AllowOverride All
# </Directory>
```

#### D. PHP Ayarları
```bash
# php.ini kontrol
php -i | grep -E "upload_max_filesize|post_max_size|max_execution_time"

# Önerilen değerler:
# upload_max_filesize = 20M
# post_max_size = 20M
# max_execution_time = 300
```

---

### 4️⃣ Yaygın Hatalar ve Çözümleri

#### Hata 1: 404 Not Found - /storage/...
**Neden:** .htaccess storage routing'i çalışmıyor
```bash
# Çözüm:
1. public_html/.htaccess dosyasını kontrol et
2. mod_rewrite aktif mi kontrol et
3. AllowOverride All olmalı
```

#### Hata 2: Broken Image Icon
**Neden:** Görsel path yanlış veya dosya yok
```bash
# Çözüm:
1. Browser'da görselin URL'ine direkt git
2. 404 alıyorsan: Dosya gerçekten var mı kontrol et
3. 403 alıyorsan: İzinleri kontrol et
4. Redirect alıyorsan: .htaccess routing'i kontrol et
```

#### Hata 3: localhost:8000 URL'leri production'da
**Neden:** .env dosyası güncel değil veya cache temizlenmemiş
```bash
# Çözüm:
cd backend
php artisan config:clear
php artisan cache:clear
# .env dosyasını kontrol et
# APP_URL=https://ripehome.com.tr olmalı
php artisan config:cache
```

#### Hata 4: Mixed Content Warning
**Neden:** HTTP içeriği HTTPS sayfasında yükleniyor
```bash
# Çözüm:
1. TÜM URL'ler https:// ile başlamalı
2. .env kontrol: APP_URL=https://...
3. Database'deki eski http:// URL'leri temizle
```

#### Hata 5: CORS Error
**Neden:** Backend CORS ayarları frontend'e izin vermiyor
```bash
# Çözüm backend/config/cors.php:
'allowed_origins' => ['https://ripehome.com.tr'],
'allowed_origins_patterns' => [],
'supports_credentials' => true,
```

---

### 5️⃣ Debug Mode (Geçici)

Production'da sorun çözümü için geçici debug açma:

```bash
# backend/.env
APP_DEBUG=true
APP_ENV=local

# Cache temizle
php artisan config:clear
php artisan cache:clear

# SORUN ÇÖZÜLDÜKTEN SONRA MUTLAKA KAPAT:
# APP_DEBUG=false
# APP_ENV=production
# php artisan config:cache
```

---

### 6️⃣ Hızlı Test Script

```bash
#!/bin/bash
# test-images.sh

echo "🔍 Görsel Path Test Scripti"
echo "============================"

# 1. Storage link
echo -n "Storage link: "
if [ -L "public/storage" ]; then
    echo "✅ Mevcut"
else
    echo "❌ Yok! Çalıştır: php artisan storage:link"
fi

# 2. .env APP_URL
echo -n "APP_URL: "
grep "^APP_URL=" .env

# 3. Storage dosyaları
echo "Storage klasörleri:"
ls -ld storage/app/public/*/

# 4. İzinler
echo "Storage izinleri:"
ls -la storage/app/public/ | head -5

# 5. API Test
echo -n "API Test: "
curl -s -o /dev/null -w "%{http_code}" http://localhost/api/categories
echo ""

echo ""
echo "Test tamamlandı!"
```

---

### 7️⃣ SQL Sorguları ile Path Düzeltme

Eğer database'de yanlış path'ler varsa:

```sql
-- Kategorilerde localhost varsa temizle
UPDATE categories 
SET image = REPLACE(image, 'http://localhost:8000/storage/', '')
WHERE image LIKE '%localhost%';

-- Ürünlerde localhost temizle (JSON içinde)
-- MySQL için:
UPDATE products 
SET images = REPLACE(images, 'http://localhost:8000/storage/', '')
WHERE images LIKE '%localhost%';

-- Başında /storage/ varsa temizle
UPDATE categories 
SET image = REPLACE(image, '/storage/', '')
WHERE image LIKE '/storage/%';

-- Kontrol
SELECT id, name, image FROM categories LIMIT 5;
SELECT id, name, images FROM products LIMIT 5;
```

---

### 8️⃣ Checklist: Görsel Sorunları Çözümü

- [ ] `php artisan storage:link` çalıştırıldı mı?
- [ ] Backend `.env` APP_URL doğru mu?
- [ ] Backend `.env` ASSET_URL ayarlandı mı?
- [ ] Frontend `.env` VITE_API_URL doğru mu?
- [ ] `php artisan config:cache` çalıştırıldı mı?
- [ ] Storage klasörü izinleri 755/775 mi?
- [ ] public_html/.htaccess dosyası doğru mu?
- [ ] Database'deki path'ler doğru formattta mı? (products/...)
- [ ] mod_rewrite aktif mi?
- [ ] Browser cache temizlendi mi?
- [ ] API'den dönen URL'ler https:// ile başlıyor mu?
- [ ] Görseller fiziksel olarak storage/app/public/'da mı?

---

### 9️⃣ İletişim ve Destek

Sorun devam ediyorsa:

1. **Laravel Log:** `tail -f storage/logs/laravel.log`
2. **Apache Error Log:** `tail -f /var/log/apache2/error_log`
3. **Browser Console:** F12 > Console + Network tabs
4. **Test URL:** Direkt görselin URL'ini browser'a yapıştır

---

**Son Güncelleme:** 2026-01-12


