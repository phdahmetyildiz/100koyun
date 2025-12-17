<?php
/**
 * 100 Koyun - Sayfa Başlığı
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/children.php';

// Otomatik giriş kontrolü (remember me cookie'si varsa)
$isLoggedIn = Auth::isLoggedIn();

if (!$isLoggedIn && isset($_COOKIE['remember_token'])) {
    $auth = new Auth();
    if ($auth->loginWithRememberToken($_COOKIE['remember_token'])) {
        $isLoggedIn = true;
    }
}

// Son aktivite kontrolü (30 gün kuralı)
if ($isLoggedIn) {
    $auth = new Auth();
    if (!$auth->checkLastActivity()) {
        $isLoggedIn = false;
    }
}

$currentUser = Auth::getCurrentUser();
$children = [];
$activeChild = null;

if ($isLoggedIn) {
    $childrenManager = new Children();
    $children = $childrenManager->getChildren($_SESSION['user_id']);
    $activeChild = $childrenManager->getActiveChild($_SESSION['user_id']);
}

$pageTitle = $pageTitle ?? 'Günün Masalı';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="100 Koyun - Çocuklar için uyku masalları. Her gece 100 koyun sayarak tatlı uykular.">
    <meta name="keywords" content="çocuk masalı, uyku masalı, koyun sayma, bebek uyku, çocuk hikayesi">
    <meta name="author" content="Hakkı Ayyıldız">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="100 Koyun - <?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="Çocuklarınız için her gece 100 koyun sayarak tatlı uykular.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= SITE_URL ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/og-image.png">
    
    <title><?= htmlspecialchars($pageTitle) ?> - 100 Koyun</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700&family=Patrick+Hand&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
</head>
<body>
    <!-- Animated Background -->
    <div class="background-scene">
        <div class="sky"></div>
        <div class="clouds">
            <div class="cloud cloud-1"></div>
            <div class="cloud cloud-2"></div>
            <div class="cloud cloud-3"></div>
        </div>
        <div class="hills">
            <div class="hill hill-back"></div>
            <div class="hill hill-front"></div>
        </div>
        <div class="grass"></div>
        <div class="stars"></div>
    </div>
    
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="/" class="logo">
                <span class="logo-icon">🐑</span>
                <span class="logo-text">100 Koyun</span>
            </a>
            
            <nav class="main-nav">
                <a href="/" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i> Günün Masalı
                </a>
                <a href="/neden-100-koyun.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'neden-100-koyun.php' ? 'active' : '' ?>">
                    <i class="fas fa-question-circle"></i> Neden 100 Koyun?
                </a>
                <a href="/iletisim.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'iletisim.php' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i> Bize Ulaşın
                </a>
            </nav>
            
            <div class="header-actions">
                <?php if ($isLoggedIn): ?>
                    <!-- Çocuk Seçici -->
                    <?php if (!empty($children)): ?>
                    <div class="child-selector">
                        <button class="child-selector-btn" id="childSelectorBtn">
                            <span class="child-avatar"><?= $activeChild ? ($activeChild['gender'] === 'kiz' ? '👧' : '👦') : '👶' ?></span>
                            <span class="child-name"><?= $activeChild ? htmlspecialchars($activeChild['name']) : 'Çocuk Seç' ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="child-dropdown" id="childDropdown">
                            <?php foreach ($children as $child): ?>
                            <a href="/api/set-active-child.php?id=<?= $child['id'] ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                               class="child-option <?= $child['is_active'] ? 'active' : '' ?>">
                                <span class="child-avatar"><?= $child['gender'] === 'kiz' ? '👧' : '👦' ?></span>
                                <span><?= htmlspecialchars($child['name']) ?></span>
                                <?php if ($child['is_active']): ?>
                                <i class="fas fa-check"></i>
                                <?php endif; ?>
                            </a>
                            <?php endforeach; ?>
                            <hr>
                            <a href="/profil.php" class="child-option add-child">
                                <i class="fas fa-plus"></i>
                                <span>Çocuk Ekle</span>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Kullanıcı Menüsü -->
                    <div class="user-menu">
                        <button class="user-menu-btn" id="userMenuBtn">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="user-info">
                                <i class="fas fa-envelope"></i>
                                <span><?= htmlspecialchars($currentUser['email']) ?></span>
                            </div>
                            <hr>
                            <a href="/profil.php" class="dropdown-item">
                                <i class="fas fa-child"></i> Çocuklarım
                            </a>
                            <a href="/ayarlar.php" class="dropdown-item">
                                <i class="fas fa-cog"></i> Ayarlar
                            </a>
                            <hr>
                            <a href="/cikis.php" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/giris.php" class="btn btn-outline">Giriş Yap</a>
                    <a href="/kayit.php" class="btn btn-primary">Kayıt Ol</a>
                <?php endif; ?>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <nav class="mobile-nav">
            <a href="/" class="mobile-nav-link">
                <i class="fas fa-book-open"></i> Günün Masalı
            </a>
            <a href="/neden-100-koyun.php" class="mobile-nav-link">
                <i class="fas fa-question-circle"></i> Neden 100 Koyun?
            </a>
            <a href="/iletisim.php" class="mobile-nav-link">
                <i class="fas fa-envelope"></i> Bize Ulaşın
            </a>
            <?php if ($isLoggedIn): ?>
            <hr>
            <a href="/profil.php" class="mobile-nav-link">
                <i class="fas fa-child"></i> Çocuklarım
            </a>
            <a href="/cikis.php" class="mobile-nav-link">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
            <?php else: ?>
            <hr>
            <a href="/giris.php" class="mobile-nav-link">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </a>
            <a href="/kayit.php" class="mobile-nav-link">
                <i class="fas fa-user-plus"></i> Kayıt Ol
            </a>
            <?php endif; ?>
        </nav>
    </div>
    
    <main class="main-content">

