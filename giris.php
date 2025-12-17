<?php
/**
 * 100 Koyun - Giriş Sayfası
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Zaten giriş yapmışsa ana sayfaya yönlendir
if (Auth::isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF kontrolü
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        if (!$email || !$password) {
            $error = 'E-posta ve şifre gereklidir.';
        } else {
            $rememberMe = isset($_POST['remember']) && $_POST['remember'] === 'on';
            $auth = new Auth();
            $result = $auth->login($email, $password, $rememberMe);
            
            if ($result['success']) {
                // Yönlendirme
                $redirect = $_GET['redirect'] ?? '/';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = 'Giriş Yap';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-icon">🐑</span>
            <h1>Hoş Geldiniz!</h1>
            <p>Hesabınıza giriş yapın</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['verified'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            E-posta adresiniz doğrulandı! Şimdi giriş yapabilirsiniz.
        </div>
        <?php endif; ?>
        
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
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Şifre
                </label>
                <div class="password-input">
                    <input type="password" id="password" name="password" required 
                           placeholder="Şifreniz">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    <span class="checkmark"></span>
                    <span class="checkbox-text">Beni hatırla</span>
                </label>
                <a href="/sifremi-unuttum.php" class="forgot-password">Şifremi unuttum</a>
            </div>
            
            <button type="submit" class="btn btn-primary btn-large btn-block">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Hesabınız yok mu? <a href="/kayit.php">Kayıt Olun</a></p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const field = document.getElementById('password');
    const icon = document.querySelector('.toggle-password i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

