# Hamilelik Takip Uygulaması

Gebelik haftası takibi, tetkik takvimi ve takip araçları. Türkiye pazarı, Türkçe.

- **[PLAN.md](PLAN.md)** — ürün ve teknik plan (kapsam, veri modeli, API, yol haritası)
- **engine/** — gebelik yaşı motoru, PHP ve TypeScript olarak

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
