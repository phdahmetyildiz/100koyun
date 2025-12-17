<?php
/**
 * 100 Koyun - Kullanım Koşulları
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
$pageTitle = 'Kullanım Koşulları';
include __DIR__ . '/includes/header.php';
?>

<div class="page-container legal-page">
    <div class="page-header">
        <h1>
            <span class="page-icon">📋</span>
            Kullanım Koşulları
        </h1>
    </div>
    
    <div class="content-card legal-content">
        <p class="legal-date">Son güncelleme: <?= date('d.m.Y') ?></p>
        
        <section>
            <h2>1. Kabul</h2>
            <p>
                100 Koyun web sitesini ("Site") kullanarak bu kullanım koşullarını kabul etmiş olursunuz. 
                Bu koşulları kabul etmiyorsanız, lütfen siteyi kullanmayın.
            </p>
        </section>
        
        <section>
            <h2>2. Hizmet Tanımı</h2>
            <p>
                100 Koyun, ebeveynlerin çocuklarına uyku masalları okumasına yardımcı olan ücretsiz bir web hizmetidir. 
                Hizmet, masalların kişiselleştirilmesi, sesli okuma ve çocuk profili yönetimi özelliklerini içerir.
            </p>
        </section>
        
        <section>
            <h2>3. Hesap Oluşturma</h2>
            <ul>
                <li>Hesap oluşturmak için 18 yaşından büyük olmanız gerekmektedir.</li>
                <li>Doğru ve güncel bilgiler sağlamakla yükümlüsünüz.</li>
                <li>Hesap güvenliğinizden siz sorumlusunuz.</li>
                <li>Şifrenizi kimseyle paylaşmamalısınız.</li>
                <li>Hesabınızdaki tüm aktivitelerden siz sorumlusunuz.</li>
            </ul>
        </section>
        
        <section>
            <h2>4. Kabul Edilebilir Kullanım</h2>
            <p>Siteyi kullanırken aşağıdakileri kabul edersiniz:</p>
            <ul>
                <li>Yasalara uygun davranacağınızı</li>
                <li>Başkalarının haklarına saygı göstereceğinizi</li>
                <li>Sahte veya yanıltıcı bilgi vermeyeceğinizi</li>
                <li>Siteye zarar verecek faaliyetlerde bulunmayacağınızı</li>
                <li>Spam veya istenmeyen içerik paylaşmayacağınızı</li>
            </ul>
        </section>
        
        <section>
            <h2>5. Fikri Mülkiyet</h2>
            <p>
                Sitedeki tüm içerikler (metin, grafikler, logolar, ses dosyaları vb.) 
                100 Koyun'a veya lisans verenlerine aittir. Bu içerikleri izinsiz 
                kopyalayamaz, dağıtamaz veya değiştiremezsiniz.
            </p>
            <p>
                Masalları kişisel kullanım için yazdırabilirsiniz ancak ticari amaçla kullanamazsınız.
            </p>
        </section>
        
        <section>
            <h2>6. Kullanıcı İçeriği</h2>
            <p>
                Çocuk profilleri için girdiğiniz bilgiler "kullanıcı içeriği" olarak kabul edilir. 
                Bu içerikler için siz sorumlusunuz ve bunların doğru olduğunu beyan edersiniz.
            </p>
        </section>
        
        <section>
            <h2>7. Sorumluluk Reddi</h2>
            <p>
                Site "olduğu gibi" ve "mevcut olduğu şekilde" sunulmaktadır. 
                Hizmetin kesintisiz veya hatasız olacağını garanti etmiyoruz.
            </p>
            <p>
                100 Koyun, sitenin kullanımından doğabilecek doğrudan veya dolaylı zararlardan sorumlu değildir.
            </p>
        </section>
        
        <section>
            <h2>8. Hizmet Değişiklikleri</h2>
            <p>
                Hizmeti önceden bildirimde bulunmaksızın değiştirme, askıya alma veya 
                sonlandırma hakkını saklı tutarız. Önemli değişiklikler için kayıtlı 
                kullanıcılara e-posta ile bildirim yapılacaktır.
            </p>
        </section>
        
        <section>
            <h2>9. Hesap Sonlandırma</h2>
            <p>
                Hesabınızı istediğiniz zaman ayarlar sayfasından silebilirsiniz. 
                Ayrıca, bu koşulları ihlal etmeniz durumunda hesabınızı askıya alma 
                veya sonlandırma hakkını saklı tutarız.
            </p>
        </section>
        
        <section>
            <h2>10. Uygulanacak Hukuk</h2>
            <p>
                Bu kullanım koşulları Türkiye Cumhuriyeti yasalarına tabidir. 
                Herhangi bir uyuşmazlık durumunda İstanbul Mahkemeleri yetkilidir.
            </p>
        </section>
        
        <section>
            <h2>11. Koşul Değişiklikleri</h2>
            <p>
                Bu kullanım koşullarını zaman zaman güncelleyebiliriz. 
                Değişiklikler bu sayfada yayınlandığı tarihte yürürlüğe girer. 
                Siteyi kullanmaya devam etmeniz, güncellenmiş koşulları kabul ettiğiniz anlamına gelir.
            </p>
        </section>
        
        <section>
            <h2>12. İletişim</h2>
            <p>
                Bu kullanım koşulları hakkında sorularınız için:
            </p>
            <ul>
                <li>E-posta: <a href="mailto:bilgi@100koyun.net">bilgi@100koyun.net</a></li>
                <li>Web: <a href="/iletisim.php">İletişim Formu</a></li>
            </ul>
        </section>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

