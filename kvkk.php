<?php
/**
 * 100 Koyun - KVKK Aydınlatma Metni
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
$pageTitle = 'KVKK Aydınlatma Metni';
include __DIR__ . '/includes/header.php';
?>

<div class="page-container legal-page">
    <div class="page-header">
        <h1>
            <span class="page-icon">📜</span>
            KVKK Aydınlatma Metni
        </h1>
        <p class="page-subtitle">6698 Sayılı Kişisel Verilerin Korunması Kanunu Kapsamında</p>
    </div>
    
    <div class="content-card legal-content">
        <p class="legal-date">Son güncelleme: <?= date('d.m.Y') ?></p>
        
        <section>
            <h2>1. Veri Sorumlusu</h2>
            <p>
                100 Koyun web sitesi ("Site") olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") 
                kapsamında veri sorumlusu sıfatıyla kişisel verilerinizi aşağıda açıklanan amaçlar ve hukuki 
                sebepler dahilinde işlemekteyiz.
            </p>
        </section>
        
        <section>
            <h2>2. Toplanan Kişisel Veriler</h2>
            <p>Sitemizi kullanırken aşağıdaki kişisel verileriniz toplanmaktadır:</p>
            
            <h3>2.1. Kullanıcı Bilgileri</h3>
            <ul>
                <li>E-posta adresi</li>
                <li>Şifre (şifrelenmiş olarak)</li>
                <li>Kayıt ve giriş tarihleri</li>
            </ul>
            
            <h3>2.2. Çocuk Bilgileri</h3>
            <ul>
                <li>Çocuğun adı</li>
                <li>Cinsiyeti</li>
                <li>Yaşadığı şehir (isteğe bağlı)</li>
                <li>Doğum tarihi (isteğe bağlı)</li>
            </ul>
            
            <h3>2.3. Teknik Veriler</h3>
            <ul>
                <li>IP adresi</li>
                <li>Tarayıcı bilgileri</li>
                <li>Çerez verileri</li>
            </ul>
        </section>
        
        <section>
            <h2>3. Kişisel Verilerin İşlenme Amaçları</h2>
            <p>Kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
            <ul>
                <li>Hesap oluşturma ve yönetimi</li>
                <li>Masalların çocuğunuza özel olarak kişiselleştirilmesi</li>
                <li>Hizmet kalitesinin artırılması</li>
                <li>İletişim taleplerinin yanıtlanması</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi</li>
                <li>Site güvenliğinin sağlanması</li>
            </ul>
        </section>
        
        <section>
            <h2>4. Kişisel Verilerin İşlenme Hukuki Sebepleri</h2>
            <p>Kişisel verileriniz KVKK'nın 5. ve 6. maddelerinde belirtilen aşağıdaki hukuki sebeplere dayanarak işlenmektedir:</p>
            <ul>
                <li>Açık rızanızın bulunması</li>
                <li>Sözleşmenin kurulması veya ifası için gerekli olması</li>
                <li>Hukuki yükümlülüğün yerine getirilmesi için zorunlu olması</li>
                <li>İlgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla, veri sorumlusunun meşru menfaatleri için gerekli olması</li>
            </ul>
        </section>
        
        <section>
            <h2>5. Kişisel Verilerin Aktarılması</h2>
            <p>
                Kişisel verileriniz, yasal zorunluluklar dışında üçüncü kişi veya kuruluşlarla paylaşılmamaktadır. 
                Verileriniz yurt içinde güvenli sunucularda saklanmaktadır.
            </p>
        </section>
        
        <section>
            <h2>6. Kişisel Verilerin Saklanma Süresi</h2>
            <p>
                Kişisel verileriniz, işleme amaçlarının gerektirdiği süre boyunca ve yasal saklama süreleri 
                dahilinde muhafaza edilmektedir. Hesabınızı sildiğinizde, kişisel verileriniz yasal saklama 
                süreleri sonunda sistemlerimizden kalıcı olarak silinir.
            </p>
        </section>
        
        <section>
            <h2>7. Veri Güvenliği</h2>
            <p>Kişisel verilerinizin güvenliği için aşağıdaki önlemler alınmaktadır:</p>
            <ul>
                <li>SSL/TLS şifreleme ile güvenli veri iletimi</li>
                <li>Şifrelerin hash algoritmaları ile korunması</li>
                <li>Güvenlik duvarları ve saldırı tespit sistemleri</li>
                <li>Düzenli güvenlik güncellemeleri</li>
                <li>Erişim kontrolleri ve yetkilendirme</li>
            </ul>
        </section>
        
        <section>
            <h2>8. Haklarınız</h2>
            <p>KVKK'nın 11. maddesi kapsamında aşağıdaki haklara sahipsiniz:</p>
            <ul>
                <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                <li>İşlenmişse buna ilişkin bilgi talep etme</li>
                <li>İşlenme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                <li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme</li>
                <li>Eksik veya yanlış işlenmişse düzeltilmesini isteme</li>
                <li>KVKK'nın 7. maddesinde öngörülen şartlar çerçevesinde silinmesini veya yok edilmesini isteme</li>
                <li>Düzeltme, silme veya yok etme işlemlerinin aktarıldığı üçüncü kişilere bildirilmesini isteme</li>
                <li>İşlenen verilerin münhasıran otomatik sistemler vasıtasıyla analiz edilmesi suretiyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme</li>
                <li>Kanuna aykırı olarak işlenmesi sebebiyle zarara uğramanız halinde zararın giderilmesini talep etme</li>
            </ul>
        </section>
        
        <section>
            <h2>9. Başvuru Yöntemi</h2>
            <p>
                KVKK kapsamındaki haklarınızı kullanmak için <a href="mailto:bilgi@100koyun.net">bilgi@100koyun.net</a> 
                adresine e-posta gönderebilir veya <a href="/iletisim.php">iletişim formumuzu</a> kullanabilirsiniz.
            </p>
            <p>
                Başvurularınız en geç 30 gün içinde ücretsiz olarak sonuçlandırılacaktır. 
                İşlemin ayrıca bir maliyet gerektirmesi halinde Kişisel Verileri Koruma Kurulu 
                tarafından belirlenen ücret tarifesi uygulanabilir.
            </p>
        </section>
        
        <section>
            <h2>10. Değişiklikler</h2>
            <p>
                Bu aydınlatma metni gerektiğinde güncellenebilir. Güncellemeler sitede yayınlandığı 
                tarihte yürürlüğe girer. Önemli değişiklikler olması halinde kayıtlı e-posta adresinize 
                bilgilendirme yapılacaktır.
            </p>
        </section>
        
        <div class="legal-footer">
            <p>
                <strong>100 Koyun</strong><br>
                E-posta: <a href="mailto:bilgi@100koyun.net">bilgi@100koyun.net</a><br>
                Web: <a href="https://www.100koyun.net">www.100koyun.net</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

