# Laravel 11 Backend API

## Hızlı Başlangıç

```bash
# Kurulum
composer install
cp .env.example .env
php artisan key:generate

# MySQL Veritabanı Ayarları (.env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

# Migration
php artisan migrate

# Sunucuyu Başlat
php artisan serve
# http://localhost:8000
```

## API Endpoints

- **Base URL:** `http://localhost:8000/api`
- **Health Check:** `GET /api/health`
- **User Info:** `GET /api/user` (Protected)

## CORS Ayarları

`.env` dosyasında frontend URL'inizi belirtin:
```env
FRONTEND_URL=http://localhost:5173
```

## Frontend Entegrasyonu

```typescript
// React/TypeScript
const API_URL = 'http://localhost:8000/api';

fetch(`${API_URL}/health`)
  .then(res => res.json())
  .then(data => console.log(data));
```

## Daha Fazla Bilgi

Ana dizindeki `BACKEND_README.md` dosyasına bakın.


