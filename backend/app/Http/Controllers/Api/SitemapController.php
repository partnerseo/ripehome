<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    private array $langs = ['tr', 'en', 'ar', 'ru', 'de'];

    private array $S = [
        'about'     => ['tr' => 'hakkimizda', 'en' => 'about', 'ar' => 'hawlana', 'ru' => 'o-nas', 'de' => 'ueber-uns'],
        'contact'   => ['tr' => 'iletisim', 'en' => 'contact', 'ar' => 'tawasul', 'ru' => 'kontakty', 'de' => 'kontakt'],
        'products'  => ['tr' => 'urunler', 'en' => 'products', 'ar' => 'muntajat', 'ru' => 'produktsiya', 'de' => 'produkte'],
        'wholesale' => ['tr' => 'toptan-siparis', 'en' => 'wholesale', 'ar' => 'talab-jumla', 'ru' => 'optovyy-zakaz', 'de' => 'grosshandel'],
        'faq'       => ['tr' => 'sss', 'en' => 'faq', 'ar' => 'asila', 'ru' => 'voprosy', 'de' => 'haeufige-fragen'],
        'shipping'  => ['tr' => 'kargo-teslimat', 'en' => 'shipping-delivery', 'ar' => 'tawsil', 'ru' => 'dostavka', 'de' => 'versand'],
        'returns'   => ['tr' => 'iade-degisim', 'en' => 'returns-exchange', 'ar' => 'istibdal', 'ru' => 'vozvrat-obmen', 'de' => 'ruckgabe'],
        'privacy'   => ['tr' => 'gizlilik-politikasi', 'en' => 'privacy-policy', 'ar' => 'khososiya', 'ru' => 'konfidentsialnost', 'de' => 'datenschutz'],
        'terms'     => ['tr' => 'kullanim-kosullari', 'en' => 'terms-of-service', 'ar' => 'shurut-istikhdam', 'ru' => 'usloviya-ispolzovaniya', 'de' => 'nutzungsbedingungen'],
        'blog'      => ['tr' => 'blog', 'en' => 'blog', 'ar' => 'mudawana', 'ru' => 'blog', 'de' => 'blog'],
    ];
    private array $CAT = ['tr' => 'kategori', 'en' => 'category', 'ar' => 'fia', 'ru' => 'kategoriya', 'de' => 'kategorie'];
    private array $PRD = ['tr' => 'urun', 'en' => 'product', 'ar' => 'muntaj', 'ru' => 'tovar', 'de' => 'produkt'];

    public function index(): Response
    {
        $base = 'https://' . (request()->getHost() ?: 'ripehome.com.tr');
        $rows = [];

        $rows[] = $this->alts(fn($l) => "/$l", '1.0');
        foreach ($this->S as $slugs) {
            $rows[] = $this->alts(fn($l) => "/$l/" . $slugs[$l], '0.8');
        }
        foreach (Product::query()->where('is_active', true)->get(['slug', 'slug_en', 'slug_ar', 'slug_ru', 'slug_de']) as $p) {
            $rows[] = $this->alts(fn($l) => "/$l/" . $this->PRD[$l] . '/' . $this->slug($p, $l), '0.7');
        }
        foreach (Category::query()->where('is_active', true)->get(['slug', 'slug_en', 'slug_ar', 'slug_ru', 'slug_de']) as $c) {
            $rows[] = $this->alts(fn($l) => "/$l/" . $this->CAT[$l] . '/' . $this->slug($c, $l), '0.6');
        }
        foreach (BlogPost::query()->where('is_published', true)->get(['slug', 'slug_en', 'slug_ar', 'slug_ru', 'slug_de']) as $b) {
            $rows[] = $this->alts(fn($l) => "/$l/" . $this->S['blog'][$l] . '/' . $this->slug($b, $l), '0.6');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        foreach ($rows as $r) {
            foreach ($this->langs as $l) {
                $xml .= "<url><loc>" . htmlspecialchars($base . $r['paths'][$l]) . "</loc>";
                foreach ($this->langs as $a) {
                    $xml .= '<xhtml:link rel="alternate" hreflang="' . $a . '" href="' . htmlspecialchars($base . $r['paths'][$a]) . '"/>';
                }
                $xml .= '<xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($base . $r['paths']['tr']) . '"/>';
                $xml .= "<priority>{$r['priority']}</priority></url>\n";
            }
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $base = 'https://' . (request()->getHost() ?: 'ripehome.com.tr');
        $txt = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /api\n\nSitemap: $base/sitemap.xml\n";
        return response($txt, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function slug($m, string $l): string
    {
        if ($l === 'tr') return $m->slug;
        return $m->{"slug_$l"} ?: $m->slug;
    }

    private function alts(\Closure $fn, string $priority): array
    {
        $paths = [];
        foreach ($this->langs as $l) $paths[$l] = $fn($l);
        return ['paths' => $paths, 'priority' => $priority];
    }
}
