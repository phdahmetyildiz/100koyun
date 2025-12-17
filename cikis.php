<?php
/**
 * 100 Koyun - Çıkış
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/includes/auth.php';

$auth = new Auth();
$auth->logout();

header('Location: /');
exit;

