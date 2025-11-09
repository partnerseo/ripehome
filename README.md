# RipeHome - Luxury Home Textiles

Modern ev tekstili e-ticaret platformu. React, TypeScript, Laravel ve Filament ile geliştirilmiştir.

## 🚀 Kurulum

### Frontend

```bash
# Bağımlılıkları yükle
npm install

# Geliştirme sunucusunu başlat
npm run dev

# Production build
npm run build
```

### Backend

```bash
cd backend

# Bağımlılıkları yükle
composer install

# .env dosyasını oluştur
cp .env.example .env

# Uygulama anahtarını oluştur
php artisan key:generate

# Veritabanını oluştur
php artisan migrate --seed

# Sunucuyu başlat
php artisan serve
```

## 🖼️ Ürün Hazırlama (Otomatik Pipeline)

Ürün görselleri genellikle 3-5 MB boyutundadır ve karmaşık klasör yapısındadır. Bu sistem görselleri otomatik olarak optimize eder ve düzleştirir.

### 📦 Tek Komut İle Hazırlık

```bash
# 1. Görselleri public/ÜRÜNLER/ klasörüne koyun
# 2. Otomatik hazırla (optimize + düzleştir):
npm run prepare-products

# 3. Backend'e aktar:
cd backend
php artisan migrate:fresh --seed
```

### 🔧 Adım Adım Kullanım

#### 1. Görselleri Yerleştir

```
public/ÜRÜNLER/
  2 KAT BATİK BORNOZ/
    BATİK BORNOZ ANTRASİT/
      _RNC7608.jpg
      _RNC7613.jpg
    BATİK BORNOZ MAVİ/
      _RNC7620.jpg
```

#### 2. Optimize Et

```bash
npm run optimize
```

**Çıktı:** `public/ÜRÜNLER-optimized/`
- Görseller 200-500 KB'a düşürülür
- 1920px maksimum genişlik
- Progressive JPEG, %80 kalite

#### 3. Klasör Yapısını Düzleştir

```bash
npm run flatten
```

**Çıktı:** `public/ÜRÜNLER-temiz/`

```
ÜRÜNLER-temiz/
  BATIK BORNOZ/
    antrasit-1.jpg
    antrasit-2.jpg
    mavi-1.jpg
```

- "2 KAT", "3 KAT" önekleri kaldırılır
- Alt klasörler renk isimlerine dönüştürülür
- Görseller `renk-numara.jpg` formatında isimlendirilir

#### 4. Backend'e Aktar

```bash
cd backend
php artisan db:seed --class=ImportProductsFromFolderSeeder
```

Bu komut:
- ✅ Her klasörü otomatik kategori olarak oluşturur
- ✅ Her görseli otomatik ürün olarak ekler
- ✅ Renkleri otomatik tag olarak oluşturur
- ✅ Görselleri `storage/app/public/products/` klasörüne kopyalar
- ✅ Ürün isimlerini otomatik formatlar

#### 5. Kontrol Et

- **Admin Panel:** http://localhost:8000/admin
- **Kategoriler:** http://localhost:8000/admin/categories
- **Ürünler:** http://localhost:8000/admin/products

## 📊 Optimizasyon İstatistikleri

Script çalıştığında şu bilgileri göreceksiniz:

```
🖼️  ÜRÜNLER klasörü optimize ediliyor...

📦 Toplam 2899 görsel bulundu

[1/2899] 2 KAT BATİK BORNOZ/BATİK BORNOZ ANTRASİT/_RNC7394.jpg
   📏 Boyut: 4.52 MB
   ✅ 0.38 MB (91.6% küçültme, 4.14 MB kazanç)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✨ Optimize tamamlandı!
✅ Başarılı: 2899/2899
📦 Toplam Öncesi: 12458.32 MB
📦 Toplam Sonrası: 1842.17 MB
💾 Kazanılan Alan: 10616.15 MB
📊 Ortalama Küçülme: 85.2%
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

## 🎯 Özellikler

- ✅ Responsive tasarım
- ✅ Modern UI/UX
- ✅ Kategori yönetimi
- ✅ Ürün yönetimi
- ✅ Toptan sipariş sistemi
- ✅ Admin paneli (Filament)
- ✅ RESTful API
- ✅ Görsel optimizasyon

## 🛠️ Teknolojiler

**Frontend:**
- React 18
- TypeScript
- Vite
- Tailwind CSS
- React Router DOM
- Lucide Icons

**Backend:**
- Laravel 11
- Filament v3
- SQLite
- RESTful API

## 📁 Proje Yapısı

```
ripehome/
├── src/                    # Frontend kaynak kodları
│   ├── components/         # React bileşenleri
│   ├── pages/             # Sayfa bileşenleri
│   ├── lib/               # API servisleri
│   └── types/             # TypeScript tip tanımları
├── backend/               # Laravel backend
│   ├── app/
│   │   ├── Models/        # Eloquent modelleri
│   │   ├── Http/          # Controller'lar
│   │   └── Filament/      # Admin paneli
│   └── database/          # Migration ve seeder'lar
├── public/                # Statik dosyalar
│   └── ÜRÜNLER/          # Ürün görselleri
└── scripts/              # Yardımcı scriptler
    └── optimize-images.js # Görsel optimizasyon
```

## 🔗 Linkler

- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api
- **Admin Panel:** http://localhost:8000/admin

## 👤 Admin Girişi

- **Email:** admin@ripehome.com
- **Şifre:** password

## 📝 Lisans

Bu proje özel bir projedir.

