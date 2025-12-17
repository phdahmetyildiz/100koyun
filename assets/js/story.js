/**
 * 100 Koyun - Masal ve TTS JavaScript
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

let speechSynthesis = window.speechSynthesis;
let currentUtterance = null;
let isPaused = false;
let isReading = false;
let continuationVisible = false;

/**
 * Masalı sesli oku (TTS)
 */
function readStory() {
    if (!speechSynthesis) {
        showAlert('Tarayıcınız sesli okuma özelliğini desteklemiyor.', 'error');
        return;
    }
    
    if (isReading && isPaused) {
        // Devam et
        speechSynthesis.resume();
        isPaused = false;
        updateReadingUI(true);
        return;
    }
    
    // Mevcut okumayı durdur
    speechSynthesis.cancel();
    
    // Masal metnini al
    const storyContent = document.getElementById('storyContent');
    if (!storyContent) return;
    
    // HTML'den düz metin çıkar
    let text = storyContent.innerText;
    
    // Gereksiz boşlukları temizle
    text = text.replace(/\s+/g, ' ').trim();
    
    // Türkçe ses bul
    const voices = speechSynthesis.getVoices();
    let turkishVoice = voices.find(voice => voice.lang.startsWith('tr'));
    
    // Utterance oluştur
    currentUtterance = new SpeechSynthesisUtterance(text);
    
    if (turkishVoice) {
        currentUtterance.voice = turkishVoice;
    }
    
    currentUtterance.lang = 'tr-TR';
    currentUtterance.rate = 0.85; // Biraz yavaş, çocuklar için
    currentUtterance.pitch = 1.1; // Biraz tiz, daha samimi
    
    // Event listeners
    currentUtterance.onstart = function() {
        isReading = true;
        updateReadingUI(true);
        highlightCurrentSection(0);
    };
    
    currentUtterance.onend = function() {
        isReading = false;
        isPaused = false;
        updateReadingUI(false);
        clearHighlights();
    };
    
    currentUtterance.onerror = function(event) {
        console.error('TTS Hatası:', event);
        isReading = false;
        isPaused = false;
        updateReadingUI(false);
        
        if (event.error !== 'canceled') {
            showAlert('Sesli okuma sırasında bir hata oluştu.', 'error');
        }
    };
    
    // Okumayı başlat
    speechSynthesis.speak(currentUtterance);
}

/**
 * Okumayı duraklat
 */
function pauseReading() {
    if (speechSynthesis && isReading) {
        speechSynthesis.pause();
        isPaused = true;
        
        document.getElementById('pauseBtn').innerHTML = '<i class="fas fa-play"></i><span>Devam Et</span>';
        document.getElementById('pauseBtn').onclick = resumeReading;
    }
}

/**
 * Okumaya devam et
 */
function resumeReading() {
    if (speechSynthesis && isPaused) {
        speechSynthesis.resume();
        isPaused = false;
        
        document.getElementById('pauseBtn').innerHTML = '<i class="fas fa-pause"></i><span>Duraklat</span>';
        document.getElementById('pauseBtn').onclick = pauseReading;
    }
}

/**
 * Okumayı durdur
 */
function stopReading() {
    if (speechSynthesis) {
        speechSynthesis.cancel();
        isReading = false;
        isPaused = false;
        updateReadingUI(false);
        clearHighlights();
    }
}

/**
 * Okuma UI'ını güncelle
 */
function updateReadingUI(reading) {
    const readBtn = document.getElementById('readStoryBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const stopBtn = document.getElementById('stopBtn');
    
    if (reading) {
        readBtn.style.display = 'none';
        pauseBtn.style.display = 'inline-flex';
        stopBtn.style.display = 'inline-flex';
        pauseBtn.innerHTML = '<i class="fas fa-pause"></i><span>Duraklat</span>';
        pauseBtn.onclick = pauseReading;
    } else {
        readBtn.style.display = 'inline-flex';
        pauseBtn.style.display = 'none';
        stopBtn.style.display = 'none';
    }
}

/**
 * Mevcut bölümü vurgula
 */
function highlightCurrentSection(index) {
    const sections = document.querySelectorAll('.story-section');
    sections.forEach((section, i) => {
        section.classList.toggle('reading', i === index);
    });
}

/**
 * Vurguları temizle
 */
function clearHighlights() {
    document.querySelectorAll('.story-section').forEach(section => {
        section.classList.remove('reading');
    });
}

/**
 * Devam kısmını göster/gizle
 */
function toggleContinuation() {
    const jumpingSection = document.getElementById('sheep-jumping');
    const sleepingSection = document.getElementById('sheep-sleeping');
    const continuationNote = document.querySelector('.continuation-note');
    const continuationText = document.querySelector('.story-continuation p:not(.continuation-note)');
    const toggleText = document.getElementById('continuationToggleText');
    
    continuationVisible = !continuationVisible;
    
    if (sleepingSection) {
        sleepingSection.style.display = continuationVisible ? 'block' : 'none';
    }
    
    if (continuationNote) {
        continuationNote.style.display = continuationVisible ? 'block' : 'none';
    }
    
    if (continuationText) {
        continuationText.style.display = continuationVisible ? 'block' : 'none';
    }
    
    if (toggleText) {
        toggleText.textContent = continuationVisible ? 'Devamını Gizle' : 'Devamını Göster';
    }
}

/**
 * Paylaş modalı aç
 */
function shareStory() {
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.classList.add('open');
    }
}

/**
 * Paylaş modalı kapat
 */
function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.classList.remove('open');
    }
}

/**
 * Paylaşım linkini kopyala
 */
function copyShareLink() {
    const urlInput = document.getElementById('shareUrl');
    if (urlInput) {
        urlInput.select();
        document.execCommand('copy');
        showAlert('Link kopyalandı!', 'success');
    }
}

/**
 * Koyun animasyonunu başlat
 */
function animateSheep(number) {
    const sheep = document.getElementById('animatedSheep');
    const animation = document.getElementById('sheepAnimation');
    
    if (!sheep || !animation) return;
    
    animation.classList.add('active');
    sheep.classList.add('jumping');
    
    setTimeout(() => {
        sheep.classList.remove('jumping');
    }, 1000);
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Ses sentezi hazır olduğunda sesleri al
    if (speechSynthesis) {
        speechSynthesis.onvoiceschanged = function() {
            // Sesler yüklendi
        };
    }
    
    // Devam kısmını varsayılan olarak gizle
    const sleepingSection = document.getElementById('sheep-sleeping');
    const continuationNote = document.querySelector('.continuation-note');
    const continuationText = document.querySelector('.story-continuation p:not(.continuation-note)');
    
    if (sleepingSection) sleepingSection.style.display = 'none';
    if (continuationNote) continuationNote.style.display = 'none';
    if (continuationText) continuationText.style.display = 'none';
    
    // Modal dışına tıklayınca kapat
    const shareModal = document.getElementById('shareModal');
    if (shareModal) {
        shareModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeShareModal();
            }
        });
    }
    
    // Koyun sayma satırlarına hover efekti
    document.querySelectorAll('.sheep-line').forEach((line, index) => {
        line.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(10px)';
        });
        line.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});

// Sayfa kapatılırken okumayı durdur
window.addEventListener('beforeunload', function() {
    if (speechSynthesis) {
        speechSynthesis.cancel();
    }
});

