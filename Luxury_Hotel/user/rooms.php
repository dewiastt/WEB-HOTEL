<?php
// FILE: user/rooms.php
session_start();
$page_title = "Our Rooms - Luxury Hotel";
require '../db/database.php';

// Logika Mapping Gambar (diimplementasikan di sini)
// Path disesuaikan dengan struktur folder yang Anda tunjukkan (assets/foto/rooms)
$base_image_path = '../assets/foto/rooms/'; 

$file_map = [
    'standard' => 'standar.jpg', 
    'deluxe' => 'deluxe.jpg',
    'suite' => 'suite.jpeg',
    'executive' => 'executive.jpg',
    'family' => 'family.png', 
    'presidential' => 'presidential.jpeg',
    'superior' => 'suite.jpeg', 
    'default' => 'hotel.jpg' 
];

// Ambil semua kamar
$stmt = $pdo->query("SELECT * FROM rooms ORDER BY price ASC");
$all_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dummy Data untuk Kartu Kamar (Rating & Reviews)
$rooms_data = [];
$is_logged_in = isset($_SESSION['user_id']); 

foreach ($all_rooms as $room) {
    // 1. Tentukan path gambar berdasarkan tipe kamar
    $room_type_lower = strtolower($room['type']);
    $mapped_filename = $file_map[$room_type_lower] ?? $file_map['default'];
    
    $room['image_path'] = $base_image_path . $mapped_filename;
    
    // 2. Data Dummy
    $room['rating_score'] = round(rand(80, 89) / 10, 1);
    $room['review_count'] = rand(2000, 9000);
    $rooms_data[] = $room;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-navy: #1e3d73;
            --color-gold: #d4af37;
            --color-orange: #ff6347;
            --color-text-dark: #333;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            padding-top: 0; 
        }

        h1, h2, h3, h4, h5, h6 { 
            font-family: 'Playfair Display', serif; 
            color: var(--color-text-dark); 
        }
        .text-gold { 
            color: var(--color-gold) !important; 
        }
        
        /* === HERO SECTION (Background Foto & Overlay Navy) === */
        .search-header-bg {
            background: linear-gradient(rgba(30, 61, 115, 0.8), rgba(30, 61, 115, 0.9)), 
                        url('../assets/foto/hotel.jpg') center center/cover no-repeat fixed; 
            padding: 100px 0 140px 0;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            position: relative; /* Untuk menampung tombol logout absolut */
        }
        .header-title-container {
             max-width: 800px;
             margin: 0 auto;
        }
        .header-title {
             font-family: 'Playfair Display', serif; 
             font-size: 3.5rem; 
             font-weight: 700;
             text-shadow: 0 3px 8px rgba(0,0,0,0.7);
        }
        .header-subtitle {
             font-size: 1.2rem;
             color: rgba(255, 255, 255, 0.9);
        }
        
        /* Logout Button di Hero */
        .hero-logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
            z-index: 20;
        }
        .hero-logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--color-gold);
        }

        /* === Search Form Card (Floating) === */
        .search-form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            padding: 30px;
            margin-top: -100px;
            position: relative;
            z-index: 10;
        }
        .btn-search {
            background-color: var(--color-gold); 
            border-color: var(--color-gold);
            color: var(--color-navy); 
            font-weight: 700;
            padding: 0.8rem 1rem;
            border-radius: 10px;
        }
        .btn-search:hover {
            background-color: #b8962e;
            border-color: #b8962e;
            color: white;
        }
        .form-control:focus, .form-select:focus {
             box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.4); 
             border-color: var(--color-gold);
        }

        /* === Room Card List === */
        .room-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1); 
            transition: transform 0.3s ease-in-out;
            height: 100%;
            overflow: hidden;
            background: white;
        }
        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        }
        .room-img {
            height: 200px;
            object-fit: cover; 
            width: 100%;
        }
        .room-location-tag { 
            background: var(--color-navy); 
        }
        .price-discount {
            color: var(--color-gold); 
            font-size: 1.6rem;
            font-weight: 700;
        }

        /* Tombol Booking di Card */
        .btn-book-action {
             background-color: var(--color-navy); 
             border-color: var(--color-navy);
             color: white; 
             font-weight: 600;
             border-radius: 5px; 
             padding: 0.6rem;
        }
        .btn-book-action:hover {
            background-color: var(--color-gold);
            border-color: var(--color-gold);
            color: var(--color-navy);
        }
        .btn-login-action {
            background-color: var(--color-gold); 
            color: var(--color-navy);
            border: 1px solid var(--color-gold);
        }
        .btn-login-action:hover {
            background-color: var(--color-navy);
            color: white;
            border-color: var(--color-navy);
        }
    </style>
</head>
<body>

<section class="search-header-bg">
    
    <?php if ($is_logged_in): ?>
        <a href="../auth/logout.php" class="btn hero-logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout (<?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>)
        </a>
    <?php endif; ?>

    <div class="container text-center pt-5 pb-4">
        <h1 class="header-title text-center">Temukan Kamar Impian Anda</h1>
        <p class="header-subtitle">Rasakan kemewahan yang tak tertandingi di Luxury Hotel.</p>
    </div>
</section>

<div class="container">
    <div class="search-form-card">
        <h5 class="mb-4"><i class="fas fa-search me-2"></i> Cari Kamar Tersedia</h5>
        
        <form method="GET" action="rooms.php" class="row g-3">
            <div class="col-md-6 col-lg-3">
                <label class="form-label d-block small text-muted">Tipe Kamar / Nomor</label>
                <input type="text" name="search" class="form-control" placeholder="Ex: Deluxe, Suite..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label d-block small text-muted">Check-in</label>
                <input type="date" name="check_in" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label d-block small text-muted">Check-out</label>
                <input type="date" name="check_out" class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
            <div class="col-md-6 col-lg-3 d-flex align-items-end">
                <button type="submit" class="btn btn-search w-100">
                    <i class="fas fa-search me-2"></i> Cari Kamar
                </button>
            </div>
        </form>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <h3 class="mb-5" style="color: var(--color-navy); font-weight: 700; text-align: center;"><i class="fas fa-list-alt me-2"></i> Pilihan Terbaik Kami</h3>
        
        <div class="row g-4" id="roomsContainer">
            <?php if (empty($rooms_data)): ?>
                 <div class="col-12 text-center py-5">
                    <p class="text-muted">Saat ini tidak ada kamar yang terdaftar dalam sistem.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($rooms_data as $room): ?>
            <div class="col-sm-6 col-lg-3 room-item">
                <div class="room-card">
                    <div style="position: relative;">
                        <img src="<?= htmlspecialchars($room['image_path'] ?? '../assets/img/rooms/default.jpg') ?>"
                             alt="<?= htmlspecialchars($room['type']) ?>" class="room-img room-image"> 
                        <span class="room-location-tag">No. <?= htmlspecialchars($room['room_number']) ?></span>
                        <span class="room-save-tag">Save 25%</span> </div>
                    <div class="room-details p-3">
                        <h5 class="room-title"><?= htmlspecialchars($room['type']) ?></h5>
                        <div class="room-rating mb-2">
                            <i class="fas fa-star text-gold"></i><i class="fas fa-star text-gold"></i><i class="fas fa-star text-gold"></i><i class="fas fa-star text-gold"></i><i class="fas fa-star-half-alt text-gold"></i>
                            <span class="ms-1 fw-bold" style="color: var(--color-navy);"><?= $room['rating_score'] ?? '8.5' ?>/10</span>
                            <span class="text-muted small">(<?= number_format($room['review_count'] ?? 500, 0, ',', '.') ?>)</span>
                        </div>
                        
                        <div class="price-tag-large">
                            $<?= number_format($room['price'], 2) ?>
                        </div>

                        <ul class="list-unstyled mt-3">
                            <li class="feature-item"><i class="fas fa-wifi feature-icon"></i> Free WiFi</li>
                            <li class="feature-item"><i class="fas fa-snowflake feature-icon"></i> AC & Heating</li>
                        </ul>
                        
                        <?php if ($is_logged_in): ?>
                            <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-book-action w-100 mt-2">
                                Book Now
                            </a>
                        <?php else: ?>
                             <a href="../auth/login.php" class="btn btn-book-action w-100 btn-login-action mt-2">
                                Login to Book
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php // include '../include/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>