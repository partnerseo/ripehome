# 🚀 Manuel Deployment Rehberi (SSH Olmadan)

## ✅ Bu Rehber İçin

SSH/Terminal erişimi olmayan cPanel hosting için hazırlanmıştır.

---

## 📋 ADIMLAR

### ✅ ADIM 1: Frontend Yükle (5 dakika)

1. **cPanel → File Manager**
2. `public_html/` klasörüne git
3. **Upload** butonuna tıkla
4. `dist/` klasörünün **içindeki tüm dosyaları** seç ve yükle:
   - `index.html`
   - `assets/` klasörü (tümü)
   - `ripehomelogo.jpg`
   - `yikamatalimati.pdf`

✅ **Test:** `https://ripehome.com.tr` → Sayfa açılmalı (kategoriler gelmeyecek, normal)

---

### ✅ ADIM 2: Backend Hazırlığı

#### A. Bilgisayarınızda Backend Klasörünü ZIP'le

1. `backend/` klasörüne sağ tıklayın
2. **"Sıkıştır"** veya **"Compress to ZIP"**
3. `backend.zip` oluşacak

**ÖNEMLİ:** `backend/.env` dosyasını ZIP'e **EKLEMEYIN** (zaten yok)

---

### ✅ ADIM 3: Backend Yükle (10 dakika)

1. **cPanel → File Manager**
2. `public_html/` içinde **"+ Folder"** → İsim: `api`
3. `api/` klasörüne gir
4. **Upload** → `backend.zip` yükle
5. `backend.zip`'e sağ tıkla → **Extract**
6. Extract bittikten sonra `backend.zip`'i sil

✅ **Sonuç:**
```
public_html/api/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── routes/
├── storage/
├── vendor/ (var!)
├── artisan
├── composer.json
└── .env.example
```

---

### ✅ ADIM 4: Database Oluştur (5 dakika)

1. **cPanel → MySQL® Databases**

2. **Create New Database:**
   - Database Name: `ripehome_db`
   - **Create Database**

3. **MySQL Users → Add New User:**
   - Username: `ripehome_user`
   - Password: [güçlü şifre oluşturun]
   - **Password Generator** kullanabilirsiniz
   - **Create User**

4. **Add User To Database:**
   - User: `ripehome_user`
   - Database: `ripehome_db`
   - **Add**
   - **ALL PRIVILEGES** seçin
   - **Make Changes**

**NOT ALIN:**
- Database adı: `[cpanel_kullanıcı]_ripehome_db`
- Database kullanıcı: `[cpanel_kullanıcı]_ripehome_user`
- Şifre: `[belirlediğiniz_şifre]`

---

### ✅ ADIM 5: Database Tablolarını Oluştur (5 dakika)

1. **cPanel → phpMyAdmin**
2. Sol taraftan `[cpanel_kullanıcı]_ripehome_db`'yi seç
3. Üst menüden **Import** (İçe Aktar)
4. **Choose File** → `backend/database_mysql.sql` seç
5. **Go** (veya İçe Aktar)

✅ **Kontrol:** Tablolar oluşmuş mu?
- users
- categories
- products
- settings
- vb.

---

### ✅ ADIM 6: .env Dosyası Oluştur (5 dakika)

1. **File Manager → public_html/api/**
2. `.env.example` dosyasına sağ tıkla → **Copy**
3. Yeni isim: `.env`
4. `.env` dosyasına sağ tıkla → **Edit**

**Düzenle:**

```env
APP_NAME="Ripe Home"
APP_ENV=production
APP_KEY=base64:QrG0Ofn9rJYTu8eMlCRdz3f/FZmAbwOt35+zgMsILPc=
APP_DEBUG=false
APP_URL=https://ripehome.com.tr

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=[cpanel_user]_ripehome_db
DB_USERNAME=[cpanel_user]_ripehome_user
DB_PASSWORD=[adım_4_teki_şifre]

FILESYSTEM_DISK=public

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

TELEGRAM_BOT_TOKEN=8099911715:AAGhw02TJkpF843tNd1w7v9w01i9433gF-U
TELEGRAM_CHAT_ID=8363052797

FRONTEND_URL=https://ripehome.com.tr
```

**Önemli Değişiklikler:**
- `APP_KEY` → Yukarıdaki değeri kullanın
- `DB_DATABASE` → Database adınızı yazın
- `DB_USERNAME` → Database kullanıcınızı yazın
- `DB_PASSWORD` → Şifrenizi yazın

6. **Save Changes**

---

### ✅ ADIM 7: .htaccess Oluştur (2 dakika)

#### A. public_html/api/.htaccess

1. **File Manager → public_html/api/**
2. **+ File** → İsim: `.htaccess`
3. **Edit** ile aç

**İçeriği:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

4. **Save**

#### B. public_html/api/public/.htaccess

Laravel'de zaten var, kontrol edin:

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

### ✅ ADIM 8: Storage Klasör İzinleri (2 dakika)

1. **File Manager → public_html/api/storage/**
2. `storage` klasörüne sağ tıkla → **Permissions**
3. **775** yazın veya:
   - Owner: Read, Write, Execute
   - Group: Read, Write, Execute
   - World: Read, Execute
4. **"Recurse into subdirectories"** işaretle
5. **Change Permissions**

6. Aynısını `bootstrap/cache/` için de yap

---

### ✅ ADIM 9: PHP Version Ayarla (1 dakika)

1. **cPanel → MultiPHP Manager**
2. `api` klasörünü seç
3. PHP Version: **8.1** veya **8.2** seç
4. **Apply**

---

### ✅ ADIM 10: TEST ET! 🎉

#### 1. Backend API Test

Tarayıcıda aç:
```
https://ripehome.com.tr/api/api/categories
```

✅ **Görmeli:**
```json
{
  "success": true,
  "data": [...]
}
```

❌ **Hata Alıyorsanız:**
- `.htaccess` dosyaları doğru mu?
- PHP 8.1+ seçili mi?
- `.env` database bilgileri doğru mu?
- İzinler 775 mi?

#### 2. Frontend Test

```
https://ripehome.com.tr
```

✅ **Görmeli:**
- Anasayfa açılmalı
- Kategoriler görünmeli
- Ürünler görünmeli
- WhatsApp butonu çalışmalı

#### 3. Admin Panel Test

```
https://ripehome.com.tr/admin
```

✅ **Giriş:**
- Email: `admin@admin.com`
- Şifre: `password`

**İLK GİRİŞTE ŞİFRENİZİ DEĞİŞTİRİN!**

---

## 🔧 Sorun Giderme

### Kategoriler Görünmüyor

1. Backend API'yi test et: `https://ripehome.com.tr/api/api/categories`
2. Eğer JSON geliyorsa → Frontend sorunu (dist/ yeniden yükle)
3. Eğer hata veriyorsa → Backend sorunu

**Backend Hata Log:**
- File Manager → `public_html/api/storage/logs/laravel.log`

### 500 Internal Server Error

1. `.htaccess` dosyaları kontrol et
2. İzinler 775 mi kontrol et
3. `.env` dosyası var mı kontrol et
4. PHP 8.1+ seçili mi kontrol et

### Database Connection Error

1. `.env` dosyasını aç
2. `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` kontrol et
3. phpMyAdmin'de database'e erişebiliyor musun?

### Görseller Görünmüyor

1. **Storage Link:**
   - File Manager → `public_html/api/public/storage/` klasörü var mı?
   - Yoksa: Manuel symlink yapılamayacağı için config'i değiştir

2. **Config Değişikliği (storage link yerine):**
   - `.env` dosyasında: `FILESYSTEM_DISK=public`
   - Görselleri `public_html/api/public/` altına yükle

---

## 🎉 Tamamlandı!

- ✅ Frontend yüklü
- ✅ Backend yüklü
- ✅ Database hazır
- ✅ Admin panel çalışıyor

**Site canlı:** `https://ripehome.com.tr`  
**Admin:** `https://ripehome.com.tr/admin`

---

## 📝 Sonraki Adımlar

1. Admin panelden kategorileri düzenle
2. Ürünleri ekle
3. Görselleri yükle
4. Site ayarlarını güncelle
5. Admin şifresini değiştir

**İyi satışlar! 🚀**

