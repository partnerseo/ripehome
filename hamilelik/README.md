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
| POST | `/api/v1/sync` | Çevrimdışı kuyruğun toplu gönderimi |
| POST | `/api/v1/consents` | Açık rıza kaydı |
| GET | `/api/v1/me/export` | Tüm verinin dışa aktarımı |
| DELETE | `/api/v1/me` | Hesabı ve tüm veriyi kalıcı siler |
| GET | `/api/v1/logs/health` | Kilo, tansiyon, şeker geçmişi |
| GET | `/api/v1/logs/symptoms` | Belirti günlüğü |
| GET | `/api/v1/kick-sessions` | Hareket sayımı geçmişi |
| GET | `/api/v1/contraction-sessions` | Sancı sayımı geçmişi |

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

## Çevrimdışı kayıt ve takip araçları

**Yazma tek kapıdan geçer: `/sync`.** Uygulama her kaydı önce cihaza yazar,
sonra göndermeyi dener. Hastanede internet olmadığı için yerel yazma birincil
yoldur — kullanıcı sancı sayarken bağlantı olup olmadığını düşünmemeli.

Her kayıt cihazda üretilmiş bir `client_uuid` taşır ve sunucu bunu idempotency
anahtarı olarak kullanır. Bağlantı **yanıt alınmadan** koptuğunda istemci aynı
kuyruğu güvenle tekrar gönderir; ikinci gönderim kopya üretmez. Hareket
oturumlarında olaylar da oturumla birlikte yeniden yazılır, yani tekrar
gönderim aynı hareketleri iki kez eklemez.

Kuyruk cihazda SQLite'ta, web'de localStorage'da durur. `store.ts` /
`store.web.ts` ayrımını Metro platforma göre kendisi seçer — böylece
`expo-sqlite` web paketine hiç girmez.

### Güvenlik eşikleri

`/sync` yanıtı, gönderilen kayıtlardan çıkan uyarıları da döndürür. Uygulama
teşhis koymaz; başvurmayı söyler.

| Eşik | Uyarı |
|---|---|
| Tansiyon ≥ 140/90 | `blood_pressure` |
| 2 saatte 10 hareket sayılamadı | `fetal_movement` |
| Kasılmalar 5-1-1 kuralına uyuyor | `contractions` |
| Acil belirti işaretlendi (kanama, görme bulanıklığı, su gelmesi…) | `symptom` |

5-1-1 kuralı hem sunucuda hem istemcide var — çünkü uyarı bağlantı olmadan da
çıkmalı: doğum sancısı hastaneye giderken başlar, orada internet olmayabilir.
İkisi de aynı eşikleri kullanır ve ayrı ayrı test edilir.

## Acil belirtiler ve gebelik kaybı

### Kırmızı bayrak listesi neden kodda?

Acil belirti listesi (`app/src/lib/redFlags.ts`) bilinçli olarak uygulamanın
içinde, panelde değil. İki sebebi var:

1. **Her zaman dolu olmalı.** Panelden yönetilseydi bir kayıt yanlışlıkla
   taslağa düştüğünde liste boşalırdı — boş bir acil belirti listesi,
   olmamasından daha kötüdür.
2. **Çevrimdışı çalışmalı.** Kanama başlayan biri bağlantı beklemez.

Metin yine hekim gözden geçirmesinden geçer, ama sürüm yayınıyla: değişiklik
kod incelemesinden geçer, panelden anlık düzenlenmez.

Günlükte acil bir belirti işaretlendiğinde kullanıcı `/acil` ekranına gider:
tek cümlelik yönlendirme, tek dokunuşla 112, erteleme düğmesi yok. Yönlendirme
kaydın gönderilmesine bağlı değil — kayıt kuyrukta kalsa bile ekran açılır.

### Gebelik kaydını kapatma

Uygulamanın en çok özen isteyen ekranı (`/gebelik-kapat`):

- Sebep sormak zorunlu değil, "belirtmek istemiyorum" seçeneği var
- Dil yargısız ve kısa; "emin misiniz?" tonuna yer yok
- Kapandığı an tüm hatırlatmalar ve haftalık bildirimler susar
- Veriler silinmez, arşivlenir
- Kayıp seçildiyse `/kapandi` ekranı gelir: hafta yok, geri sayım yok, bebek
  boyutu yok, "yeni gebelik başlat" çağrısı yok

Doğrulanmış davranış: kapanan bir gebelikte zamanı gelmiş hatırlatmalar
tabloda durur ama **hiçbiri kuyruğa alınmaz**.

## KVKK

- **Açık rıza sürümlenir** (`Consent::CURRENT_VERSION`). Metin değişince eski
  rıza sayılmaz ve kullanıcıdan yeniden istenir; hangi kullanıcının hangi metne
  rıza verdiği kayıtta durur.
- **Dışa aktarma** tüm kayıtları okunabilir JSON olarak verir; uygulamada dosya
  olarak paylaşılır.
- **Kalıcı silme** uygulama içinden, destek talebi gerekmeden. Onay için
  e-postanın birebir yazılması istenir. Cihaz kayıtları da silinir: silinen
  hesaba bildirim gönderilecek bir yol kalmamalı.

### İçerik paneli

Kendi yazdığımız panel: Blade + tek bir elle yazılmış CSS dosyası
(`public/css/admin.css`). **Derleme adımı yok** — dosyayı kopyalayıp çalıştırırsınız,
cPanel'de npm gerekmez. Toplam yaklaşık 1.500 satır.

Filament ile başlanmıştı, sonra çıkarıldı. Gerekçe: panelin işi düz CRUD değil —
hekimin gözden geçirme akışı ayrı bir ekran istiyor ve panelin kendi kimliği
olması isteniyordu. Bir çatının grain'ine uymak yerine altı ekranı doğrudan
yazmak burada daha az kod ve daha az bağımlılık çıkardı.

```bash
php artisan app:create-admin --name="Editör" --email="..." --password="..."
```

| Yol | Ekran |
|---|---|
| `/admin/giris` | Giriş. Hangi alanın yanlış olduğu söylenmez — e-posta varlığı sızdırılmaz |
| `/admin` | Genel bakış: kaç hafta yayında, kaçı onay bekliyor, kaçı hiç yazılmamış |
| `/admin/onay` | **Hekimin kuyruğu** |
| `/admin/haftalar` | Hafta içerikleri, durum ve dile göre süzülür |
| `/admin/tetkikler` | Tetkik takvimi |

Panel kullanıcıları **ayrı bir tabloda ve ayrı guard'da**: hamile kullanıcının
hesabı hiçbir koşulda içerik paneline giremez.

#### Hekimin gözden geçirme ekranı

Editörün formuyla hekimin ihtiyacı aynı şey değil. Editör 15 alan doldurur;
hekim okur ve onaylar. `/admin/onay` bu ikinci işi ayrı bir akışa aldı:

- Onay bekleyenler tek listede, tetkikler önce (yanlış bir hafta kaçırılmış
  tarama demek, en kritik olan o)
- Okuma ekranında yalnızca onayın kapsadığı alanlar ve dayanak kaynaklar
- Altında tek bir onay kutusu: onaylayan, tarih, isteğe bağlı not
- "Düzeltme gerekiyor" düğmesi editörün formuna götürür

Onay verildiği anda kayıt yayına çıkar ve uygulamada içeriğin altında hekimin
adı görünür.

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
| `/kick` | Hareket sayacı — 10 hareket, 2 saat sınırı |
| `/contractions` | Sancı sayacı — süre, aralık, 5-1-1 uyarısı |
| `/belirtiler` | Günlük: ruh hâli, belirtiler, acil belirti yönlendirmesi |
| `/acil` | Acil başvuru yönlendirmesi ve 112 |
| `/profil` | Rıza, veri indirme, hesap silme |
| `/gebelik-kapat` · `/kapandi` | Gebelik kaydını kapatma ve kayıp sonrası |

**Kurulumda önizleme istemcide hesaplanır.** Kullanıcı kaydetmeden önce hangi
haftada olduğunu görür; sunucuya istek gitmez. Motorun istemcide çalışıyor
olmasının ilk somut karşılığı bu — çevrimdışı ana ekran da aynı yoldan gelecek.

### Doğrulama

Uygulama gerçek API'ye karşı gerçek tarayıcıda uçtan uca çalıştırıldı:
giriş → kod → kurulum → ana ekran → hafta detayı (yayındaki içerik ve onay satırı
dahil) → takvim (9 tetkik penceresi) → hareket sayacı (10 dokunuş, sunucuda
doğrulandı) → acil belirti yönlendirmesi → gebelik kaydını kapatma; sıfır konsol
hatası, 12 adım. İçerik paneli de aynı şekilde doğrulandı.
Betikler `e2e/`, görüntüler `app/screenshots/` ve `api/screenshots/`.

Panel de aynı şekilde: giriş, genel bakış, onay kuyruğu, gözden geçirme ekranı ve
onay işlemi gerçek tarayıcıda çalıştırıldı — sıfır sayfa/istek hatası.
Betik `e2e/panel.mjs`, görüntüler `api/screenshots/`.

**Henüz yapılmadı:** hafta içeriğinin cihazda önbelleğe alınması. Yazma tarafı
(kuyruk) çalışıyor, okuma tarafı henüz sunucuya bağlı. `/weeks` ucu ETag ile
hazır; içeriği kuyruğun yanına yazmak sonraki adım.
