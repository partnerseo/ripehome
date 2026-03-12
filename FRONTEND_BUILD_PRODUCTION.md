# 🎨 Frontend - Production Build Nasıl Yapılır

## ⚠️ ÖNEMLİ: Build yapmadan önce!

Frontend'i production için build yaparken **MUTLAKA** `.env` dosyasını oluştur:

---

## Adım 1: .env Dosyası Oluştur

```bash
# Proje root klasöründe (ripehome/)
echo "VITE_API_URL=https://ripehome.com.tr/api" > .env
```

veya manuel oluştur:

**.env** dosyası:
```env
VITE_API_URL=https://ripehome.com.tr/api
```

---

## Adım 2: Build Yap

```bash
# Dependencies yüklü değilse:
npm install

# Production build
npm run build
```

✅ `dist/` klasörü oluşacak

---

## Adım 3: Dist Klasörünü Yükle

Sunucuda `public_html/` altına:

```
public_html/
├── index.html          ← dist/index.html
├── assets/             ← dist/assets/
├── ripehomelogo.jpg    ← dist/ripehomelogo.jpg
├── yikamatalimati.pdf  ← dist/yikamatalimati.pdf
└── backend/            ← Backend klasörü
```

---

## ✅ Kontrol

Build doğru mu diye kontrol et:

```bash
# dist/assets/*.js dosyalarına bak
grep -r "localhost" dist/assets/*.js

# ❌ Çıktı varsa: Build yanlış, .env ekleyip yeniden build et
# ✅ Çıktı yoksa: Build doğru, yükleyebilirsin
```

---

## 🐛 Sorun Giderme

### Build'de hala localhost görünüyor:

```bash
# 1. .env dosyasını kontrol et
cat .env

# 2. Node cache temizle
rm -rf node_modules/.vite

# 3. Yeniden build
npm run build
```

### Build sonrası API çalışmıyor:

- Tarayıcı console'u aç (F12)
- Network tab'e bak
- API istekleri `https://ripehome.com.tr/api/...` olmalı
- `localhost:8000` görüyorsan build yanlış yapılmış

---

## 💡 Hızlı Çözüm

Tek komutla:

```bash
# Local'de (bilgisayarında)
echo "VITE_API_URL=https://ripehome.com.tr/api" > .env && \
npm run build && \
echo "✅ Build tamamlandı! dist/ klasörünü sunucuya yükle"
```

---

## 📋 Özet

**HATIRLA:** 
- Frontend build YAPMADAN ÖNCE `.env` oluştur ✅
- `.env` olmadan build yaparsan API localhost'a gider ❌
- Build sonrası dist/ klasörünü public_html'e yükle ✅


