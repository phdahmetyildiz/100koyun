<?php
/**
 * 100 Koyun - Veritabanı Yapılandırması
 * SQLite kullanılıyor
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

define('DB_PATH', __DIR__ . '/../data/100koyun.db');
define('SITE_NAME', '100 Koyun');
define('SITE_URL', 'https://www.100koyun.net');
define('CONTACT_EMAIL', 'bilgi@100koyun.net');

// Oturum güvenliği
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);

// Hata raporlama (production'da kapatılmalı)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// CSRF Token oluşturma
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token doğrulama
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Güvenli input temizleme
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Veritabanı bağlantısı
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            // Data klasörünü oluştur
            $dataDir = dirname(DB_PATH);
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0700, true);
            }
            
            $db = new PDO('sqlite:' . DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // WAL modu performans için
            $db->exec('PRAGMA journal_mode=WAL');
            
        } catch (PDOException $e) {
            error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
            die("Sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.");
        }
    }
    
    return $db;
}

// Mail gönderme fonksiyonu
function sendEmail($to, $subject, $body) {
    $headers = "From: " . SITE_NAME . " <noreply@100koyun.net>\r\n";
    $headers .= "Reply-To: " . CONTACT_EMAIL . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $body, $headers);
}

// Şifre hash'leme
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID);
}

// Şifre doğrulama
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

