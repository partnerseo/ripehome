# 🖼️ Görsel Görünme Sorunu - Çözüm Adımları

## ✅ Yapılan Düzeltmeler

1. **CORS Ayarı Güncellendi**
   - `storage/*` path'i CORS'a eklendi
   - Görseller artık frontend'den erişilebilir

2. **Cache Temizlendi**
   - Laravel cache temizlendi
   - Config cache temizlendi

---

## 🔍 Kontrol Adımları

### 1. Backend Kontrol

```bash
# Görsellerin varlığını kontrol et:
ls backend/storage/app/public/products/

# Symbolic link kontrol:
ls -la backend/public/storage

# Test URL (tarayıcıda açın):
http://localhost:8000/storage/products/2-kat-batik-bornoz/_RNC7608.jpg
```

### 2. API Kontrol

```bash
# API'den dönen görselleri kontrol et:
curl http://localhost:8000/api/products | grep "images"
```

### 3. Frontend Kontrol

Tarayıcıda aşağıdaki adımları yapın:

1. **http://localhost:5173/** adresine gidin
2. **F12** ile Developer Tools'u açın
3. **Console** sekmesini kontrol edin
4. **Network** sekmesinde görsellerin yüklenip yüklenmediğini kontrol edin

---

## 🐛 Olası Sorunlar ve Çözümleri

### Sorun 1: 404 Not Found
**Çözüm:** Storage link oluşturun
```bash
cd backend
php artisan storage:link
```

### Sorun 2: CORS Hatası
**Çözüm:** Zaten düzeltildi, cache temizleyin
```bash
cd backend
php artisan config:clear
php artisan cache:clear
```

### Sorun 3: Görseller Eski Import'tan
**Çözüm:** Yeni düzleştirilmiş klasörden import yapın
```bash
# Veritabanını sıfırla ve yeni import yap:
cd backend
php artisan migrate:fresh --seed

# Veya sadece yeni seeder çalıştır:
php artisan db:seed --class=ImportProductsFromFolderSeeder
```

### Sorun 4: Mixed Content (HTTP/HTTPS)
**Eğer:**
- Backend HTTPS kullanıyorsa
- Frontend HTTP kullanıyorsa

**Çözüm:** İkisini de aynı protokolde (HTTP veya HTTPS) çalıştırın

---

## 🚀 Tavsiye Edilen Çözüm

Eğer görseller hala görünmüyorsa:

1. **Yeni Import Yapın:**
```bash
cd /Users/ahmetalkan/Downloads/ripehome

# Düzleştirilmiş klasörden import yap:
cd backend
php artisan migrate:fresh --seed --class=ImportProductsFromFolderSeeder
```

2. **Sunucuları Yeniden Başlatın:**
```bash
# Terminal 1 - Backend:
cd backend
php artisan serve

# Terminal 2 - Frontend:
cd /Users/ahmetalkan/Downloads/ripehome
npm run dev
```

3. **Tarayıcıda Test Edin:**
   - http://localhost:5173/
   - F12 → Console → Hata var mı?
   - F12 → Network → Görseller yükleniyor mu?

---

## 📝 Not

Görseller şu URL formatında olmalı:
```
http://localhost:8000/storage/products/{kategori-slug}/{gorsel-adi}.jpg
```

Örnek:
```
http://localhost:8000/storage/products/2-kat-batik-bornoz/_RNC7608.jpg
```

