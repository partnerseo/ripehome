#!/bin/bash
# Ripe Home - Sunucu Düzeltme Komutları
# SSH'de çalıştır: bash SSH_KOMUTLARI.sh

echo "════════════════════════════════════════════════════════════"
echo "🚀 Ripe Home - Sunucu Düzeltme Başlıyor..."
echo "════════════════════════════════════════════════════════════"
echo ""

# 1. Backend klasörüne git
echo "📂 Backend klasörüne gidiliyor..."
cd /home/ripehome/public_html/api || exit

# 2. Debug modunu aç
echo "🔧 Debug modu açılıyor..."
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
echo "✅ Debug modu: AÇIK"
echo ""

# 3. Cache temizle
echo "🧹 Cache temizleniyor..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Cache temizlendi"
echo ""

# 4. Dosya izinlerini düzelt
echo "🔐 Dosya izinleri düzeltiliyor..."
chmod -R 775 storage bootstrap/cache
chown -R $(whoami):$(whoami) storage bootstrap/cache
echo "✅ İzinler düzeltildi"
echo ""

# 5. Storage link oluştur
echo "🔗 Storage link oluşturuluyor..."
php artisan storage:link
echo "✅ Storage link oluşturuldu"
echo ""

# 6. Storage link kontrolü
echo "📋 Storage link kontrol ediliyor..."
ls -la public/ | grep storage
echo ""

# 7. Database tablolarını kontrol et
echo "📊 Database tabloları kontrol ediliyor..."
php artisan tinker --execute="
echo '════════════════════════════════════════════════════════════' . PHP_EOL;
echo '📊 DATABASE TABLO SAYILARI:' . PHP_EOL;
echo '════════════════════════════════════════════════════════════' . PHP_EOL;
try { echo '✅ Categories: ' . \DB::table('categories')->count() . PHP_EOL; } catch (\Exception \$e) { echo '❌ Categories: TABLO YOK VEYA HATA!' . PHP_EOL; }
try { echo '✅ Products: ' . \DB::table('products')->count() . PHP_EOL; } catch (\Exception \$e) { echo '❌ Products: TABLO YOK VEYA HATA!' . PHP_EOL; }
try { echo '✅ Featured Products: ' . \DB::table('featured_products')->count() . PHP_EOL; } catch (\Exception \$e) { echo '❌ Featured Products: TABLO YOK!' . PHP_EOL; }
try { echo '✅ Home Sliders: ' . \DB::table('home_sliders')->count() . PHP_EOL; } catch (\Exception \$e) { echo '❌ Home Sliders: TABLO YOK!' . PHP_EOL; }
try { echo '✅ Featured Sections: ' . \DB::table('featured_sections')->count() . PHP_EOL; } catch (\Exception \$e) { echo '❌ Featured Sections: TABLO YOK!' . PHP_EOL; }
echo '════════════════════════════════════════════════════════════' . PHP_EOL;
"
echo ""

# 8. Route listesini göster
echo "🛣️  Route listesi:"
php artisan route:list --path=api | head -20
echo ""

# 9. Log dosyasını göster
echo "📝 Son Laravel log kayıtları (SON 30 SATIR):"
echo "════════════════════════════════════════════════════════════"
tail -30 storage/logs/laravel.log 2>/dev/null || echo "⚠️  Log dosyası bulunamadı!"
echo "════════════════════════════════════════════════════════════"
echo ""

echo "✅ Tüm kontroller tamamlandı!"
echo ""
echo "🧪 ŞİMDİ TEST ET:"
echo "════════════════════════════════════════════════════════════"
echo "curl https://ripehome.com.tr/api/ping"
echo "curl https://ripehome.com.tr/api/test-db"
echo "curl https://ripehome.com.tr/api/categories"
echo "════════════════════════════════════════════════════════════"


