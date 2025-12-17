<?php
/**
 * 100 Koyun - Ayarlar Sayfası
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Giriş kontrolü
if (!Auth::isLoggedIn()) {
    header('Location: /giris.php?redirect=/ayarlar.php');
    exit;
}

$user = Auth::getCurrentUser();
$error = '';
$success = '';

// Şifre değiştirme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Güvenlik doğrulaması başarısız.';
    } else {
        if ($_POST['action'] === 'change_password') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (strlen($newPassword) < 8) {
                $error = 'Yeni şifre en az 8 karakter olmalıdır.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Yeni şifreler eşleşmiyor.';
            } else {
                // Mevcut şifreyi doğrula
                $db = getDB();
                $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $userData = $stmt->fetch();
                
                if (!verifyPassword($currentPassword, $userData['password'])) {
                    $error = 'Mevcut şifreniz hatalı.';
                } else {
                    // Şifreyi güncelle
                    $newHash = hashPassword($newPassword);
                    $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = datetime('now') WHERE id = ?");
                    $stmt->execute([$newHash, $_SESSION['user_id']]);
                    
                    $success = 'Şifreniz başarıyla güncellendi.';
                }
            }
        } elseif ($_POST['action'] === 'delete_account') {
            $password = $_POST['confirm_delete_password'] ?? '';
            
            $db = getDB();
            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $userData = $stmt->fetch();
            
            if (!verifyPassword($password, $userData['password'])) {
                $error = 'Şifreniz hatalı.';
            } else {
                // Hesabı ve ilişkili verileri sil
                $db->beginTransaction();
                try {
                    $stmt = $db->prepare("DELETE FROM children WHERE user_id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    
                    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    
                    $db->commit();
                    
                    // Çıkış yap ve yönlendir
                    session_destroy();
                    header('Location: /?deleted=1');
                    exit;
                    
                } catch (Exception $e) {
                    $db->rollBack();
                    $error = 'Hesap silinirken bir hata oluştu.';
                }
            }
        }
    }
}

$pageTitle = 'Ayarlar';
include __DIR__ . '/includes/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <h1><i class="fas fa-cog"></i> Ayarlar</h1>
        <p>Hesap ayarlarınızı yönetin</p>
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
    <?php endif; ?>
    
    <!-- Hesap Bilgileri -->
    <div class="content-card">
        <h2><i class="fas fa-user"></i> Hesap Bilgileri</h2>
        
        <div class="settings-item">
            <div class="settings-label">
                <strong>E-posta Adresi</strong>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>
        
        <div class="settings-item">
            <div class="settings-label">
                <strong>Hesap Oluşturma Tarihi</strong>
                <p><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
    </div>
    
    <!-- Şifre Değiştir -->
    <div class="content-card">
        <h2><i class="fas fa-lock"></i> Şifre Değiştir</h2>
        
        <form method="POST" class="settings-form">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
                <label for="current_password">Mevcut Şifre</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Yeni Şifre</label>
                <input type="password" id="new_password" name="new_password" required minlength="8">
                <small>En az 8 karakter</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Yeni Şifre (Tekrar)</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Şifreyi Güncelle
            </button>
        </form>
    </div>
    
    <!-- Hesabı Sil -->
    <div class="content-card danger-zone">
        <h2><i class="fas fa-exclamation-triangle"></i> Tehlikeli Alan</h2>
        
        <div class="danger-content">
            <p>
                <strong>Hesabınızı silmek istediğinizden emin misiniz?</strong><br>
                Bu işlem geri alınamaz. Tüm verileriniz (çocuk profilleri dahil) kalıcı olarak silinecektir.
            </p>
            
            <button type="button" class="btn btn-danger" onclick="openDeleteAccountModal()">
                <i class="fas fa-trash"></i> Hesabımı Sil
            </button>
        </div>
    </div>
</div>

<!-- Hesap Silme Modal -->
<div class="modal" id="deleteAccountModal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2>Hesabı Sil</h2>
            <button class="modal-close" onclick="closeDeleteAccountModal()">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <div class="delete-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Bu işlem geri alınamaz!</strong>
                        <p>Tüm verileriniz kalıcı olarak silinecektir.</p>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 1rem;">
                    <label for="confirm_delete_password">Şifrenizi Girin</label>
                    <input type="password" id="confirm_delete_password" name="confirm_delete_password" required>
                </div>
            </div>
            <div class="modal-actions">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="delete_account">
                
                <button type="button" class="btn btn-outline" onclick="closeDeleteAccountModal()">İptal</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hesabı Sil
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.settings-item {
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.settings-item:last-child {
    border-bottom: none;
}

.settings-label strong {
    display: block;
    margin-bottom: 0.25rem;
}

.settings-label p {
    color: var(--text-secondary);
    margin: 0;
}

.settings-form {
    max-width: 400px;
}

.danger-zone {
    border: 2px solid var(--error);
}

.danger-zone h2 {
    color: var(--error);
}

.danger-content {
    padding: 1rem;
    background: rgba(245, 101, 101, 0.1);
    border-radius: var(--radius-md);
}

.danger-content p {
    margin-bottom: 1rem;
}
</style>

<script>
function openDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('open');
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.remove('open');
}

// Modal dışına tıklayınca kapat
document.getElementById('deleteAccountModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteAccountModal();
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

