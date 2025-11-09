# Laravel 11 Backend API + Admin Panel (Filament v3)

Bu proje, mevcut React web siteniz için Laravel 11 tabanlı backend API ve Filament v3 admin paneli içermektedir.

## 🎨 Admin Panel

**URL:** `http://localhost:8000/admin`

**Giriş Bilgileri:**
- Email: `admin@luxuryhome.com`
- Password: `admin123`

> **⚠️ Admin Giriş Yapamıyorsanız:**
> ```bash
> cd backend
> php artisan tinker
> ```
> Tinker'da:
> ```php
> \App\Models\User::updateOrCreate(
>     ['email' => 'admin@luxuryhome.com'],
>     ['name' => 'Admin', 'password' => bcrypt('admin123')]
> );
> exit
> ```

**Özellikler:**
- ✅ Filament v3 Admin Panel
- ✅ Türkçe Dil Desteği
- ✅ Modern ve Responsive Tasarım
- ✅ Brand: "Luxury Home Textiles Admin"
- ✅ Primary Color: #2B5F82
- ✅ Collapsible Sidebar

### 📦 Admin Panel Modülleri

**İçerik Yönetimi:**
- **Kategoriler** (`/admin/categories`) - Ürün kategorileri yönetimi
- **Etiketler** (`/admin/tags`) - Ürün etiketleri ve renkleri
- **Ürünler** (`/admin/products`) - Ürün CRUD, çoklu görseller, özellikler, SEO
- **Anasayfa Slider** (`/admin/home-sliders`) - Anasayfa kaydırıcı görselleri
- **Öne Çıkan Bölümler** (`/admin/featured-sections`) - Öne çıkan içerik bölümleri
- **Sayfalar** (`/admin/pages`) - Statik sayfa yönetimi

**Diğer:**
- **İletişim Mesajları** (`/admin/contact-messages`) - Gelen mesajları görüntüleme
- **Site Ayarları** (`/admin/settings`) - Logo, sosyal medya, iletişim bilgileri

## 📁 Klasör Yapısı

```
ripehome/
├── backend/          # Laravel 11 API + Admin Panel
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   │   ├── api.php   # API endpoint'leri
│   │   └── web.php   # Admin panel route'ları
│   └── ...
└── (React Frontend - mevcut)
```

## 🌐 Frontend Routing Yapısı

Bu proje `/ripehome/` alt dizininde çalışacak şekilde yapılandırılmıştır.

**Frontend URL'leri:**
- Anasayfa: `http://localhost:5174/ripehome/`
- Kategori: `http://localhost:5174/ripehome/category/havlu`
- Ürün Detay: `http://localhost:5174/ripehome/product/[slug]`
- Toptan Sipariş: `http://localhost:5174/ripehome/toptan-siparis`

**API URL:**
- Base URL: `http://localhost:8000/api`
- API'de `/ripehome` prefix'i YOK, sadece frontend'de var

**Yapılandırma:**
- `vite.config.ts`: `base: '/ripehome/'` ✓
- `App.tsx`: `<BrowserRouter basename="/ripehome">` ✓
- Tüm Link'ler: `to="/path"` formatında (basename otomatik eklenir) ✓

## 🚀 Kurulum

### 1. Backend Kurulumu

```bash
cd backend

# Bağımlılıkları yükle
composer install

# .env dosyasını oluştur
cp .env.example .env

# Uygulama anahtarı oluştur
php artisan key:generate

# MySQL veritabanını yapılandır (.env dosyasında)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

# Veritabanı migration'larını çalıştır
php artisan migrate

# Test data ekle (opsiyonel ama önerilir)
php artisan db:seed

# Development sunucusunu başlat
php artisan serve
# Backend: http://localhost:8000
```

### 2. Test Data Ekleme

Backend ile birlikte örnek veriler gelir. Test data eklemek için:

```bash
cd backend

# Sadece seed (mevcut veriler korunur)
php artisan db:seed

# Ya da migration + seed (veritabanını sıfırlar ve yeniden oluşturur!)
php artisan migrate:fresh --seed
```

**Test Data İçeriği:**
- ✅ 5 Kategori (Havlu, Nevresim, Bornoz, Yatak Örtüsü, Çocuk)
- ✅ 5 Etiket (renkli)
- ✅ 6 Ürün (detaylı açıklamalar ve özelliklerle)
- ✅ 4 Sayfa (Hakkımızda, Gizlilik, Kargo/İade, Kullanım Koşulları)
- ✅ 3 Anasayfa Slider
- ✅ 4 Öne Çıkan Bölüm
- ✅ 1 Site Ayarları kaydı

**Not:** `migrate:fresh --seed` komutu mevcut tüm verileri siler! Production'da kullanmayın.

### 3. Frontend CORS Ayarları

Backend `.env` dosyasında frontend URL'inizi belirtin:

```env
FRONTEND_URL=http://localhost:5173
```

Production için:
```env
FRONTEND_URL=https://yourdomain.com
```

## 🔌 API Kullanımı

### Base URL

**Development:**
```
http://localhost:8000/api
```

**Production:**
```
https://api.yourdomain.com/api
```

### 📋 Public API Endpoints

Tüm endpoint'ler public'tir (authentication gerektirmez) ve `is_active=true` olan kayıtları döner.

#### **Kategoriler**

**Tüm Kategoriler:**
```http
GET /api/categories

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Havlu",
      "slug": "havlu",
      "description": "Yumuşak ve emici havlular",
      "image": "http://localhost:8000/storage/categories/havlu.jpg",
      "order": 1,
      "products_count": 12
    }
  ]
}
```

**Tekil Kategori:**
```http
GET /api/categories/{slug}

Example: GET /api/categories/havlu
```

#### **Ürünler**

**Tüm Ürünler (Paginated):**
```http
GET /api/products

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Bambu Havlu Seti",
      "slug": "bambu-havlu-seti",
      "description": "<p>Premium bambu havlu</p>",
      "images": [
        "http://localhost:8000/storage/products/img1.jpg",
        "http://localhost:8000/storage/products/img2.jpg"
      ],
      "category": {
        "id": 1,
        "name": "Havlu",
        "slug": "havlu"
      },
      "tags": [
        {"id": 1, "name": "Organik", "color": "#10B981"}
      ],
      "features": [
        {
          "icon": "heroicon-o-star",
          "title": "%100 Pamuk",
          "description": "Organik pamuktan üretilmiştir"
        }
      ],
      "is_featured": true,
      "order": 1
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 58
  }
}
```

**Tekil Ürün:**
```http
GET /api/products/{slug}

Example: GET /api/products/bambu-havlu-seti
```

**Kategoriye Göre Ürünler:**
```http
GET /api/products/category/{category_slug}

Example: GET /api/products/category/havlu
```

#### **Sayfalar**

**Sayfa İçeriği:**
```http
GET /api/pages/{slug}

Example: GET /api/pages/hakkimizda

Response:
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Hakkımızda",
    "slug": "hakkimizda",
    "content": "<p>Şirket hikayemiz...</p>",
    "meta_title": "Hakkımızda - Luxury Home",
    "meta_description": "..."
  }
}
```

#### **Anasayfa Slider**

```http
GET /api/home-sliders

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Yeni Sezon Koleksiyonu",
      "subtitle": "İndirimdeki Ürünleri Keşfedin",
      "button_text": "Alışverişe Başla",
      "button_link": "/products",
      "image": "http://localhost:8000/storage/sliders/slider1.jpg",
      "order": 1
    }
  ]
}
```

#### **Öne Çıkan Bölümler**

```http
GET /api/featured-sections

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Ücretsiz Kargo",
      "description": "500 TL ve üzeri alışverişlerde",
      "image": "...",
      "icon": "heroicon-o-truck",
      "link": "/shipping",
      "order": 1
    }
  ]
}
```

#### **Site Ayarları**

```http
GET /api/settings

Response:
{
  "success": true,
  "data": {
    "logo": "http://localhost:8000/storage/settings/logo.png",
    "favicon": "http://localhost:8000/storage/settings/favicon.ico",
    "phone": "+90 555 123 4567",
    "email": "info@luxuryhome.com",
    "address": "İstanbul, Türkiye",
    "social_media": {
      "facebook": "https://facebook.com/...",
      "instagram": "https://instagram.com/...",
      "twitter": "https://twitter.com/...",
      "linkedin": "https://linkedin.com/..."
    },
    "footer_text": "© 2025 Luxury Home Textiles"
  }
}
```

#### **İletişim Formu**

```http
POST /api/contact

Request Body:
{
  "name": "Ahmet Yılmaz",
  "email": "ahmet@example.com",
  "subject": "Ürün Sorgusu",
  "message": "Merhaba, ürünleriniz hakkında bilgi almak istiyorum."
}

Response (201):
{
  "success": true,
  "message": "Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.",
  "data": {
    "id": 123
  }
}

Error Response (422):
{
  "success": false,
  "message": "Lütfen tüm alanları doldurunuz",
  "errors": {
    "email": ["Geçerli bir e-posta adresi giriniz"]
  }
}
```

#### **Health Check (Public)**
```http
GET /api/health

Response:
{
  "status": "ok",
  "message": "API is running",
  "timestamp": "2025-10-15T07:00:00+00:00"
}
```

### 🔐 Protected Endpoints (Authentication Required)

```http
GET /api/user
Headers:
  Authorization: Bearer {token}

Response:
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com"
}
```

### 💻 Frontend Kullanım Örnekleri

#### React/TypeScript

```typescript
// services/api.ts
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

// Kategorileri çek
export async function getCategories() {
  const response = await fetch(`${API_URL}/categories`);
  const data = await response.json();
  return data;
}

// Ürünleri çek
export async function getProducts(page = 1) {
  const response = await fetch(`${API_URL}/products?page=${page}`);
  return response.json();
}

// Tekil ürün
export async function getProduct(slug: string) {
  const response = await fetch(`${API_URL}/products/${slug}`);
  return response.json();
}

// Kategoriye göre ürünler
export async function getProductsByCategory(categorySlug: string) {
  const response = await fetch(`${API_URL}/products/category/${categorySlug}`);
  return response.json();
}

// Sayfa içeriği
export async function getPage(slug: string) {
  const response = await fetch(`${API_URL}/pages/${slug}`);
  return response.json();
}

// Anasayfa slider
export async function getHomeSliders() {
  const response = await fetch(`${API_URL}/home-sliders`);
  return response.json();
}

// Öne çıkan bölümler
export async function getFeaturedSections() {
  const response = await fetch(`${API_URL}/featured-sections`);
  return response.json();
}

// Site ayarları
export async function getSettings() {
  const response = await fetch(`${API_URL}/settings`);
  return response.json();
}

// İletişim formu gönder
export async function submitContact(data: {
  name: string;
  email: string;
  subject?: string;
  message: string;
}) {
  const response = await fetch(`${API_URL}/contact`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  });
  return response.json();
}
```

#### React Component Örneği

```tsx
// components/ProductList.tsx
import { useEffect, useState } from 'react';
import { getProducts } from '../services/api';

export function ProductList() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchProducts() {
      try {
        const response = await getProducts();
        if (response.success) {
          setProducts(response.data);
        }
      } catch (error) {
        console.error('Ürünler yüklenirken hata:', error);
      } finally {
        setLoading(false);
      }
    }
    
    fetchProducts();
  }, []);

  if (loading) return <div>Yükleniyor...</div>;

  return (
    <div className="grid grid-cols-4 gap-4">
      {products.map((product) => (
        <div key={product.id} className="product-card">
          <img src={product.images[0]} alt={product.name} />
          <h3>{product.name}</h3>
          <p>{product.category.name}</p>
        </div>
      ))}
    </div>
  );
}
```

#### İletişim Formu Örneği

```tsx
// components/ContactForm.tsx
import { useState } from 'react';
import { submitContact } from '../services/api';

export function ContactForm() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
  });
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus('loading');

    try {
      const response = await submitContact(formData);
      if (response.success) {
        setStatus('success');
        setFormData({ name: '', email: '', subject: '', message: '' });
        alert(response.message);
      }
    } catch (error) {
      setStatus('error');
      alert('Mesaj gönderilirken hata oluştu');
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <input
        type="text"
        placeholder="Ad Soyad"
        value={formData.name}
        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
        required
      />
      <input
        type="email"
        placeholder="E-posta"
        value={formData.email}
        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
        required
      />
      <input
        type="text"
        placeholder="Konu"
        value={formData.subject}
        onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
      />
      <textarea
        placeholder="Mesajınız"
        value={formData.message}
        onChange={(e) => setFormData({ ...formData, message: e.target.value })}
        required
      />
      <button type="submit" disabled={status === 'loading'}>
        {status === 'loading' ? 'Gönderiliyor...' : 'Gönder'}
      </button>
    </form>
  );
}
```

### 🌐 Frontend .env Ayarları

```env
# .env.development
VITE_API_URL=http://localhost:8000/api

# .env.production
VITE_API_URL=https://api.yourdomain.com/api
```

## 🔐 Authentication (Laravel Sanctum)

Backend, Laravel Sanctum ile API token authentication kullanmaktadır.

### Frontend'de API İstekleri

```typescript
// Frontend'de API client örneği (axios)
import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true, // CORS credentials için
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Token eklemek için
apiClient.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Kullanım örneği
const fetchData = async () => {
  try {
    const response = await apiClient.get('/health');
    console.log(response.data);
  } catch (error) {
    console.error('API Error:', error);
  }
};
```

### React ile Entegrasyon

```typescript
// src/services/api.ts
export const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export async function fetchProducts() {
  const response = await fetch(`${API_BASE_URL}/products`);
  return response.json();
}

export async function fetchProductById(id: string) {
  const response = await fetch(`${API_BASE_URL}/products/${id}`);
  return response.json();
}
```

Frontend `.env` dosyası:
```env
VITE_API_URL=http://localhost:8000/api
```

## 📦 Production Deployment

### Backend Sunucu Gereksinimleri

- PHP 8.2 veya üzeri
- MySQL 8.0 veya üzeri
- Composer
- Nginx/Apache

### Production Yapılandırması

1. **Backend `.env` dosyası:**

```env
APP_NAME="Admin Panel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-strong-password

FRONTEND_URL=https://yourdomain.com
```

2. **Optimize edilmiş kurulum:**

```bash
# Production bağımlılıkları
composer install --optimize-autoloader --no-dev

# Cache oluştur
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migration'ları çalıştır
php artisan migrate --force
```

3. **Nginx Yapılandırması:**

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    root /var/www/backend/public;

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

4. **Frontend Production Ayarları:**

```env
# Frontend .env.production
VITE_API_URL=https://api.yourdomain.com/api
```

## 🛠️ Geliştirme

### Yeni API Endpoint Eklemek

```bash
# Controller oluştur
php artisan make:controller Api/ProductController

# routes/api.php dosyasına ekle
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
```

### Model ve Migration Oluşturmak

```bash
# Model + Migration + Controller oluştur
php artisan make:model Product -mc

# Migration dosyasını düzenle ve çalıştır
php artisan migrate
```

### Admin Panel Geliştirme (Filament v3)

**Filament Resource Oluşturma:**
```bash
# Yeni resource oluştur (Model + Migration + Resource)
php artisan make:filament-resource Product --generate

# Sadece resource oluştur
php artisan make:filament-resource Product

# Yeni admin user oluştur
php artisan make:filament-user
```

**Filament Page Oluşturma:**
```bash
php artisan make:filament-page Settings
```

**Filament Widget Oluşturma:**
```bash
php artisan make:filament-widget StatsOverview
```

Admin panel dosyaları:
- `app/Filament/Resources/` - Admin panel resource'ları
- `app/Filament/Pages/` - Custom sayfalar
- `app/Filament/Widgets/` - Dashboard widget'ları
- `app/Providers/Filament/AdminPanelProvider.php` - Panel ayarları

## 📝 Önemli Notlar

1. **Admin Panel:** `http://localhost:8000/admin` - Filament v3 (admin@luxuryhome.com / admin123)
2. **CORS:** Frontend URL'iniz `.env` dosyasında `FRONTEND_URL` olarak tanımlanmalıdır
3. **Database:** Development'da SQLite, production'da MySQL kullanın
4. **Security:** Production'da `APP_DEBUG=false` olmalıdır
5. **API Prefix:** Tüm API route'ları `/api/*` prefix'i ile gelir
6. **Authentication:** Laravel Sanctum token-based auth kullanır
7. **Timezone:** Europe/Istanbul - config/app.php
8. **Locale:** tr (Türkçe) - config/app.php

## 🔧 Yararlı Komutlar

```bash
# Development sunucusu
php artisan serve

# Database
php artisan migrate                     # Migration'ları çalıştır
php artisan migrate:fresh               # Veritabanını sıfırla ve migration'ları çalıştır
php artisan migrate:fresh --seed        # Migration + Test data ekle (DİKKAT: Tüm veriyi siler!)
php artisan db:seed                     # Test data ekle
php artisan db:seed --class=CategorySeeder  # Belirli bir seeder çalıştır

# Admin Panel
php artisan make:filament-user          # Yeni admin kullanıcı oluştur
php artisan make:filament-resource Product  # Yeni resource oluştur
php artisan make:filament-page Settings     # Yeni sayfa oluştur
php artisan make:filament-widget Stats      # Yeni widget oluştur

# Route listesi
php artisan route:list
php artisan route:list --path=admin     # Sadece admin route'ları
php artisan route:list --path=api       # Sadece API route'ları

# Cache temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan filament:cache-components   # Filament component cache

# Storage
php artisan storage:link

# Tinker (Laravel REPL)
php artisan tinker

# Test çalıştır
php artisan test
```

## 📞 API Test

```bash
# Health check
curl http://localhost:8000/api/health

# Authenticated request
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost:8000/api/user
```

## 🎯 Sonraki Adımlar

1. **✅ Admin Panel'e giriş yapın:** `http://localhost:8000/admin` (admin@luxuryhome.com / admin123)
2. **✅ Filament Resources hazır:**
   - Kategoriler, Etiketler, Ürünler
   - Anasayfa Slider, Öne Çıkan Bölümler
   - Sayfalar, İletişim Mesajları, Site Ayarları
3. **API endpoint'leri ekleyin:** Frontend için CRUD API'leri
   ```bash
   php artisan make:controller Api/ProductController
   php artisan make:controller Api/CategoryController
   ```
4. **Frontend entegrasyonu:** React'te API service'leri oluşturun
   ```typescript
   // Frontend API client
   const API_URL = 'http://localhost:8000/api';
   export const getProducts = () => fetch(`${API_URL}/products`);
   export const getCategories = () => fetch(`${API_URL}/categories`);
   ```
5. **Authentication:** Login/Register endpoint'leri ekleyin
6. **Logo ekleyin:** Admin panelde Site Ayarları > Logo yükleyin
7. **İçerik ekleyin:** Admin panelden kategoriler, ürünler ve diğer içerikleri ekleyin

## 🛒 Toptan Sipariş Test

### Test Verileri Oluştur

```bash
cd backend
php artisan db:seed --class=WholesaleTestSeeder
```

**Sonuç:**
- ✅ Her kategoriye 2 toptan test ürünü
- ✅ Placeholder görseller
- ✅ Toptan sipariş için özellikler

### Test Et

1. **Frontend URL:**
   ```
   http://localhost:5174/ripehome/toptan-siparis
   ```

2. **Kontrol Listesi:**
   - ✅ Ürünler görünüyor mu?
   - ✅ Görseller yükleniyor mu?
   - ✅ Adet girişi çalışıyor mu?
   - ✅ Sepete ekleme çalışıyor mu?
   - ✅ Form gönderimi çalışıyor mu?

3. **Test Siparişi:**
   - Birkaç üründen adet seç (örn: 50, 100, 200)
   - "Devam Et" tıkla
   - Formu doldur:
     - **Şirket:** Test Tekstil A.Ş.
     - **Yetkili:** Test Kullanıcı
     - **Email:** test@example.com
     - **Tel:** 0555 123 4567
   - Gönder

4. **Admin Panelde Kontrol:**
   ```
   http://localhost:8000/admin/wholesale-orders
   ```
   - Sipariş geldi mi?
   - Ürün bilgileri doğru mu?
   - İletişim bilgileri görünüyor mu?

### Hızlı Test Komutu

```bash
# Tüm verileri sıfırlayıp test verileri ekle
php artisan migrate:fresh --seed
```

Bu komut:
- ✅ Tüm tabloları sıfırlar
- ✅ Kategoriler oluşturur
- ✅ Ürünler ekler (görsellerle birlikte)
- ✅ Toptan test ürünleri ekler
- ✅ Admin kullanıcı oluşturur (admin@luxuryhome.com / admin123)

---

## 🗑️ Demo Verileri Temizleme ve Görsel Güncelleme

### 1. Demo Verileri Sil

Demo kategorileri (Havlu, Nevresim, Bornoz vb.) ve bunlara ait ürünleri temizle:

```bash
cd backend
php artisan db:seed --class=CleanDemoDataSeeder
```

**Silinecek Kategoriler:**
- Havlu
- Nevresim
- Bornoz
- Yatak Örtüsü
- Çocuk Koleksiyonu

**Sonuç:**
```
🗑️  Demo veriler temizleniyor...
  ❌ Siliniyor: Havlu
  ❌ Siliniyor: Nevresim
  ❌ Siliniyor: Bornoz
✅ Demo veriler temizlendi!
   📦 5 kategori silindi
   🛍️  30 ürün silindi
   ⭐ 6 featured product silindi
```

---

### 2. Kategori Görsellerini Güncelle

Her kategoriye, ilk ürününün görselini otomatik ata:

```bash
php artisan db:seed --class=UpdateCategoryImagesSeeder
```

**Nasıl Çalışır:**
- Her kategori için ilk aktif ürünü bulur
- Ürünün ilk görselini kategori görseli olarak atar
- `storage/products/...` yolunu kullanır

**Sonuç:**
```
🖼️  Kategori görselleri güncelleniyor...
  ✅ BATİK BORNOZ: Görsel eklendi
  ✅ JAKARLI KİMONO: Görsel eklendi
  ✅ MÜSLİNLER: Görsel eklendi
✅ Kategori görselleri güncellendi!
   ✅ 19 kategori güncellendi
```

---

### 3. Genel Görselleri Güncelle

Slider ve Featured Section görsellerini ürün görsellerinden güncelle:

```bash
php artisan db:seed --class=UpdateGeneralImagesSeeder
```

**Güncellenecekler:**
- ✅ **Home Sliders:** Anasayfa carousel görselleri
- ✅ **Featured Sections:** Öne çıkan bölüm görselleri

**Sonuç:**
```
🖼️  Genel görseller güncelleniyor...
   📦 20 ürün görseli bulundu
  ✅ Slider 'Yaz Koleksiyonu' güncellendi
  ✅ Featured Section 'Premium Ürünler' güncellendi
✅ Genel görseller güncellendi!
   🎠 3 slider güncellendi
   ⭐ 4 featured section güncellendi
```

---

### 4. Tümünü Bir Komutta Çalıştır

Database'i sıfırlayıp tüm seeders'ı çalıştır (görseller de dahil):

```bash
php artisan migrate:fresh --seed
```

Bu komut şunları yapar:
1. ✅ Tüm tabloları sıfırlar
2. ✅ Kategoriler, ürünler, sliders ekler
3. ✅ **Kategori görsellerini otomatik günceller**
4. ✅ **Genel görselleri otomatik günceller**

> **Not:** `CleanDemoDataSeeder` varsayılan olarak kapalı. Aktif etmek için:
> `backend/database/seeders/DatabaseSeeder.php` dosyasında ilgili satırı uncomment edin.

---

### 5. Sadece Görselleri Güncelle (Verilere Dokunma)

Mevcut verileri koruyup sadece görselleri güncellemek için:

```bash
# Kategori görselleri
php artisan db:seed --class=UpdateCategoryImagesSeeder

# Genel görseller (slider, featured sections)
php artisan db:seed --class=UpdateGeneralImagesSeeder
```

---

### 6. Admin Panelde Kontrol Et

Görsellerin güncellendiğini kontrol et:

```
http://localhost:8000/admin/categories
http://localhost:8000/admin/home-sliders
http://localhost:8000/admin/featured-sections
```

---

### 7. Frontend'de Kontrol Et

Kategori görsellerinin sitede göründüğünü kontrol et:

```
http://localhost:5173/
```

**Beklenen Sonuç:**
- ✅ Kategori kartlarında görseller var
- ✅ Hero slider'lar görselli
- ✅ Featured sections görselli

---

## 📚 Dokümantasyon

- [Laravel 11 Docs](https://laravel.com/docs/11.x)
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [Filament v3 Docs](https://filamentphp.com/docs/3.x/panels/installation)
- [Filament Resources](https://filamentphp.com/docs/3.x/panels/resources)
- [API Resources](https://laravel.com/docs/11.x/eloquent-resources)

Pro