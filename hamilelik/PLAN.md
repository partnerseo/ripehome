# Hamilelik Takip Uygulaması — Ürün ve Teknik Planı

> Durum: taslak v1 · Hedef: Türkiye pazarı, Türkçe (sonra EN) · Platform: Expo (React Native) + Laravel 11 API

**Önemli:** Bu dokümandaki tıbbi eşikler ve tetkik haftaları yaygın obstetrik pratiğe dayanır ve
yayına çıkmadan önce bir kadın doğum uzmanı tarafından gözden geçirilmelidir. Uygulama bir
teşhis aracı değildir; hiçbir ekranda doz, teşhis veya "bekleyin geçer" ifadesi bulunmaz.

---

## 1. Karar özeti

| Konu | Karar | Gerekçe |
|---|---|---|
| İstemci | Expo (React Native), TypeScript | EAS Update ile mağaza onayı beklemeden düzeltme (sağlık uygulamasında belirleyici); push bildirim, çevrimdışı çalışma; hafta motoru TypeScript olduğu için ileride web yüzeyinde tekrar kullanılabilir |
| Backend | Laravel 11 + Sanctum 4 | Ekipte mevcut (ripehome ile aynı yığın), PHP 8.4 |
| Admin / içerik | Filament 3 + spatie/laravel-translatable | Hafta içeriği ve tetkik şablonları koddan değil panelden yönetilir |
| Veritabanı | MySQL 8 | Mevcut altyapı |
| Yerel depolama | SQLite (expo-sqlite) + kuyruk tabanlı senkron | Hastanede internet yok; uygulama çevrimdışı çalışmak zorunda |
| Kimlik | E-posta + OTP (şifresiz) | ripehome'da kurulan akışın olgunlaşmış hâli |
| İtme bildirimi | Expo Push | Ek servis maliyeti yok |

**v1 kapsamı:** çekirdek hafta motoru + haftalık içerik, takip araçları (kick, kontraksiyon,
kilo, tansiyon, belirti), tetkik takvimi ve hatırlatmalar, kırmızı bayrak yönlendirmesi,
gebelik kaybı akışı. Topluluk/forum, bebek isimleri ve doğum sonrası modu v2'ye bırakılır.

---

## 2. Mimari

```
Expo (iOS/Android)
  ├── expo-sqlite         → yerel kayıtlar, çevrimdışı yazma kuyruğu
  ├── expo-notifications  → yerel + push bildirim
  ├── TanStack Query      → sunucu durumu, yeniden deneme
  └── REST /api/v1        → Sanctum kişisel erişim token'ı
                                │
Laravel 11 API ────────────────┤
  ├── Hafta motoru (saf PHP servis, istemcide TS ikizi)
  ├── İçerik: week_contents (Filament'ten yönetilir)
  ├── Planlayıcı: tetkik hatırlatma + haftalık bildirim job'ları
  └── Filament admin: içerik, tetkik şablonu, kullanıcı desteği
```

**Kritik kural:** Gebelik haftası hesabı hem sunucuda (PHP) hem istemcide (TS) bulunur ve
**aynı test vektörleriyle** doğrulanır. İstemci çevrimdışıyken hesabı kendisi yapar; sunucu
bildirimleri planlarken aynı sonucu üretmek zorundadır. Ortak test verisi:
`hamilelik/ga-test-vectors.json`.

---

## 3. Gebelik haftası motoru (çekirdek alan mantığı)

Uygulamanın tamamı bu tek hesabın üzerinde durur. Hatalı bir gün kayması, yanlış haftada
içerik ve yanlış tarihte tetkik uyarısı demektir.

### 3.1 Tahmini doğum tarihi (TDT)

| Giriş yöntemi | Formül |
|---|---|
| Son adet tarihi (SAT) | `TDT = SAT + 280 gün` (Naegele) |
| SAT + düzensiz döngü | `TDT = SAT + 280 + (döngü_uzunluğu − 28)` |
| Gebe kalma / ovülasyon tarihi | `TDT = tarih + 266 gün` |
| IVF — 5. gün (blastosist) | `TDT = transfer + 261 gün` |
| IVF — 3. gün embriyo | `TDT = transfer + 263 gün` |
| Doktorun verdiği TDT | Doğrudan girilir; SAT geriye doğru türetilir |

### 3.2 Gebelik yaşı (GA)

```
gün_farkı = bugün − hesaplanan_SAT           // takvim günü farkı, saat değil
GA_gün    = gün_farkı
hafta     = floor(GA_gün / 7)
gün       = GA_gün % 7                        // gösterim: "24h 3g"
kalan_gün = TDT − bugün
ilerleme  = clamp(GA_gün / 280, 0, 1)
```

**Trimester:** 1. → 0h0g–13h6g · 2. → 14h0g–27h6g · 3. → 28h0g ve sonrası.

### 3.3 USG ile yeniden tarihleme (redating)

Doktor ultrasonda farklı bir hafta söylediğinde kullanıcı bunu girebilmeli. Kayıt:
`measured_on` (ölçüm tarihi) + `ga_days_at_measure` (o gün ölçülen gebelik günü).

```
yeni_SAT = measured_on − ga_days_at_measure
```

En son eklenen düzeltme geçerlidir; orijinal SAT saklanır ve geri alınabilir.

### 3.4 Sınır durumları (hepsi test edilecek)

- **Saat değil gün:** tüm hesap `Y-m-d` takvim günü üzerinden. Kullanıcının cihaz saat dilimi
  değişince hafta atlamamalı. Sunucu UTC saklar, kullanıcının `timezone` alanına göre gün sınırı belirlenir.
- **Yaz saati:** gün farkı `DateTime::diff` ile değil, `->startOfDay()` normalizasyonundan sonra alınır.
- **Artık yıl:** gün bazlı toplama kullanıldığı için sorun çıkmaz; yine de 29 Şubat vektörü eklenir.
- **Gelecek tarihli SAT:** reddedilir.
- **Çok eski SAT:** 44 haftadan büyükse "gebelik tamamlandı mı?" sorulur, otomatik kapatılmaz.
- **42+ hafta:** ilerleme %100'de kilitlenir, içerik 40. haftada durur, "doğum geçti" durumu gösterilir.
- **Çoğul gebelik:** hafta hesabı aynı, içerik metni farklı (ikiz varyantı), TDT beklentisi ~37 hafta not edilir.

---

## 4. Veri modeli

### 4.1 Kimlik ve gebelik

**users** — `id, email (unique), name, locale, timezone, created_at`
Şifre yok; OTP ile giriş. Sanctum token'ı `token_prefix` + hash olarak saklanır (aramada tam tarama yapılmaz).

**pregnancies**
```
id, user_id, method (lmp|due_date|conception|ivf_d3|ivf_d5)
lmp_date, due_date, cycle_length (default 28), input_date
baby_count (default 1)
status (active|completed|ended), ended_at, ended_reason (birth|loss|other)
created_at, updated_at
```
Bir kullanıcının aynı anda tek `active` gebeliği olur (kısmi unique index).

**pregnancy_redatings** — `id, pregnancy_id, measured_on, ga_days_at_measure, source (usg|doctor), note`

### 4.2 İçerik (Filament'ten yönetilir)

**week_contents** — `id, week (1–42), locale, baby_size_label, baby_length_mm, baby_weight_g,
baby_body, mother_body, tips_body, is_published`
`(week, locale)` unique. `*_body` alanları zengin metin.

**screening_templates** — tetkik takvimi kaynağı
```
id, code, name, week_start, week_end, category (usg|lab|vaccine|visit)
description, is_optional, country (default TR), sort
```

### 4.3 Takip araçları

**health_logs** — `id, pregnancy_id, type (weight|bp|glucose), value_1, value_2, unit,
measured_on, note`
Tansiyon `value_1=sistolik, value_2=diyastolik`; kilo `value_1=kg`; şeker `value_1=mg/dL`.

**kick_sessions** — `id, pregnancy_id, started_at, ended_at, target_count (default 10), kick_count`
**kick_events** — `id, kick_session_id, occurred_at`

**contraction_sessions** — `id, pregnancy_id, started_at, ended_at`
**contractions** — `id, contraction_session_id, started_at, ended_at, duration_sec, interval_sec`

**symptom_logs** — `id, pregnancy_id, logged_on, symptoms (json), mood (1–5), note`

**belly_photos** — `id, pregnancy_id, week, path, taken_on`

### 4.4 Takvim ve hatırlatma

**appointments**
```
id, pregnancy_id, screening_template_id (nullable), title, category
scheduled_at (nullable), window_start_week, window_end_week
location, doctor_name, notes, reminder_at, completed_at
source (auto|manual)
```
Gebelik oluşturulduğunda `screening_templates` üzerinden `source=auto` kayıtlar üretilir;
kullanıcı tarih atadıkça `scheduled_at` dolar.

**devices** — `id, user_id, expo_push_token (unique), platform, locale, timezone, last_seen_at`

**checklist_items** — `id, pregnancy_id, template_key, title, is_done, sort` (hastane çantası)

### 4.5 Paylaşım

**pregnancy_shares** — `id, pregnancy_id, invited_email, role (viewer), token, accepted_at,
revoked_at` — eşin salt-okunur erişimi.

---

## 5. API uçları (v1)

```
POST   /api/v1/auth/otp/request        { email }
POST   /api/v1/auth/otp/verify         { email, code }        → token
POST   /api/v1/auth/logout
GET    /api/v1/me

POST   /api/v1/pregnancies             { method, input_date, cycle_length, baby_count }
GET    /api/v1/pregnancies/current     → GA, TDT, trimester, ilerleme, hafta içeriği özeti
PATCH  /api/v1/pregnancies/{id}
POST   /api/v1/pregnancies/{id}/redate { measured_on, ga_days_at_measure }
POST   /api/v1/pregnancies/{id}/end    { reason }             → bildirimleri anında durdurur

GET    /api/v1/weeks/{week}            → o haftanın içeriği (locale'e göre)
GET    /api/v1/weeks                   → çevrimdışı ön yükleme için toplu içerik (ETag)

GET    /api/v1/appointments            ?from=&to=
POST   /api/v1/appointments
PATCH  /api/v1/appointments/{id}
DELETE /api/v1/appointments/{id}

POST   /api/v1/logs/health             { type, value_1, value_2, measured_on }
GET    /api/v1/logs/health             ?type=&from=&to=
POST   /api/v1/logs/symptoms
POST   /api/v1/kick-sessions           → oturum + olaylar tek istekte (çevrimdışı toplu gönderim)
POST   /api/v1/contraction-sessions

POST   /api/v1/sync                    → çevrimdışı kuyruğun toplu gönderimi (idempotency-key)
POST   /api/v1/devices                 { expo_push_token, platform, timezone }
```

**Çevrimdışı senkron:** her yerel kayıt bir `client_uuid` taşır; `/sync` bu anahtarı
idempotency anahtarı olarak kullanır, tekrar gönderim kopya üretmez.

---

## 6. Ekranlar (v1)

1. **Karşılama / kurulum** — yöntem seçimi (SAT / TDT / IVF), tarih girişi, döngü uzunluğu, çoğul gebelik
2. **Ana ekran** — hafta rozeti ("24h 3g"), ilerleme halkası, kalan gün, bebek boyutu, o haftanın özeti, sıradaki randevu
3. **Hafta detayı** — bebekte ne oluyor / annede ne oluyor / bu hafta ipuçları, hafta kaydırıcı
4. **Takvim** — otomatik tetkik penceresi + kullanıcı randevuları, ay ve liste görünümü
5. **Araçlar** — kick counter, kontraksiyon sayacı, kilo grafiği, tansiyon, belirti günlüğü
6. **Günlük** — belirti + ruh hali + not, karın fotoğrafı zaman tüneli
7. **Hazırlık** — hastane çantası, doğum planı notu
8. **Profil** — gebelik bilgileri, USG düzeltmesi, eş paylaşımı, bildirim tercihleri, veri indirme/silme

**Kick counter davranışı:** 28. haftadan itibaren önerilir. Oturum başlar, her dokunuş bir
`kick_event`. 10 harekete ulaşınca oturum kapanır ve geçen süre gösterilir. 2 saatte 10 harekete
ulaşılmazsa: "Doktorunuzu arayın" yönlendirmesi — sayı yorumu yapılmaz.

**Kontraksiyon sayacı:** başlat/bitir ile süre ve aralık. 5-1-1 kuralı (5 dakikada bir gelen,
1 dakika süren, 1 saattir devam eden) sağlandığında hastane yönlendirmesi gösterilir.

---

## 7. Tetkik takvimi şablonu (TR, taslak)

| Hafta | Tetkik | Kategori |
|---|---|---|
| 6–10 | İlk muayene, gebelik tespiti, temel kan sayımı | visit |
| 11–13+6 | NT (ense saydamlığı) + ikili tarama | usg |
| 16–18 | Üçlü/dörtlü tarama (tercihe bağlı) | lab |
| 18–22 | Detaylı ultrason (anomali taraması) | usg |
| 24–28 | Şeker yükleme testi (OGTT) | lab |
| 28 | Anti-D immünglobulin (Rh negatif anne) | vaccine |
| 27–36 | Tdap aşısı | vaccine |
| 28–36 | İki haftada bir kontrol | visit |
| 36–40 | Haftalık kontrol, NST | visit |

Bu tablo `screening_templates` seed'ine birebir girer; hekim gözden geçirmesinden sonra
kesinleşir. Ülke alanı ileride başka pazarlar için ayrım sağlar.

---

## 8. Kırmızı bayraklar (atlanamaz)

Uygulama teşhis koymaz, **yönlendirir**. Aşağıdaki belirtiler seçildiğinde veya ilgili ölçüm
girildiğinde tam ekran uyarı ve tek dokunuşla arama (112 / doktor numarası) gösterilir:

- Vajinal kanama
- Şiddetli veya geçmeyen baş ağrısı, görme bulanıklığı/ışık çakması, sağ üst karın ağrısı,
  ani el-yüz ödemi → preeklampsi şüphesi
- Tansiyon ≥ 140/90 girilmesi
- 28. haftadan sonra bebek hareketlerinde belirgin azalma veya kick sayımının tamamlanamaması
- Su gelmesi / amniyon sıvısı kaçağı
- 38 °C üzeri ateş
- Şiddetli, geçmeyen karın ağrısı veya düzenli erken kontraksiyon (37. haftadan önce)

Uyarı metni sabit ve nettir: *"Bu belirti acil değerlendirme gerektirebilir. Lütfen hemen
doktorunuza veya en yakın acil servise başvurun."* Ardından arama düğmesi. Erteleme yok,
"muhtemelen normaldir" yok.

---

## 9. Gebelik kaybı akışı

Bu ekran uygulamanın en çok özen isteyen yeri.

- Profil → "Gebeliği sonlandır" tek adımda ulaşılabilir, onay dilinde nazik ve yargısız
- Sebep isteğe bağlı (`birth | loss | other`) — seçmeden de kapatılabilir
- **Kapatıldığı an:** planlanmış tüm haftalık bildirimler, randevu hatırlatmaları ve pazarlama
  iletileri iptal edilir (`ended_at` sonrası hiçbir job kullanıcıya ulaşmaz)
- Veriler silinmez, arşivlenir; kullanıcı isterse dışa aktarır veya kalıcı siler
- Kayıp seçildiyse ana ekran hafta/geri sayım göstermez; destek kaynakları sunulur
- Yeni gebelik başlatma her zaman mümkün

**Teknik gereklilik:** haftalık bildirim job'ı gönderimden hemen önce `pregnancy.status`
kontrolü yapar. Kuyruğa girmiş eski bir job'ın gönderilme ihtimali sıfır olmalıdır.

---

## 10. Gizlilik ve KVKK

Hamilelik verisi KVKK m.6 kapsamında **özel nitelikli kişisel veri**.

- Kayıt sırasında açık rıza ekranı; rıza metni sürümlenir ve tarihiyle saklanır
- Sağlık kayıtları uygulama içi analitiğe ve reklam SDK'larına **hiç** gönderilmez
- Analitik yalnızca olay bazlı ve kimliksiz (hangi ekran açıldı, hangi araç kullanıldı)
- Veri dışa aktarma (JSON) ve hesabı kalıcı silme uygulama içinden, destek talebi olmadan
- Aktarımda TLS, sunucuda hassas alanlarda uygulama seviyesi şifreleme
- Sanctum token'ları `token_prefix` + hash ile saklanır, süresi dolan token'lar temizlenir
- Yedekler şifreli, saklama süresi tanımlı
- Eş paylaşımı salt-okunur ve her an iptal edilebilir

---

## 11. Test stratejisi

**Sunucu (Pest/PHPUnit)**
- Hafta motoru: ortak vektör dosyasından tablo testi (her giriş yöntemi × sınır durumu)
- Yeniden tarihleme sonrası GA'nın doğru kaydığı
- Gebelik kapatıldıktan sonra hiçbir bildirim job'ının gönderilmediği
- `/sync` idempotency: aynı `client_uuid` iki kez gönderilince tek kayıt
- Yetki: kullanıcı başkasının gebeliğine erişemez (policy testi)

**İstemci (Jest + React Native Testing Library)**
- TS hafta motoru aynı vektörlerle — PHP ile bit bazında aynı sonuç
- Kick counter: 10 harekete ulaşma, 2 saat aşımı yönlendirmesi
- Kontraksiyon 5-1-1 tetikleme
- Çevrimdışı yazma → bağlantı gelince kuyruğun boşalması

---

## 12. Yol haritası

| Sprint | Çıktı |
|---|---|
| 1 | Laravel iskeleti, OTP kimlik, `pregnancies` + hafta motoru + test vektörleri, `/pregnancies/current` |
| 2 | Expo iskeleti, kurulum akışı, ana ekran, hafta detayı, TS hafta motoru + testler |
| 3 | Filament içerik yönetimi, `week_contents` seed (1–42 hafta), çevrimdışı içerik ön yükleme |
| 4 | Tetkik şablonları + otomatik randevu üretimi, takvim ekranı, bildirim planlayıcı |
| 5 | Kick counter, kontraksiyon sayacı, kilo/tansiyon/belirti kayıtları, çevrimdışı kuyruk + `/sync` |
| 6 | Kırmızı bayrak akışı, gebelik kaybı akışı, KVKK ekranları (rıza, dışa aktarma, silme) |
| 7 | Eş paylaşımı, karın fotoğrafı, hastane çantası, mağaza hazırlığı (ikon, gizlilik formu, ekran görüntüleri) |

**v2:** doğum sonrası modu (emzirme, bez, uyku, aşı takvimi), bebek isimleri, topluluk, EN yerelleştirme.

---

## 13. Açık sorular

1. Hafta içeriğini kim yazacak? (telif riski — hazır metin kopyalanamaz, özgün yazılmalı)
2. Tıbbi gözden geçirmeyi yapacak hekim belirlendi mi?
3. Uygulama ücretsiz mi, abonelik mi? Abonelikse hangi özellik ücretli tarafta?
4. App Store "Sağlık ve Fitness" kategorisi ek gizlilik beyanı ister — kim dolduracak?
5. Bu uygulama ripehome deposunda mı kalacak, yoksa kendi deposuna mı taşınacak? (öneri: kendi deposu)
