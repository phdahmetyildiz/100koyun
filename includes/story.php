<?php
/**
 * 100 Koyun - Masal Yönetimi
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

require_once __DIR__ . '/../config/database.php';

class Story {
    private $db;
    
    // Varsayılan masal şablonu
    private $defaultStoryTemplate = "
Bir varmış, bir yokmuş,
evvel zaman içinde, kalbur saman içinde,
develer tellal iken, pireler berber iken,
çok uzak bir diyarda, çok yakın bir şehirde,
#SEHIR# tam göbeğinde,
#ISIM# adında bir #COCUK# yaşarmış.

Günlerden bir gün, #ISIM# anne ve babasıyla çok güzel bir gün geçirmiş.
#OZEL_ALAN#
Akşam olduğunda o kadar yorulmuş ki, koyunlarına yem veremeden uyuyakalmış.

Koyunlar da karınlarını doyurmak için tepedeki çimenliğe gitmeye karar vermişler. Ama oraya giden yolda bir çit varmış, onun üstünden atlamaları gerekiyormuş.

#KOYUN_ATLAMA#

#DEVAM#
";

    // Çocuk henüz uyumadıysa devam metni
    private $continuationTemplate = "
Karınlarını doyurunca koyunların da uykusu gelmiş.

#KOYUN_UYUMA#
";
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Koyun sayma metni oluştur
     */
    private function generateSheepCounting($action = 'atlamışşşş', $count = 100) {
        $text = "";
        for ($i = 1; $i <= $count; $i++) {
            $text .= "{$i} koyun {$action},\n";
        }
        return trim($text);
    }
    
    /**
     * Masalı kişiselleştir
     */
    public function personalizeStory($child = null, $includeContinuation = false) {
        $story = $this->defaultStoryTemplate;
        
        // Varsayılan değerler
        $name = "güzel çocuk";
        $city = "senin şehrinin";
        $childWord = "çocuk";
        $customArea = "(Burada bugün veya yakın zamanda çocuğunuzun yaşadığı şeylerden bahsedebilirsiniz)";
        
        // Çocuk bilgileri varsa kişiselleştir
        if ($child) {
            $name = htmlspecialchars($child['name']);
            
            if (!empty($child['city'])) {
                $city = htmlspecialchars($child['city']) . "'in";
            }
            
            $childWord = ($child['gender'] === 'kiz') ? 'kız' : 'oğlan';
        }
        
        // Yer tutucuları değiştir
        $story = str_replace('#ISIM#', $name, $story);
        $story = str_replace('#SEHIR#', $city, $story);
        $story = str_replace('#COCUK#', $childWord, $story);
        $story = str_replace('#OZEL_ALAN#', $customArea, $story);
        $story = str_replace('#KOYUN_ATLAMA#', $this->generateSheepCounting('atlamışşşş', 100), $story);
        
        // Devam kısmı
        if ($includeContinuation) {
            $continuation = str_replace('#KOYUN_UYUMA#', $this->generateSheepCounting('uyumuşşşş', 100), $this->continuationTemplate);
            $story = str_replace('#DEVAM#', $continuation, $story);
        } else {
            $story = str_replace('#DEVAM#', '', $story);
        }
        
        return trim($story);
    }
    
    /**
     * Masalı HTML formatında al (özelleştirilebilir alanlar renkli)
     */
    public function getStoryHTML($child = null, $includeContinuation = false) {
        // Varsayılan değerler
        $name = '<span class="personalized" data-field="name">güzel çocuk</span>';
        $city = '<span class="personalized" data-field="city">senin şehrinin</span>';
        $childWord = '<span class="personalized" data-field="gender">çocuk</span>';
        $customArea = '<span class="editable-area">(Burada bugün veya yakın zamanda çocuğunuzun yaşadığı şeylerden bahsedebilirsiniz)</span>';
        
        // Çocuk bilgileri varsa kişiselleştir
        if ($child) {
            $name = '<span class="personalized filled" data-field="name">' . htmlspecialchars($child['name']) . '</span>';
            
            if (!empty($child['city'])) {
                $city = '<span class="personalized filled" data-field="city">' . htmlspecialchars($child['city']) . "'in</span>";
            }
            
            $childWord = '<span class="personalized filled" data-field="gender">' . 
                        (($child['gender'] === 'kiz') ? 'kız' : 'oğlan') . '</span>';
        }
        
        $html = '
        <div class="story-section story-intro">
            <p>Bir varmış, bir yokmuş,</p>
            <p>evvel zaman içinde, kalbur saman içinde,</p>
            <p>develer tellal iken, pireler berber iken,</p>
            <p>çok uzak bir diyarda, çok yakın bir şehirde,</p>
            <p>' . $city . ' tam göbeğinde,</p>
            <p>' . $name . ' adında bir ' . $childWord . ' yaşarmış.</p>
        </div>
        
        <div class="story-section story-middle">
            <p>Günlerden bir gün, ' . $name . ' anne ve babasıyla çok güzel bir gün geçirmiş.</p>
            <p>' . $customArea . '</p>
            <p>Akşam olduğunda o kadar yorulmuş ki, koyunlarına yem veremeden uyuyakalmış.</p>
        </div>
        
        <div class="story-section story-transition">
            <p>Koyunlar da karınlarını doyurmak için tepedeki çimenliğe gitmeye karar vermişler. Ama oraya giden yolda bir çit varmış, onun üstünden atlamaları gerekiyormuş.</p>
        </div>
        
        <div class="story-section story-counting" id="sheep-jumping">
            ' . $this->getSheepCountingHTML('atlamışşşş') . '
        </div>';
        
        if ($includeContinuation) {
            $html .= '
            <div class="story-section story-continuation">
                <p class="continuation-note">(Çocuğunuz henüz uyumadıysa devam)</p>
                <p>Karınlarını doyurunca koyunların da uykusu gelmiş.</p>
            </div>
            
            <div class="story-section story-counting" id="sheep-sleeping">
                ' . $this->getSheepCountingHTML('uyumuşşşş') . '
            </div>';
        }
        
        return $html;
    }
    
    /**
     * Koyun sayma HTML'i
     */
    private function getSheepCountingHTML($action) {
        $html = '<div class="sheep-counter">';
        for ($i = 1; $i <= 100; $i++) {
            $html .= '<p class="sheep-line" data-number="' . $i . '">';
            $html .= '<span class="sheep-number">' . $i . '</span> ';
            $html .= '<span class="sheep-icon">🐑</span> ';
            $html .= 'koyun ' . $action;
            $html .= '</p>';
        }
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Masalı TTS için düz metin olarak al
     */
    public function getStoryForTTS($child = null, $includeContinuation = false) {
        return $this->personalizeStory($child, $includeContinuation);
    }
    
    /**
     * Gelecek için: AI ile masal üret
     * Bu fonksiyon şu an aktif değil, altyapı hazır
     */
    public function generateAIStory($child, $theme = null) {
        // TODO: AI API entegrasyonu
        // OpenAI, Claude veya başka bir API kullanılabilir
        
        /*
        $prompt = "Bir çocuk masalı yaz. Çocuğun adı: {$child['name']}, 
                   yaşadığı şehir: {$child['city']}. 
                   Masal sonunda 100 koyunun çit üzerinden atlaması ile bitsin.";
        
        // API çağrısı yapılacak
        $response = $this->callAIAPI($prompt);
        
        // Sonucu veritabanına kaydet
        $stmt = $this->db->prepare("
            INSERT INTO stories (title, content, story_date, is_ai_generated) 
            VALUES (?, ?, date('now'), 1)
        ");
        $stmt->execute(['AI Masal - ' . date('d.m.Y'), $response]);
        
        return $response;
        */
        
        return null; // Henüz aktif değil
    }
    
    /**
     * Günün masalını getir (varsa veritabanından, yoksa varsayılan)
     */
    public function getTodaysStory() {
        $stmt = $this->db->prepare("
            SELECT * FROM stories WHERE story_date = date('now') LIMIT 1
        ");
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Masal kaydet (admin için)
     */
    public function saveStory($title, $content, $date = null) {
        $date = $date ?? date('Y-m-d');
        
        try {
            $stmt = $this->db->prepare("
                INSERT OR REPLACE INTO stories (title, content, story_date) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$title, $content, $date]);
            
            return ['success' => true, 'message' => 'Masal kaydedildi.'];
            
        } catch (Exception $e) {
            error_log("Masal kaydetme hatası: " . $e->getMessage());
            return ['success' => false, 'message' => 'Bir hata oluştu.'];
        }
    }
}

