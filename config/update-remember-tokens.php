<?php
/**
 * 100 Koyun - Remember Tokens Tablosu Güncelleme Scripti
 * Mevcut veritabanına remember_tokens tablosunu ekler
 * Bu dosyayı bir kere çalıştırın, sonra silin!
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

require_once __DIR__ . '/database.php';

try {
    $db = getDB();
    
    // Remember me tokenları tablosunu oluştur
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
    $db->exec("CREATE INDEX IF NOT EXISTS idx_remember_token ON remember_tokens(token)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_remember_user ON remember_tokens(user_id)");
    
    echo "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <title>Güncelleme Başarılı</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; }
        .warning { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='success'>
        <h2>✅ Güncelleme başarıyla tamamlandı!</h2>
        <p><strong>remember_tokens</strong> tablosu oluşturuldu.</p>
        <p>Artık 'Beni hatırla' özelliği çalışacak:</p>
        <ul>
            <li>✓ Kullanıcılar 30 gün boyunca otomatik giriş yapabilecek</li>
            <li>✓ 30 gün boyunca sayfa açılmazsa oturum sonlanacak</li>
            <li>✓ Güvenli cookie tabanlı oturum yönetimi aktif</li>
        </ul>
    </div>
    <div class='warning'>
        <strong>⚠️ Güvenlik Uyarısı:</strong><br>
        Bu dosyayı (update-remember-tokens.php) sunucunuzdan silmeyi unutmayın!
    </div>
</body>
</html>";

} catch (Exception $e) {
    echo "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <title>Hata</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; color: #721c24; }
    </style>
</head>
<body>
    <div class='error'>
        <h2>❌ Hata</h2>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
    </div>
</body>
</html>";
}

