<?php

// SQLite → MySQL Export Script
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$output = "-- Ripe Home - Localhost Database Export (MySQL Format)\n";
$output .= "-- Export Date: " . date('Y-m-d H:i:s') . "\n\n";
$output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$output .= "SET time_zone = \"+00:00\";\n\n";

// Export Categories
$output .= "-- =====================================================\n";
$output .= "-- CATEGORIES\n";
$output .= "-- =====================================================\n\n";

$categories = DB::table('categories')->get();
if ($categories->count() > 0) {
    foreach ($categories as $cat) {
        $output .= sprintf(
            "INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `order`, `created_at`, `updated_at`) VALUES\n(%d, %s, %s, %s, %s, %d, %d, %s, %s);\n\n",
            $cat->id,
            DB::connection()->getPdo()->quote($cat->name),
            DB::connection()->getPdo()->quote($cat->slug),
            $cat->description ? DB::connection()->getPdo()->quote($cat->description) : 'NULL',
            $cat->image ? DB::connection()->getPdo()->quote($cat->image) : 'NULL',
            $cat->is_active ?? 1,
            $cat->order ?? 0,
            $cat->created_at ? DB::connection()->getPdo()->quote($cat->created_at) : 'NULL',
            $cat->updated_at ? DB::connection()->getPdo()->quote($cat->updated_at) : 'NULL'
        );
    }
}

// Export Products
$output .= "\n-- =====================================================\n";
$output .= "-- PRODUCTS\n";
$output .= "-- =====================================================\n\n";

$products = DB::table('products')->get();
if ($products->count() > 0) {
    foreach ($products as $product) {
        $output .= sprintf(
            "INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `price`, `stock`, `images`, `is_active`, `is_featured`, `order`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES\n(%d, %d, %s, %s, %s, %s, %s, %d, %s, %d, %d, %d, %s, %s, %s, %s);\n\n",
            $product->id,
            $product->category_id,
            DB::connection()->getPdo()->quote($product->name),
            DB::connection()->getPdo()->quote($product->slug),
            isset($product->description) && $product->description ? DB::connection()->getPdo()->quote($product->description) : 'NULL',
            isset($product->short_description) && $product->short_description ? DB::connection()->getPdo()->quote($product->short_description) : 'NULL',
            isset($product->price) && $product->price ? DB::connection()->getPdo()->quote($product->price) : 'NULL',
            $product->stock ?? 0,
            isset($product->images) && $product->images ? DB::connection()->getPdo()->quote($product->images) : 'NULL',
            $product->is_active ?? 1,
            $product->is_featured ?? 0,
            $product->order ?? 0,
            isset($product->meta_title) && $product->meta_title ? DB::connection()->getPdo()->quote($product->meta_title) : 'NULL',
            isset($product->meta_description) && $product->meta_description ? DB::connection()->getPdo()->quote($product->meta_description) : 'NULL',
            isset($product->created_at) && $product->created_at ? DB::connection()->getPdo()->quote($product->created_at) : 'NULL',
            isset($product->updated_at) && $product->updated_at ? DB::connection()->getPdo()->quote($product->updated_at) : 'NULL'
        );
    }
}

// Export Featured Products
$output .= "\n-- =====================================================\n";
$output .= "-- FEATURED PRODUCTS\n";
$output .= "-- =====================================================\n\n";

$featuredProducts = DB::table('featured_products')->get();
if ($featuredProducts->count() > 0) {
    foreach ($featuredProducts as $fp) {
        $output .= sprintf(
            "INSERT INTO `featured_products` (`id`, `product_id`, `order`, `is_active`, `created_at`, `updated_at`) VALUES\n(%d, %d, %d, %d, %s, %s);\n\n",
            $fp->id,
            $fp->product_id,
            $fp->order ?? 0,
            $fp->is_active ?? 1,
            $fp->created_at ? DB::connection()->getPdo()->quote($fp->created_at) : 'NULL',
            $fp->updated_at ? DB::connection()->getPdo()->quote($fp->updated_at) : 'NULL'
        );
    }
}

// Export Settings
$output .= "\n-- =====================================================\n";
$output .= "-- SETTINGS\n";
$output .= "-- =====================================================\n\n";

$settings = DB::table('settings')->get();
if ($settings->count() > 0) {
    foreach ($settings as $setting) {
        $output .= sprintf(
            "INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES\n(%d, %s, %s, %s, %s);\n\n",
            $setting->id,
            DB::connection()->getPdo()->quote($setting->key),
            $setting->value ? DB::connection()->getPdo()->quote($setting->value) : 'NULL',
            $setting->created_at ? DB::connection()->getPdo()->quote($setting->created_at) : 'NULL',
            $setting->updated_at ? DB::connection()->getPdo()->quote($setting->updated_at) : 'NULL'
        );
    }
}

// Export Home Sliders
$output .= "\n-- =====================================================\n";
$output .= "-- HOME SLIDERS\n";
$output .= "-- =====================================================\n\n";

$sliders = DB::table('home_sliders')->get();
if ($sliders->count() > 0) {
    foreach ($sliders as $slider) {
        $output .= sprintf(
            "INSERT INTO `home_sliders` (`id`, `title`, `subtitle`, `image`, `button_text`, `button_link`, `is_active`, `order`, `created_at`, `updated_at`) VALUES\n(%d, %s, %s, %s, %s, %s, %d, %d, %s, %s);\n\n",
            $slider->id,
            DB::connection()->getPdo()->quote($slider->title),
            $slider->subtitle ? DB::connection()->getPdo()->quote($slider->subtitle) : 'NULL',
            $slider->image ? DB::connection()->getPdo()->quote($slider->image) : 'NULL',
            $slider->button_text ? DB::connection()->getPdo()->quote($slider->button_text) : 'NULL',
            $slider->button_link ? DB::connection()->getPdo()->quote($slider->button_link) : 'NULL',
            $slider->is_active ?? 1,
            $slider->order ?? 0,
            $slider->created_at ? DB::connection()->getPdo()->quote($slider->created_at) : 'NULL',
            $slider->updated_at ? DB::connection()->getPdo()->quote($slider->updated_at) : 'NULL'
        );
    }
}

// Export Users (admin)
$output .= "\n-- =====================================================\n";
$output .= "-- USERS (Admin)\n";
$output .= "-- =====================================================\n\n";

$users = DB::table('users')->get();
if ($users->count() > 0) {
    foreach ($users as $user) {
        $output .= sprintf(
            "INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES\n(%d, %s, %s, %s, %s, %s, %s, %s);\n\n",
            $user->id,
            DB::connection()->getPdo()->quote($user->name),
            DB::connection()->getPdo()->quote($user->email),
            $user->email_verified_at ? DB::connection()->getPdo()->quote($user->email_verified_at) : 'NULL',
            DB::connection()->getPdo()->quote($user->password),
            $user->remember_token ? DB::connection()->getPdo()->quote($user->remember_token) : 'NULL',
            $user->created_at ? DB::connection()->getPdo()->quote($user->created_at) : 'NULL',
            $user->updated_at ? DB::connection()->getPdo()->quote($user->updated_at) : 'NULL'
        );
    }
}

$output .= "\nCOMMIT;\n";

// Save to file
$filename = __DIR__ . '/LOCALHOST_DATABASE_MYSQL.sql';
file_put_contents($filename, $output);

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║         ✅ DATABASE EXPORT TAMAMLANDI!                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "📊 EXPORT EDİLEN VERİLER:\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "✅ Kategoriler: " . $categories->count() . " adet\n";
echo "✅ Ürünler: " . $products->count() . " adet\n";
echo "✅ Öne Çıkan Ürünler: " . $featuredProducts->count() . " adet\n";
echo "✅ Ayarlar: " . $settings->count() . " adet\n";
echo "✅ Sliderlar: " . $sliders->count() . " adet\n";
echo "✅ Kullanıcılar: " . $users->count() . " adet\n";
echo "\n";
echo "📁 Dosya: LOCALHOST_DATABASE_MYSQL.sql\n";
echo "📏 Boyut: " . number_format(strlen($output)) . " bytes\n";
echo "\n";

