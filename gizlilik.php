<?php
/**
 * 100 Koyun - Gizlilik Politikası
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
$pageTitle = 'Gizlilik Politikası';
include __DIR__ . '/includes/header.php';
?>

<div class="page-container legal-page">
    <div class="page-header">
        <h1>
            <span class="page-icon">🔒</span>
            Gizlilik Politikası
        </h1>
    </div>
    
    <div class="content-card legal-content">
        <p class="legal-date">Son güncelleme: <?= date('d.m.Y') ?></p>
        
        <section>
            <h2>1. Genel Bakış</h2>
            <p>
                100 Koyun ("biz", "bizim") olarak gizliliğinize saygı duyuyor ve kişisel verilerinizi 
                korumayı taahhüt ediyoruz. Bu gizlilik politikası, web sitemizi (www.100koyun.net) 
                kullandığınızda hangi bilgilerin toplandığını, nasıl kullanıldığını ve korunduğunu açıklar.
            </p>
        </section>
        
        <section>
            <h2>2. Topladığımız Bilgiler</h2>
            
            <h3>2.1. Sizin Sağladığınız Bilgiler</h3>
            <ul>
                <li>Kayıt olurken: e-posta adresi ve şifre</li>
                <li>Çocuk profili oluştururken: çocuğun adı, cinsiyeti, şehri (isteğe bağlı), doğum tarihi (isteğe bağlı)</li>
                <li>İletişim formunu kullanırken: adınız, e-postanız ve mesajınız</li>
            </ul>
            
            <h3>2.2. Otomatik Toplanan Bilgiler</h3>
            <ul>
                <li>IP adresi</li>
                <li>Tarayıcı türü ve sürümü</li>
                <li>Cihaz bilgileri</li>
                <li>Sayfa görüntüleme verileri</li>
                <li>Çerezler</li>
            </ul>
        </section>
        
        <section>
            <h2>3. Bilgilerin Kullanımı</h2>
            <p>Topladığımız bilgileri şu amaçlarla kullanırız:</p>
            <ul>
                <li>Hesap oluşturma ve yönetimi</li>
                <li>Masalların kişiselleştirilmesi</li>
                <li>Hizmet kalitesinin iyileştirilmesi</li>
                <li>Teknik sorunların giderilmesi</li>
                <li>İletişim taleplerine yanıt verilmesi</li>
                <li>Güvenlik önlemlerinin alınması</li>
            </ul>
        </section>
        
        <section>
            <h2>4. Çerezler</h2>
            <p>
                Sitemiz, oturum yönetimi ve kullanıcı deneyimini iyileştirmek için çerezler kullanır. 
                Çerezler, tarayıcınızda saklanan küçük metin dosyalarıdır.
            </p>
            <p>Kullandığımız çerez türleri:</p>
            <ul>
                <li><strong>Zorunlu Çerezler:</strong> Oturum yönetimi için gereklidir</li>
                <li><strong>Fonksiyonel Çerezler:</strong> Tercihlerinizi hatırlar</li>
            </ul>
            <p>
                Tarayıcı ayarlarınızdan çerezleri engelleyebilirsiniz, ancak bu durumda 
                bazı özellikler düzgün çalışmayabilir.
            </p>
        </section>
        
        <section>
            <h2>5. Veri Güvenliği</h2>
            <p>Verilerinizi korumak için aşağıdaki önlemleri alıyoruz:</p>
            <ul>
                <li>SSL/TLS şifreleme ile güvenli bağlantı</li>
                <li>Şifrelerin güçlü hash algoritmaları ile saklanması</li>
                <li>Düzenli güvenlik güncellemeleri</li>
                <li>Erişim kontrolü ve yetkilendirme</li>
                <li>Güvenlik duvarları</li>
            </ul>
        </section>
        
        <section>
            <h2>6. Veri Paylaşımı</h2>
            <p>
                Kişisel verilerinizi üçüncü taraflarla satmıyor veya kiralamıyoruz. 
                Verileriniz yalnızca aşağıdaki durumlarda paylaşılabilir:
            </p>
            <ul>
                <li>Yasal zorunluluk durumunda (mahkeme kararı vb.)</li>
                <li>Sizin açık onayınızla</li>
            </ul>
        </section>
        
        <section>
            <h2>7. Veri Saklama</h2>
            <p>
                Verileriniz, hesabınız aktif olduğu sürece saklanır. 
                Hesabınızı sildiğinizde, verileriniz 30 gün içinde sistemlerimizden kalıcı olarak silinir.
            </p>
        </section>
        
        <section>
            <h2>8. Çocukların Gizliliği</h2>
            <p>
                Hizmetimiz ebeveynler tarafından kullanılmak üzere tasarlanmıştır. 
                Çocuklar hakkında toplanan bilgiler (isim, cinsiyet, şehir) yalnızca masalların 
                kişiselleştirilmesi amacıyla kullanılır ve hiçbir şekilde üçüncü taraflarla paylaşılmaz.
            </p>
        </section>
        
        <section>
            <h2>9. Haklarınız</h2>
            <p>Kişisel verilerinizle ilgili şu haklara sahipsiniz:</p>
            <ul>
                <li>Verilerinize erişim hakkı</li>
                <li>Verilerin düzeltilmesini isteme hakkı</li>
                <li>Verilerin silinmesini isteme hakkı</li>
                <li>Veri işlenmesine itiraz hakkı</li>
            </ul>
            <p>
                Bu haklarınızı kullanmak için <a href="mailto:bilgi@100koyun.net">bilgi@100koyun.net</a> 
                adresinden bize ulaşabilirsiniz.
            </p>
        </section>
        
        <section>
            <h2>10. Politika Değişiklikleri</h2>
            <p>
                Bu gizlilik politikasını zaman zaman güncelleyebiliriz. 
                Önemli değişiklikler olduğunda kayıtlı e-posta adresinize bildirim göndereceğiz. 
                Değişiklikler, bu sayfada yayınlandığı tarihte yürürlüğe girer.
            </p>
        </section>
        
        <section>
            <h2>11. İletişim</h2>
            <p>
                Gizlilik politikamız hakkında sorularınız varsa, lütfen bizimle iletişime geçin:
            </p>
            <ul>
                <li>E-posta: <a href="mailto:bilgi@100koyun.net">bilgi@100koyun.net</a></li>
                <li>Web: <a href="/iletisim.php">İletişim Formu</a></li>
            </ul>
        </section>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

