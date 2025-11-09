# ✅ Cache Temizlendi + Yeni Portlara Geçiliyor

## ✅ Tamamlanan İşlemler

1. ✅ Laravel cache temizlendi
2. ✅ Frontend API URL güncellendi: `http://localhost:8080/api`
3. ✅ Dosyalar hazır

---

## 🚀 Şimdi Servisleri Manuel Başlat

### 1. Backend'i Port 8080'de Başlat

**Yeni Terminal Aç (Terminal 1):**
```bash
cd /Users/ahmetalkan/Downloads/ripehome/backend
php artisan serve --port=8080
```

**Beklenen Çıktı:**
```
Starting Laravel development server: http://127.0.0.1:8080
[Sun Nov 10 2024] PHP 8.x Development Server (http://127.0.0.1:8080) started
```

**✅ Backend Hazır!** → `http://localhost:8080`

---

### 2. Frontend'i Başlat

**Yeni Terminal Aç (Terminal 2):**
```bash
cd /Users/ahmetalkan/Downloads/ripehome
npm run dev
```

**Beklenen Çıktı:**
```
VITE v5.x.x  ready in 500 ms

➜  Local:   http://localhost:5173/
```

**✅ Frontend Hazır!** → `http://localhost:5173/`

---

## 🧪 Test Et

### 1. Backend API Test

**Yeni Terminal (Terminal 3):**
```bash
curl http://localhost:8080/api/categories
```

**Beklenen:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "2 KAT BATİK BORNOZ",
      "slug": "2-kat-batik-bornoz",
      ...
    }
  ]
}
```

---

### 2. Frontend Test

**Tarayıcıda aç:**
```
http://localhost:5173/
```

**F12 (DevTools) → Console:**

Şunları göreceksin:
```
✅ Categories API response: ...
📦 Total categories: 13
  - 2 KAT BATİK BORNOZ: 42 products
  - 2 KAT JAKARLI KİMONO: 754 products
  ...
```

---

### 3. Hard Refresh Yap

**Tarayıcıda:**
- **Mac:** `Cmd + Shift + R`
- **Windows:** `Ctrl + Shift + R`

Veya:
- F12 → Application → Clear site data → Clear all
- Sayfayı yenile

---

## 🔍 Sorun Giderme

### Backend Başlamıyor

**Hata:** `Address already in use`

**Çözüm:**
```bash
# Port 8080 meşgul mü?
lsof -i :8080

# Process'i öldür
kill -9 <PID>

# Yeniden başlat
php artisan serve --port=8080
```

---

### Frontend Başlamıyor

**Hata:** `Port 5173 is already in use`

**Çözüm:**
```bash
# Port 5173 meşgul mü?
lsof -i :5173

# Process'i öldür
kill -9 <PID>

# Yeniden başlat
npm run dev
```

---

### API Bağlanmıyor

**F12 → Network → /api/categories:**
- **Status 0 (Failed):** Backend çalışmıyor
- **Status 404:** Route yanlış
- **Status 500:** Backend hatası

**Çözüm:**
```bash
cd backend
php artisan route:list | grep categories
```

Şunu göreceksin:
```
GET|HEAD  api/categories ......... CategoryController@index
```

---

## 📋 Port Özeti

| Servis | Port | URL |
|--------|------|-----|
| **Backend** | 8080 | `http://localhost:8080` |
| **Backend API** | 8080 | `http://localhost:8080/api` |
| **Frontend** | 5173 | `http://localhost:5173/` |

---

## 🎯 Checklist

**Backend (Terminal 1):**
- [ ] `cd /Users/ahmetalkan/Downloads/ripehome/backend`
- [ ] `php artisan serve --port=8080`
- [ ] Görüyorsun: "Starting Laravel development server: http://127.0.0.1:8080"

**Frontend (Terminal 2):**
- [ ] `cd /Users/ahmetalkan/Downloads/ripehome`
- [ ] `npm run dev`
- [ ] Görüyorsun: "Local: http://localhost:5173/"

**Test (Terminal 3):**
- [ ] `curl http://localhost:8080/api/categories`
- [ ] JSON response görüyorsun

**Tarayıcı:**
- [ ] `http://localhost:5173/` aç
- [ ] F12 → Console → log'ları kontrol et
- [ ] Kategoriler görünüyor

---

## 💡 İpucu: Gizli Sekme

Cache sorunlarını bypass etmek için:

**Chrome:**
```
Cmd + Shift + N
http://localhost:5173/
```

**Safari:**
```
Cmd + Shift + P
http://localhost:5173/
```

---

## 📝 Değiştirilen Dosyalar

1. ✅ `src/lib/api.ts` → API URL: `http://localhost:8080/api`
2. ✅ Backend cache temizlendi
3. ✅ Yeni raporlar oluşturuldu

---

**🚀 Her şey hazır! Şimdi yukarıdaki komutları 2 terminalde çalıştır.**

**Terminal 1:** Backend (Port 8080)
**Terminal 2:** Frontend (Port 5173)

**Sonra tarayıcıda:** `http://localhost:5173/`

