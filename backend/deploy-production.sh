#!/bin/bash

# =============================================================================
# Ripe Home - Production Deployment Script
# =============================================================================
# Bu script'i production sunucusunda çalıştırın
# Kullanım: bash deploy-production.sh
# =============================================================================

set -e  # Hata durumunda dur

echo "🚀 Ripe Home - Production Deployment Başlıyor..."
echo "================================================"

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Composer dependencies (production mode)
echo -e "\n${YELLOW}📦 Composer dependencies yükleniyor...${NC}"
composer install --optimize-autoloader --no-dev

# 2. .env dosyası kontrolü
echo -e "\n${YELLOW}🔍 .env dosyası kontrol ediliyor...${NC}"
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env dosyası bulunamadı!${NC}"
    echo "Lütfen .env.production.example dosyasını .env olarak kopyalayın ve düzenleyin"
    echo "cp .env.production.example .env"
    exit 1
fi

# APP_KEY kontrolü
if ! grep -q "APP_KEY=base64:" .env; then
    echo -e "${YELLOW}🔑 APP_KEY oluşturuluyor...${NC}"
    php artisan key:generate --force
fi

# 3. Storage link oluştur
echo -e "\n${YELLOW}🔗 Storage link oluşturuluyor...${NC}"
php artisan storage:link --force

# 4. İzinleri ayarla
echo -e "\n${YELLOW}🔐 Dosya izinleri ayarlanıyor...${NC}"
chmod -R 775 storage bootstrap/cache
# Production sunucunuzun web server kullanıcısına göre ayarlayın (www-data, apache, nginx vb.)
# chown -R www-data:www-data storage bootstrap/cache public/storage

# 5. Cache'leri temizle
echo -e "\n${YELLOW}🧹 Cache temizleniyor...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Production için cache oluştur
echo -e "\n${YELLOW}⚡ Production cache'leri oluşturuluyor...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Database migration (dikkatli!)
echo -e "\n${YELLOW}💾 Database migration...${NC}"
read -p "Database migration çalıştırılsın mı? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    echo -e "${GREEN}✅ Migration tamamlandı${NC}"
else
    echo -e "${YELLOW}⏭️  Migration atlandı${NC}"
fi

# 8. Storage izinlerini kontrol et
echo -e "\n${YELLOW}📂 Storage izinleri kontrol ediliyor...${NC}"
if [ -d "storage/app/public/products" ]; then
    chmod -R 755 storage/app/public
    echo -e "${GREEN}✅ Storage izinleri ayarlandı${NC}"
else
    echo -e "${RED}⚠️  storage/app/public/products klasörü bulunamadı${NC}"
fi

# 9. Public storage izinleri
if [ -L "public/storage" ]; then
    chmod -R 755 public/storage
    echo -e "${GREEN}✅ Public storage izinleri ayarlandı${NC}"
else
    echo -e "${RED}⚠️  public/storage symbolic link bulunamadı${NC}"
    echo "Çalıştırın: php artisan storage:link"
fi

# 10. Test API endpoint
echo -e "\n${YELLOW}🧪 API endpoint test ediliyor...${NC}"
if [ -f "public/index.php" ]; then
    echo -e "${GREEN}✅ public/index.php mevcut${NC}"
else
    echo -e "${RED}❌ public/index.php bulunamadı!${NC}"
fi

# 11. Log dosyası oluştur
echo -e "\n${YELLOW}📝 Log dosyası kontrol ediliyor...${NC}"
touch storage/logs/laravel.log
chmod 775 storage/logs/laravel.log

echo ""
echo "================================================"
echo -e "${GREEN}✅ Deployment tamamlandı!${NC}"
echo "================================================"
echo ""
echo "📋 Sonraki adımlar:"
echo "1. .env dosyasını kontrol edin (APP_URL, ASSET_URL, DB bilgileri)"
echo "2. Frontend build dosyalarını deploy edin"
echo "3. public_html/.htaccess dosyasını kontrol edin"
echo "4. Test URL'leri:"
echo "   - https://yourdomain.com/api/categories"
echo "   - https://yourdomain.com/storage/products/..."
echo "5. Log dosyasını izleyin: tail -f storage/logs/laravel.log"
echo ""
echo "⚠️  Önemli: Web server'ı restart etmeyi unutmayın!"
echo "   - Apache: sudo systemctl restart apache2"
echo "   - Nginx: sudo systemctl restart nginx"
echo ""


