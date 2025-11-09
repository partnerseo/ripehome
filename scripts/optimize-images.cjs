const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const INPUT_DIR = path.join(__dirname, '../public/ÜRÜNLER');
const OUTPUT_DIR = path.join(__dirname, '../public/ÜRÜNLER-optimized');

// Output klasörü oluştur
if (!fs.existsSync(OUTPUT_DIR)) {
  fs.mkdirSync(OUTPUT_DIR, { recursive: true });
}

// Tüm görselleri bul (alt klasörler dahil)
function getAllImages(dir) {
  const files = fs.readdirSync(dir);
  let images = [];

  files.forEach(file => {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);

    if (stat.isDirectory()) {
      // Alt klasörleri tara
      images = images.concat(getAllImages(fullPath));
    } else if (/\.(jpg|jpeg|png|webp|JPG|JPEG|PNG)$/i.test(file)) {
      images.push(fullPath);
    }
  });

  return images;
}

// Optimize fonksiyonu
async function optimizeImages() {
  console.log('🖼️  ÜRÜNLER klasörü optimize ediliyor...\n');

  const images = getAllImages(INPUT_DIR);
  
  if (images.length === 0) {
    console.log('❌ Görsel bulunamadı!');
    console.log('📁 Kontrol edin: public/ÜRÜNLER/\n');
    return;
  }

  console.log(`📦 Toplam ${images.length} görsel bulundu\n`);

  let totalBefore = 0;
  let totalAfter = 0;
  let successCount = 0;

  for (let i = 0; i < images.length; i++) {
    const imagePath = images[i];
    const relativePath = path.relative(INPUT_DIR, imagePath);
    const outputPath = path.join(OUTPUT_DIR, relativePath);
    const outputDir = path.dirname(outputPath);

    // Output klasörü yoksa oluştur
    if (!fs.existsSync(outputDir)) {
      fs.mkdirSync(outputDir, { recursive: true });
    }

    try {
      const stats = fs.statSync(imagePath);
      const sizeBefore = stats.size;
      totalBefore += sizeBefore;

      console.log(`[${i + 1}/${images.length}] ${relativePath}`);
      console.log(`   📏 Boyut: ${(sizeBefore / 1024 / 1024).toFixed(2)} MB`);

      // Optimize et
      await sharp(imagePath)
        .resize(1920, null, {
          withoutEnlargement: true,
          fit: 'inside'
        })
        .jpeg({ 
          quality: 80,
          progressive: true 
        })
        .toFile(outputPath);

      const newStats = fs.statSync(outputPath);
      const sizeAfter = newStats.size;
      totalAfter += sizeAfter;

      const savedPercent = ((sizeBefore - sizeAfter) / sizeBefore * 100).toFixed(1);
      const savedMB = ((sizeBefore - sizeAfter) / 1024 / 1024).toFixed(2);
      
      console.log(`   ✅ ${(sizeAfter / 1024 / 1024).toFixed(2)} MB (${savedPercent}% küçültme, ${savedMB} MB kazanç)\n`);
      
      successCount++;

    } catch (error) {
      console.error(`   ❌ Hata: ${error.message}\n`);
    }
  }

  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('✨ Optimize tamamlandı!');
  console.log(`✅ Başarılı: ${successCount}/${images.length}`);
  console.log(`📦 Toplam Öncesi: ${(totalBefore / 1024 / 1024).toFixed(2)} MB`);
  console.log(`📦 Toplam Sonrası: ${(totalAfter / 1024 / 1024).toFixed(2)} MB`);
  console.log(`💾 Kazanılan Alan: ${((totalBefore - totalAfter) / 1024 / 1024).toFixed(2)} MB`);
  console.log(`📊 Ortalama Küçülme: ${((totalBefore - totalAfter) / totalBefore * 100).toFixed(1)}%`);
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');
  console.log('📁 Optimize edilmiş görseller:');
  console.log('   public/ÜRÜNLER-optimized/\n');
  console.log('ℹ️  Bu görselleri backend/storage/app/public/ klasörüne taşıyın.');
}

optimizeImages().catch(console.error);

