<?php
/**
 * 100 Koyun - Sayfa Altlığı
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */
?>
    </main>
    
    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <span class="footer-logo">🐑 100 Koyun</span>
                    <p>Çocuklarınız için her gece 100 koyun sayarak tatlı uykular.</p>
                </div>
                
                <div class="footer-links">
                    <h4>Sayfalar</h4>
                    <a href="/">Günün Masalı</a>
                    <a href="/neden-100-koyun.php">Neden 100 Koyun?</a>
                    <a href="/iletisim.php">Bize Ulaşın</a>
                </div>
                
                <div class="footer-links">
                    <h4>Yasal</h4>
                    <a href="/kvkk.php">KVKK Aydınlatma Metni</a>
                    <a href="/gizlilik.php">Gizlilik Politikası</a>
                    <a href="/kullanim-kosullari.php">Kullanım Koşulları</a>
                </div>
                
                <div class="footer-social">
                    <h4>Bizi Takip Edin</h4>
                    <div class="social-icons">
                        <a href="https://instagram.com/100koyun" target="_blank" rel="noopener" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://twitter.com/100koyun" target="_blank" rel="noopener" title="X (Twitter)">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        <a href="https://facebook.com/100koyun" target="_blank" rel="noopener" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://youtube.com/@100koyun" target="_blank" rel="noopener" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="https://tiktok.com/@100koyun" target="_blank" rel="noopener" title="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                    <p class="hashtag">#100koyun</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> 100 Koyun. Tüm hakları saklıdır.</p>
                <p class="author">Hakkı Ayyıldız tarafından sevgiyle hazırlandı 💚</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="/assets/js/main.js"></script>
    <?php if (isset($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>

