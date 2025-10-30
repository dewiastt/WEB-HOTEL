<?php
// FILE: user/rooms.php
session_start();
$page_title = "Our Rooms - Luxury Hotel";
require '../db/database.php';

// Logika Mapping Gambar (diimplementasikan di sini)
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

// Ambil semua kamar untuk DAFTAR LENGKAP
$stmt_all = $pdo->query("SELECT * FROM rooms ORDER BY price ASC");
$all_rooms = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

// Ambil 3 Kamar Termahal/Terbaik untuk PROMOSI KHUSUS
$stmt_promo = $pdo->query("SELECT * FROM rooms ORDER BY price DESC LIMIT 3");
$promo_rooms = $stmt_promo->fetchAll(PDO::FETCH_ASSOC);

// Siapkan data promo
$promo_data = [];
$promo_room_ids = []; 

foreach ($promo_rooms as $room) {
    $room_type_lower = strtolower($room['type']);
    $mapped_filename = $file_map[$room_type_lower] ?? $file_map['default'];
    
    $room['image_path'] = $base_image_path . $mapped_filename;
    $room['discount'] = 30; // Promosi khusus: 30% Off
    $room['promo_price'] = $room['price'] * (1 - ($room['discount'] / 100));
    $room['rating_score'] = round(rand(80, 89) / 10, 1);
    
    $promo_data[] = $room;
    $promo_room_ids[] = $room['id'];
}

// Filter kamar yang tidak termasuk dalam promo untuk daftar lengkap
$normal_rooms = array_filter($all_rooms, function($room) use ($promo_room_ids) {
    return !in_array($room['id'], $promo_room_ids);
});

// Tambahkan path gambar dan data dummy ke kamar normal
$rooms_data = [];
foreach ($normal_rooms as $room) {
    $room_type_lower = strtolower($room['type']);
    $mapped_filename = $file_map[$room_type_lower] ?? $file_map['default'];
    
    $room['image_path'] = $base_image_path . $mapped_filename;
    $room['rating_score'] = round(rand(80, 89) / 10, 1);
    $room['review_count'] = rand(2000, 9000);
    $rooms_data[] = $room;
}


$is_logged_in = isset($_SESSION['user_id']); 

// AKTIFKAN INCLUDE HEADER DAN NAVBAR
include '../include/header.php';
include '../include/navbar.php'; 
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
            --color-red: #dc3545; 
            --color-text-dark: #333;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            padding-top: 76px; /* Dibiarkan 76px untuk space navbar fixed */
        }

        h1, h2, h3, h4, h5, h6 { 
            font-family: 'Playfair Display', serif; 
            color: var(--color-text-dark); 
        }
        .text-gold { 
            color: var(--color-gold) !important; 
        }
        .room-image { 
            height: 200px; 
            object-fit: cover; 
            width: 100%; 
            border-radius: 12px 12px 0 0; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .price-tag-large { 
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--color-navy); 
            margin-bottom: 0.5rem; 
        }
        .feature-icon {
            color: var(--color-gold); 
            margin-right: 15px;
            font-size: 1.5rem;
            width: 30px;
            text-align: center;
        }

        /* === HERO SECTION (Background Foto & Overlay Navy) === */
        /* Margin Top tidak lagi dibutuhkan karena sudah ada padding-top di body */
        .search-header-bg {
            background: linear-gradient(rgba(30, 61, 115, 0.8), rgba(30, 61, 115, 0.9)), 
                        url('../assets/foto/hotel.jpg') center center/cover no-repeat fixed; 
            padding: 100px 0 140px 0;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            position: relative; 
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

        /* === Search Form Card (Floating) === */
        .search-form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            padding: 30px;
            margin-top: -100px; /* Diangkat ke atas hero section */
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

        /* === PROMO SECTION STYLES === */
        .promo-section-title {
            color: var(--color-navy);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }
        .promo-card-wrapper {
            transition: transform 0.3s ease-in-out;
            border: 1px solid #eee;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 100%;
        }
        .promo-tag {
            position: absolute;
            top: 20px;
            left: -15px;
            background-color: var(--color-red);
            color: white;
            padding: 5px 20px;
            font-weight: 700;
            z-index: 5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.4);
            transform: rotate(-5deg);
        }
        .price-old {
            text-decoration: line-through;
            color: #888;
            font-size: 1rem;
        }
        .price-new {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-gold);
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

<?php if (!empty($promo_data)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="promo-section-title text-center text-gold">🔥 Penawaran Eksklusif (Hemat <?= $promo_data[0]['discount'] ?>%!)</h2>
        
        <div class="row g-4 justify-content-center">
            <?php foreach ($promo_data as $room): ?>
            <div class="col-md-4">
                <div class="promo-card-wrapper">
                    <div class="promo-img-wrapper">
                        <img src="<?= htmlspecialchars($room['image_path']) ?>" alt="<?= htmlspecialchars($room['type']) ?>" class="promo-img">
                        <span class="promo-tag">Diskon <?= $room['discount'] ?>%</span>
                    </div>
                    
                    <div class="p-4">
                        <h4 style="color: var(--color-navy);"><?= htmlspecialchars($room['type']) ?></h4>
                        <p class="small text-muted mb-3">No. Kamar: <?= htmlspecialchars($room['room_number']) ?></p>
                        
                        <div class="d-flex justify-content-between align-items-end mb-4">
                            <div>
                                <p class="price-old mb-0">$<?= number_format($room['price'], 2) ?></p>
                                <p class="price-new">$<?= number_format($room['promo_price'], 2) ?></p>
                            </div>
                            <span class="badge" style="background-color: var(--color-navy); color: white;">
                                <i class="fas fa-star me-1"></i> <?= $room['rating_score'] ?? '8.5' ?>/10
                            </span>
                        </div>
                        
                        <?php if ($is_logged_in): ?>
                            <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-promo-book w-100">
                                PESAN PROMO INI
                            </a>
                        <?php else: ?>
                             <a href="../auth/login.php" class="btn btn-promo-book w-100">
                                Login untuk Pesan
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5">
    <div class="container">
        <h3 class="mb-5" style="color: var(--color-navy); font-weight: 700; text-align: center;"><i class="fas fa-bed me-2"></i> Daftar Kamar Lengkap</h3>
        
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
                        <span class="room-save-tag">Save 25%</span>
                    </div>
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

<?php include '../include/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>