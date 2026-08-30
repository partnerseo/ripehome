# Hamilelik Takip Uygulaması

Gebelik haftası takibi, tetkik takvimi ve takip araçları. Türkiye pazarı, Türkçe.

- **[PLAN.md](PLAN.md)** — ürün ve teknik plan (kapsam, veri modeli, API, yol haritası)
- **engine/** — gebelik yaşı motoru, PHP ve TypeScript olarak
- **api/** — Laravel 11 API (kimlik + gebelik uçları)

> Bu klasör ileride kendi deposuna taşınacak; ripehome ile kod paylaşımı yok.

## Teknoloji

| Katman | Seçim |
|---|---|
| Mobil uygulama | Expo (React Native), TypeScript |
| API | Laravel 11 + Sanctum 4 |
| Yönetim paneli | Filament 3 (içerik ve tetkik şablonları) |
| Yerel depolama | expo-sqlite + yazma kuyruğu |

Expo'nun Flutter'a tercih edilme gerekçesi: **EAS Update**. JS tarafındaki bir düzeltme
mağaza onayı beklemeden dağıtılabiliyor. Sağlık uygulamasında yanlış bir tıbbi metnin
veya kırık bir kırmızı bayrak akışının günlerce ekranda kalması kabul edilebilir değil.
İkincil gerekçe: hafta motoru zaten iki dilde yazılıyor (PHP + istemci); TypeScript
seçilince ileride bir web yüzeyinde aynı kod tekrar kullanılabiliyor, Dart'ta üçüncü kez
yazmak gerekirdi.

## Gebelik yaşı motoru

Uygulamanın tamamı bu tek hesabın üzerinde durur. Bir günlük kayma, yanlış haftada içerik
ve yanlış tarihte tetkik uyarısı demektir.

Motor iki yerde bulunur çünkü iki yerde de çalışması gerekir: istemci çevrimdışıyken
(hastanede internet yok) hesabı kendisi yapar, sunucu ise bildirimleri planlarken aynı
sonucu üretmek zorundadır.

```
engine/
├── php/GestationalAge.php      → Laravel'de App\Services\GestationalAge olur
├── php/run-tests.php
├── ts/gestationalAge.ts        → Expo uygulamasına doğrudan girer
├── ts/run-tests.ts
└── compare.sh                  → iki motorun ayrışmadığını doğrular
```

İkisi de kök dizindeki `ga-test-vectors.json` dosyasını okur: 17 vektör — her giriş yöntemi,
uzun/kısa döngü düzeltmesi, dört trimester sınırı, artık yıl, yaz saati aralığı, termin aşımı.

### Çalıştırma

```bash
php engine/php/run-tests.php                              # 25 kontrol
node --experimental-strip-types engine/ts/run-tests.ts    # aynı 25 kontrol
./engine/compare.sh                                       # ikisinin eşleştiğini doğrular
```

`compare.sh` CI'da kırmızı olursa iki motor ayrışmış demektir — ilk bakılacak yer orasıdır.

### Vektörleri yeniden üretme

`ga-test-vectors.json` elle düzenlenmez; beklenen değerler `generate-vectors.php` ile üretilir:

```bash
php generate-vectors.php    # dosyayı bulunduğun dizine yazar
```

Yeni bir senaryo eklerken betikteki `$cases` dizisine ekleyip yeniden üretin, sonra
`compare.sh` çalıştırın.

## Tıbbi içerik uyarısı

Plandaki tetkik haftaları ve tıbbi eşikler yaygın obstetrik pratiğe dayanır ve **yayına
çıkmadan önce bir kadın doğum uzmanı tarafından gözden geçirilmelidir**. Uygulama teşhis
koymaz, doz önermez — yönlendirir.


## API

Laravel 11 + Sanctum. Motor `api/composer.json` içinden `../engine/php` dizinine
bağlanır — API kendi kopyasını tutmaz, iki taraf bu yüzden ayrışamaz.

### Kurulum

```bash
cd api
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite      # veya .env icinde MySQL ayarla
php artisan migrate
php artisan serve
```

### Uçlar

| Yöntem | Yol | Not |
|---|---|---|
| POST | `/api/v1/auth/otp/request` | Kod ister. Yanıt her durumda 202 — e-posta varlığı sızdırılmaz |
| POST | `/api/v1/auth/otp/verify` | Kodu doğrular, jeton döndürür. Kullanıcı yoksa oluşturulur |
| GET | `/api/v1/me` | |
| POST | `/api/v1/auth/logout` | Yalnızca o cihazın jetonunu iptal eder |
| POST | `/api/v1/pregnancies` | Gebelik oluşturur; ikinci aktif kayıt 409 döner |
| GET | `/api/v1/pregnancies/current` | Aktif gebelik + bugünkü hafta durumu |
| POST | `/api/v1/pregnancies/{id}/redate` | USG düzeltmesi; en son ölçüm geçerli |
| POST | `/api/v1/pregnancies/{id}/end` | Gebeliği kapatır (`birth` / `loss` / `other`) |

### Kararlar

**Giriş kodu hiçbir koşulda API yanıtında dönmez.** Hata ayıklama kolaylığı için
bile: `APP_DEBUG` açıkken kodu yanıta koymak, üretimde debug açık unutulduğu anda
herkesin herkesin hesabına girmesi demektir. Yerel geliştirmede kod log dosyasında.

**Sınırlama gerçekten sayar.** E-posta ve IP sınırları `RateLimiter::hit` ile
işler; sayacı her istekte yeniden yazan bir kurgu (örneğin `updateOrCreate`)
sınırı hiç devreye sokmaz. Ayrıca rota seviyesinde `throttle:6,1` var.

**Hafta kullanıcının saat dilimine göre hesaplanır.** Sunucu UTC'dedir; bu dönüşüm
atlanırsa kullanıcı günün bir kısmında yanlış haftayı görür. Test bunu İstanbul ve
Los Angeles kullanıcılarıyla aynı anda doğruluyor.

**Kapanmış gebelikte hafta ve geri sayım dönmez.** `PregnancyResource` bu alanları
yalnızca aktif gebelikte ekler.

### Testler

```bash
cd api && php artisan test      # 50 test, 237 iddia
./vendor/bin/pint --test        # kod stili
```
