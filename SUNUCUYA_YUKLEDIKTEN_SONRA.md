# 🚀 Sunucuya Yükledikten Sonra YAPILMASI GEREKENLER

## ⚠️ ÖNEMLİ: Bu 3 adımı MUTLAKA yapmalısınız!

Backend ve dist dosyalarını yükleyip SQL'i import ettikten sonra:

---

## 1️⃣ Backend .env Dosyasını Düzenle (2 dakika)

```bash
# Sunucuda backend klasörüne git
cd public_html/backend  # veya nerede ise

# .env dosyasını düzenle
nano .env
```

**Bu 2 satırı değiştir:**
```env
APP_URL=https://ripehome.com.tr
ASSET_URL=https://ripehome.com.tr
```

Kaydet ve çık (Ctrl+X, Y, Enter)

---

## 2️⃣ Storage Link Oluştur (30 saniye)

```bash
# Backend klasöründe
php artisan storage:link
```

✅ Beklenen çıktı: "The [public/storage] link has been connected to [storage/app/public]"

---

## 3️⃣ İzinleri Ayarla (30 saniye)

```bash
# Backend klasöründe
chmod -R 775 storage bootstrap/cache
chmod -R 755 storage/app/public
```

---

## ✅ BU KADAR!

### Test Et:

Tarayıcıda aç:
- `https://ripehome.com.tr` → Ana sayfa
- `https://ripehome.com.tr/api/categories` → JSON görmeli
- Kategori görselleri görünüyor mu?

---

## 🐛 Hata Alıyorsan:

### Görsel 404 hatası:
```bash
ls -la public/storage  # Link var mı kontrol et
php artisan storage:link --force  # Yeniden oluştur
```

### 500 Hatası:
```bash
# Cache temizle
php artisan config:clear
php artisan cache:clear

# .env kontrol
cat .env | grep APP_URL  # Doğru domain'i göstermeli
```

### Görseller hala görünmüyor:
```bash
# İzinleri kontrol
ls -la storage/app/public/products/  # Dosyalar var mı?
chmod -R 755 storage/app/public
```

---

## 📋 Özet: Yapılması Gerekenler

✅ SQL import edildi
✅ Backend dosyaları yüklendi  
✅ Dist (frontend) dosyaları yüklendi
⚠️ **YAPILMASI GEREKENLER:**
- [ ] .env → APP_URL düzenle (2 dk)
- [ ] php artisan storage:link (30 sn)
- [ ] chmod izinleri (30 sn)

**Toplam Süre: 3 dakika**

---

## ❓ Neden Otomatik Değil?

1. **.env dosyası** → Her sunucu farklı domain kullanır
2. **Storage link** → Symbolic link sunucuda oluşturulmalı
3. **İzinler** → Güvenlik için manuel ayarlanmalı

---

## 💡 İpucu: Tek Komut

Hepsini tek komutla:

```bash
cd backend && \
php artisan storage:link && \
chmod -R 775 storage bootstrap/cache && \
chmod -R 755 storage/app/public && \
echo "✅ Tamamlandı! .env dosyasını kontrol et: APP_URL"
```

Sonra sadece .env'i düzenle, o kadar!

