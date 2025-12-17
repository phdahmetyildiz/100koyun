<?php
/**
 * 100 Koyun - Çocuk Profil Yönetimi
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

require_once __DIR__ . '/../config/database.php';

class Children {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Çocuk ekle
     */
    public function addChild($userId, $name, $gender, $city = null, $birthDate = null) {
        $name = sanitizeInput($name);
        $city = $city ? sanitizeInput($city) : null;
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Çocuk ismi gereklidir.'];
        }
        
        if (!in_array($gender, ['erkek', 'kiz'])) {
            return ['success' => false, 'message' => 'Geçersiz cinsiyet seçimi.'];
        }
        
        try {
            // Mevcut aktif çocuğu pasif yap
            $stmt = $this->db->prepare("UPDATE children SET is_active = 0 WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Yeni çocuğu ekle ve aktif yap
            $stmt = $this->db->prepare("
                INSERT INTO children (user_id, name, gender, city, birth_date, is_active) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$userId, $name, $gender, $city, $birthDate]);
            
            $childId = $this->db->lastInsertId();
            $_SESSION['active_child_id'] = $childId;
            
            return [
                'success' => true, 
                'message' => 'Çocuk profili eklendi!',
                'child_id' => $childId
            ];
            
        } catch (Exception $e) {
            error_log("Çocuk ekleme hatası: " . $e->getMessage());
            return ['success' => false, 'message' => 'Bir hata oluştu.'];
        }
    }
    
    /**
     * Çocuk güncelle
     */
    public function updateChild($childId, $userId, $data) {
        // Çocuğun bu kullanıcıya ait olduğunu doğrula
        $stmt = $this->db->prepare("SELECT id FROM children WHERE id = ? AND user_id = ?");
        $stmt->execute([$childId, $userId]);
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Çocuk bulunamadı.'];
        }
        
        $updates = [];
        $params = [];
        
        if (isset($data['name'])) {
            $updates[] = "name = ?";
            $params[] = sanitizeInput($data['name']);
        }
        
        if (isset($data['gender']) && in_array($data['gender'], ['erkek', 'kiz'])) {
            $updates[] = "gender = ?";
            $params[] = $data['gender'];
        }
        
        if (isset($data['city'])) {
            $updates[] = "city = ?";
            $params[] = sanitizeInput($data['city']);
        }
        
        if (isset($data['birth_date'])) {
            $updates[] = "birth_date = ?";
            $params[] = $data['birth_date'];
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'Güncellenecek veri yok.'];
        }
        
        $updates[] = "updated_at = datetime('now')";
        $params[] = $childId;
        
        $sql = "UPDATE children SET " . implode(", ", $updates) . " WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Çocuk profili güncellendi!'];
            
        } catch (Exception $e) {
            error_log("Çocuk güncelleme hatası: " . $e->getMessage());
            return ['success' => false, 'message' => 'Bir hata oluştu.'];
        }
    }
    
    /**
     * Çocuk sil
     */
    public function deleteChild($childId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM children WHERE id = ? AND user_id = ?");
        $stmt->execute([$childId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            // Silinen çocuk aktifse, başka bir çocuğu aktif yap
            if (isset($_SESSION['active_child_id']) && $_SESSION['active_child_id'] == $childId) {
                $stmt = $this->db->prepare("SELECT id FROM children WHERE user_id = ? LIMIT 1");
                $stmt->execute([$userId]);
                $newActive = $stmt->fetch();
                
                if ($newActive) {
                    $this->setActiveChild($newActive['id'], $userId);
                } else {
                    unset($_SESSION['active_child_id']);
                }
            }
            
            return ['success' => true, 'message' => 'Çocuk profili silindi.'];
        }
        
        return ['success' => false, 'message' => 'Çocuk bulunamadı.'];
    }
    
    /**
     * Kullanıcının çocuklarını getir
     */
    public function getChildren($userId) {
        $stmt = $this->db->prepare("
            SELECT id, name, gender, city, birth_date, is_active, created_at 
            FROM children 
            WHERE user_id = ? 
            ORDER BY is_active DESC, name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Tek bir çocuğu getir
     */
    public function getChild($childId, $userId = null) {
        $sql = "SELECT * FROM children WHERE id = ?";
        $params = [$childId];
        
        if ($userId !== null) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Aktif çocuğu getir
     */
    public function getActiveChild($userId) {
        // Session'dan aktif çocuk ID'si varsa onu kullan
        if (isset($_SESSION['active_child_id'])) {
            $child = $this->getChild($_SESSION['active_child_id'], $userId);
            if ($child) {
                return $child;
            }
        }
        
        // Yoksa veritabanından aktif çocuğu al
        $stmt = $this->db->prepare("
            SELECT * FROM children WHERE user_id = ? AND is_active = 1 LIMIT 1
        ");
        $stmt->execute([$userId]);
        $child = $stmt->fetch();
        
        if ($child) {
            $_SESSION['active_child_id'] = $child['id'];
        }
        
        return $child;
    }
    
    /**
     * Aktif çocuğu değiştir
     */
    public function setActiveChild($childId, $userId) {
        // Önce çocuğun bu kullanıcıya ait olduğunu doğrula
        $stmt = $this->db->prepare("SELECT id FROM children WHERE id = ? AND user_id = ?");
        $stmt->execute([$childId, $userId]);
        
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Çocuk bulunamadı.'];
        }
        
        // Tüm çocukları pasif yap
        $stmt = $this->db->prepare("UPDATE children SET is_active = 0 WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Seçili çocuğu aktif yap
        $stmt = $this->db->prepare("UPDATE children SET is_active = 1 WHERE id = ?");
        $stmt->execute([$childId]);
        
        $_SESSION['active_child_id'] = $childId;
        
        return ['success' => true, 'message' => 'Aktif çocuk değiştirildi.'];
    }
}

