<?php
// FILE: include/navbar.php

// Ambil status login dan peran
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? 'guest';

// Tentukan URL dasar dashboard/home
$home_url = '../index.php'; // Default home
$rooms_url = '../rooms.php'; // URL ke halaman rooms (sudah benar)

if ($is_logged_in) {
    if ($user_role === 'admin') {
        $dashboard_url = '../admin/dashboard.php';
    } else { // user
        $dashboard_url = '../user/rooms.php';
    }
} else {
    $dashboard_url = '../auth/login.php';
}

// Tambahkan definisi variabel CSS untuk styling dasar
echo '<style>
    :root {
        --color-navy: #1e3d73;
        --color-gold: #d4af37;
    }
    .navbar {
        background-color: var(--color-navy) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .navbar-brand, .nav-link {
        color: white !important;
        font-weight: 500;
    }
    .navbar-brand.text-gold {
        color: var(--color-gold) !important;
        font-family: \'Playfair Display\', serif;
        font-size: 1.5rem;
    }
    .nav-link.active {
        color: var(--color-gold) !important;
        font-weight: 600;
    }
    .btn-gold {
        background-color: var(--color-gold);
        color: var(--color-navy);
        border-color: var(--color-gold);
        font-weight: 600;
    }
    .btn-gold:hover {
        background-color: #b8962e;
        color: white;
    }
</style>';
?>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-gold" href="<?= $user_role === 'guest' ? $home_url : $dashboard_url ?>">
            <i class="fas fa-hotel me-1"></i> Luxury Hotel
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: rgba(255, 255, 255, 0.5) !important;">
            <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $home_url ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user/rooms.php">rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../user/facilities.php">Facilities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../user/contact.php">Contact</a>
                </li>
            </ul>

            <div class="d-flex gap-2 align-items-center">
                <?php if ($is_logged_in): ?>
                    <a href="<?= $dashboard_url ?>" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                    <a href="auth/logout.php" class="btn btn-outline-light btn-sm" style="border-color: var(--color-gold); color: var(--color-gold);">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-outline-light btn-sm">Login</a>
                    <a href="auth/register.php" class="btn btn-gold btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>