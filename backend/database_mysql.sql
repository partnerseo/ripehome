-- Ripe Home Database - MySQL Version
-- Manuel kurulum için hazırlanmıştır

-- Admin kullanıcısı
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@admin.com', NOW(), '$2y$12$LQv3c1ySDlI8wi8JRNa.SeXy0Ss3k.pF3UOKKKKKKKKKKKKKKKKKu', NULL, NOW(), NOW());

-- Kategoriler (Örnekler - Admin panelden düzenlenebilir)
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `order`, `created_at`, `updated_at`) VALUES
(1, 'Bornozlar', 'bornozlar', 'Yumuşak ve konforlu bornozlar', NULL, 1, 1, NOW(), NOW()),
(2, 'Havlular', 'havlular', 'Kaliteli pamuklu havlular', NULL, 1, 2, NOW(), NOW()),
(3, 'Pike Takımları', 'pike-takimlari', 'Şık ve rahat pike takımları', NULL, 1, 3, NOW(), NOW());

-- Site ayarları
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('site_name', 'Ripe Home', NOW(), NOW()),
('site_description', 'Ev tekstili ürünleri', NOW(), NOW()),
('email', 'info@ripehome.com.tr', NOW(), NOW()),
('phone', '+90 544 251 9716', NOW(), NOW()),
('address', 'Türkiye', NOW(), NOW()),
('footer_text', '© 2025 Ripe Home. Tüm hakları saklıdır.', NOW(), NOW());

-- Örnek sayfalar
INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Hakkımızda', 'hakkimizda', '<p>Ripe Home hakkında bilgiler...</p>', 'Hakkımızda', 'Ripe Home hakkında', 1, NOW(), NOW()),
(2, 'İletişim', 'iletisim', '<p>Bizimle iletişime geçin...</p>', 'İletişim', 'İletişim bilgileri', 1, NOW(), NOW()),
(3, 'Gizlilik Politikası', 'gizlilik-politikasi', '<p>Gizlilik politikamız...</p>', 'Gizlilik Politikası', 'Gizlilik Politikası', 1, NOW(), NOW()),
(4, 'Kullanım Koşulları', 'kullanim-kosullari', '<p>Kullanım koşulları...</p>', 'Kullanım Koşulları', 'Kullanım Koşulları', 1, NOW(), NOW());

