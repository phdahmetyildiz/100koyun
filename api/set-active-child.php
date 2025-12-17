<?php
/**
 * 100 Koyun - Aktif Çocuk Değiştirme API
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/children.php';

// Giriş kontrolü
if (!Auth::isLoggedIn()) {
    header('Location: /giris.php');
    exit;
}

$childId = intval($_GET['id'] ?? 0);
$redirect = $_GET['redirect'] ?? '/';

if ($childId > 0) {
    $childrenManager = new Children();
    $childrenManager->setActiveChild($childId, $_SESSION['user_id']);
}

// Güvenli yönlendirme (sadece aynı site içinde)
if (strpos($redirect, '/') === 0) {
    header('Location: ' . $redirect);
} else {
    header('Location: /');
}
exit;

