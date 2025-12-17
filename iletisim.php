<?php
/**
 * 100 Koyun - İletişim Sayfası
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');
        
        if (empty($name) || !$email || empty($subject) || empty($message)) {
            $error = 'Lütfen tüm alanları doldurun.';
        } else {
            try {
                $db = getDB();
                
                // Veritabanına kaydet
                $stmt = $db->prepare("
                    INSERT INTO contact_messages (name, email, subject, message, ip_address) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $email, $subject, $message, $_SERVER['REMOTE_ADDR']]);
                
                // E-posta gönder
                $emailBody = "
                <html>
                <body>
                    <h2>Yeni İletişim Formu Mesajı</h2>
                    <p><strong>Gönderen:</strong> {$name}</p>
                    <p><strong>E-posta:</strong> {$email}</p>
                    <p><strong>Konu:</strong> {$subject}</p>
                    <p><strong>Mesaj:</strong></p>
                    <p>{$message}</p>
                    <hr>
                    <p><small>IP: {$_SERVER['REMOTE_ADDR']} | Tarih: " . date('d.m.Y H:i') . "</small></p>
                </body>
                </html>
                ";
                
                sendEmail(CONTACT_EMAIL, "100 Koyun İletişim: " . $subject, $emailBody);
                
                $success = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
                
                // Form verilerini temizle
                $_POST = [];
                
            } catch (Exception $e) {
                error_log("İletişim formu hatası: " . $e->getMessage());
                $error = 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            }
        }
    }
}

$pageTitle = 'Bize Ulaşın';
include __DIR__ . '/includes/header.php';
?>

<div class="page-container contact-page">
    <div class="page-header">
        <h1>
            <span class="page-icon">✉️</span>
            Bize Ulaşın
        </h1>
        <p class="page-subtitle">Sorularınız, önerileriniz veya geri bildirimleriniz için bize yazın</p>
    </div>
    
    <div class="contact-grid">
        <div class="content-card contact-form-card">
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
            <?php else: ?>
            
            <form method="POST" class="contact-form">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i> Adınız Soyadınız *
                        </label>
                        <input type="text" id="name" name="name" required 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                               placeholder="Adınızı girin">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> E-posta Adresiniz *
                        </label>
                        <input type="email" id="email" name="email" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="ornek@email.com">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="subject">
                        <i class="fas fa-tag"></i> Konu *
                    </label>
                    <select id="subject" name="subject" required>
                        <option value="">Konu seçin</option>
                        <option value="Öneri" <?= ($_POST['subject'] ?? '') === 'Öneri' ? 'selected' : '' ?>>💡 Öneri</option>
                        <option value="Soru" <?= ($_POST['subject'] ?? '') === 'Soru' ? 'selected' : '' ?>>❓ Soru</option>
                        <option value="Teknik Sorun" <?= ($_POST['subject'] ?? '') === 'Teknik Sorun' ? 'selected' : '' ?>>🔧 Teknik Sorun</option>
                        <option value="İş Birliği" <?= ($_POST['subject'] ?? '') === 'İş Birliği' ? 'selected' : '' ?>>🤝 İş Birliği</option>
                        <option value="Diğer" <?= ($_POST['subject'] ?? '') === 'Diğer' ? 'selected' : '' ?>>📝 Diğer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">
                        <i class="fas fa-comment"></i> Mesajınız *
                    </label>
                    <textarea id="message" name="message" rows="6" required 
                              placeholder="Mesajınızı buraya yazın..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large btn-block">
                    <i class="fas fa-paper-plane"></i> Gönder
                </button>
            </form>
            
            <?php endif; ?>
        </div>
        
        <div class="contact-info-card">
            <div class="content-card">
                <h3><i class="fas fa-info-circle"></i> İletişim Bilgileri</h3>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>E-posta</strong>
                        <a href="mailto:bilgi@100koyun.net">bilgi@100koyun.net</a>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-globe"></i>
                    <div>
                        <strong>Web Sitesi</strong>
                        <a href="https://www.100koyun.net">www.100koyun.net</a>
                    </div>
                </div>
            </div>
            
            <div class="content-card">
                <h3><i class="fas fa-share-alt"></i> Sosyal Medya</h3>
                <p>Bizi sosyal medyada takip edin ve #100koyun etiketiyle paylaşımlarınızı görün!</p>
                
                <div class="social-links">
                    <a href="https://instagram.com/100koyun" target="_blank" class="social-link instagram">
                        <i class="fab fa-instagram"></i>
                        <span>@100koyun</span>
                    </a>
                    <a href="https://twitter.com/100koyun" target="_blank" class="social-link twitter">
                        <i class="fab fa-x-twitter"></i>
                        <span>@100koyun</span>
                    </a>
                    <a href="https://facebook.com/100koyun" target="_blank" class="social-link facebook">
                        <i class="fab fa-facebook"></i>
                        <span>/100koyun</span>
                    </a>
                    <a href="https://youtube.com/@100koyun" target="_blank" class="social-link youtube">
                        <i class="fab fa-youtube"></i>
                        <span>@100koyun</span>
                    </a>
                    <a href="https://tiktok.com/@100koyun" target="_blank" class="social-link tiktok">
                        <i class="fab fa-tiktok"></i>
                        <span>@100koyun</span>
                    </a>
                </div>
            </div>
            
            <div class="content-card faq-card">
                <h3><i class="fas fa-question-circle"></i> Sıkça Sorulan Sorular</h3>
                
                <div class="faq-item">
                    <strong>100 Koyun ücretsiz mi?</strong>
                    <p>Evet! Tüm özellikler tamamen ücretsizdir.</p>
                </div>
                
                <div class="faq-item">
                    <strong>Birden fazla çocuk ekleyebilir miyim?</strong>
                    <p>Evet, istediğiniz kadar çocuk profili ekleyebilirsiniz.</p>
                </div>
                
                <div class="faq-item">
                    <strong>Verilerim güvende mi?</strong>
                    <p>Evet, KVKK kapsamında tüm verileriniz güvenle saklanır.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

