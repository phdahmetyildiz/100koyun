<?php
/**
 * 100 Koyun - E-posta Doğrulama
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    $error = 'Geçersiz doğrulama linki.';
} else {
    $auth = new Auth();
    $result = $auth->verifyEmail($token);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'E-posta Doğrulama';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-icon"><?= $success ? '✅' : '❌' ?></span>
            <h1>E-posta Doğrulama</h1>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <div class="auth-footer">
            <p><a href="/kayit.php">Yeniden kayıt olmak için tıklayın</a></p>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
        <div class="auth-footer">
            <a href="/giris.php?verified=1" class="btn btn-primary btn-large">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

