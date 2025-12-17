# 🐑 100 Koyun

**Çocuklar için uyku masalı web uygulaması**

100 Koyun, ebeveynlerin çocuklarına uyku masalları okumasına yardımcı olan ücretsiz bir web hizmetidir. Her gece aynı masalı okumak yerine, kısa bir masal girişinin ardından 100 koyunun çit üzerinden atlaması sayılarak çocukların hem sakinleşmesi hem de sayıları öğrenmesi hedeflenir.

🌐 **Web Sitesi:** [www.100koyun.net](https://www.100koyun.net)

## ✨ Özellikler

- 📖 **Kişiselleştirilmiş Masallar:** Çocuğun adı, cinsiyeti ve şehri masala yerleştirilir
- 👶 **Çocuk Profilleri:** Birden fazla çocuk profili oluşturulabilir
- 🔊 **Sesli Okuma (TTS):** Masallar sesli olarak okunabilir
- 🎨 **Çocuk Dostu Tasarım:** Mavi gökyüzü, yeşil çimenlik, beyaz koyunlar
- 🔒 **KVKK Uyumlu:** Türkiye KVKK'ya uygun veri koruma
- 🌙 **Gece Modu:** Akşam saatlerinde otomatik gece teması
- 💾 **Beni Hatırla:** 30 günlük otomatik giriş özelliği
- ⏰ **Akıllı Oturum Yönetimi:** 30 gün boyunca sayfa açılmazsa oturum sonlanır

## 🛠 Teknolojiler

- **Backend:** PHP 7.4+
- **Veritabanı:** SQLite
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Fontlar:** Baloo 2, Patrick Hand (Google Fonts)
- **İkonlar:** Font Awesome 6

## 📁 Proje Yapısı

```
100koyun/
├── api/                    # API endpoint'leri
│   └── set-active-child.php
├── assets/
│   ├── css/
│   │   └── style.css       # Ana stil dosyası
│   ├── js/
│   │   ├── main.js         # Ana JavaScript
│   │   └── story.js        # Masal ve TTS işlemleri
│   └── images/
│       └── favicon.svg
├── config/
│   ├── database.php        # Veritabanı konfigürasyonu
│   └── install.php         # Kurulum scripti
├── data/                   # SQLite veritabanı (otomatik oluşur)
├── includes/
│   ├── auth.php            # Kimlik doğrulama
│   ├── children.php        # Çocuk yönetimi
│   ├── story.php           # Masal işlemleri
│   ├── header.php          # Sayfa başlığı
│   └── footer.php          # Sayfa altlığı
├── logs/                   # Hata logları
├── index.php               # Ana sayfa (Günün Masalı)
├── kayit.php               # Kayıt sayfası
├── giris.php               # Giriş sayfası
├── profil.php              # Çocuk profil yönetimi
├── ayarlar.php             # Hesap ayarları
├── neden-100-koyun.php     # Hakkında sayfası
├── iletisim.php            # İletişim formu
├── kvkk.php                # KVKK Aydınlatma Metni
├── gizlilik.php            # Gizlilik Politikası
├── kullanim-kosullari.php  # Kullanım Koşulları
├── error.php               # Hata sayfası
├── .htaccess               # Apache konfigürasyonu
└── README.md
```

## 🚀 Kurulum

### Gereksinimler

- PHP 7.4 veya üzeri
- Apache web sunucusu (mod_rewrite aktif)
- SQLite3 PHP uzantısı

### Adımlar

1. **Dosyaları sunucuya yükleyin**
   ```bash
   git clone https://github.com/yourrepo/100koyun.git
   cd 100koyun
   ```

2. **Klasör izinlerini ayarlayın**
   ```bash
   chmod 755 data/
   chmod 755 logs/
   ```

3. **Kurulum scriptini çalıştırın**
   
   Tarayıcınızda `https://yourdomain.com/config/install.php` adresini açın.
   Bu, veritabanı tablolarını oluşturacaktır.

4. **Kurulum dosyasını silin**
   ```bash
   rm config/install.php
   ```

5. **Mevcut veritabanı için güncelleme (opsiyonel)**
   
   Eğer daha önce kurulum yaptıysanız ve "Beni hatırla" özelliğini eklemek istiyorsanız:
   `https://yourdomain.com/config/update-remember-tokens.php` adresini açın.
   Sonra bu dosyayı da silin.

5. **Konfigürasyonu güncelleyin**
   
   `config/database.php` dosyasında `SITE_URL` değişkenini güncelleyin:
   ```php
   define('SITE_URL', 'https://www.100koyun.net');
   ```

6. **E-posta ayarlarını yapın**
   
   PHP'nin `mail()` fonksiyonunun çalıştığından emin olun veya SMTP kullanın.

## 🔮 Gelecek Özellikler

- [ ] AI ile özel masal üretimi (OpenAI/Claude API)
- [ ] Sesli masal kütüphanesi
- [ ] Mobil uygulama
- [ ] Çoklu dil desteği
- [ ] Ebeveyn kontrol paneli

## 📝 Masal Şablonu

Temel masal şablonu `includes/story.php` dosyasında bulunur. Kişiselleştirilebilir alanlar:

- `#ISIM#` - Çocuğun adı
- `#SEHIR#` - Yaşadığı şehir
- `#COCUK#` - Cinsiyet (kız/oğlan)
- `#OZEL_ALAN#` - Günlük aktiviteler için alan

## 🔒 Güvenlik

- Şifreler Argon2ID ile hashlenir
- CSRF token koruması
- SQL injection koruması (PDO prepared statements)
- XSS koruması (htmlspecialchars)
- Hassas dosyalara erişim engellenir

## 📄 Lisans

MIT License - Detaylar için `LICENSE` dosyasına bakın.

## 👤 Geliştirici

**Hakkı Ayyıldız**  
Bilgisayar Mühendisi & Yazar

## 📧 İletişim

- E-posta: bilgi@100koyun.net
- Web: [www.100koyun.net/iletisim](https://www.100koyun.net/iletisim.php)

## 🙏 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add amazing feature'`)
4. Branch'i push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

---

💚 Sevgiyle hazırlandı - Her çocuğa tatlı uykular! 🐑

