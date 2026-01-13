<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Hakkımızda',
                'slug' => 'hakkimizda',
                'content' => '<h2>Luxury Home Textiles Hakkında</h2>
<p>1995 yılından bu yana, ev tekstili sektöründe kalite ve güvenin adresi olarak hizmet vermekteyiz. Müşterilerimize en iyi ürünleri sunma misyonuyla yola çıktık ve bugün Türkiye\'nin önde gelen ev tekstili markalarından biri haline geldik.</p>

<h3>Vizyonumuz</h3>
<p>Her evde konfor, kalite ve şıklığı bir araya getiren ev tekstili ürünleri sunmak. Doğaya saygılı, sürdürülebilir üretim anlayışıyla sektörde öncü olmak.</p>

<h3>Misyonumuz</h3>
<p>Müşteri memnuniyetini ön planda tutarak, organik ve doğal malzemelerden üretilmiş, sağlığa ve çevreye duyarlı ürünler sunmak. Her üründe mükemmelliği hedeflemek.</p>

<h3>Değerlerimiz</h3>
<ul>
<li><strong>Kalite:</strong> Her üründe en yüksek kalite standardı</li>
<li><strong>Güven:</strong> Müşterilerimizle uzun vadeli güven ilişkisi</li>
<li><strong>Yenilik:</strong> Sürekli gelişim ve yenilikçi ürünler</li>
<li><strong>Doğa Dostu:</strong> Çevre bilinci ve sürdürülebilirlik</li>
</ul>',
                'meta_title' => 'Hakkımızda - Luxury Home Textiles',
                'meta_description' => 'Luxury Home Textiles hakkında bilgi edinin. Vizyonumuz, misyonumuz ve değerlerimiz.',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Gizlilik Politikası',
                'slug' => 'gizlilik-politikasi',
                'content' => '<h2>Gizlilik Politikası</h2>
<p>Luxury Home Textiles olarak müşterilerimizin gizliliğine önem veriyoruz. Bu sayfa, kişisel verilerinizin nasıl toplandığını, kullanıldığını ve korunduğunu açıklar.</p>

<h3>Toplanan Bilgiler</h3>
<p>Web sitemizi ziyaret ettiğinizde veya alışveriş yaptığınızda aşağıdaki bilgiler toplanabilir:</p>
<ul>
<li>İsim, e-posta adresi, telefon numarası</li>
<li>Teslimat adresi ve fatura bilgileri</li>
<li>Ödeme bilgileri (güvenli şekilde)</li>
<li>Tarayıcı ve cihaz bilgileri</li>
</ul>

<h3>Bilgilerin Kullanımı</h3>
<p>Topladığımız bilgiler şu amaçlarla kullanılır:</p>
<ul>
<li>Siparişlerinizi işleme almak ve teslim etmek</li>
<li>Müşteri hizmetleri sağlamak</li>
<li>Ürün ve hizmetlerimizi geliştirmek</li>
<li>Kampanya ve duyurular göndermek (izninizle)</li>
</ul>

<h3>Veri Güvenliği</h3>
<p>Kişisel verileriniz, yetkisiz erişime, kullanıma veya ifşaya karşı uygun güvenlik önlemleriyle korunmaktadır. SSL şifreleme teknolojisi kullanarak bilgilerinizi güvende tutuyoruz.</p>

<h3>İletişim</h3>
<p>Gizlilik politikamız hakkında sorularınız için: info@luxuryhome.com</p>',
                'meta_title' => 'Gizlilik Politikası - Luxury Home',
                'meta_description' => 'Luxury Home Textiles gizlilik politikası. Kişisel verileriniz nasıl korunur?',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Kargo ve İade',
                'slug' => 'kargo-ve-iade',
                'content' => '<h2>Kargo ve İade Koşulları</h2>

<h3>Kargo Bilgileri</h3>
<p>Siparişleriniz, özenle paketlenerek anlaşmalı kargo firmaları aracılığıyla tarafınıza ulaştırılır.</p>

<h4>Teslimat Süreleri</h4>
<ul>
<li><strong>Şehir içi:</strong> 1-2 iş günü</li>
<li><strong>Türkiye geneli:</strong> 2-5 iş günü</li>
<li><strong>Yurtdışı:</strong> 7-15 iş günü (destinasyona göre değişir)</li>
</ul>

<h4>Kargo Ücretleri</h4>
<ul>
<li>500 TL ve üzeri alışverişlerde <strong>ücretsiz kargo</strong></li>
<li>500 TL altı alışverişlerde 29,90 TL kargo ücreti</li>
</ul>

<h3>İade Koşulları</h3>
<p>Ürünlerimizi teslim aldıktan sonra 14 gün içinde iade edebilirsiniz.</p>

<h4>İade Şartları</h4>
<ul>
<li>Ürün kullanılmamış ve orijinal ambalajında olmalı</li>
<li>Ürün etiketi sökülmemiş olmalı</li>
<li>Hijyen gerektiren ürünler (iç çamaşırı vb.) açılmamış olmalı</li>
<li>Fatura ve iade formu eksiksiz doldurulmalı</li>
</ul>

<h4>İade Süreci</h4>
<ol>
<li>İade talebinizi web sitemiz veya müşteri hizmetleri üzerinden oluşturun</li>
<li>Ürünü orijinal ambalajı ile birlikte kargo ile gönderin</li>
<li>İade onayı sonrası 5-7 iş günü içinde ödemeniz iade edilir</li>
</ol>

<h3>İletişim</h3>
<p>Kargo ve iade ile ilgili sorularınız için:<br>
Telefon: +90 555 123 4567<br>
E-posta: siparis@luxuryhome.com</p>',
                'meta_title' => 'Kargo ve İade Koşulları - Luxury Home',
                'meta_description' => 'Kargo teslimat süreleri ve iade koşulları hakkında detaylı bilgi.',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'Kullanım Koşulları',
                'slug' => 'kullanim-kosullari',
                'content' => '<h2>Kullanım Koşulları</h2>
<p>Bu web sitesini kullanarak aşağıdaki şartları ve koşulları kabul etmiş sayılırsınız.</p>

<h3>Hizmet Kullanımı</h3>
<p>Web sitemizi ziyaret ettiğinizde ve hizmetlerimizi kullandığınızda:</p>
<ul>
<li>18 yaşından büyük olduğunuzu veya yasal bir vasinin onayı ile hareket ettiğinizi kabul edersiniz</li>
<li>Doğru ve güncel bilgiler sağlamayı taahhüt edersiniz</li>
<li>Hesap bilgilerinizin güvenliğinden sorumlu olduğunuzu kabul edersiniz</li>
</ul>

<h3>Fikri Mülkiyet</h3>
<p>Bu web sitesindeki tüm içerik, görseller, logolar ve tasarımlar Luxury Home Textiles\'e aittir ve telif hakları ile korunmaktadır. İzinsiz kullanımı yasaktır.</p>

<h3>Fiyat ve Ödeme</h3>
<ul>
<li>Web sitesinde belirtilen fiyatlar KDV dahildir</li>
<li>Fiyatlar önceden haber verilmeksizin değiştirilebilir</li>
<li>Ödeme güvenliği için SSL sertifikası kullanılmaktadır</li>
<li>Kredi kartı, banka kartı ve havale ile ödeme kabul edilir</li>
</ul>

<h3>Sorumluluk Sınırlaması</h3>
<p>Web sitemizde yer alan ürün bilgileri mümkün olduğunca doğru ve güncel tutulmaya çalışılsa da, teknik hatalar veya yazım yanlışlıkları olabilir. Bu durumda sorumluluk kabul edilmez.</p>

<h3>Değişiklikler</h3>
<p>Luxury Home Textiles, bu kullanım koşullarını önceden haber vermeksizin değiştirme hakkını saklı tutar. Değişiklikler web sitesinde yayınlandığı anda yürürlüğe girer.</p>

<h3>İletişim</h3>
<p>Kullanım koşulları hakkında sorularınız için: info@luxuryhome.com</p>

<p><em>Son güncelleme: Ocak 2025</em></p>',
                'meta_title' => 'Kullanım Koşulları - Luxury Home',
                'meta_description' => 'Luxury Home Textiles web sitesi kullanım koşulları ve şartları.',
                'is_active' => true,
                'order' => 4,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}













