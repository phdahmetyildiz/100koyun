<?php
/**
 * 100 Koyun - Masal Yönetimi
 * 
 * Clarifai entegrasyonu ile her gün yeni bir masal orta kısmı üretir.
 * Giriş ve 100 koyunun çitten atlama kısmı sabit kalır, orta kısım AI ile gelir.
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

require_once __DIR__ . '/../config/database.php';

class Story {
    private $db;

    // Varsayılan masal şablonu (tam metin, orta kısım dinamik doldurulur)
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

    // Clarifai ayarları
    private $clarifaiPat;
    private $clarifaiModelId;
    
    public function __construct() {
        $this->db = getDB();

        // Clarifai Personal Access Token ve model ID'yi ortam değişkenlerinden oku
        // Sunucuda ayarlamanız gerekir:
        //   CLARIFAI_PAT       -> Clarifai Personal Access Token
        //   CLARIFAI_MODEL_ID  -> Metin üreten model ID'si
        $this->clarifaiPat = getenv('CLARIFAI_PAT') ?: null;
        $this->clarifaiModelId = getenv('CLARIFAI_MODEL_ID') ?: null;
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
     * Bugünün AI orta kısmını getir (yoksa üret ve kaydet).
     * Dönen metin düz metindir; içinde şu placeholder'lar olabilir:
     *   {{CHILD_NAME}}, {{CITY_NAME}}
     */
    public function getOrCreateTodayMiddleSection(): string {
        $today = date('Y-m-d');

        // Önce veritabanından dene
        $stmt = $this->db->prepare("
            SELECT content FROM stories 
            WHERE story_date = ? AND is_ai_generated = 1
            LIMIT 1
        ");
        $stmt->execute([$today]);
        $row = $stmt->fetch();

        if ($row && !empty($row['content'])) {
            return $row['content'];
        }

        // Yoksa Clarifai ile üret
        $generated = $this->generateTodaysAIStoryMiddle();
        if ($generated) {
            return $generated;
        }

        // Clarifai başarısız olursa fallback statik metin
        return "(Burada bugün veya yakın zamanda çocuğunuzun yaşadığı şeylerden bahsedebilirsiniz)";
    }

    /**
     * Clarifai ile bugünün orta kısmını üret ve veritabanına kaydet.
     * Cron job bu fonksiyonu çağıracak.
     */
    public function generateTodaysAIStoryMiddle(): ?string {
        $today = date('Y-m-d');

        // Clarifai ayarları yoksa hiç deneme
        if (!$this->clarifaiPat || !$this->clarifaiModelId) {
            error_log('Clarifai ayarları bulunamadı (CLARIFAI_PAT veya CLARIFAI_MODEL_ID).');
            return null;
        }

        $todayHuman = date('d.m.Y');

        $prompt = "
Sen bir çocuk masalı yazarı ve editörüsün.

Görevin, 3-6 yaş arası çocuklar için Türkçe, çok sakin ve pozitif bir masalın SADECE ORTA KISMINI yazmak.
Masalın başında klasik giriş (\"Bir varmış, bir yokmuş\" vb.) ve sonunda 100 koyunun çitten atlaması zaten sistemde var.

Senin üreteceğin kısım:
- 1 veya 2 paragraf uzunluğunda olsun.
- Küçük çocuklar için güvenli, korkutucu veya üzücü hiçbir öğe içermesin.
- Temalar: arkadaşlık, oyun, birlikte yemek yeme, paylaşma, yardımseverlik gibi nötr ve pozitif konular olsun.
- Metnin içinde ÇOCUĞUN ADI ve YAŞADIĞI ŞEHRİ yerleştirmek için şu yer tutucuları kullan:
    - Çocuğun adı için: {{CHILD_NAME}}
    - Şehir için: {{CITY_NAME}}
- Örnek: \"{{CITY_NAME}} şehrinde yaşayan {{CHILD_NAME}} o gün arkadaşlarıyla parka gitmişti.\" gibi.
- Dil: Sade, akıcı, kısa cümleler, 3-6 yaş seviyesi.
- Tarih bilgisi: Bugün {$todayHuman}. Dilersen bu günü mevsim, hava durumu gibi detaylarla hissettirebilirsin ama tarih rakamlarını yazmak zorunda değilsin.

ÇIKTI SADECE MASAL METNİ OLSUN.
Başlık, madde işaretleri, alıntı işaretleri, açıklama vb. ekleme. Sadece temiz masal metnini ver.
";

        $aiText = $this->callClarifaiTextGenerationApi($prompt);
        if (!$aiText) {
            return null;
        }

        $aiText = trim($aiText);

        // Aynı güne ait kayıt var mı tekrar kontrol et
        $stmt = $this->db->prepare("
            SELECT id FROM stories WHERE story_date = ? AND is_ai_generated = 1 LIMIT 1
        ");
        $stmt->execute([$today]);
        $row = $stmt->fetch();

        $title = 'Günün Masalı Orta Kısmı - ' . $todayHuman;

        if ($row) {
            $stmt = $this->db->prepare("
                UPDATE stories 
                SET content = ?, title = ?, created_at = created_at
                WHERE id = ?
            ");
            $stmt->execute([$aiText, $title, $row['id']]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO stories (title, content, story_date, is_ai_generated) 
                VALUES (?, ?, ?, 1)
            ");
            $stmt->execute([$title, $aiText, $today]);
        }

        return $aiText;
    }

    /**
     * Clarifai Text Generation API çağrısı.
     * Kullandığınız Clarifai modeline göre response path'ini uyarlamanız gerekebilir.
     */
    private function callClarifaiTextGenerationApi(string $prompt): ?string {
        $modelId = $this->clarifaiModelId;
        $pat = $this->clarifaiPat;

        $url = "https://api.clarifai.com/v2/models/{$modelId}/outputs";

        $body = [
            'inputs' => [
                [
                    'data' => [
                        'text' => [
                            'raw' => $prompt
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Key ' . $pat,
            ],
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            error_log('Clarifai API hatası (curl): ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('Clarifai API HTTP hatası: ' . $httpCode . ' - ' . $response);
            return null;
        }

        $data = json_decode($response, true);

        // Bu path, Clarifai'de kullandığınız text modeli için örnek bir path'tir.
        // Gerekirse kendi modelinizin döndürdüğü JSON'a göre uyarlayın.
        if (!isset($data['outputs'][0]['data']['text']['raw'])) {
            error_log('Clarifai API yanıt formatı beklenenden farklı: ' . $response);
            return null;
        }

        return $data['outputs'][0]['data']['text']['raw'];
    }
    
    /**
     * Masalı kişiselleştir (TTS için düz metin)
     */
    public function personalizeStory($child = null, $includeContinuation = false) {
        $story = $this->defaultStoryTemplate;
        
        // Varsayılan değerler
        $name = "güzel çocuk";
        $city = "senin şehrinin";
        $childWord = "çocuk";
        
        // Çocuk bilgileri varsa kişiselleştir
        if ($child) {
            $name = $child['name'] ?? $name;
            
            if (!empty($child['city'])) {
                $city = $child['city'] . "'in";
            }
            
            $childWord = ($child['gender'] ?? '') === 'kiz' ? 'kız' : 'oğlan';
        }

        // Orta kısmı AI'den (veya fallback'ten) al
        $middle = $this->getOrCreateTodayMiddleSection();

        // Placeholder'ları doldur
        $middleText = str_replace(
            ['{{CHILD_NAME}}', '{{CITY_NAME}}'],
            [$name, $city],
            $middle
        );
        
        // Yer tutucuları değiştir
        $story = str_replace('#ISIM#', $name, $story);
        $story = str_replace('#SEHIR#', $city, $story);
        $story = str_replace('#COCUK#', $childWord, $story);
        $story = str_replace('#OZEL_ALAN#', $middleText, $story);
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
        // Varsayılan değerler (HTML)
        $nameSpan = '<span class="personalized" data-field="name">güzel çocuk</span>';
        $citySpan = '<span class="personalized" data-field="city">senin şehrinin</span>';
        $childWordSpan = '<span class="personalized" data-field="gender">çocuk</span>';
        
        // Çocuk bilgileri varsa kişiselleştir
        if ($child) {
            $nameSpan = '<span class="personalized filled" data-field="name">' . htmlspecialchars($child['name']) . '</span>';
            
            if (!empty($child['city'])) {
                $citySpan = '<span class="personalized filled" data-field="city">' . htmlspecialchars($child['city']) . "'in</span>";
            }
            
            $childWordSpan = '<span class="personalized filled" data-field="gender">' . 
                        (($child['gender'] === 'kiz') ? 'kız' : 'oğlan') . '</span>';
        }

        // Orta kısmı al (AI + fallback)
        $middle = $this->getOrCreateTodayMiddleSection();

        // Placeholder'lar için token kullan, sonra escape et, sonra token'ları span'lerle değiştir
        $tokenChild = '__CHILD_NAME_TOKEN__';
        $tokenCity = '__CITY_NAME_TOKEN__';

        $middleWithTokens = str_replace(
            ['{{CHILD_NAME}}', '{{CITY_NAME}}'],
            [$tokenChild, $tokenCity],
            $middle
        );

        $middleEscaped = htmlspecialchars($middleWithTokens, ENT_QUOTES, 'UTF-8');
        $middleEscaped = nl2br($middleEscaped);

        $middleHtml = str_replace(
            [$tokenChild, $tokenCity],
            [$nameSpan, $citySpan],
            $middleEscaped
        );
        
        $html = '
        <div class="story-section story-intro">
            <p>Bir varmış, bir yokmuş,</p>
            <p>evvel zaman içinde, kalbur saman içinde,</p>
            <p>develer tellal iken, pireler berber iken,</p>
            <p>çok uzak bir diyarda, çok yakın bir şehirde,</p>
            <p>' . $citySpan . ' tam göbeğinde,</p>
            <p>' . $nameSpan . ' adında bir ' . $childWordSpan . ' yaşarmış.</p>
        </div>
        
        <div class="story-section story-middle">
            ' . $middleHtml . '
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
     * Günün masalını getir (varsa veritabanından, yoksa varsayılan)
     * Not: Şu an stories tablosunda sadece orta kısmı AI ile tutuyoruz.
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

