# 🚀 Hızlı Çözüm: Production'da Görseller Görünmüyor

## ⚡ 5 Dakikada Çözüm

### 1. Backend .env Dosyası (EN ÖNEMLİ!)

```bash
cd backend
nano .env
```

**Bu satırları değiştir:**
```env
APP_URL=https://ripehome.com.tr
ASSET_URL=https://ripehome.com.tr
APP_ENV=production
APP_DEBUG=false
```

### 2. Storage Link Oluştur

```bash
cd backend
php artisan storage:link --force
```

### 3. Cache Temizle ve Yeniden Oluştur

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 4. İzinleri Düzelt

```bash
chmod -R 775 storage bootstrap/cache
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

### 5. Frontend .env Dosyası

```bash
cd .. # root klasöre dön
echo "VITE_API_URL=https://ripehome.com.tr/api" > .env
```

## ✅ Test Et

Tarayıcıda aç:
- https://ripehome.com.tr/api/categories (JSON görmeli)
- https://ripehome.com.tr/storage/products/... (Görsel görmeli)

## 🐛 Hala Çalışmıyor mu?

```bash
# Log'a bak
tail -f backend/storage/logs/laravel.log

# Storage link kontrol
ls -la backend/public/storage
```

**Kırık link varsa:**
```bash
rm backend/public/storage
php artisan storage:link
```

## 📞 Acil Destek Komutları

```bash
# Tek komutla tüm düzeltmeler
cd backend && \
php artisan storage:link --force && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan config:cache && \
chmod -R 775 storage bootstrap/cache && \
chmod -R 755 storage/app/public public/storage && \
echo "✅ Tamamlandı! Tarayıcıda test et."
```

---

**Detaylı bilgi için:** `DEPLOYMENT_CHECKLIST.md` ve `IMAGE_PATH_TROUBLESHOOTING.md`
