# 🚀 BASIT DEPLOYMENT REHBERİ

## ✅ BACKEND TAMAMEN OTOMATİK!

Artık tüm görseller, path'ler ve URL'ler otomatik çalışıyor! 

**ImageHelper** sistemi sayesinde:
- `ASSET_URL` bir kez ayarla, her yer otomatik çalışsın! ✅
- Görseller her zaman doğru URL ile gelir ✅
- Hiçbir yerde manuel path düzeltmesi gerekmez ✅

---

## 📦 1. BACKEND'İ YÜKLEBackend

klasörünü `api` klasörü olarak yükle:

```
/home/ripehome/public_html/api/
```

---

## ⚙️ 2. .ENV DOSYASINI AYARLA

`api/.env` dosyasını oluştur ve şunu yaz:

```env
APP_NAME="Ripe Home"
APP_ENV=production
APP_KEY=base64:gFiTn3IjFtrm+SPJ+J8yhoAo/S0+DZQv8IheAiPIjqA=
APP_DEBUG=false
APP_URL=https://ripehome.com.tr

# Bu satır sayesinde tüm görseller otomatik çalışır!
ASSET_URL=https://ripehome.com.tr/api

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ripehome_ripe
DB_USERNAME=ripehome_ripe
DB_PASSWORD=SENIN_ŞIFREN

FILESYSTEM_DISK=public
```

**ÖNEMLİ:** `ASSET_URL=https://ripehome.com.tr/api` satırı sayesinde tüm görseller otomatik doğru URL ile gelir!

---

## 📁 3. STORAGE'I KOPYALA

```
api/storage/app/public/  →  KOPYALA
api/public/               →  YAPIŞIR
api/public/public/        →  RENAME: "storage"
```

**Sonuç:** `api/public/storage/products/` oluşmalı!

---

## 🗄️ 4. DATABASE'İ İMPORT ET

phpMyAdmin'de `COMPLETE_RIPEHOME_DATABASE.sql` dosyasını import et.

---

## 🔧 5. İZİNLERİ AYARLA

```
api/storage/         → 775
api/bootstrap/cache/ → 775
```

---

## 🌐 6. .HTACCESS DOSYALARI

### `public_html/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # API isteklerini api/public/ dizinine yönlendir
    RewriteRule ^api/(.*)$ api/public/$1 [L]
    
    # Frontend SPA routing
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !^/api
    RewriteRule ^(.*)$ /index.html [L]
</IfModule>
```

### `api/public/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## ✅ BİTTİ!

Siteyi aç: https://ripehome.com.tr

**Her şey otomatik çalışacak!** 🎉

---

## 🔧 NASIL ÇALIŞIYOR?

**ImageHelper** sistemi:

1. `.env`'deki `ASSET_URL` değerini okur
2. Her görsel için otomatik tam URL oluşturur
3. Örnek: `products/xxx.jpg` → `https://ripehome.com.tr/api/storage/products/xxx.jpg`

**Artık hiçbir yerde manuel path düzeltmesi gerekmiyor!** ✅

---

## 📝 GÜNCELLEMELER

Eğer backend'de değişiklik yaparsan:

1. Sadece değişen dosyaları yükle
2. `.env`'i kontrol et
3. `api/bootstrap/cache/*.php` dosyalarını sil
4. Bitti!

**ImageHelper** her zaman doğru URL'leri verir!



