<?php
/**
 * 100 Koyun - Ana Sayfa (Günün Masalı)
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/children.php';
require_once __DIR__ . '/includes/story.php';

$storyManager = new Story();
$activeChild = null;

if (Auth::isLoggedIn()) {
    $childrenManager = new Children();
    $activeChild = $childrenManager->getActiveChild($_SESSION['user_id']);
}

$pageTitle = 'Günün Masalı';
include __DIR__ . '/includes/header.php';
?>

<div class="story-container">
    <div class="story-header">
        <h1 class="story-title">
            <span class="title-icon">📖</span>
            Günün Masalı
        </h1>
        
        <?php if ($activeChild): ?>
        <p class="story-subtitle">
            <span class="child-badge">
                <?= $activeChild['gender'] === 'kiz' ? '👧' : '👦' ?>
                <?= htmlspecialchars($activeChild['name']) ?> için hazırlandı
            </span>
        </p>
        <?php else: ?>
        <p class="story-subtitle">
            Kişiselleştirilmiş masal için <a href="/giris.php">giriş yapın</a> veya <a href="/kayit.php">kayıt olun</a>
        </p>
        <?php endif; ?>
    </div>
    
    <div class="story-card">
        <div class="story-content" id="storyContent">
            <?= $storyManager->getStoryHTML($activeChild, true) ?>
        </div>
        
        <div class="story-actions">
            <button class="btn btn-primary btn-large" id="readStoryBtn" onclick="readStory()">
                <i class="fas fa-volume-up"></i>
                <span>Masalı Oku</span>
            </button>
            
            <button class="btn btn-secondary" id="pauseBtn" onclick="pauseReading()" style="display: none;">
                <i class="fas fa-pause"></i>
                <span>Duraklat</span>
            </button>
            
            <button class="btn btn-secondary" id="stopBtn" onclick="stopReading()" style="display: none;">
                <i class="fas fa-stop"></i>
                <span>Durdur</span>
            </button>
            
            <div class="story-controls">
                <button class="btn btn-outline btn-small" onclick="toggleContinuation()">
                    <i class="fas fa-plus-circle"></i>
                    <span id="continuationToggleText">Devamını Göster</span>
                </button>
                
                <button class="btn btn-outline btn-small" onclick="shareStory()">
                    <i class="fas fa-share-alt"></i>
                    Paylaş
                </button>
            </div>
        </div>
    </div>
    
    <!-- Bilgi Kutusu -->
    <?php if (!$activeChild): ?>
    <div class="info-box">
        <div class="info-icon">💡</div>
        <div class="info-content">
            <h3>Masalı Kişiselleştirin!</h3>
            <p>
                Kayıt olarak çocuğunuzun adını, cinsiyetini ve şehrini girebilirsiniz. 
                Masal otomatik olarak çocuğunuza özel hale gelir!
            </p>
            <a href="/kayit.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Hemen Kayıt Ol
            </a>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Koyun Animasyonu -->
    <div class="sheep-animation" id="sheepAnimation">
        <div class="fence"></div>
        <div class="sheep-container">
            <div class="animated-sheep" id="animatedSheep">🐑</div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal" id="shareModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Paylaş</h2>
            <button class="modal-close" onclick="closeShareModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>100 Koyun'u arkadaşlarınızla paylaşın!</p>
            <div class="share-buttons">
                <a href="https://twitter.com/intent/tweet?text=Çocuğum%20100%20Koyun%20ile%20her%20gece%20tatlı%20uykular%20uyuyor!%20%23100koyun&url=https://www.100koyun.net" 
                   target="_blank" class="share-btn twitter">
                    <i class="fab fa-x-twitter"></i> X (Twitter)
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.100koyun.net" 
                   target="_blank" class="share-btn facebook">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a href="https://api.whatsapp.com/send?text=Çocuğum%20100%20Koyun%20ile%20her%20gece%20tatlı%20uykular%20uyuyor!%20https://www.100koyun.net" 
                   target="_blank" class="share-btn whatsapp">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="https://t.me/share/url?url=https://www.100koyun.net&text=Çocuğum%20100%20Koyun%20ile%20her%20gece%20tatlı%20uykular%20uyuyor!" 
                   target="_blank" class="share-btn telegram">
                    <i class="fab fa-telegram"></i> Telegram
                </a>
            </div>
            <div class="share-link">
                <input type="text" value="https://www.100koyun.net" id="shareUrl" readonly>
                <button class="btn btn-primary btn-small" onclick="copyShareLink()">
                    <i class="fas fa-copy"></i> Kopyala
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script src="/assets/js/story.js"></script>';
include __DIR__ . '/includes/footer.php';
?>

