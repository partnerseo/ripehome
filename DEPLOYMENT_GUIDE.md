# 🚀 Ripe Home - Deployment Rehberi

## 📋 Özet

**Frontend:** `ripehome.com.tr`  
**Backend:** `api.ripehome.com.tr`  
**Admin Panel:** `api.ripehome.com.tr/admin`

---

## 📦 1. FRONTEND DEPLOYMENT

### ✅ Hazır Build
`dist/` klasörü hazır ve kullanıma hazır (2.4 MB)

### Upload Adımları

1. **cPanel File Manager** veya **FTP** ile bağlanın
2. `public_html/` klasörüne gidin
3. `dist/` klasörünün **içindeki tüm dosyaları** yükleyin:
   - `index.html`
   - `assets/` klasörü
   - `ripehomelogo.jpg`
   - `yikamatalimati.pdf`

### Sonuç
```
public_html/
├── index.html
├── assets/
│   ├── index-[hash].css
│   └── index-[hash].js
├── ripehomelogo.jpg
└── yikamatalimati.pdf
```

---

## 🖥️ 2. BACKEND DEPLOYMENT

### Adım 1: Subdomain Oluştur

cPanel'de **Subdomains** bölümünden:
- **Subdomain:** `api`
- **Domain:** `ripehome.com.tr`
- **Document Root:** `/home/username/api.ripehome.com.tr`

### Adım 2: Backend Dosyalarını Yükle

#### Yöntem A: Git Clone (Önerilen)
```bash
cd /home/username/api.ripehome.com.tr
git clone https://github.com/partnerseo/ripehome.git temp
mv temp/backend/* .
mv temp/backend/.* .
rm -rf temp
```

#### Yöntem B: Manuel Upload
1. `backend/` klasörünü zip'le
2. cPanel File Manager ile yükle
3. Extract et

### Adım 3: Composer Install

SSH ile:
```bash
cd /home/username/api.ripehome.com.tr
composer install --no-dev --optimize-autoloader
```

### Adım 4: .env Dosyası Ayarla

```bash
cp .env.example .env
nano .env
```

**Düzenlenecekler:**
```env
APP_NAME=RipeHome
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.ripehome.com.tr

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ripehome_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Telegram Bot (Sipariş Bildirimleri)
TELEGRAM_BOT_TOKEN=8099911715:AAGhw02TJkpF843tNd1w7v9w01i9433gF-U
TELEGRAM_CHAT_ID=8363052797
```

### Adım 5: Laravel Kurulum Komutları

```bash
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Adım 6: Klasör İzinleri

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Adım 7: Web Server Ayarları

#### Apache (.htaccess)
`public/` klasöründe zaten mevcut.

#### Nginx
```nginx
server {
    listen 80;
    server_name api.ripehome.com.tr;
    root /home/username/api.ripehome.com.tr/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔗 3. DNS AYARLARI

### A Record Ekle
```
Host: api
Type: A
Value: [Sunucu IP'niz]
TTL: 3600
```

---

## ✅ 4. TEST

### Backend Test
```bash
curl https://api.ripehome.com.tr/api/categories
```

**Beklenen:** JSON response
```json
{
  "success": true,
  "data": [...]
}
```

### Frontend Test
1. Tarayıcıda `https://ripehome.com.tr` açın
2. Kategorilerin göründüğünü kontrol edin
3. Ürünlere tıklayın
4. WhatsApp ve telefon butonlarını test edin

### Admin Panel Test
```
URL: https://api.ripehome.com.tr/admin
Email: admin@example.com
Şifre: [.env'deki admin şifresi]
```

---

## 🔐 5. GÜVENLİK

### SSL Sertifikası
cPanel'de **Let's Encrypt SSL** aktif edin:
- ✅ `ripehome.com.tr`
- ✅ `api.ripehome.com.tr`

### .env Dosyası
❌ `.env` dosyasını ASLA GitHub'a yüklemeyin!  
✅ `.gitignore`'da zaten var.

### Database Backup
Düzenli yedek alın:
```bash
php artisan backup:run
```

---

## 📝 6. SORUN GİDERME

### Kategoriler Görünmüyor
```bash
# Backend çalışıyor mu?
curl https://api.ripehome.com.tr/api/categories

# CORS hatası varsa backend .env'ye ekle:
FRONTEND_URL=https://ripehome.com.tr
```

### 500 Internal Server Error
```bash
# Log'ları kontrol et
tail -f storage/logs/laravel.log

# Cache'i temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Görseller Görünmüyor
```bash
# Storage link kontrol
php artisan storage:link

# İzinler
chmod -R 775 storage
```

---

## 📱 7. TELEGRAM BOT AYARI

Zaten ayarlanmış:
- **Bot:** @Ripehomeebot
- **Token:** `8099911715:AAGhw02TJkpF843tNd1w7v9w01i9433gF-U`
- **Chat ID:** `8363052797`

Yeni sipariş geldiğinde Telegram'a bildirim gider!

---

## 🎉 TAMAMLANDI!

Deployment tamamlandı! Herhangi bir sorun olursa:

1. Logları kontrol edin
2. Cache'leri temizleyin
3. İzinleri kontrol edin
4. DNS propagation bekleyin (24 saat)

**İyi satışlar! 🚀**

