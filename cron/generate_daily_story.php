<?php
/**
 * 100 Koyun - Günlük Masal Orta Kısmı Üretme Scripti (Cron için)
 * 
 * Her gün saat 03:33'te çalışacak cron job bu dosyayı çağırabilir.
 * Örnek cron satırı (Linux):
 *   33 3 * * * /usr/bin/php /var/www/100koyun/cron/generate_daily_story.php >> /var/log/100koyun-cron.log 2>&1
 * 
 * Bu script:
 *  - Clarifai'den o gün için 1-2 paragraflık masal orta kısmını alır
 *  - Metni stories tablosuna (is_ai_generated = 1) kaydeder
 *  - index.php ve diğer sayfalar daha sonra bu orta kısmı kullanır
 */

// CLI'den çalıştırıldığından emin ol
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Bu script sadece CLI üzerinden çalıştırılmalıdır.\n";
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/story.php';

date_default_timezone_set('Europe/Istanbul');

echo "=== 100 Koyun - Günlük Masal Üretimi ===\n";
echo "Tarih: " . date('Y-m-d H:i:s') . "\n";

$storyManager = new Story();

$result = $storyManager->generateTodaysAIStoryMiddle();

if ($result) {
    echo "Başarılı: Bugünün masal orta kısmı üretildi ve kaydedildi.\n";
} else {
    echo "Uyarı: Clarifai'den masal üretilemedi. Logları kontrol edin.\n";
}

echo "Bitti.\n";


