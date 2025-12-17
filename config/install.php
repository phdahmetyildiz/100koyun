<?php
/**
 * 100 Koyun - Veritabanı Kurulum Scripti
 * Bu dosyayı bir kere çalıştırın, sonra silin!
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

require_once __DIR__ . '/database.php';

try {
    $db = getDB();
    
    // Kullanıcılar tablosu
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            email_verified INTEGER DEFAULT 0,
            verification_token TEXT,
            reset_token TEXT,
            reset_expires DATETIME,
            kvkk_accepted INTEGER DEFAULT 0,
            kvkk_accepted_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login DATETIME
        )
    ");
    
    // Çocuklar tablosu
    $db->exec("
        CREATE TABLE IF NOT EXISTS children (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            gender TEXT CHECK(gender IN ('erkek', 'kiz')) NOT NULL,
            city TEXT,
            birth_date DATE,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // İletişim mesajları tablosu
    $db->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            is_read INTEGER DEFAULT 0
        )
    ");
    
    // Masallar tablosu (gelecekte AI ile üretilen masallar için)
    $db->exec("
        CREATE TABLE IF NOT EXISTS stories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            story_date DATE UNIQUE,
            is_ai_generated INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Oturum logları (güvenlik için)
    $db->exec("
        CREATE TABLE IF NOT EXISTS login_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            ip_address TEXT,
            user_agent TEXT,
            success INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Remember me tokenları (30 günlük oturum için)
    $db->exec("
        CREATE TABLE IF NOT EXISTS remember_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            token TEXT UNIQUE NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // İndeksler
    $db->exec("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_children_user ON children(user_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_stories_date ON stories(story_date)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_remember_token ON remember_tokens(token)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_remember_user ON remember_tokens(user_id)");
    
    echo "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <title>Kurulum Başarılı</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; }
        .warning { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='success'>
        <h2>✅ Veritabanı kurulumu başarıyla tamamlandı!</h2>
        <p>Tablolar oluşturuldu:</p>
        <ul>
            <li>users - Kullanıcı hesapları</li>
            <li>children - Çocuk profilleri</li>
            <li>contact_messages - İletişim mesajları</li>
            <li>stories - Masallar</li>
            <li>login_logs - Güvenlik logları</li>
            <li>remember_tokens - Beni hatırla tokenları (30 gün)</li>
        </ul>
    </div>
    <div class='warning'>
        <strong>⚠️ Güvenlik Uyarısı:</strong><br>
        Bu dosyayı (install.php) sunucunuzdan silmeyi unutmayın!
    </div>
</body>
</html>";

} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}

