# 🔴 500 Internal Server Error - Çözüm

## Hata: `GET https://ripehome.com.tr/admin` 500 hatası veriyor

---

## ⚡ HIZLI ÇÖZÜM (Sunucuda çalıştır):

```bash
# Backend klasörüne git
cd public_html/backend  # veya backend klasörün nerede ise

# 1. Cache'leri temizle (ÇOK ÖNEMLİ!)
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Storage link oluştur
php artisan storage:link

# 3. İzinleri düzelt
chmod -R 775 storage bootstrap/cache
chmod 644 .env

# 4. Config cache (temizledikten sonra)
php artisan config:cache

# 5. Log'a bak
tail -50 storage/logs/laravel.log
```

---

## 🔍 ADIM ADIM KONTROL:

### 1. .env Dosyası Kontrolü

```bash
cd backend
cat .env | grep -E "APP_URL|DB_"
```

**Olması gereken:**
```env
APP_URL=https://ripehome.com.tr
ASSET_URL=https://ripehome.com.tr
DB_CONNECTION=mysql
DB_DATABASE=ripehome_ripe
DB_USERNAME=ripehome_ripe
DB_PASSWORD=ahm20685485
```

### 2. Database Bağlantısı Test

```bash
php artisan tinker
# İçinde:
>>> \DB::connection()->getPdo();
# Hata almazsan DB bağlantısı OK ✅
```

Veya:

```bash
mysql -u ripehome_ripe -p ripehome_ripe -e "SELECT COUNT(*) FROM categories;"
# Şifre: ahm20685485
```

### 3. Composer Dependencies

```bash
cd backend
composer install --no-dev --optimize-autoloader
```

### 4. Storage Klasörü Kontrolü

```bash
ls -la storage/
ls -la bootstrap/cache/
```

Her ikisi de **yazılabilir** olmalı (755 veya 775)

### 5. .htaccess Kontrolü

**public_html/.htaccess** dosyasında admin routing olmalı:

```apache
# Admin panel
RewriteCond %{REQUEST_URI} ^/admin
RewriteRule ^(.*)$ backend/public/$1 [L,QSA]
```

### 6. PHP Versiyonu

```bash
php -v
```

**Minimum PHP 8.2** olmalı (Laravel 11 için)

---

## 📋 LOG DOSYASINA BAK (EN ÖNEMLİ!)

```bash
# Son hataları göster
tail -100 backend/storage/logs/laravel.log

# veya
cat backend/storage/logs/laravel.log | grep "ERROR"
```

---

## 🐛 YAYGLN HATALAR VE ÇÖZÜMLER:

### Hata 1: "No such file or directory"
```bash
# storage/logs klasörü yoksa oluştur
mkdir -p storage/logs
touch storage/logs/laravel.log
chmod 775 storage/logs
```

### Hata 2: "Permission denied"
```bash
# Web server kullanıcısına sahiplik ver
chown -R www-data:www-data storage bootstrap/cache
# veya
chown -R apache:apache storage bootstrap/cache
# veya hosting'in kullanıcısı
```

### Hata 3: "Class not found"
```bash
# Composer autoload
composer dump-autoload
php artisan clear-compiled
```

### Hata 4: "Database connection failed"
```bash
# .env database bilgilerini kontrol et
# cPanel'den database var mı kontrol et
# MySQL kullanıcı izinleri kontrol et
```

### Hata 5: "419 Page Expired" (CSRF)
```bash
# Session klasörü yazılabilir mi?
chmod -R 775 storage/framework/sessions
```

---

## ✅ KONTROL LİSTESİ:

Şunları kontrol et:

- [ ] `.env` dosyası var mı ve doğru mu?
- [ ] `APP_KEY` set edilmiş mi?
- [ ] `APP_URL=https://ripehome.com.tr` doğru mu?
- [ ] Database bilgileri doğru mu?
- [ ] `storage/` klasörü yazılabilir mi? (775)
- [ ] `bootstrap/cache/` yazılabilir mi? (775)
- [ ] `php artisan storage:link` çalıştırıldı mı?
- [ ] Composer dependencies yüklü mü?
- [ ] PHP versiyonu 8.2+ mı?
- [ ] MySQL database oluşturuldu mu?
- [ ] SQL import edildi mi?

---

## 🚀 TEK KOMUTLA TÜM ÇÖZÜM:

```bash
cd backend && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan route:clear && \
php artisan view:clear && \
php artisan storage:link && \
chmod -R 775 storage bootstrap/cache && \
chmod 644 .env && \
php artisan config:cache && \
echo "✅ Tamamlandı! Tarayıcıyı yenile (Ctrl+Shift+R)"
```

---

## 📞 HALA ÇALIŞMIYORSA:

1. **Log dosyasını kontrol et:**
   ```bash
   tail -100 storage/logs/laravel.log
   ```

2. **Apache/Nginx error log:**
   ```bash
   tail -50 /var/log/apache2/error.log
   # veya
   tail -50 /var/log/nginx/error.log
   ```

3. **PHP error log:**
   ```bash
   tail -50 /var/log/php-fpm/error.log
   ```

4. **Debug mode aç (GEÇİCİ!):**
   ```bash
   nano .env
   # APP_DEBUG=true yap
   # Tarayıcıda tam hatayı göreceksin
   # SONRA TEKRAR false YAP!
   ```

---

## 💡 EN YAYGN SEBEP:

**%90 ihtimalle:** Cache temizlenmemiş veya .env doğru ayarlanmamış!

Çözüm:
```bash
php artisan config:clear
php artisan cache:clear
```

Sonra `.env` dosyasını kontrol et.

---

**Hatayı çözdüğünde bana log'dan gelen hata mesajını gönder, daha spesifik yardım edeyim!**

