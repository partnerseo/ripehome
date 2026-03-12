# 🚀 Ripe Home - Production Deployment Checklist

## 📋 Deployment Öncesi Kontrol Listesi

### 1. Backend Konfigürasyonu

#### .env Dosyası Ayarları
- [ ] `.env.production.example` dosyasını `.env` olarak kopyala
- [ ] `APP_ENV=production` olarak ayarla
- [ ] `APP_DEBUG=false` olarak ayarla
- [ ] `APP_URL` değerini production domain'e ayarla (örn: `https://ripehome.com.tr`)
- [ ] `ASSET_URL` değerini production domain'e ayarla (örn: `https://ripehome.com.tr`)
- [ ] `APP_KEY` oluştur: `php artisan key:generate`
- [ ] Database bilgilerini production sunucusuna göre ayarla

#### Storage ve İzinler
```bash
# Storage link'ini oluştur
php artisan storage:link

# Storage klasörüne yazma izni ver
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Public storage'a izin ver
chmod -R 755 public/storage
```

#### Cache ve Optimize
```bash
# Config cache'i oluştur
php artisan config:cache

# Route cache'i oluştur
php artisan route:cache

# View cache'i oluştur
php artisan view:cache

# Event cache'i oluştur
php artisan event:cache

# Autoload optimize
composer install --optimize-autoloader --no-dev
```

#### Database Migration
```bash
# Production'da migration çalıştır
php artisan migrate --force

# Gerekirse seeder çalıştır (dikkatli!)
# php artisan db:seed --force
```

---

### 2. Frontend Konfigürasyonu

#### Environment Variables
- [ ] `.env` dosyası oluştur
- [ ] `VITE_API_URL` değerini production API URL'e ayarla
  ```
  VITE_API_URL=https://ripehome.com.tr/api
  ```

#### Build
```bash
# Dependencies yükle
npm install

# Production build oluştur
npm run build

# Dist klasörünü deploy et
```

---

### 3. Sunucu Konfigürasyonu

#### Apache .htaccess (public_html root)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # HTTPS yönlendirme
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Backend API istekleri
    RewriteCond %{REQUEST_URI} ^/api/
    RewriteRule ^(.*)$ backend/public/$1 [L,QSA]
    
    # Storage istekleri
    RewriteCond %{REQUEST_URI} ^/storage/
    RewriteRule ^(.*)$ backend/public/$1 [L,QSA]
    
    # Frontend routing (React Router için)
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.html [L,QSA]
</IfModule>

# MIME types
AddType image/jpeg .jpg .jpeg
AddType image/png .png
AddType image/gif .gif
AddType image/webp .webp

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

#### Backend .htaccess (backend/public)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

### 4. Klasör Yapısı (public_html)

```
public_html/
├── backend/              # Laravel backend
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/          # Backend public folder
│   │   ├── index.php
│   │   └── storage/     # Symbolic link
│   ├── storage/
│   │   └── app/
│   │       └── public/  # Gerçek görseller burada
│   │           ├── products/
│   │           ├── categories/
│   │           ├── sliders/
│   │           └── settings/
│   └── .env
├── index.html           # Frontend build output
├── assets/              # Frontend assets (JS, CSS)
├── .htaccess           # Ana routing dosyası
└── ripehomelogo.jpg
```

---

### 5. Görsel Path Sorunları - Kontrol Listesi

#### ✅ Backend Kontrol
1. [ ] `ImageHelper::getStorageUrl()` metodunu kontrol et
2. [ ] `APP_URL` ve `ASSET_URL` .env'de doğru ayarlanmış mı?
3. [ ] `php artisan storage:link` çalıştırıldı mı?
4. [ ] Database'deki image path'leri kontrol et:
   ```sql
   SELECT image FROM categories LIMIT 5;
   SELECT images FROM products LIMIT 5;
   ```
5. [ ] Path'ler `products/...` veya `categories/...` formatında mı?

#### ✅ Frontend Kontrol
1. [ ] `.env` dosyasında `VITE_API_URL` doğru mu?
2. [ ] Hardcoded path'ler var mı? (BrandPhilosophy.tsx kontrol et)
3. [ ] API'den gelen image URL'leri console'da kontrol et

#### 🔍 Test URL'leri
Production'da şu URL'leri test edin:
- `https://ripehome.com.tr/api/categories` → Kategori listesi
- `https://ripehome.com.tr/api/products` → Ürün listesi
- `https://ripehome.com.tr/storage/products/...` → Görsel erişimi

---

### 6. Güvenlik

- [ ] `.env` dosyası public erişime kapalı mı?
- [ ] `storage` ve `bootstrap/cache` klasörleri web'den erişilemez mi?
- [ ] HTTPS aktif mi?
- [ ] SQL injection koruması var mı? (Laravel ORM kullanıldığı için varsayılan olarak var)
- [ ] CORS ayarları doğru mu?

---

### 7. Performance

- [ ] Gzip compression aktif mi?
- [ ] Browser caching ayarları yapıldı mı?
- [ ] Laravel cache'leri oluşturuldu mu?
- [ ] Frontend build optimize edildi mi?
- [ ] Görseller optimize edildi mi?

---

### 8. Monitoring ve Logging

```bash
# Laravel log'ları kontrol et
tail -f storage/logs/laravel.log

# PHP error log'ları
tail -f /var/log/apache2/error.log  # veya nginx error.log
```

---

### 9. Rollback Planı

Deployment başarısız olursa:
1. Önceki `.env` dosyasını geri yükle
2. Database backup'ı geri yükle
3. Önceki code versiyonunu deploy et
4. Cache'leri temizle: `php artisan cache:clear`

---

### 10. Post-Deployment Test

- [ ] Ana sayfa yükleniyor mu?
- [ ] Kategoriler görünüyor mu?
- [ ] Kategori görselleri yükleniyor mu?
- [ ] Ürünler görünüyor mu?
- [ ] Ürün görselleri yükleniyor mu?
- [ ] Ürün detay sayfası çalışıyor mu?
- [ ] İletişim formu çalışıyor mu?
- [ ] Toptan sipariş formu çalışıyor mu?
- [ ] Admin paneli erişilebilir mi? (`/admin`)

---

## 🐛 Yaygın Sorunlar ve Çözümleri

### Problem: Görseller görünmüyor
**Çözüm:**
```bash
# Storage link'i yeniden oluştur
php artisan storage:link --force

# İzinleri kontrol et
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

### Problem: API istekleri çalışmıyor
**Çözüm:**
- `.htaccess` dosyasını kontrol et
- `mod_rewrite` aktif mi kontrol et
- CORS ayarlarını kontrol et (`config/cors.php`)

### Problem: 500 Internal Server Error
**Çözüm:**
```bash
# Log'ları kontrol et
tail -f storage/logs/laravel.log

# Cache'leri temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# İzinleri kontrol et
chmod -R 775 storage bootstrap/cache
```

### Problem: Mix/Vite manifest not found
**Çözüm:**
```bash
# Frontend build'i yeniden çalıştır
npm run build

# Build dosyalarını doğru konuma kopyala
```

---

## 📞 Destek

Herhangi bir sorun yaşarsanız:
1. Laravel log dosyasını kontrol edin (`storage/logs/laravel.log`)
2. Browser console'u kontrol edin (F12)
3. Network tab'de API isteklerini kontrol edin
4. Sunucu error log'larını kontrol edin

---

## ✅ Final Checklist

Deployment tamamlandıktan sonra:
- [ ] Tüm sayfalar yükleniyor
- [ ] Tüm görseller görünüyor
- [ ] Formlar çalışıyor
- [ ] Admin paneli erişilebilir
- [ ] HTTPS çalışıyor
- [ ] Mobile responsive çalışıyor
- [ ] SEO meta tag'leri doğru
- [ ] Performance tatmin edici

**🎉 Deployment Başarılı!**


