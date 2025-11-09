# 🧹 Cache ve Çerez Temizleme

## 🔍 Mevcut Durum

**Frontend:**
- Port: `5173` (Vite)
- API URL: `http://localhost:8000/api`

**Backend:**
- Port: `8000` (Laravel)
- API Endpoint: `/api/*`

---

## 🧹 Çözüm 1: Cache ve Çerezleri Temizle

### 1. Backend Cache Temizle

```bash
cd backend

# Laravel cache'i temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Opcache temizle (varsa)
php artisan optimize:clear
```

---

### 2. Tarayıcı Cache ve Çerezleri Temizle

#### Chrome/Edge:
```
1. Siteyi aç: http://localhost:5173/
2. F12 (DevTools)
3. Application tab
4. Storage → Clear site data
5. Sayfayı yenile: Ctrl+Shift+R (Windows) veya Cmd+Shift+R (Mac)
```

#### Manuel:
```
1. Tarayıcı ayarları
2. Privacy/Gizlilik
3. Clear browsing data / Tarama verilerini temizle
4. Son 1 saat seç
5. Cookies ve Cache seç
6. Temizle
```

---

## 🔄 Çözüm 2: Farklı Portlarda Çalıştır

### Backend'i Farklı Portta Başlat

```bash
cd backend

# Port 8080'de çalıştır
php artisan serve --port=8080
```

**Sonra frontend'i güncelle:**

`src/lib/api.ts`:
```typescript
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080/api';
```

---

### Frontend'i Farklı Portta Başlat

```bash
cd /Users/ahmetalkan/Downloads/ripehome

# Port 3000'de çalıştır
npm run dev -- --port 3000
```

**Sonra aç:**
```
http://localhost:3000/
```

---

## 🔄 Çözüm 3: Servisleri Yeniden Başlat

### 1. Tüm Servisleri Durdur

**Terminalde çalışan tüm Laravel ve Vite process'lerini durdur:**

```bash
# Mac/Linux:
killall php
killall node

# Veya manuel olarak Ctrl+C ile durdur
```

---

### 2. Backend'i Temiz Başlat

```bash
cd backend

# Cache temizle
php artisan optimize:clear

# Database'i kontrol et
php artisan migrate:status

# Serve başlat
php artisan serve
```

**Konsol çıktısı:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

---

### 3. Frontend'i Temiz Başlat

**Yeni terminal:**
```bash
cd /Users/ahmetalkan/Downloads/ripehome

# Node modules cache temizle (opsiyonel)
rm -rf node_modules/.vite

# Dev server başlat
npm run dev
```

**Konsol çıktısı:**
```
VITE v5.x.x  ready in 500 ms

➜  Local:   http://localhost:5173/
```

---

## 🧪 Test

### 1. Backend Test

**Yeni terminal:**
```bash
curl http://localhost:8000/api/categories
```

**Beklenen:**
```json
{
  "success": true,
  "data": [...]
}
```

**Hata alırsan:**
```json
curl: (7) Failed to connect to localhost port 8000
```
→ Backend çalışmıyor, yeniden başlat

---

### 2. Frontend Test

**Tarayıcıda:**
```
http://localhost:5173/
```

**DevTools Console (F12):**
```javascript
fetch('http://localhost:8000/api/categories')
  .then(r => r.json())
  .then(console.log)
```

**Beklenen:**
```
{success: true, data: Array(13)}
```

---

## 🐛 Sorun Giderme

### Sorun: "Failed to fetch"

**Sebep:** Backend çalışmıyor veya CORS hatası

**Çözüm:**
```bash
cd backend

# Backend çalışıyor mu?
curl http://localhost:8000/api/ping

# CORS ayarlarını kontrol et
cat config/cors.php
```

---

### Sorun: "ERR_CONNECTION_REFUSED"

**Sebep:** Port meşgul veya servis çalışmıyor

**Çözüm:**
```bash
# Port kullanımda mı?
lsof -i :8000
lsof -i :5173

# Meşgulse öldür
kill -9 <PID>
```

---

### Sorun: Kategori görselleri yüklenmiyor

**Sebep:** Storage link eksik

**Çözüm:**
```bash
cd backend
php artisan storage:link
```

---

## 🚀 Hızlı Çözüm (Tümü)

**Tüm cache'i temizle ve yeniden başlat:**

```bash
# Backend
cd backend
php artisan optimize:clear
php artisan storage:link
php artisan serve &

# Frontend (yeni terminal)
cd /Users/ahmetalkan/Downloads/ripehome
rm -rf node_modules/.vite
npm run dev
```

**Tarayıcıda:**
1. `http://localhost:5173/` aç
2. F12 → Application → Clear site data
3. Cmd+Shift+R (hard refresh)

---

## 📋 Checklist

- [ ] Backend çalışıyor: `curl http://localhost:8000/api/categories`
- [ ] Frontend çalışıyor: `http://localhost:5173/`
- [ ] Laravel cache temiz: `php artisan optimize:clear`
- [ ] Tarayıcı cache temiz: `Cmd+Shift+R`
- [ ] Storage link var: `php artisan storage:link`
- [ ] CORS ayarları doğru: `backend/config/cors.php`
- [ ] API URL doğru: `src/lib/api.ts` → `http://localhost:8000/api`

---

## 💡 İpuçları

1. **Tarayıcı Private/Incognito Modda Test Et:**
   - Çerez sorunlarını bypass eder
   - Cmd+Shift+N (Chrome) veya Cmd+Shift+P (Firefox)

2. **Farklı Tarayıcıda Dene:**
   - Chrome → Safari veya Firefox

3. **Network Tab'ı İncele:**
   - F12 → Network
   - `/api/categories` isteğine bak
   - Status code: 200 ✅, 500/404/CORS ❌

---

**🎯 Hala sorun varsa, konsol çıktısını ve network tab'ı paylaş!**

