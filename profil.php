<?php
/**
 * 100 Koyun - Profil ve Çocuk Yönetimi
 * 
 * @author Auto (Cursor AI)
 * @programmed-by Auto (Cursor AI)
 */

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/children.php';

// Giriş kontrolü
if (!Auth::isLoggedIn()) {
    header('Location: /giris.php?redirect=/profil.php');
    exit;
}

$childrenManager = new Children();
$userId = $_SESSION['user_id'];
$children = $childrenManager->getChildren($userId);

$error = '';
$success = '';

// Çocuk ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Güvenlik doğrulaması başarısız.';
    } else {
        switch ($_POST['action']) {
            case 'add_child':
                $result = $childrenManager->addChild(
                    $userId,
                    $_POST['name'] ?? '',
                    $_POST['gender'] ?? '',
                    $_POST['city'] ?? null,
                    $_POST['birth_date'] ?? null
                );
                if ($result['success']) {
                    $success = $result['message'];
                    $children = $childrenManager->getChildren($userId);
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'update_child':
                $result = $childrenManager->updateChild(
                    $_POST['child_id'],
                    $userId,
                    [
                        'name' => $_POST['name'],
                        'gender' => $_POST['gender'],
                        'city' => $_POST['city'],
                        'birth_date' => $_POST['birth_date']
                    ]
                );
                if ($result['success']) {
                    $success = $result['message'];
                    $children = $childrenManager->getChildren($userId);
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete_child':
                $result = $childrenManager->deleteChild($_POST['child_id'], $userId);
                if ($result['success']) {
                    $success = $result['message'];
                    $children = $childrenManager->getChildren($userId);
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

$pageTitle = 'Çocuklarım';
include __DIR__ . '/includes/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <h1><i class="fas fa-child"></i> Çocuklarım</h1>
        <p>Masalları kişiselleştirmek için çocuklarınızın bilgilerini yönetin</p>
    </div>
    
    <?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>
    
    <!-- Çocuk Listesi -->
    <div class="children-list">
        <?php if (empty($children)): ?>
        <div class="empty-state">
            <span class="empty-icon">👶</span>
            <h3>Henüz çocuk eklenmemiş</h3>
            <p>İlk çocuğunuzu ekleyerek masalları kişiselleştirin!</p>
        </div>
        <?php else: ?>
        <?php foreach ($children as $child): ?>
        <div class="child-card <?= $child['is_active'] ? 'active' : '' ?>">
            <div class="child-card-header">
                <div class="child-avatar-large">
                    <?= $child['gender'] === 'kiz' ? '👧' : '👦' ?>
                </div>
                <div class="child-info">
                    <h3><?= htmlspecialchars($child['name']) ?></h3>
                    <p>
                        <?= $child['gender'] === 'kiz' ? 'Kız' : 'Erkek' ?>
                        <?php if ($child['city']): ?>
                        • <?= htmlspecialchars($child['city']) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($child['is_active']): ?>
                <span class="active-badge">
                    <i class="fas fa-star"></i> Aktif
                </span>
                <?php endif; ?>
            </div>
            
            <div class="child-card-actions">
                <?php if (!$child['is_active']): ?>
                <a href="/api/set-active-child.php?id=<?= $child['id'] ?>&redirect=/profil.php" 
                   class="btn btn-primary btn-small">
                    <i class="fas fa-star"></i> Aktif Yap
                </a>
                <?php endif; ?>
                
                <button class="btn btn-outline btn-small" 
                        onclick="openEditModal(<?= htmlspecialchars(json_encode($child)) ?>)">
                    <i class="fas fa-edit"></i> Düzenle
                </button>
                
                <button class="btn btn-danger btn-small" 
                        onclick="confirmDelete(<?= $child['id'] ?>, '<?= htmlspecialchars($child['name']) ?>')">
                    <i class="fas fa-trash"></i> Sil
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Yeni Çocuk Ekle -->
    <div class="add-child-section">
        <button class="btn btn-primary btn-large" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Yeni Çocuk Ekle
        </button>
    </div>
</div>

<!-- Çocuk Ekleme/Düzenleme Modal -->
<div class="modal" id="childModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Çocuk Ekle</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" class="modal-form" id="childForm">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="action" id="formAction" value="add_child">
            <input type="hidden" name="child_id" id="childId" value="">
            
            <div class="form-group">
                <label for="childName">
                    <i class="fas fa-user"></i> Çocuğun Adı *
                </label>
                <input type="text" id="childName" name="name" required 
                       placeholder="Örn: Ali, Ayşe">
            </div>
            
            <div class="form-group">
                <label>
                    <i class="fas fa-venus-mars"></i> Cinsiyet *
                </label>
                <div class="gender-select">
                    <label class="gender-option">
                        <input type="radio" name="gender" value="erkek" required>
                        <span class="gender-box">
                            <span class="gender-icon">👦</span>
                            <span>Erkek</span>
                        </span>
                    </label>
                    <label class="gender-option">
                        <input type="radio" name="gender" value="kiz" required>
                        <span class="gender-box">
                            <span class="gender-icon">👧</span>
                            <span>Kız</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="childCity">
                    <i class="fas fa-city"></i> Yaşadığı Şehir
                </label>
                <input type="text" id="childCity" name="city" 
                       placeholder="Örn: İstanbul, Ankara">
                <small>Masalda şehir ismi kullanılır</small>
            </div>
            
            <div class="form-group">
                <label for="childBirthDate">
                    <i class="fas fa-birthday-cake"></i> Doğum Tarihi
                </label>
                <input type="date" id="childBirthDate" name="birth_date">
                <small>İsteğe bağlı</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal()">İptal</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Silme Onay Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2>Çocuk Profilini Sil</h2>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p class="delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong id="deleteChildName"></strong> adlı çocuğun profilini silmek istediğinizden emin misiniz?
            </p>
            <p>Bu işlem geri alınamaz.</p>
        </div>
        <form method="POST" class="modal-actions">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="action" value="delete_child">
            <input type="hidden" name="child_id" id="deleteChildId" value="">
            
            <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">İptal</button>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Sil
            </button>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Çocuk Ekle';
    document.getElementById('formAction').value = 'add_child';
    document.getElementById('childId').value = '';
    document.getElementById('childForm').reset();
    document.getElementById('childModal').classList.add('open');
}

function openEditModal(child) {
    document.getElementById('modalTitle').textContent = 'Çocuk Düzenle';
    document.getElementById('formAction').value = 'update_child';
    document.getElementById('childId').value = child.id;
    document.getElementById('childName').value = child.name;
    document.getElementById('childCity').value = child.city || '';
    document.getElementById('childBirthDate').value = child.birth_date || '';
    
    // Cinsiyet seçimi
    document.querySelector(`input[name="gender"][value="${child.gender}"]`).checked = true;
    
    document.getElementById('childModal').classList.add('open');
}

function closeModal() {
    document.getElementById('childModal').classList.remove('open');
}

function confirmDelete(childId, childName) {
    document.getElementById('deleteChildId').value = childId;
    document.getElementById('deleteChildName').textContent = childName;
    document.getElementById('deleteModal').classList.add('open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('open');
}

// Modal dışına tıklayınca kapat
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('open');
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

