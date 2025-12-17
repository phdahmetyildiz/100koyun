<?php
/**
 * 100 Koyun - Hata Sayfası
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();

$errorCode = intval($_GET['code'] ?? 404);

$errors = [
    400 => ['title' => 'Geçersiz İstek', 'message' => 'İstek anlaşılamadı.'],
    401 => ['title' => 'Yetkisiz Erişim', 'message' => 'Bu sayfaya erişim yetkiniz yok.'],
    403 => ['title' => 'Erişim Engellendi', 'message' => 'Bu sayfaya erişiminiz engellendi.'],
    404 => ['title' => 'Sayfa Bulunamadı', 'message' => 'Aradığınız sayfa bulunamadı.'],
    500 => ['title' => 'Sunucu Hatası', 'message' => 'Bir şeyler yanlış gitti. Lütfen daha sonra tekrar deneyin.'],
];

$error = $errors[$errorCode] ?? $errors[404];

$pageTitle = $error['title'];
include __DIR__ . '/includes/header.php';
?>

<div class="error-container">
    <div class="error-card">
        <div class="error-icon">
            <?php if ($errorCode === 404): ?>
            🐑❓
            <?php else: ?>
            😔
            <?php endif; ?>
        </div>
        
        <h1 class="error-code"><?= $errorCode ?></h1>
        <h2 class="error-title"><?= htmlspecialchars($error['title']) ?></h2>
        <p class="error-message"><?= htmlspecialchars($error['message']) ?></p>
        
        <?php if ($errorCode === 404): ?>
        <p class="error-joke">Görünüşe göre bu koyun sürüden ayrılmış... 🐑</p>
        <?php endif; ?>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Ana Sayfaya Dön
            </a>
            <button onclick="history.back()" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Geri Git
            </button>
        </div>
    </div>
</div>

<style>
.error-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 200px);
    padding: 2rem;
}

.error-card {
    text-align: center;
    max-width: 500px;
    background: var(--bg-card);
    padding: 3rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
}

.error-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
}

.error-code {
    font-size: 6rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.error-title {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.error-message {
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.error-joke {
    font-style: italic;
    color: var(--text-light);
    margin-bottom: 2rem;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>

