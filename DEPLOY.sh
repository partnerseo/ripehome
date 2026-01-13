#!/bin/bash

echo "🚀 PRODUCTION DEPLOYMENT BAŞLIYOR..."

# Frontend .env.production oluştur
echo "📝 Frontend .env.production oluşturuluyor..."
cat > .env.production << 'EOF'
VITE_API_URL=https://ripehome.com.tr/api
EOF

# Backend hazırlık
echo "🔧 Backend optimize ediliyor..."
cd backend

# Cache'leri temizle
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Production cache oluştur
php artisan config:cache
php artisan route:cache
php artisan filament:optimize

# Storage link (local'de test için)
php artisan storage:link 2>/dev/null || true

cd ..

# Frontend build
echo "🎨 Frontend build..."
npm run build

# ZIP'leri oluştur
echo "📦 ZIP dosyaları oluşturuluyor..."

# Backend ZIP (vendor ile)
zip -r backend_PRODUCTION.zip backend/ \
  -x "backend/node_modules/*" \
  -x "backend/.git/*" \
  -x "backend/database/database.sqlite" \
  -x "backend/.env" \
  -x "backend/.env.*" \
  -x "backend/storage/logs/*" \
  -x "backend/storage/framework/sessions/*" \
  -x "backend/storage/framework/cache/data/*"

# Frontend ZIP  
zip -r dist_PRODUCTION.zip dist/

echo ""
echo "✅ HAZIR! ZIP dosyaları oluşturuldu:"
ls -lh backend_PRODUCTION.zip dist_PRODUCTION.zip

echo ""
echo "📋 SONRAKI ADIMLAR:"
echo ""
echo "1️⃣  cPanel File Manager'a git"
echo "2️⃣  backend_PRODUCTION.zip + dist_PRODUCTION.zip yükle"
echo "3️⃣  Extract et"
echo "4️⃣  dist/ içindekini public_html'e taşı"
echo "5️⃣  backend/.env oluştur (ENV_PRODUCTION_READY.txt'den kopyala)"
echo "6️⃣  public_html/.htaccess oluştur (PUBLIC_HTML_HTACCESS_FINAL.txt'den kopyala)"
echo "7️⃣  İzinler: backend/storage → 755 (recursive)"
echo "8️⃣  İzinler: backend/bootstrap/cache → 755 (recursive)"
echo "9️⃣  phpMyAdmin'de admin user ekle"
echo "🔟 Test: https://ripehome.com.tr/admin"
echo ""
echo "🎉 BİTTİ!"

