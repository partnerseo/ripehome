import os
import shutil
from pathlib import Path
import re

BASE_DIR = Path(__file__).parent.parent / 'public' / 'ÜRÜNLER-optimized'
OUTPUT_DIR = Path(__file__).parent.parent / 'public' / 'ÜRÜNLER-temiz'

def flatten_folders():
    if not BASE_DIR.exists():
        print(f"❌ Klasör bulunamadı: {BASE_DIR}")
        return
    
    # Eski klasörü sil
    if OUTPUT_DIR.exists():
        print(f"🗑️  Eski klasör siliniyor: {OUTPUT_DIR}")
        shutil.rmtree(OUTPUT_DIR)
    
    OUTPUT_DIR.mkdir(parents=True)
    print(f"📁 Yeni klasör oluşturuldu: {OUTPUT_DIR}\n")
    
    stats = {'categories': 0, 'images': 0}
    
    # Ana klasörleri tara
    for main_folder in sorted(BASE_DIR.iterdir()):
        if not main_folder.is_dir():
            continue
        
        # Kategori adı = klasör adı (AYNEN!)
        category_name = main_folder.name
        
        print(f"📁 {category_name}")
        
        # Output klasörü oluştur
        output_cat_dir = OUTPUT_DIR / category_name
        output_cat_dir.mkdir(exist_ok=True)
        stats['categories'] += 1
        
        # Tüm alt klasörlerdeki görselleri bul
        all_images = []
        for root, dirs, files in os.walk(main_folder):
            for file in files:
                if file.lower().endswith(('.jpg', '.jpeg', '.png', '.webp')):
                    file_path = Path(root) / file
                    all_images.append(file_path)
        
        if not all_images:
            print(f"   ⚠️  Görsel yok\n")
            continue
        
        print(f"   🖼️  {len(all_images)} görsel bulundu")
        
        # Görselleri alt klasörlere göre grupla
        grouped = {}
        for img_path in all_images:
            relative = img_path.relative_to(main_folder)
            parts = relative.parts
            
            # Alt klasör adını al
            if len(parts) > 1:
                subfolder = parts[0]
                color = extract_color_name(subfolder)
            else:
                color = 'genel'
            
            if color not in grouped:
                grouped[color] = []
            grouped[color].append(img_path)
        
        # Görselleri kopyala
        for color, images in grouped.items():
            print(f"      🎨 {color}: {len(images)} görsel")
            
            for idx, img_path in enumerate(sorted(images), 1):
                extension = img_path.suffix
                new_name = f"{color}-{idx}{extension}"
                dest_path = output_cat_dir / new_name
                
                try:
                    shutil.copy2(img_path, dest_path)
                    stats['images'] += 1
                except Exception as e:
                    print(f"         ❌ Hata: {e}")
        
        print(f"   ✅ {len(all_images)} görsel kopyalandı\n")
    
    print('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
    print(f'✨ Tamamlandı!')
    print(f"📦 Kategori: {stats['categories']}")
    print(f"🖼️  Görsel: {stats['images']}")
    print(f"📁 Klasör: {OUTPUT_DIR}")
    print('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')

def extract_color_name(subfolder_name):
    """Alt klasör adından rengi çıkar"""
    name = subfolder_name.upper()
    
    colors = [
        'ANTRASİT', 'ANTRASIT',
        'MAVİ', 'MAVI',
        'PEMBE',
        'BEJ',
        'SİYAH', 'SIYAH',
        'BEYAZ',
        'SARI',
        'YEŞİL', 'YESIL',
        'KIRMIZI',
        'MOR',
        'TURUNCU',
        'GRİ', 'GRI',
        'AÇIK GRİ', 'ACIK GRI',
        'KOYU GRİ', 'KOYU GRI',
        'LACİVERT', 'LACIVERT',
        'KAHVERENGİ', 'KAHVERENGI',
        'VİZON', 'VIZON',
        'KREM',
        'PETROL',
        'TURKUAZ',
        'MİNT', 'MINT',
        'HAKİ', 'HAKI',
        'FUSYA',
        'GOLD',
        'HARDAL',
    ]
    
    for color in colors:
        if color in name:
            return color.lower().replace('ı', 'i').replace(' ', '-')
    
    # Renk bulunamazsa son kelime
    words = name.split()
    if words:
        return words[-1].lower().replace('ı', 'i')
    
    return 'diger'

if __name__ == '__main__':
    flatten_folders()
