/**
 * 100 Koyun - Ana JavaScript Dosyası
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobil menü toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('open');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });
    }
    
    // Çocuk seçici dropdown
    const childSelectorBtn = document.getElementById('childSelectorBtn');
    const childDropdown = document.getElementById('childDropdown');
    
    if (childSelectorBtn && childDropdown) {
        childSelectorBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            childDropdown.classList.toggle('open');
        });
    }
    
    // Kullanıcı menüsü dropdown
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
        });
    }
    
    // Dışarı tıklayınca dropdownları kapat
    document.addEventListener('click', function() {
        document.querySelectorAll('.child-dropdown, .user-dropdown').forEach(dropdown => {
            dropdown.classList.remove('open');
        });
    });
    
    // Arka plan animasyonları
    initBackgroundAnimations();
    
    // Gece/gündüz modu kontrolü
    updateDayNightMode();
});

/**
 * Arka plan animasyonlarını başlat
 */
function initBackgroundAnimations() {
    const sky = document.querySelector('.sky');
    const stars = document.querySelector('.stars');
    
    if (!sky || !stars) return;
    
    // Yıldızları oluştur
    for (let i = 0; i < 50; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 60 + '%';
        star.style.animationDelay = Math.random() * 3 + 's';
        star.style.animationDuration = (2 + Math.random() * 2) + 's';
        stars.appendChild(star);
    }
}

/**
 * Gece/gündüz modunu güncelle
 */
function updateDayNightMode() {
    const hour = new Date().getHours();
    const body = document.body;
    
    // Akşam 7'den sonra ve sabah 7'den önce gece modu
    if (hour >= 19 || hour < 7) {
        body.classList.add('night-mode');
    } else {
        body.classList.remove('night-mode');
    }
}

/**
 * Smooth scroll
 */
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Alert göster
 */
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} floating-alert`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        ${message}
    `;
    
    document.body.appendChild(alertDiv);
    
    // Animasyon
    setTimeout(() => alertDiv.classList.add('show'), 10);
    
    // 3 saniye sonra kaldır
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

/**
 * Loading overlay göster/gizle
 */
function showLoading(show = true) {
    let overlay = document.querySelector('.loading-overlay');
    
    if (show) {
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-spinner">
                    <div class="sheep-loader">🐑</div>
                    <p>Yükleniyor...</p>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.classList.add('show');
    } else if (overlay) {
        overlay.classList.remove('show');
    }
}

/**
 * Tarih formatla
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('tr-TR', options);
}

/**
 * Sayıyı Türkçe yazıya çevir
 */
function numberToTurkish(num) {
    const ones = ['', 'bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'];
    const tens = ['', 'on', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'];
    
    if (num === 0) return 'sıfır';
    if (num === 100) return 'yüz';
    
    let result = '';
    
    if (num >= 100) {
        result += 'yüz ';
        num %= 100;
    }
    
    if (num >= 10) {
        result += tens[Math.floor(num / 10)] + ' ';
        num %= 10;
    }
    
    if (num > 0) {
        result += ones[num];
    }
    
    return result.trim();
}

