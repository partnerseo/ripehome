# 🏠 RIPE HOME - Admin Panel Kullanım Kılavuzu

## 📖 İçindekiler

1. [Giriş Yapma](#giriş-yapma)
2. [Dashboard (Ana Sayfa)](#dashboard)
3. [Kategori Yönetimi](#kategori-yönetimi)
4. [Ürün Yönetimi](#ürün-yönetimi)
5. [Etiket Yönetimi](#etiket-yönetimi)
6. [Öne Çıkan Bölümler](#öne-çıkan-bölümler)
7. [Öne Çıkan Ürünler](#öne-çıkan-ürünler)
8. [Anasayfa Slider](#anasayfa-slider)
9. [Sayfa Yönetimi](#sayfa-yönetimi)
10. [İletişim Mesajları](#iletişim-mesajları)
11. [Toptan Siparişler](#toptan-siparişler)
12. [Site Ayarları](#site-ayarları)
13. [Resim Yükleme ve Yönetimi](#resim-yükleme)
14. [Sık Sorulan Sorular](#sss)

---

## 🔐 Giriş Yapma

### Adım 1: Admin Paneline Erişim

**URL:** `https://ripehome.com.tr/admin`

### Adım 2: Giriş Bilgileri

- **Email:** `admin@admin.com`
- **Şifre:** `20685485`

**Not:** İlk girişten sonra şifrenizi değiştirmeniz önerilir.

### Şifre Değiştirme:

1. Sağ üst köşede profil simgesine tıklayın
2. "Profil" seçeneğini seçin
3. Yeni şifrenizi girin
4. "Kaydet" butonuna tıklayın

---

## 📊 Dashboard (Ana Sayfa)

Dashboard, sitenizin genel durumunu görüntüleyebileceğiniz ana sayfadır.

### Gösterilen Bilgiler:

- **Toplam Ürün Sayısı:** Sistemdeki tüm ürünler
- **Toplam Kategori Sayısı:** Aktif kategoriler
- **Toplam İletişim Mesajı:** Gelen mesajlar
- **Toplam Toptan Sipariş:** Toptan satış talepleri

### Hızlı Erişim Menüsü:

Sol menüden aşağıdaki bölümlere hızlıca erişebilirsiniz:

- 📂 Kategoriler
- 📦 Ürünler
- 🏷️ Etiketler
- 📑 Öne Çıkan Bölümler
- ⭐ Öne Çıkan Ürünler
- 🖼️ Anasayfa Slider
- 📄 Sayfalar
- 💬 İletişim Mesajları
- 📋 Toptan Siparişler
- ⚙️ Ayarlar

---

## 📂 Kategori Yönetimi

Kategoriler, ürünlerinizi gruplamak için kullanılır.

### Yeni Kategori Ekleme:

1. Sol menüden **"Kategoriler"** seçin
2. Sağ üstteki **"Yeni Kategori"** butonuna tıklayın
3. Formu doldurun:
   - **İsim:** Kategori adı (örn: "Jakarli Kimono")
   - **Slug:** URL dostu isim (otomatik oluşur, manuel düzenlenebilir)
   - **Açıklama:** Kategori hakkında kısa bilgi
   - **Görsel:** Kategori resmi (850x850px önerilir)
   - **Sıra:** Listelemede gösterilme sırası (küçük numara önce gelir)
   - **Aktif:** Kategorinin sitede görünüp görünmeyeceği
4. **"Kaydet"** butonuna tıklayın

### Kategori Düzenleme:

1. Kategori listesinden düzenlemek istediğiniz kategorinin satırına tıklayın
2. Bilgileri güncelleyin
3. **"Kaydet"** butonuna tıklayın

### Kategori Silme:

1. Kategori listesinde silmek istediğiniz kategorinin sağındaki **üç nokta (...)** ikonuna tıklayın
2. **"Sil"** seçeneğini seçin
3. Onay penceresinde **"Evet"** butonuna tıklayın

**⚠️ DİKKAT:** Kategori silindiğinde, o kategoriye ait ürünler kategorisiz kalır!

---

## 📦 Ürün Yönetimi

Ürünler, sitenizin ana içeriğidir. Her ürün bir kategoriye ait olmalıdır.

### Yeni Ürün Ekleme:

1. Sol menüden **"Ürünler"** seçin
2. Sağ üstteki **"Yeni Ürün"** butonuna tıklayın
3. Formu doldurun:

#### 📝 Genel Bilgiler:
- **Ürün Adı:** Ürün adı (örn: "Natural Kimono - Kırmızı")
- **Slug:** URL dostu isim (otomatik oluşur, düzenlenemez)
- **Açıklama:** Ürün detayları (zengin metin editörü - biçimlendirme yapabilirsiniz)

#### 🖼️ Görseller:
- **Ürün Görselleri:** Çoklu görsel yükleme (en fazla 10 resim)
- İlk görsel ana görsel olur, diğerleri galeriydi gösterilir
- Sürükle-bırak ile sıralama yapabilirsiniz
- **Önerilen Boyut:** 800x800px veya 1200x1200px

#### 🔗 İlişkiler:
- **Kategori:** Ürünün ait olduğu kategori (dropdown'dan seçin)
  - İsterseniz buradan yeni kategori de ekleyebilirsiniz
- **Etiketler:** Ürüne ait etiketleri seçin (checkboxlist - çoklu seçim)

#### ⚙️ Özellikler:
- **Ürün Özellikleri:** (Repeater - ekle/çıkar)
  - **İkon:** Heroicon adı (örn: `heroicon-o-star`)
  - **Başlık:** Özellik başlığı (örn: "Yüksek Emicilik")
  - **Açıklama:** Özellik detayı

**💡 İpucu:** Özellikler ürün detay sayfasında ikons halinde gösterilir.

#### 🔍 SEO (Opsiyonel):
- **Meta Başlık:** Arama motorları için başlık (max 255 karakter)
- **Meta Açıklama:** Arama motorları için açıklama

#### 📢 Yayın Ayarları:
- **Sıra:** Listede gösterilme sırası (küçük önce gelir)
- **Aktif:** Ürünün sitede görünüp görünmeyeceği
- **Öne Çıkan:** Anasayfada öne çıkan bölümde göster

**⚠️ NOT:** Bu sistemde fiyat bilgisi YOK. Sadece gösterim amaçlı katalog sitesidir.

4. **"Kaydet"** butonuna tıklayın

### Toplu İşlemler:

Birden fazla ürünü aynı anda işleyebilirsiniz:

1. Ürün listesinde işlem yapmak istediğiniz ürünlerin solundaki kutucuğu işaretleyin
2. Üst menüden işlem seçin:
   - **Aktif Yap:** Seçili ürünleri yayına al
   - **Pasif Yap:** Seçili ürünleri yayından kaldır
   - **Sil:** Seçili ürünleri sil

### Ürün Filtreleme:

Ürün listesinin üstündeki filtreleri kullanarak arama yapabilirsiniz:

- **Arama:** Ürün adına göre ara
- **Kategori:** Kategoriye göre filtrele
- **Durum:** Aktif/Pasif ürünleri filtrele
- **Öne Çıkan:** Sadece öne çıkan ürünleri göster

---

## 🏷️ Etiket Yönetimi

Etiketler, ürünlerinizi anahtar kelimelerle etiketlemek için kullanılır.

### Yeni Etiket Ekleme:

1. Sol menüden **"Etiketler"** seçin
2. **"Yeni Etiket"** butonuna tıklayın
3. Formu doldurun:
   - **İsim:** Etiket adı (örn: "Bornoz", "Kimono", "Natural")
   - **Slug:** URL dostu isim (otomatik oluşur)
4. **"Kaydet"** butonuna tıklayın

### Etiketleri Kullanma:

Ürün eklerken veya düzenlerken **"Etiketler"** alanından:
- Mevcut etiketleri seçebilirsiniz
- Yeni etiket oluşturabilirsiniz (yazmaya başlayın ve Enter'a basın)

**💡 İpucu:** Etiketler SEO için önemlidir. İlgili ve açıklayıcı etiketler kullanın.

---

## 📑 Öne Çıkan Bölümler

Anasayfada ürün kategorilerini öne çıkaran özel bölümlerdir.

### Yeni Öne Çıkan Bölüm Ekleme:

1. Sol menüden **"Öne Çıkan Bölümler"** seçin
2. **"Yeni Öne Çıkan Bölüm"** butonuna tıklayın
3. Formu doldurun:
   - **Başlık:** Bölüm başlığı (örn: "Yeni Sezon Ürünleri")
   - **Açıklama:** Bölüm açıklaması
   - **Görsel:** Bölüm görseli (1200x600px önerilir)
   - **Link:** Tıklandığında gidilecek sayfa
   - **Buton Metni:** "Tümünü Gör", "Keşfet" gibi
   - **Sıra:** Gösterilme sırası
   - **Aktif:** Anasayfada göster/gizle
4. **"Kaydet"** butonuna tıklayın

**Kullanım Örneği:**
- "Yeni Sezon Koleksiyonu"
- "En Çok Satanlar"
- "İndirimli Ürünler"
- "Özel Tasarım"

---

## ⭐ Öne Çıkan Ürünler

Anasayfada büyük kartlar halinde gösterilen özel ürünlerdir.

### Yeni Öne Çıkan Ürün Ekleme:

1. Sol menüden **"Öne Çıkan Ürünler"** seçin
2. **"Yeni Öne Çıkan Ürün"** butonuna tıklayın
3. Formu doldurun:

#### Genel Bilgiler:
- **Üst Etiket:** Kategori etiketi (örn: "PREMIUM SPA DENEYİMİ")
- **Başlık:** Ana başlık (örn: "Özel Dokuma Waffle Bornoz")
- **Açıklama:** Ürün açıklaması (4-5 satır, detaylı)

#### Görsel:
- **Ana Görsel:** Öne çıkan ürün görseli (1200x800px önerilir)

#### Etiketler ve Buton:
- **Etiketler:** Ürün özellikleri (örn: "%100 Pamuk", "Yüksek Emicilik")
  - Enter ile yeni etiket ekleyin
- **Buton Metni:** "Detayları Gör", "İncele" gibi
- **Buton Linki:** Tıklandığında gidilecek sayfa (örn: `/products/waffle-bornoz`)

#### Ayarlar:
- **Sıra:** Gösterilme sırası
- **Aktif:** Anasayfada göster/gizle

4. **"Kaydet"** butonuna tıklayın

**💡 İpucu:** Anasayfada en fazla 3-4 öne çıkan ürün gösterilmesi önerilir.

---

## 🖼️ Anasayfa Slider

Anasayfanın en üstünde dönen banner/slider görselleridir.

### Yeni Slider Ekleme:

1. Sol menüden **"Anasayfa Slider"** seçin
2. **"Yeni Slider"** butonuna tıklayın
3. Formu doldurun:
   - **Başlık:** Slider üzerindeki ana başlık (ZORUNLU)
   - **Alt Başlık:** İkincil metin (Opsiyonel)
   - **Buton Metni:** "Keşfet", "Alışverişe Başla" gibi (Opsiyonel)
   - **Buton Linki:** Tıklandığında gidilecek sayfa (Opsiyonel, URL formatında)
   - **Slider Görseli:** Banner resmi (1920x800px önerilir) - ZORUNLU
   - **Sıra:** Gösterilme sırası (küçük numara önce gelir)
   - **Aktif:** Slider'ı yayında göster/gizle
4. **"Kaydet"** butonuna tıklayın

**📱 Mobil Uyumluluk:** Aynı görsel hem masaüstü hem mobilde gösterilir (responsive).

---

## 📄 Sayfa Yönetimi

Kurumsal sayfalar (Hakkımızda, İletişim, Gizlilik Politikası vb.) oluşturabilirsiniz.

### Yeni Sayfa Ekleme:

1. Sol menüden **"Sayfalar"** seçin
2. **"Yeni Sayfa"** butonuna tıklayın
3. Formu doldurun:
   - **Başlık:** Sayfa başlığı
   - **Slug:** URL (örn: `hakkimizda` → `/hakkimizda`)
   - **İçerik:** Sayfa içeriği (Zengin metin editörü)
   - **Meta Başlık:** SEO başlığı
   - **Meta Açıklama:** SEO açıklaması
   - **Aktif:** Sayfayı yayında göster/gizle
4. **"Kaydet"** butonuna tıklayın

### Zengin Metin Editörü Kullanımı:

Editörde şunları yapabilirsiniz:

- **Kalın/İtalik:** Metin biçimlendirme
- **Başlıklar:** H1, H2, H3 başlıkları
- **Listeler:** Sıralı/sırasız listeler
- **Linkler:** Bağlantı ekleme
- **Resimler:** Görsel yükleme
- **Tablolar:** Tablo oluşturma

---

## 💬 İletişim Mesajları

Siteden gelen iletişim formları burada listelenir.

### Mesaj Görüntüleme:

1. Sol menüden **"İletişim Mesajları"** seçin
2. Mesaj listesinde görmek istediğiniz mesaja tıklayın
3. Detaylar açılır:
   - **İsim:** Gönderenin adı
   - **Email:** İletişim email adresi
   - **Telefon:** (Varsa) Telefon numarası
   - **Konu:** Mesaj konusu
   - **Mesaj:** Mesaj içeriği
   - **Tarih:** Gönderilme tarihi

### Mesaj Silme:

1. Mesaj detayında sağ üstteki **"Sil"** butonuna tıklayın
2. Onaylayın

**📧 Email Bildirimi:** Yeni mesaj geldiğinde otomatik email bildirimi alırsınız (ayarlardan açılabilir).

---

## 📋 Toptan Siparişler

Toptan satış talepleri burada listelenir.

### Sipariş Görüntüleme:

1. Sol menüden **"Toptan Siparişler"** seçin
2. Sipariş listesinde görmek istediğiniz siparişe tıklayın
3. Detaylar:
   - **Şirket Adı:** Firma bilgisi
   - **İletişim Bilgileri:** Ad, email, telefon
   - **Adres:** Teslimat adresi
   - **Vergi Bilgileri:** Vergi dairesi, numarası
   - **Ürünler:** Sipariş edilen ürünler ve miktarlar
   - **Not:** Müşteri notu
   - **Tarih:** Sipariş tarihi
   - **Durum:** Beklemede/İşlemde/Tamamlandı

### Sipariş Durumu Güncelleme:

1. Sipariş detayında **"Durum"** alanını değiştirin
2. **"Kaydet"** butonuna tıklayın

**📱 Telegram Bildirimi:** Yeni toptan sipariş geldiğinde Telegram'dan bildirim alırsınız.

---

## ⚙️ Site Ayarları

Genel site ayarlarını buradan yönetebilirsiniz.

### Ayarlar Sayfası:

Sol menüden **"Ayarlar"** seçin.

#### 🏢 Genel Bilgiler:
- **Site Adı:** Ripe Home
- **Site Başlığı:** SEO için ana başlık
- **Site Açıklaması:** Meta açıklama
- **Logo:** Site logosu (PNG, 200x80px)
- **Favicon:** Tarayıcı ikonu (32x32px)

#### 📞 İletişim Bilgileri:
- **Telefon:** Müşteri hizmetleri telefonu
- **Email:** İletişim email adresi
- **Adres:** Şirket adresi
- **Çalışma Saatleri:** Mesai saatleri

#### 🌐 Sosyal Medya:
- **Facebook:** Facebook profil linki
- **Instagram:** Instagram profil linki
- **Twitter:** Twitter profil linki
- **LinkedIn:** LinkedIn profil linki
- **WhatsApp:** WhatsApp numarası

#### 📧 Email Ayarları:
- **SMTP Sunucu:** Mail sunucusu
- **SMTP Port:** Port numarası
- **SMTP Kullanıcı:** Email adresi
- **SMTP Şifre:** Email şifresi

#### 🔔 Bildirim Ayarları:
- **Email Bildirimleri:** Açık/Kapalı
- **Telegram Bildirimleri:** Açık/Kapalı
- **Telegram Bot Token:** Bot API anahtarı
- **Telegram Chat ID:** Bildirim gönderilecek chat

**Kaydet:** Değişiklikleri kaydetmeyi unutmayın!

---

## 🖼️ Resim Yükleme ve Yönetimi

### Resim Yükleme Adımları:

1. İlgili bölümde (Ürün, Kategori, vb.) resim alanına tıklayın
2. **"Dosya Seç"** butonuna tıklayın
3. Bilgisayarınızdan resmi seçin
4. Resim otomatik yüklenir

### Resim Boyutları (Önerilen):

| Bölüm | Boyut | Format |
|-------|-------|--------|
| Kategori Görseli | 850x850px | JPG/PNG |
| Ürün Görselleri | 800x800px veya 1200x1200px | JPG/PNG |
| Öne Çıkan Ürün | 1200x800px | JPG/PNG |
| Slider | 1920x800px | JPG |
| Logo | 200x80px | PNG |
| Favicon | 32x32px | PNG/ICO |

### Resim Optimizasyonu:

**💡 İpuçları:**
- Resimleri yüklemeden önce optimize edin (TinyPNG, ImageOptim)
- JPG formatı fotoğraflar için, PNG şeffaf görseller için kullanın
- Maksimum dosya boyutu: 2MB
- Yüksek çözünürlüklü resimler kullanın (Retina için 2x)

### Çoklu Resim Yükleme:

Ürün galeri gibi alanlarda birden fazla resim yükleyebilirsiniz:

1. Resim alanına tıklayın
2. Bilgisayarınızdan birden fazla resim seçin (Ctrl/Cmd+Click)
3. Veya sürükle-bırak yapın
4. Tüm resimler yüklenir

**Sıralama:** Resimleri sürükleyerek sırasını değiştirebilirsiniz.

---

## 🔧 Sık Sorulan Sorular (SSS)

### 1. **Ürün görseli yüklenmiyor?**

**Çözüm:**
- Dosya boyutunun 2MB'dan küçük olduğundan emin olun
- Dosya formatının JPG veya PNG olduğunu kontrol edin
- Tarayıcınızı yenileyin ve tekrar deneyin

### 2. **Değişiklikler sitede görünmüyor?**

**Çözüm:**
- Tarayıcı önbelleğini temizleyin (Ctrl+F5)
- Gizli pencerede kontrol edin
- 5-10 dakika bekleyin (cache süresi)

### 3. **Şifremi unuttum?**

**Çözüm:**
- Giriş sayfasında "Şifremi Unuttum" linkine tıklayın
- Email adresinizi girin
- Gelen linke tıklayarak yeni şifre oluşturun

### 4. **Kategorideki ürünler görünmüyor?**

**Kontrol:**
- Ürünlerin "Aktif" olduğundan emin olun
- Ürünlerin doğru kategoriye atandığını kontrol edin
- Kategori'nin de "Aktif" olduğunu kontrol edin

### 5. **Slider görünmüyor?**

**Kontrol:**
- Slider'ın "Aktif" olduğundan emin olun
- Görsel yüklendiğini kontrol edin
- Tarayıcı önbelleğini temizleyin

### 6. **Toplu işlem yapamıyorum?**

**Çözüm:**
- Ürünlerin solundaki kutucukları işaretleyin
- Üst menüden toplu işlem seçin
- İşlem tamamlanana kadar bekleyin

### 7. **Email bildirimleri gelmiyor?**

**Kontrol:**
- Ayarlar → Email Ayarları → doğru yapılandırıldığından emin olun
- SMTP bilgilerini kontrol edin
- Spam klasörünü kontrol edin

### 8. **Telegram bildirimleri gelmiyor?**

**Kontrol:**
- Ayarlar → Telegram Ayarları → Bot Token ve Chat ID doğru mu?
- Bot'u Telegram'da başlattınız mı? (/start komutu)

---

## 💡 İpuçları ve Püf Noktaları

### 🎯 SEO İpuçları:

1. **URL (Slug):** Her ürün ve kategoriye özgün, açıklayıcı URL kullanın
2. **Meta Başlık:** 60 karakter, anahtar kelime içeren
3. **Meta Açıklama:** 160 karakter, ürünü tanımlayan
4. **Görsel Alt Metni:** Görsellere açıklayıcı alt metin ekleyin

### 📱 Mobil Uyumluluk:

1. **Slider:** Hem masaüstü hem mobil görsel yükleyin
2. **Başlıklar:** Kısa ve öz tutun (mobilde uzun başlıklar kesilir)
3. **Test:** Değişiklikleri hem masaüstü hem mobilde test edin

### ⚡ Performans:

1. **Görseller:** Her zaman optimize edilmiş resim kullanın
2. **Açıklama:** Çok uzun açıklamalar yerine öz ve etkili yazın
3. **Kategori:** Ürünleri mantıklı kategorilere ayırın

### 🎨 Tasarım:

1. **Tutarlılık:** Benzer ürünlerde benzer görseller kullanın
2. **Beyaz Arka Plan:** Ürün fotoğraflarında beyaz/açık arka plan tercih edin
3. **Kalite:** Yüksek çözünürlüklü, profesyonel fotoğraflar kullanın

### 📊 İçerik Yönetimi:

1. **Düzenli Güncelleme:** Ürün ve kategori açıklamalarını güncel tutun
2. **Stok Kontrolü:** Tükenen ürünleri "Pasif" yapın
3. **Fiyat:** Fiyat değişikliklerini düzenli kontrol edin

### 🔐 Güvenlik:

1. **Şifre:** Güçlü şifre kullanın (büyük/küçük harf, rakam, sembol)
2. **Oturum:** İşiniz bittiğinde çıkış yapın
3. **Yedekleme:** Düzenli yedek alın

---

## 📞 Destek

Herhangi bir sorunuz veya sorununuz olursa:

- **Email:** info@ripehome.com.tr
- **Telefon:** 0534 573 06 69
- **WhatsApp:** +90 534 573 06 69
- **Adres:** Sevindik mahallesi 2291 sokak No: 7 Merkezefendi / Denizli

**Çalışma Saatleri:** Hafta içi 09:00 - 18:00

---

## ✅ Hızlı Başlangıç Kontrol Listesi

İlk kez kullanıyorsanız şu adımları izleyin:

- [ ] Giriş yapın ve şifrenizi değiştirin
- [ ] Site ayarlarını kontrol edin (Logo, İletişim bilgileri)
- [ ] En az 3 kategori oluşturun
- [ ] Her kategoriye 5-10 ürün ekleyin
- [ ] Anasayfa slider'a 3-5 görsel ekleyin
- [ ] 2-3 öne çıkan ürün belirleyin
- [ ] "Hakkımızda" ve "İletişim" sayfalarını oluşturun
- [ ] Email/Telegram bildirimlerini ayarlayın
- [ ] Sitede test edin ve kontrol edin

---

**Son Güncelleme:** 13 Ocak 2026  
**Versiyon:** 1.0  
**Hazırlayan:** Ripe Home Teknik Ekibi

---

© 2026 Ripe Home. Tüm hakları saklıdır.

