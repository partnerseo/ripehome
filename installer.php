<?php
/**
 * RIPE HOME - Web Installer
 * Tarayıcıdan çalışır, terminal gerekmez!
 * 
 * KULLANIM:
 * 1. Bu dosyayı public_html/ klasörüne yükle
 * 2. Tarayıcıda aç: https://ripehome.com.tr/installer.php
 * 3. Formu doldur
 * 4. Kur butonuna tıkla
 * 5. BİTTİ!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = $_GET['step'] ?? 'start';
$error = null;
$success = null;

// POST işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'install') {
    $db_host = $_POST['db_host'] ?? '127.0.0.1';
    $db_port = $_POST['db_port'] ?? '3306';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';
    $site_url = $_POST['site_url'] ?? '';
    
    try {
        // 1. Database bağlantı testi
        $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 2. .env dosyası oluştur
        $env_content = <<<ENV
APP_NAME="Ripe Home"
APP_ENV=production
APP_KEY=base64:gFiTn3IjFtrm+SPJ+J8yhoAo/S0+DZQv8IheAiPIjqA=
APP_DEBUG=false
APP_TIMEZONE=Europe/Istanbul
APP_URL=$site_url
ASSET_URL=$site_url

APP_LOCALE=tr
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=tr_TR

APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=$db_host
DB_PORT=$db_port
DB_DATABASE=$db_name
DB_USERNAME=$db_user
DB_PASSWORD=$db_pass

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=.{$_SERVER['HTTP_HOST']}

FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=file

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS="info@{$_SERVER['HTTP_HOST']}"
MAIL_FROM_NAME="\${APP_NAME}"

FRONTEND_URL=$site_url
ENV;
        
        $env_path = __DIR__ . '/backend/.env';
        if (!file_put_contents($env_path, $env_content)) {
            throw new Exception('.env dosyası oluşturulamadı!');
        }
        
        // 3. Storage symlink oluştur
        $link = __DIR__ . '/backend/public/storage';
        $target = __DIR__ . '/backend/storage/app/public';
        
        // Symlink fonksiyonu disabled olabilir, skip et
        if (function_exists('symlink')) {
            if (file_exists($link)) {
                if (is_link($link)) {
                    @unlink($link);
                }
            }
            
            if (!@symlink($target, $link)) {
                $error = "⚠️ Storage symlink oluşturulamadı. cPanel'den manuel oluştur.";
            }
        } else {
            $error = "⚠️ symlink() disabled. cPanel → 'Symbolic Links' → backend/public/storage → backend/storage/app/public oluştur.";
        }
        
        // 4. Cache klasörlerini temizle
        $cache_paths = [
            __DIR__ . '/backend/bootstrap/cache/config.php',
            __DIR__ . '/backend/bootstrap/cache/routes-v7.php',
            __DIR__ . '/backend/storage/framework/cache/data/*',
        ];
        
        foreach ($cache_paths as $path) {
            if (strpos($path, '*') !== false) {
                // Wildcard için glob kullan
                $files = glob($path);
                foreach ($files as $file) {
                    if (is_file($file)) @unlink($file);
                }
            } else {
                if (file_exists($path)) @unlink($path);
            }
        }
        
        // 5. Admin user oluştur (eğer yoksa)
        $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
        
        // Önce kontrol et
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$admin_email]);
        $exists = $stmt->fetchColumn();
        
        if ($exists) {
            // Varsa güncelle
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $admin_email]);
        } else {
            // Yoksa ekle
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) 
                VALUES (?, ?, NOW(), ?, NOW(), NOW())
            ");
            $stmt->execute(['Admin', $admin_email, $hashed_password]);
        }
        
        // 6. İzinleri kontrol et
        $permissions_ok = true;
        $storage_writable = is_writable(__DIR__ . '/backend/storage');
        $cache_writable = is_writable(__DIR__ . '/backend/bootstrap/cache');
        
        if (!$storage_writable || !$cache_writable) {
            $permissions_ok = false;
        }
        
        // Başarılı!
        $success = "✅ KURULUM TAMAMLANDI!";
        $step = 'success';
        
    } catch (PDOException $e) {
        $error = "❌ Database hatası: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "❌ Hata: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ripe Home - Kurulum</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #2B5F82;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .error {
            background: #fee;
            border: 2px solid #fcc;
            color: #c33;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .success {
            background: #efe;
            border: 2px solid #cfc;
            color: #3c3;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .success-box {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        .success-box h3 {
            color: #0c4a6e;
            margin-bottom: 15px;
        }
        .success-box ul {
            list-style: none;
            padding: 0;
        }
        .success-box li {
            padding: 8px 0;
            color: #0369a1;
        }
        .success-box li::before {
            content: "✅ ";
            margin-right: 8px;
        }
        .link-box {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
        }
        .link-box a {
            color: #92400e;
            font-weight: bold;
            font-size: 18px;
            text-decoration: none;
        }
        .warning {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            color: #92400e;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($step === 'start'): ?>
            <h1>🏠 Ripe Home</h1>
            <p class="subtitle">Kurulum Sihirbazı</p>
            
            <div class="info-box">
                <strong>📋 Ön Gereksinimler:</strong><br>
                ✅ PHP 8.2+ <br>
                ✅ MySQL Database<br>
                ✅ Tüm dosyalar yüklenmiş<br>
                ✅ SQL imported (phpMyAdmin)
            </div>
            
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="?step=install">
                <h3 style="margin-bottom: 20px;">Database Ayarları</h3>
                
                <div class="form-group">
                    <label>Database Host</label>
                    <input type="text" name="db_host" value="127.0.0.1" required>
                </div>
                
                <div class="form-group">
                    <label>Database Port</label>
                    <input type="text" name="db_port" value="3306" required>
                </div>
                
                <div class="form-group">
                    <label>Database Name</label>
                    <input type="text" name="db_name" value="ripehome_ripe" required>
                </div>
                
                <div class="form-group">
                    <label>Database Username</label>
                    <input type="text" name="db_user" value="ripehome_ripe" required>
                </div>
                
                <div class="form-group">
                    <label>Database Password</label>
                    <input type="password" name="db_pass" value="" required>
                </div>
                
                <h3 style="margin: 30px 0 20px;">Site Ayarları</h3>
                
                <div class="form-group">
                    <label>Site URL (https://)</label>
                    <input type="url" name="site_url" value="https://<?= $_SERVER['HTTP_HOST'] ?>" required>
                </div>
                
                <h3 style="margin: 30px 0 20px;">Admin Kullanıcı</h3>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="admin_email" value="admin@admin.com" required>
                </div>
                
                <div class="form-group">
                    <label>Şifre</label>
                    <input type="password" name="admin_password" value="" required>
                </div>
                
                <button type="submit" class="btn">🚀 KURU!</button>
            </form>
            
        <?php elseif ($step === 'success'): ?>
            <h1>🎉 Başarılı!</h1>
            <p class="subtitle">Kurulum tamamlandı</p>
            
            <?php if ($error): ?>
                <div class="warning"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="success-box">
                <h3>✅ Yapılanlar:</h3>
                <ul>
                    <li>Backend .env oluşturuldu</li>
                    <li>Database bağlantısı test edildi</li>
                    <li>Admin kullanıcı oluşturuldu</li>
                    <li>Cache temizlendi</li>
                </ul>
            </div>
            
            <div class="link-box">
                <p style="margin-bottom: 10px;">Admin paneline git:</p>
                <a href="/admin" target="_blank">👉 /admin</a>
            </div>
            
            <div class="info-box" style="margin-top: 20px;">
                <strong>⚠️ ÖNEMLİ:</strong><br>
                1. <code>installer.php</code> dosyasını SİL!<br>
                2. <code>backend/storage/</code> → 755 izin ver<br>
                3. <code>backend/bootstrap/cache/</code> → 755 izin ver<br>
                4. <code>public_html/.htaccess</code> kontrol et
            </div>
            
            <a href="/admin" class="btn" style="margin-top: 20px; display: block; text-align: center; text-decoration: none;">
                Admin Paneline Git
            </a>
        <?php endif; ?>
    </div>
</body>
</html>

