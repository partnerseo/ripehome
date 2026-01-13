# 🎯 cPanel'de Ripe Home Kurulum - Adım Adım

## 📁 ADIM 1: DOSYALARI YÜKLE

### 1.1 File Manager'a Git
- cPanel → **File Manager**
- `public_html` klasörüne git

### 1.2 Backend Yükle
- **Upload** butonuna tıkla
- `backend` klasörünün tüm içeriğini zipleyip yükle
- Veya direkt `backend` klasörünü sürükle

### 1.3 Frontend (dist) Yükle
- `dist` klasöründeki dosyaları `public_html/` altına kopyala:
  - `index.html` → `public_html/index.html`
  - `assets/` → `public_html/assets/`
  - `ripehomelogo.jpg` → `public_html/ripehomelogo.jpg`

**Sonuç yapısı:**
```
public_html/
├── backend/              ← Laravel backend
│   ├── app/
│   ├── public/
│   ├── storage/
│   └── .env             ← Bu dosyayı oluşturacağız
├── index.html           ← React frontend
├── assets/              ← React assets
└── .htaccess           ← Root htaccess
```

---

## 💾 ADIM 2: DATABASE OLUŞTUR

### 2.1 Database Oluştur
1. cPanel → **MySQL Databases**
2. "Create New Database" → `ripehome_ripe`
3. Create Database

### 2.2 Kullanıcı Oluştur
1. Aşağı kaydır → "MySQL Users"
2. Username: `ripehome_ripe`
3. Password: `ahm20685485` (veya güçlü bir şifre)
4. Create User

### 2.3 Kullanıcıyı Database'e Ekle
1. "Add User To Database" bölümü
2. User: `ripehome_ripe` seç
3. Database: `ripehome_ripe` seç
4. Add
5. **ALL PRIVILEGES** seç → Make Changes

### 2.4 SQL İmport Et
1. cPanel → **phpMyAdmin**
2. Sol taraftan `ripehome_ripe` database'ini seç
3. Üstte **Import** sekmesi
4. **Choose File** → `PRODUCTION_DATABASE.sql` seç
5. En altta **Import** butonuna tıkla
6. ✅ "Import has been successfully finished" görmeli

---

## ⚙️ ADIM 3: .env DOSYASI OLUŞTUR

### 3.1 File Manager'da
1. `public_html/backend/` klasörüne git
2. **+ File** butonuna tıkla
3. Dosya adı: `.env`
4. Create New File
5. Dosyaya sağ tık → **Edit**

### 3.2 İçeriği Yapıştır
`ENV_PRODUCTION_READY.txt` dosyasının içeriğini kopyala yapıştır:

```env
APP_NAME="Ripe Home"
APP_ENV=production
APP_KEY=base64:gFiTn3IjFtrm+SPJ+J8yhoAo/S0+DZQv8IheAiPIjqA=
APP_DEBUG=false
APP_TIMEZONE=Europe/Istanbul
APP_URL=https://ripehome.com.tr
ASSET_URL=https://ripehome.com.tr
APP_LOCALE=tr
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=tr_TR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ripehome_ripe
DB_USERNAME=ripehome_ripe
DB_PASSWORD=ahm20685485

SESSION_DRIVER=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

LOG_CHANNEL=stack
LOG_LEVEL=error

(... geri kalanı ENV_PRODUCTION_READY.txt'den)
```

**ÖNEMLİ:** Database şifresini kendi belirlediğin şifre ile değiştir!

3. **Save Changes**

---

## 🔗 ADIM 4: STORAGE LİNK (cPanel Terminal)

### Yöntem 1: Terminal Varsa (önerilen)
1. cPanel → **Terminal**
2. Şu komutları çalıştır:

```bash
cd public_html/backend
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chmod -R 755 storage/app/public
```

### Yöntem 2: Terminal Yoksa (SSH)
Hosting firman SSH veriyorsa:
1. SSH ile bağlan (PuTTY veya Terminal)
2. Yukarıdaki komutları çalıştır

### Yöntem 3: Manuel Link (Terminal yoksa)
1. File Manager → `backend/public/` klasörüne git
2. **+ Folder** → `storage` adında klasör oluştur
3. **ÖNEMLİ:** Bu çözüm tam çalışmayabilir, hosting desteğinden "storage link" oluşturmalarını iste

---

## 📄 ADIM 5: .htaccess DOSYALARI

### 5.1 Root .htaccess (public_html/.htaccess)
1. File Manager → `public_html/` klasörüne git
2. `.htaccess` dosyası varsa düzenle, yoksa oluştur
3. İçeriği `PUBLIC_HTML_HTACCESS_PRODUCTION.txt`'den kopyala yapıştır

**Temel routing:**
```apache
RewriteEngine On

# HTTPS zorunlu
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# API istekleri
RewriteCond %{REQUEST_URI} ^/api/
RewriteRule ^(.*)$ backend/public/$1 [L,QSA]

# Storage istekleri
RewriteCond %{REQUEST_URI} ^/storage/
RewriteRule ^(.*)$ backend/public/$1 [L,QSA]

# Admin istekleri
RewriteCond %{REQUEST_URI} ^/admin
RewriteRule ^(.*)$ backend/public/$1 [L,QSA]

# Frontend için
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/api/
RewriteCond %{REQUEST_URI} !^/storage/
RewriteCond %{REQUEST_URI} !^/admin
RewriteRule ^(.*)$ index.html [L,QSA]
```

4. Save Changes

### 5.2 Backend .htaccess (zaten var)
`backend/public/.htaccess` dosyası Laravel ile gelir, dokunma.

---

## 🔐 ADIM 6: İZİNLER (ÖNEMLİ!)

File Manager'da:

### 6.1 storage klasörü
1. `backend/storage` klasörüne git
2. Sağ tık → **Change Permissions**
3. **755** veya **775** yap (ikinci rakam 7 olmalı)
4. ✅ **Change permissions of all files under..** seç
5. Change

### 6.2 bootstrap/cache
1. `backend/bootstrap/cache` klasörüne git
2. Aynı işlemi tekrarla (755 veya 775)

### 6.3 .env dosyası
1. `backend/.env` dosyasına git
2. Change Permissions → **644**

---

## 🧹 ADIM 7: CACHE TEMİZLE

### Terminal Varsa:
```bash
cd public_html/backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan config:cache
```

### Terminal Yoksa:
1. File Manager → `backend/bootstrap/cache/` klasörüne git
2. `config.php` dosyasını sil (varsa)
3. `routes-v7.php` dosyasını sil (varsa)

---

## ✅ ADIM 8: TEST ET

### 8.1 Ana Sayfa
Tarayıcıda aç: `https://ripehome.com.tr`
- ✅ Frontend yüklenmeli

### 8.2 API Test
Tarayıcıda aç: `https://ripehome.com.tr/api/categories`
- ✅ JSON response görmeli

### 8.3 Admin Panel
Tarayıcıda aç: `https://ripehome.com.tr/admin`
- ✅ Login sayfası görmeli

### 8.4 Görseller
- ✅ Kategori görselleri görünmeli
- ✅ Ürün görselleri görünmeli

---

## 🐛 SORUN GİDERME

### Hata: 500 Internal Server Error

#### Çözüm 1: Log Kontrol
1. File Manager → `backend/storage/logs/laravel.log`
2. Dosyayı aç, son satırları oku
3. Hatayı bana gönder

#### Çözüm 2: PHP Versiyon
1. cPanel → **MultiPHP Manager**
2. Domain'i seç
3. PHP versiyonunu **8.2** veya **8.3** yap

#### Çözüm 3: .htaccess Devre Dışı Test
1. `public_html/.htaccess` dosyasını geçici adlandır: `.htaccess.backup`
2. Direkt API test et: `https://ripehome.com.tr/backend/public/api/categories`
3. Çalışıyorsa .htaccess sorunu

### Hata: Görseller 404

```bash
# Terminal'de:
cd public_html/backend
ls -la public/storage
```

Link varsa → sembolik link çalışıyor ✅
Link yoksa → `php artisan storage:link` çalıştır

### Hata: Database Connection

1. phpMyAdmin'e gir
2. Database var mı kontrol et
3. `.env` dosyasında:
   - `DB_DATABASE` doğru mu?
   - `DB_USERNAME` doğru mu?
   - `DB_PASSWORD` doğru mu?

---

## 🎯 HIZLI KONTROL LİSTESİ

cPanel'de şunları kontrol et:

- [ ] **File Manager** → `backend` klasörü yüklü
- [ ] **File Manager** → `dist` içerikleri `public_html/`'da
- [ ] **MySQL Databases** → `ripehome_ripe` oluşturuldu
- [ ] **MySQL Databases** → Kullanıcı eklendi ve ALL PRIVILEGES verildi
- [ ] **phpMyAdmin** → SQL import edildi, tablolar görünüyor
- [ ] **File Manager** → `backend/.env` oluşturuldu
- [ ] **File Manager** → `storage` izinleri 755/775
- [ ] **File Manager** → `bootstrap/cache` izinleri 755/775
- [ ] **Terminal/SSH** → `php artisan storage:link` çalıştırıldı
- [ ] **File Manager** → `.htaccess` routing ayarlandı
- [ ] **MultiPHP Manager** → PHP 8.2+ seçili
- [ ] **Tarayıcı** → `https://ripehome.com.tr` çalışıyor
- [ ] **Tarayıcı** → `https://ripehome.com.tr/admin` login sayfası açılıyor

---

## 📞 YARDIM

Sorun yaşıyorsan:

1. **Log dosyasını kontrol et:**
   - File Manager → `backend/storage/logs/laravel.log`
   - Son 50 satırı kopyala gönder

2. **Test URL'leri:**
   - `https://ripehome.com.tr/api/categories`
   - Ne döndüğünü söyle

3. **Hosting bilgisi:**
   - Terminal var mı?
   - SSH erişimi var mı?
   - PHP versiyonu nedir?

---

**NOT:** Terminal yoksa hosting desteğinden "storage:link" oluşturmalarını isteyebilirsin!

