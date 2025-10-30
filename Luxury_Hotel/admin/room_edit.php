<?php
session_start();
require '../db/database.php';

// Cek login & admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch();

if (!$room) { 
    // Redirect ke dashboard jika kamar tidak ditemukan
    header("Location: dashboard.php"); 
    exit; 
}

$errors = [];
$success = false;

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dari form
    $type = trim($_POST['type'] ?? $room['type']);
    $room_number = trim($_POST['room_number'] ?? $room['room_number']);
    $price = $_POST['price'] ?? $room['price'];
    $description = trim($_POST['description'] ?? $room['description']);
    
    // Perbaikan: Ambil image lama (asumsi tidak ada upload di halaman ini)
    $image_filename = $room['image']; 

    // Validasi
    if ($type === '') $errors[] = "Tipe wajib diisi.";
    if ($room_number === '') $errors[] = "Nomor wajib diisi.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Harga tidak valid.";

    if (empty($errors)) {
        // Lakukan Update ke database
        $stmt = $pdo->prepare("UPDATE rooms SET type = ?, room_number = ?, price = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$type, $room_number, $price, $description, $image_filename, $id]);
        
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Kamar **' . htmlspecialchars($type) . '** berhasil diupdate!'];
        
        // Redirect ke dashboard.php setelah update berhasil
        header("Location: dashboard.php");
        exit;
    } else {
        // Simpan error untuk ditampilkan
        $_SESSION['msg'] = ['type' => 'danger', 'text' => implode('<br>', $errors)];
        $success = false; 
    }
    
    // Setelah post gagal, perbarui variabel $room agar nilai form tidak hilang
    $room['type'] = $type;
    $room['room_number'] = $room_number;
    $room['price'] = $price;
    $room['description'] = $description;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kamar #<?= $id ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* === PERUBAHAN BACKGROUND UTAMA DI SINI === */
        body { 
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.6)), 
                        url('../assets/foto/hotel.jpg') center center/cover no-repeat fixed;
            min-height: 100vh;
            color: white; 
        }
        /* ========================================= */
        
        h4 { font-family: 'Playfair Display', serif; }
        .card { 
            border-radius: 16px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            background: rgba(255, 255, 255, 0.95); /* Kartu utama agak transparan */
            color: #333;
        }
        .card-header {
            background-color: #1e3d73 !important; /* Navy Blue */
            border-bottom: 3px solid #d4af37; /* Garis Gold */
            color: white;
        }
        .btn-gold { background: #d4af37; color: white; transition: all 0.3s; }
        .btn-gold:hover { background: #b8962e; color: white; }
        .form-label { font-weight: 600; color: #333; }
    </style>
</head>
<body>
<?php include '../include/navbar.php'; ?>
<div class="container mt-5 mb-5">
    <div class="card mx-auto" style="max-width: 700px;">
        <div class="card-header p-3">
            <h4><i class="fas fa-edit me-2"></i> Edit Kamar #<?= $id ?>: <?= htmlspecialchars($room['type']) ?></h4>
        </div>
        <div class="card-body p-4">
            
            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert alert-<?= $_SESSION['msg']['type'] ?> alert-dismissible fade show">
                    <?= $_SESSION['msg']['text'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['msg']); ?>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe Kamar</label>
                        <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($room['type']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Kamar</label>
                        <input type="text" name="room_number" class="form-control" value="<?= htmlspecialchars($room['room_number']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga per Malam ($)</label>
                    <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($room['price']) ?>" min="1" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($room['description'] ?? '') ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-gold px-4"><i class="fas fa-save"></i> Update Kamar</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal / Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>