<?php
/**
 * 100 Koyun - Kimlik Doğrulama İşlemleri
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Kullanıcı kaydı
     */
    public function register($email, $password, $kvkk_accepted) {
        // Email kontrolü
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.'];
        }
        
        // Şifre güvenliği kontrolü
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Şifre en az 8 karakter olmalıdır.'];
        }
        
        if (!$kvkk_accepted) {
            return ['success' => false, 'message' => 'KVKK aydınlatma metnini kabul etmelisiniz.'];
        }
        
        $hashedPassword = hashPassword($password);
        $verificationToken = bin2hex(random_bytes(32));
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password, verification_token, kvkk_accepted, kvkk_accepted_at) 
                VALUES (?, ?, ?, 1, datetime('now'))
            ");
            $stmt->execute([$email, $hashedPassword, $verificationToken]);
            
            $userId = $this->db->lastInsertId();
            
            // Doğrulama emaili gönder
            $this->sendVerificationEmail($email, $verificationToken);
            
            return [
                'success' => true, 
                'message' => 'Kayıt başarılı! Lütfen e-posta adresinizi doğrulayın.',
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            error_log("Kayıt hatası: " . $e->getMessage());
            return ['success' => false, 'message' => 'Kayıt sırasında bir hata oluştu.'];
        }
    }
    
    /**
     * Doğrulama emaili gönder
     */
    private function sendVerificationEmail($email, $token) {
        $verifyUrl = SITE_URL . "/verify.php?token=" . $token;
        
        $subject = "100 Koyun - E-posta Doğrulama";
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; }
                .footer { margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>🐑 100 Koyun'a Hoş Geldiniz!</h2>
                <p>Hesabınızı etkinleştirmek için aşağıdaki butona tıklayın:</p>
                <p><a href='{$verifyUrl}' class='button'>E-postamı Doğrula</a></p>
                <p>Veya bu linki tarayıcınıza yapıştırın:</p>
                <p>{$verifyUrl}</p>
                <div class='footer'>
                    <p>Bu link 24 saat geçerlidir.</p>
                    <p>Eğer bu hesabı siz oluşturmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return sendEmail($email, $subject, $body);
    }
    
    /**
     * E-posta doğrulama
     */
    public function verifyEmail($token) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE verification_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Geçersiz doğrulama linki.'];
        }
        
        $stmt = $this->db->prepare("
            UPDATE users SET email_verified = 1, verification_token = NULL, updated_at = datetime('now')
            WHERE id = ?
        ");
        $stmt->execute([$user['id']]);
        
        return ['success' => true, 'message' => 'E-posta adresiniz doğrulandı! Şimdi giriş yapabilirsiniz.'];
    }
    
    /**
     * Kullanıcı girişi
     */
    public function login($email, $password, $rememberMe = false) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Login log kaydı
        $logStmt = $this->db->prepare("
            INSERT INTO login_logs (user_id, ip_address, user_agent, success) 
            VALUES (?, ?, ?, ?)
        ");
        
        if (!$user || !verifyPassword($password, $user['password'])) {
            $logStmt->execute([$user['id'] ?? null, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], 0]);
            return ['success' => false, 'message' => 'E-posta veya şifre hatalı.'];
        }
        
        if (!$user['email_verified']) {
            return ['success' => false, 'message' => 'Lütfen önce e-posta adresinizi doğrulayın.'];
        }
        
        // Başarılı giriş
        $logStmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], 1]);
        
        // Son giriş güncelle
        $stmt = $this->db->prepare("UPDATE users SET last_login = datetime('now') WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Session başlat
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['login_time'] = time();
        
        // Aktif çocuğu session'a ekle
        $childStmt = $this->db->prepare("SELECT id FROM children WHERE user_id = ? AND is_active = 1 LIMIT 1");
        $childStmt->execute([$user['id']]);
        $child = $childStmt->fetch();
        if ($child) {
            $_SESSION['active_child_id'] = $child['id'];
        }
        
        // Beni hatırla özelliği
        if ($rememberMe) {
            $this->createRememberToken($user['id']);
        }
        
        return ['success' => true, 'message' => 'Giriş başarılı!'];
    }
    
    /**
     * Remember me token oluştur (30 gün)
     */
    private function createRememberToken($userId) {
        // Eski tokenları temizle
        $this->db->prepare("DELETE FROM remember_tokens WHERE user_id = ?")->execute([$userId]);
        
        // Yeni token oluştur
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $this->db->prepare("
            INSERT INTO remember_tokens (user_id, token, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $token, $expiresAt]);
        
        // Cookie'ye kaydet (30 gün)
        setcookie('remember_token', $token, [
            'expires' => time() + (30 * 24 * 60 * 60), // 30 gün
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']), // HTTPS varsa secure
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    /**
     * Remember token ile otomatik giriş
     */
    public function loginWithRememberToken($token) {
        // Token'ı kontrol et
        $stmt = $this->db->prepare("
            SELECT rt.user_id, rt.expires_at, u.email, u.email_verified 
            FROM remember_tokens rt
            INNER JOIN users u ON rt.user_id = u.id
            WHERE rt.token = ? AND rt.expires_at > datetime('now')
        ");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();
        
        if (!$tokenData) {
            // Geçersiz veya süresi dolmuş token
            $this->clearRememberToken($token);
            return false;
        }
        
        if (!$tokenData['email_verified']) {
            return false;
        }
        
        // Son giriş güncelle
        $stmt = $this->db->prepare("UPDATE users SET last_login = datetime('now') WHERE id = ?");
        $stmt->execute([$tokenData['user_id']]);
        
        // Session başlat
        $_SESSION['user_id'] = $tokenData['user_id'];
        $_SESSION['user_email'] = $tokenData['email'];
        $_SESSION['login_time'] = time();
        
        // Aktif çocuğu session'a ekle
        $childStmt = $this->db->prepare("SELECT id FROM children WHERE user_id = ? AND is_active = 1 LIMIT 1");
        $childStmt->execute([$tokenData['user_id']]);
        $child = $childStmt->fetch();
        if ($child) {
            $_SESSION['active_child_id'] = $child['id'];
        }
        
        return true;
    }
    
    /**
     * Remember token'ı temizle
     */
    public function clearRememberToken($token) {
        if ($token) {
            $this->db->prepare("DELETE FROM remember_tokens WHERE token = ?")->execute([$token]);
        }
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    /**
     * Kullanıcının son aktivite zamanını kontrol et
     * 30 gün boyunca sayfa açılmazsa oturum sonlandırılır
     */
    public function checkLastActivity() {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT last_login FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !$user['last_login']) {
            return true; // İlk giriş, devam et
        }
        
        // Son aktiviteden bu yana geçen gün sayısı
        $lastLogin = strtotime($user['last_login']);
        $daysSinceLastLogin = (time() - $lastLogin) / (24 * 60 * 60);
        
        // 30 günden fazla geçmişse oturumu sonlandır
        if ($daysSinceLastLogin > 30) {
            $this->logout();
            return false;
        }
        
        // Son aktiviteyi güncelle (her sayfa yüklendiğinde)
        $this->updateLastActivity($userId);
        
        return true;
    }
    
    /**
     * Son aktiviteyi güncelle
     */
    private function updateLastActivity($userId) {
        // Sadece 24 saatte bir güncelle (gereksiz veritabanı yazmalarını önlemek için)
        if (!isset($_SESSION['last_activity_update']) || 
            (time() - $_SESSION['last_activity_update']) > (24 * 60 * 60)) {
            
            $stmt = $this->db->prepare("UPDATE users SET last_login = datetime('now') WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['last_activity_update'] = time();
        }
    }
    
    /**
     * Çıkış
     */
    public function logout() {
        // Remember token'ı temizle
        if (isset($_COOKIE['remember_token'])) {
            $this->clearRememberToken($_COOKIE['remember_token']);
        }
        
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Giriş kontrolü
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Mevcut kullanıcıyı al
     */
    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    
    /**
     * Şifre sıfırlama talebi
     */
    public function requestPasswordReset($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Güvenlik için aynı mesajı göster
            return ['success' => true, 'message' => 'Eğer bu e-posta kayıtlıysa, sıfırlama linki gönderildi.'];
        }
        
        $resetToken = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $this->db->prepare("
            UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?
        ");
        $stmt->execute([$resetToken, $expires, $user['id']]);
        
        // Reset emaili gönder
        $resetUrl = SITE_URL . "/reset-password.php?token=" . $resetToken;
        $subject = "100 Koyun - Şifre Sıfırlama";
        $body = "
        <html>
        <body>
            <h2>Şifre Sıfırlama</h2>
            <p>Şifrenizi sıfırlamak için aşağıdaki linke tıklayın:</p>
            <p><a href='{$resetUrl}'>{$resetUrl}</a></p>
            <p>Bu link 1 saat geçerlidir.</p>
        </body>
        </html>
        ";
        
        sendEmail($email, $subject, $body);
        
        return ['success' => true, 'message' => 'Eğer bu e-posta kayıtlıysa, sıfırlama linki gönderildi.'];
    }
}

