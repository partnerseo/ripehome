# Uçtan uca akış testi

Gerçek API'ye karşı gerçek tarayıcıda çalışır: giriş → kod doğrulama → kurulum →
ana ekran → hafta detayı. Her adımda ekran görüntüsü alır ve konsol hatası
sayar; bir hata varsa çıkış kodu 1 olur.

Giriş kodunu **sunucunun log dosyasından** okur — kod istemciye hiçbir zaman
gönderilmediği için testin ona ulaşabileceği tek yer orası. Bu, kodun yanıtta
dönmediğinin de dolaylı kanıtı.

## Çalıştırma

Üç şeyi ayağa kaldırın:

```bash
# 1) API
cd api && php artisan migrate:fresh --force && php artisan serve --port=8000

# 2) Web paketi
cd app && EXPO_PUBLIC_API_URL="http://127.0.0.1:8000/api/v1" npx expo export -p web --output-dir dist
cd app/dist && python3 -m http.server 8081

# 3) Akış
cd e2e && npm install playwright && node flow.mjs
```

`CHROME_PATH` ile farklı bir Chromium ikilisi verebilirsiniz.

Ekran görüntüleri `app/screenshots/` altına yazılır.
