<?php
/**
 * 100 Koyun - Kayıt Sayfası
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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF kontrolü
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $kvkkAccepted = isset($_POST['kvkk_accepted']);
        
        if (!$email) {
            $error = 'Geçerli bir e-posta adresi girin.';
        } elseif ($password !== $passwordConfirm) {
            $error = 'Şifreler eşleşmiyor.';
        } elseif (strlen($password) < 8) {
            $error = 'Şifre en az 8 karakter olmalıdır.';
        } elseif (!$kvkkAccepted) {
            $error = 'Devam etmek için KVKK Aydınlatma Metnini kabul etmelisiniz.';
        } else {
            $auth = new Auth();
            $result = $auth->register($email, $password, $kvkkAccepted);
            
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = 'Kayıt Ol';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-icon">🐑</span>
            <h1>Hesap Oluştur</h1>
            <p>Çocuğunuz için kişiselleştirilmiş masallar</p>
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
            <p style="margin-top: 10px;">
                <a href="/giris.php" class="btn btn-primary btn-small">Giriş Yap</a>
            </p>
        </div>
        <?php else: ?>
        
        <form method="POST" class="auth-form" id="registerForm">
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
                           minlength="8" placeholder="En az 8 karakter">
                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength"></div>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">
                    <i class="fas fa-lock"></i> Şifre Tekrar
                </label>
                <div class="password-input">
                    <input type="password" id="password_confirm" name="password_confirm" required 
                           minlength="8" placeholder="Şifrenizi tekrar girin">
                    <button type="button" class="toggle-password" onclick="togglePassword('password_confirm')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="kvkk_accepted" id="kvkk_accepted" required>
                    <span class="checkmark"></span>
                    <span class="checkbox-text">
                        <a href="/kvkk.php" target="_blank">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum.
                    </span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-large btn-block">
                <i class="fas fa-user-plus"></i> Kayıt Ol
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Zaten hesabınız var mı? <a href="/giris.php">Giriş Yapın</a></p>
        </div>
        
        <?php endif; ?>
    </div>
    
    <!-- KVKK Bilgilendirme Kutusu -->
    <div class="kvkk-info-box">
        <h3><i class="fas fa-shield-alt"></i> Kişisel Verilerinizin Korunması</h3>
        <p>100 Koyun olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında kişisel verilerinizin güvenliğine önem veriyoruz.</p>
        
        <div class="kvkk-points">
            <div class="kvkk-point">
                <i class="fas fa-database"></i>
                <div>
                    <strong>Toplanan Veriler</strong>
                    <p>E-posta adresi, çocuk bilgileri (isim, cinsiyet, şehir)</p>
                </div>
            </div>
            
            <div class="kvkk-point">
                <i class="fas fa-bullseye"></i>
                <div>
                    <strong>Kullanım Amacı</strong>
                    <p>Masalların kişiselleştirilmesi ve hesap yönetimi</p>
                </div>
            </div>
            
            <div class="kvkk-point">
                <i class="fas fa-user-shield"></i>
                <div>
                    <strong>Veri Güvenliği</strong>
                    <p>Verileriniz şifrelenerek güvenli sunucularda saklanır</p>
                </div>
            </div>
            
            <div class="kvkk-point">
                <i class="fas fa-hand-paper"></i>
                <div>
                    <strong>Haklarınız</strong>
                    <p>Verilerinize erişim, düzeltme ve silme hakkına sahipsiniz</p>
                </div>
            </div>
        </div>
        
        <p class="kvkk-link">
            Detaylı bilgi için <a href="/kvkk.php">KVKK Aydınlatma Metni</a>'ni inceleyebilirsiniz.
        </p>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
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

// Şifre gücü göstergesi
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthDiv = document.getElementById('passwordStrength');
    
    let strength = 0;
    let text = '';
    let className = '';
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    if (password.length === 0) {
        text = '';
    } else if (strength < 2) {
        text = 'Zayıf şifre';
        className = 'weak';
    } else if (strength < 4) {
        text = 'Orta güçte şifre';
        className = 'medium';
    } else {
        text = 'Güçlü şifre';
        className = 'strong';
    }
    
    strengthDiv.textContent = text;
    strengthDiv.className = 'password-strength ' + className;
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

