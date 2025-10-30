<?php
session_start();
require '../db/database.php';


// Cek login & admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================================================
// LOGIKA DENGAN SEARCH & PAGINATION
// ==========================================================
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 1. Ambil dan sanitasi input pencarian
$search = trim($_GET['search'] ?? '');
$search_param = '%' . $search . '%';

// 2. Buat klausa WHERE kondisional menggunakan parameter bernama
$where_clause = '';
$where_params = []; // Digunakan untuk COUNT dan SELECT
$search_url_param = ''; // Untuk ditambahkan ke URL pagination/delete

if (!empty($search)) {
    // Pastikan menggunakan parameter bernama yang unik!
    $where_clause = "WHERE type LIKE :search_type OR room_number LIKE :search_room";
    $where_params = [
        ':search_type' => $search_param,
        ':search_room' => $search_param
    ];
    $search_url_param = "&search=" . urlencode($search);
}

// Hitung total data (DENGAN klausa WHERE jika ada pencarian)
$totalStmt_sql = "SELECT COUNT(*) FROM rooms " . $where_clause;
$totalStmt = $pdo->prepare($totalStmt_sql);
// Eksekusi menggunakan array parameter bernama (hanya WHERE)
$totalStmt->execute($where_params); 
$total = $totalStmt->fetchColumn();
$pages = ceil($total / $limit);

// Pastikan page tidak lebih dari $pages jika ada hasil, atau minimal 1
if ($pages > 0 && $page > $pages) {
    $page = $pages;
    $offset = ($page - 1) * $limit;
}

// Ambil data kamar (DENGAN klausa WHERE jika ada pencarian)
$sql = "SELECT * FROM rooms " . $where_clause . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

// Bind semua parameter (WHERE, LIMIT, OFFSET)
// Bind parameter WHERE
foreach ($where_params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

// Bind parameter LIMIT dan OFFSET (Wajib menggunakan bindValue/bindParam karena bernama)
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute(); // Eksekusi tanpa argumen karena semua sudah dibind
$rooms = $stmt->fetchAll();

// Hapus
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Asumsi: Hapus file gambar terkait juga
    $stmt_img = $pdo->prepare("SELECT image FROM rooms WHERE id = ?");
    $stmt_img->execute([$id]);
    $image_path = $stmt_img->fetchColumn();
    if ($image_path && file_exists('../' . $image_path) && $image_path !== 'default.jpg') {
        unlink('../' . $image_path);
    }
    
    // Hapus record dari DB
    $stmt_del = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt_del->execute([$id]);
    
    $_SESSION['msg'] = ['type' => 'success', 'text' => 'Kamar berhasil dihapus!'];
    
    // Redirect mempertahankan parameter search dan page
    header("Location: dashboard.php?page=$page" . $search_url_param);
    exit;
}

// Tambah
if ($_POST && isset($_POST['add'])) {
    $errors = [];
    $type = trim($_POST['type'] ?? '');
    $room_number = trim($_POST['room_number'] ?? '');
    $price = $_POST['price'] ?? '';
    $description = trim($_POST['description'] ?? '');
    
    $image_path = ''; // Placeholder jika tidak ada upload
    
    // --- Validasi Input ---
    if ($type === '') $errors[] = "Tipe kamar wajib diisi.";
    if ($room_number === '') $errors[] = "Nomor kamar wajib diisi.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Harga harus angka positif.";

    if (empty($errors)) {
        // Cek apakah nomor kamar sudah ada (opsional tapi disarankan)
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE room_number = ?");
        $check_stmt->execute([$room_number]);
        if ($check_stmt->fetchColumn() > 0) {
             $errors[] = "Nomor kamar **$room_number** sudah terdaftar.";
        } else {
             $stmt_add = $pdo->prepare("INSERT INTO rooms (type, room_number, price, description, image) VALUES (?, ?, ?, ?, ?)");
             $stmt_add->execute([$type, $room_number, $price, $description, $image_path ?: 'default.jpg']); 
             
             $_SESSION['msg'] = ['type' => 'success', 'text' => 'Kamar **' . htmlspecialchars($type) . '** berhasil ditambahkan!'];
             header("Location: dashboard.php"); 
             exit;
        }
    } 
    
    if (!empty($errors)) {
        $_SESSION['msg'] = ['type' => 'danger', 'text' => implode('<br>', $errors)];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Manajemen Kamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* (CSS style tetap sama) */
        body { 
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.6)), 
                        url('../assets/foto/hotel.jpg') center center/cover no-repeat fixed;
            min-height: 100vh;
            color: white; 
        }
        h3, h4, .text-gold { font-family: 'Playfair Display', serif; color: #d4af37 !important; }
        .card { 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            border: none; 
            background: rgba(255, 255, 255, 0.95);
            color: #333; 
        }
        .btn-gold { background: #d4af37; color: white; transition: all 0.3s; }
        .btn-gold:hover { background: #b8962e; color: white; }
        .table th { background: #1e3d73; color: white; }
        .header-section { 
            background-color: #1e3d73;
            color: white; 
            border-bottom: 3px solid #d4af37; 
            border-radius: 16px 16px 0 0; 
        }
        .header-section h3, .header-section p { color: white !important; }
        .alert { color: #333; }
        .logout-link { color: white !important; font-weight: 600; }
        .logout-link:hover { color: #d4af37 !important; }
        .modal-header { background-color: #dc3545; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="card p-0">
        <div class="header-section p-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0">Selamat Datang, Admin!</h3>
                <p class="mb-0">Kelola data kamar hotel Anda di sini.</p>
            </div>
            <a href="../auth/logout.php" class="btn btn-outline-light logout-link" style="border-color: #d4af37;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <div class="card-body p-4">
            
            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert alert-<?= $_SESSION['msg']['type'] ?> alert-dismissible fade show">
                    <?= $_SESSION['msg']['text'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['msg']); ?>
            <?php endif; ?>

            <h4 class="mb-3 text-gold"><i class="fas fa-plus-circle me-2"></i> Tambah Kamar Cepat</h4>
            <form method="POST" class="row g-3 mb-4 p-3 border rounded shadow-sm">
                <div class="col-md-3">
                    <input type="text" name="type" class="form-control" placeholder="Tipe (Ex: Deluxe)" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="room_number" class="form-control" placeholder="Nomor (Ex: 201)" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="price" class="form-control" placeholder="Harga ($)" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="description" class="form-control" placeholder="Deskripsi Singkat">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add" class="btn btn-gold w-100">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Daftar Kamar (<?= $total ?> Total<?= !empty($search) ? " - Hasil Pencarian: **" . htmlspecialchars($search) . "**" : "" ?>)</h4>
                
                <form method="GET" class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Cari Tipe/Nomor Kamar" aria-label="Search" name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-primary" type="submit" style="color: #1e3d73; border-color: #1e3d73;">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!empty($search)): ?>
                    <a href="dashboard.php" class="btn btn-outline-danger ms-2" title="Hapus Pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </form>
                </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tipe</th>
                            <th>Nomor</th>
                            <th>Harga</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rooms)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada kamar yang ditemukan<?= !empty($search) ? " untuk pencarian **" . htmlspecialchars($search) . "**" : "" ?>.</td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php foreach ($rooms as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['type']) ?></td>
                            <td><?= htmlspecialchars($r['room_number']) ?></td>
                            <td>$<?= number_format($r['price'], 2) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                            <td>
                                <a href="room_detail.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info text-white me-1" title="Lihat Detail"><i class="fas fa-info-circle"></i></a>
                                <a href="room_edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>
                                
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal"
                                        data-id="<?= $r['id'] ?>"
                                        data-room="<?= htmlspecialchars($r['type']) ?> #<?= htmlspecialchars($r['room_number']) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= $search_url_param ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Penghapusan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus kamar:</p>
        <strong id="modalRoomName" class="text-danger"></strong>
        <p class="mt-2 text-muted">Aksi ini tidak dapat dibatalkan!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a id="confirmDeleteButton" class="btn btn-danger">Ya, Hapus Permanen</a>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// JavaScript untuk menangani data dinamis pada Modal Delete
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget; 
            
            // Ekstrak informasi dari atribut data-*
            const roomId = button.getAttribute('data-id');
            const roomName = button.getAttribute('data-room');
            
            // Ambil elemen yang akan diisi
            const modalRoomName = deleteModal.querySelector('#modalRoomName');
            const confirmButton = deleteModal.querySelector('#confirmDeleteButton');
            
            // Isi konten modal
            modalRoomName.textContent = roomName;
            
            // Buat jalur delete final (ID, Page, dan Search)
            // Mengambil parameter search dari URL saat ini
            const urlParams = new URLSearchParams(window.location.search);
            const currentPage = urlParams.get('page') || 1;
            const currentSearch = urlParams.get('search') || '';
            
            let deleteUrl = `?delete=${roomId}&page=${currentPage}`;
            if (currentSearch) {
                deleteUrl += `&search=${encodeURIComponent(currentSearch)}`;
            }

            // Set link pada tombol 'Ya, Hapus Permanen'
            confirmButton.setAttribute('href', deleteUrl);
        });
    }
});
</script>
</body>
</html>