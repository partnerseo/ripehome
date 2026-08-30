# Hamilelik Takip Uygulaması

Gebelik haftası takibi, tetkik takvimi ve takip araçları. Türkiye pazarı, Türkçe.

- **[PLAN.md](PLAN.md)** — ürün ve teknik plan (kapsam, veri modeli, API, yol haritası)
- **engine/** — gebelik yaşı motoru, PHP ve TypeScript olarak
- **api/** — Laravel 11 API (kimlik + gebelik uçları)
- **app/** — Expo (React Native) mobil uygulama
- **e2e/** — gerçek tarayıcıda uçtan uca akış testi

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
| GET | `/api/v1/weeks/{week}` | Yayındaki hafta içeriği |
| GET | `/api/v1/weeks` | Tüm yayındaki haftalar, ETag ile (çevrimdışı ön yükleme) |
| GET | `/api/v1/screenings` | Yayındaki tetkik takvimi |
| GET | `/api/v1/appointments` | Gebeliğin randevu ve tetkik pencereleri |
| POST | `/api/v1/appointments` | Elle randevu ekler |
| PATCH | `/api/v1/appointments/{id}` | Tarih, not, tamamlandı |
| DELETE | `/api/v1/appointments/{id}` | Elle olanı siler, otomatik olanı tamamlar |
| POST | `/api/v1/devices` | Bildirim jetonunu kaydeder |

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

## Tıbbi içerik ve gözden geçirme

Hamilelik uygulamasında yanlış bir tetkik haftası kaçırılmış bir tarama, eksik bir
belirti listesi de geç kalınmış bir başvuru demektir. Bu yüzden onay bir niyet değil,
kaydın geçemeyeceği bir kapı olarak kuruldu (`app/Models/Concerns/MedicallyReviewed.php`):

1. **Gözden geçiren kişi ve tarih olmadan hiçbir içerik yayına alınamaz.** Model
   `MedicalReviewRequired` fırlatır; panelde de aynı kural form doğrulaması olarak var.
2. **Yayındaki bir metin değiştirilirse önceki onay onu kapsamaz.** Kayıt otomatik
   olarak taslağa döner, onay alanları temizlenir ve API'den anında düşer.
3. **Değişikliğin tıbbi olup olmadığı modelde tanımlı.** `reviewableFields()` — hafta
   içeriğinde metinler ve ölçüler, tetkikte ad, kategori ve hafta aralığı. Kaynak
   listesini düzenlemek onayı bozmaz.

Her kayıt `source_refs` ile dayanağını taşır; uygulamada içeriğin altında
"Tıbbi gözden geçirme: Dr. X · tarih" satırı görünür.

**Tetkik takvimi taslak olarak gelir.** `ScreeningTemplateSeeder` Türkiye takvimini
9 kayıtla kurar, hepsi `draft` — hekim onayı verilene kadar hiçbiri kullanıcıya
gitmez. Kaynak bağlantıları bilinçli olarak boş bırakıldı; doğrulanmamış bağlantı
yazmak, olmayan bir dayanak iddia etmek olurdu.

## Randevular ve bildirimler

Gebelik oluşturulduğunda yayındaki tetkik şablonlarından randevu pencereleri
üretilir (`AppointmentPlanner`). Pencereler **tarih olarak** yazılır — takvim
ekranı her açılışta yeniden hesap yapmasın diye. Bunun bedeli, USG ile yeniden
tarihleme sonrası pencerelerin yeniden hesaplanmasıdır; `replan()` bunu yapar
ve **kullanıcının aldığı gerçek randevuya dokunmaz**: o tarih hastaneyle
konuşulup alınmıştır, gebelik haftası düzeltildi diye kaymaz.

Otomatik üretilen bir randevu silinmez, tamamlandı işaretlenir — silinse bir
sonraki planlama turunda yeniden üretilir ve kullanıcı aynı kaydı tekrar tekrar
silmek zorunda kalır.

### Bildirimin susması

Planın en başından beri merkezdeki kural: **kapanmış bir gebelik için hiçbir
bildirim gitmez.** Kritik nokta, kontrolün nerede yapıldığı. Bir iş kuyruğa
girdikten sonra kullanıcı gebeliği kapatabilir; o yüzden kontrol kuyruğa alma
anında değil, **gönderim anında** yapılır:

```php
if ($pregnancy === null || ! $pregnancy->isActive()) {
    return;
}
```

`NotificationSilenceTest` tam olarak bu sırayı test eder: iş nesnesi oluşturulur,
sonra gebelik kapatılır, sonra iş çalıştırılır — hiçbir şey gönderilmez.

Haftalık bildirim yalnızca kullanıcının **kendi takvim gününde** tam hafta
dönüşünde çıkar (`ga_days % 7 === 0`); sunucu gününe bakmak bildirimi saat
dilimine göre bir gün kaydırır veya büsbütün atlar.

```bash
php artisan app:dispatch-appointment-reminders   # saat başı
php artisan app:dispatch-weekly-milestones       # her gün 06:00
```

### İçerik paneli

Filament 3, `/admin`. Panel kullanıcıları **ayrı bir tabloda ve ayrı guard'da**:
hamile kullanıcının hesabı hiçbir koşulda içerik paneline giremez.

```bash
php artisan app:create-admin --name="Editör" --email="..." --password="..."
```

Yan menüde her kaynağın yanında kaç kaydın hâlâ yayına hazır olmadığını gösteren
sarı bir rozet durur.

### Testler

```bash
cd api && php artisan test      # 50 test, 237 iddia
./vendor/bin/pint --test        # kod stili
```


## Mobil uygulama

Expo SDK 57, React Native 0.86, expo-router. Motor kopyalanmaz: `metro.config.js`
içindeki `watchFolders` ile `../engine/ts` izlenir ve `@engine` takma adıyla
içe aktarılır. Jest tarafında aynı iş `moduleDirectories` ile yapılır.

```bash
cd app
npm install
npm start          # Expo geliştirme sunucusu
npm test           # 25 test (17'si paylaşılan motor vektörü)
npm run typecheck
```

### Ekranlar

| Yol | İş |
|---|---|
| `/` | Açılış kapısı: jeton ve aktif gebelik durumuna göre yönlendirir |
| `/sign-in` | E-posta → tek kullanımlık kod → jeton |
| `/onboarding` | Yöntem seçimi, tarih girişi, döngü uzunluğu, **canlı önizleme** |
| `/home` | Hafta halkası, tahmini doğum, geri sayım |
| `/week/[week]` | Hafta detayı: içerik, ölçüler, gözden geçiren hekim |
| `/calendar` | Tetkik pencereleri ve randevular |

**Kurulumda önizleme istemcide hesaplanır.** Kullanıcı kaydetmeden önce hangi
haftada olduğunu görür; sunucuya istek gitmez. Motorun istemcide çalışıyor
olmasının ilk somut karşılığı bu — çevrimdışı ana ekran da aynı yoldan gelecek.

### Doğrulama

Uygulama gerçek API'ye karşı gerçek tarayıcıda uçtan uca çalıştırıldı:
giriş → kod → kurulum → ana ekran → hafta detayı (yayındaki içerik ve onay satırı
dahil) → takvim (9 tetkik penceresi), sıfır konsol hatası. İçerik paneli de aynı şekilde doğrulandı.
Betikler `e2e/`, görüntüler `app/screenshots/` ve `api/screenshots/`.

**Henüz yapılmadı:** içeriğin cihazda saklanması. `/weeks` ucu ETag ile hazır ama
uygulama onu yerelde tutmuyor — çevrimdışı depolama Sprint 5'te SQLite ile
geliyor, o parçayı yarım kurmak yerine oraya bıraktım.
