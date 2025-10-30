<?php
session_start();
require '../db/database.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch();

if (!$room) {
    $_SESSION['msg'] = ['type' => 'danger', 'text' => 'Kamar tidak ditemukan.'];
    header("Location: dashboard.php"); exit; 
}

$page_title = "Detail Kamar: " . htmlspecialchars($room['type']);

// ==========================================================
// ✅ LOGIKA DINAMIS PENCARIAN FOTO BERDASARKAN TIPE KAMAR
// ==========================================================

// 1. Ambil tipe kamar (contoh: "Standard")
$room_type_slug = strtolower($room['type']); 

// 2. Tentukan nama file yang sesuai dengan tipe kamar
$file_map = [
    'standard' => 'standar.jpg', 
    'deluxe' => 'deluxe.jpg',
    'suite' => 'suite.jpeg',
    'executive' => 'executive.jpg',
    'family' => 'family.png', // Gunakan .png sesuai folder Anda
    'presidential' => 'presidential.jpeg',
    'superior' => 'suite.jpeg', 
    'default' => 'hotel.jpg' // Default jika tidak ada yang cocok
];



// Cari nama file di peta (map), jika tidak ada, gunakan 'default.jpg'
$filename = $file_map[$room_type_slug] ?? 'default.jpg';

// Gabungkan menjadi jalur lengkap
$room_image_path = "../assets/foto/rooms/" . $filename;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
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
            color: white; /* Warna teks body diatur ke putih */
        }
        /* ========================================= */
        
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; color: #333; }
        .text-gold { color: #d4af37 !important; }
        .bg-navy { background-color: #1e3d73; color: white; }
        .room-image { 
            height: 450px; 
            object-fit: cover; 
            width: 100%; 
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .price-tag {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e3d73; /* Navy Blue */
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }
        .feature-icon {
            color: #d4af37; /* Gold */
            margin-right: 15px;
            font-size: 1.5rem;
            width: 30px;
            text-align: center;
        }
        /* Card utama dibuat agak transparan agar background terlihat, dan teks di dalamnya berwarna gelap */
        .card {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pt-4"> 
        <h2 style="color: white;">Detail Kamar Tipe <?= htmlspecialchars($room['type']) ?></h2>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="row">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    
                    <img src="<?= $room_image_path ?>" 
                         alt="Foto Kamar Tipe <?= htmlspecialchars($room['type']) ?>" 
                         class="room-image img-fluid">
                </div>

                <div class="col-lg-5">
                    
                    <div class="mb-4 p-3 border-bottom">
                        <span class="price-tag text-gold">
                            $<?= number_format($room['price'], 2) ?>
                        </span>
                        <small class="text-muted fs-5">/ Malam</small>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <p class="mb-0">
                            <strong><i class="fas fa-hashtag me-2 text-gold"></i> Nomor Kamar:</strong>
                        </p>
                        <span class="badge bg-navy fs-6"><?= htmlspecialchars($room['room_number']) ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <p class="mb-0">
                            <strong><i class="fas fa-id-card me-2 text-gold"></i> ID Kamar:</strong>
                        </p>
                        <span class="text-muted">#<?= $room['id'] ?></span>
                    </div>

                    <h5 class="mt-3 mb-3 pb-2 border-bottom">Fasilitas Utama</h5>
                    <div class="row">
                        <div class="col-6 feature-item">
                            <i class="fas fa-expand-arrows-alt feature-icon"></i>
                            Luas (35m²)
                        </div>
                        <div class="col-6 feature-item">
                            <i class="fas fa-tv feature-icon"></i>
                            Smart TV 4K
                        </div>
                        <div class="col-6 feature-item">
                            <i class="fas fa-wifi feature-icon"></i>
                            WiFi Kecepatan Tinggi
                        </div>
                        <div class="col-6 feature-item">
                            <i class="fas fa-mug-hot feature-icon"></i>
                            Kopi & Teh Gratis
                        </div>
                        <div class="col-6 feature-item">
                            <i class="fas fa-box-open feature-icon"></i>
                            Safe Deposit Box
                        </div>
                        <div class="col-6 feature-item">
                            <i class="fas fa-door-closed feature-icon"></i>
                            Pemandangan Kota/Laut
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-5">

            <div class="row">
                <div class="col-12">
                    <h4 class="text-gold mb-3">Deskripsi Lengkap Kamar</h4>
                    <div class="p-3 bg-light rounded">
                        <p class="lead" style="color:#333;">
                            <?= nl2br(htmlspecialchars($room['description'] ?? 'Belum ada deskripsi yang ditambahkan untuk kamar ini. Tambahkan detail untuk menjelaskan kemewahan, pemandangan, dan ukuran kamar.')) ?>
                        </p>
                        <p class="text-muted small mt-4">
                           Detail kamar ini memberikan sentuhan elegan dan kenyamanan maksimal. Nikmati layanan kamar 24 jam dan suasana tenang yang dirancang khusus untuk istirahat premium Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-light d-flex justify-content-end">
            <a href="room_edit.php?id=<?= $room['id'] ?>" class="btn btn-warning me-2">
                 <i class="fas fa-edit"></i> Edit Kamar
            </a>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-list"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>