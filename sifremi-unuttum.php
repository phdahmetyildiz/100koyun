<?php
/**
 * 100 Koyun - Şifremi Unuttum Sayfası
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            $error = 'Geçerli bir e-posta adresi girin.';
        } else {
            $auth = new Auth();
            $result = $auth->requestPasswordReset($email);
            $success = $result['message'];
        }
    }
}

$pageTitle = 'Şifremi Unuttum';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-icon">🔑</span>
            <h1>Şifremi Unuttum</h1>
            <p>E-posta adresinizi girin, size şifre sıfırlama linki gönderelim</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
        <div class="auth-footer">
            <a href="/giris.php" class="btn btn-primary">Giriş Sayfasına Dön</a>
        </div>
        <?php else: ?>
        
        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> E-posta Adresi
                </label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="ornek@email.com">
            </div>
            
            <button type="submit" class="btn btn-primary btn-large btn-block">
                <i class="fas fa-paper-plane"></i> Sıfırlama Linki Gönder
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Şifrenizi hatırladınız mı? <a href="/giris.php">Giriş Yapın</a></p>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

