<?php
// FILE: user/booking.php (TANPA DATABASE - HANYA KONFIRMASI)
session_start();
$page_title = "Book Your Room - Luxury Hotel (Konfirmasi)";

// --- Bagian Koneksi Database Dihapus atau Dikosongkan ---
// require '../db/database.php'; 

// Cek Login: Hanya user yang sudah login yang boleh booking
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = ['type' => 'danger', 'text' => 'Anda harus login untuk membuat pemesanan.'];
    header("Location: ../auth/login.php");
    exit;
}

// --- SIMULASI DATA KAMAR (Karena DB dihapus) ---
// Kita asumsikan data kamar (price dan type) diteruskan melalui URL atau sesi
$room_id = (int)($_GET['room_id'] ?? 1); // Gunakan ID dummy jika tidak ada
$room_price_from_url = (float)($_GET['price'] ?? 150.00); 
$room_type_from_url = htmlspecialchars($_GET['type'] ?? 'Deluxe Suite');

// Data kamar yang akan ditampilkan (tanpa akses DB)
$room = [
    'id' => $room_id,
    'type' => $room_type_from_url,
    'price' => $room_price_from_url,
    'description' => 'Simulasi kamar mewah dengan konfirmasi instan.',
    'image' => '../assets/foto/rooms/default.jpg' // Path gambar dummy
];


// ==========================================================
// LOGIKA PEMROSESAN BOOKING (DISESUAIKAN UNTUK NON-DB)
// ==========================================================
$errors = [];
$check_in = $_POST['check_in'] ?? '';
$check_out = $_POST['check_out'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$total_price = 0;

if ($_POST && isset($_POST['book'])) {
    
    // 1. Validasi Tanggal (Tetap dilakukan)
    if (empty($check_in) || empty($check_out)) {
        $errors[] = "Tanggal check-in dan check-out wajib diisi.";
    } elseif (strtotime($check_in) >= strtotime($check_out)) {
        $errors[] = "Tanggal check-out harus setelah tanggal check-in.";
    } elseif (strtotime($check_in) < strtotime(date('Y-m-d'))) {
        $errors[] = "Tanggal check-in tidak boleh di masa lalu.";
    }
    
    // 2. Hitung Harga & Durasi (Tetap dilakukan)
    if (empty($errors)) {
        $check_in_dt = new DateTime($check_in);
        $check_out_dt = new DateTime($check_out);
        $interval = $check_in_dt->diff($check_out_dt);
        $days = $interval->days;
        
        if ($days < 1) {
             $errors[] = "Durasi pemesanan minimal 1 malam.";
        } else {
             $total_price = $room['price'] * $days;
        }
    }
    
    // 3. Proses Pembayaran (Upload Bukti Dihapus, hanya cek input)
    $proof_image_uploaded = false;
    
    if ($payment_method === 'qris') {
        // Karena tidak ada DB, kita hanya memeriksa apakah file DIKIRIM (walaupun tidak di-upload)
        if (empty($_FILES['proof_image']['name'])) {
             $errors[] = "Bukti pembayaran wajib diisi untuk metode QRIS.";
        } else {
             $proof_image_uploaded = true;
        }
        $display_status = "Pending (Menunggu Verifikasi Pembayaran)";
        
    } elseif ($payment_method === 'cash') {
         $display_status = 'Confirmed (Bayar di Tempat)';
    } else {
         $errors[] = "Metode pembayaran tidak valid.";
    }

    // 4. Konfirmasi dan Redirect (Pengganti Simpan ke Database)
    if (empty($errors)) {
        
        $confirmation_details = "Anda telah memesan **" . htmlspecialchars($room['type']) . "** dari $check_in hingga $check_out.";
        $confirmation_details .= " Total harga: **$" . number_format($total_price, 2) . "**.";
        $confirmation_details .= " Metode Pembayaran: **" . ucfirst($payment_method) . "**.";
        
        if ($proof_image_uploaded) {
            $confirmation_details .= " Bukti bayar diterima untuk proses verifikasi.";
        }
        
        $_SESSION['msg'] = ['type' => 'success', 'text' => "Pemesanan Berhasil Dikonfirmasi! " . $confirmation_details];
        
        // Redirect ke halaman rooms (karena user_dashboard memerlukan DB)
        header("Location: rooms.php"); 
        exit;
    }
    
    if (!empty($errors)) {
        $_SESSION['msg'] = ['type' => 'danger', 'text' => implode('<br>', $errors)];
    }
}


// Karena kita tidak menggunakan file include/, kita harus memastikan style CSS dasar ada di sini.
// (Menggunakan style dari kode sebelumnya)
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
            --color-text: #333;
        }
        body { 
            font-family: 'Montserrat', sans-serif; 
            background: #f0f2f5; 
            padding-top: 20px; /* Dikurangi karena tidak ada navbar fixed */
        }
        h3, h4, h5 { 
             font-family: 'Playfair Display', serif; 
        }
        .text-gold { 
             color: var(--color-gold) !important; 
        }
        .booking-card { 
            border-radius: 15px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.15); 
            background: white;
            margin-top: 50px; /* Disesuaikan */
        }
        
        /* Detail Kamar */
        .room-details-card { 
            background: #f8f9fa;
            border-radius: 10px; 
            padding: 25px; 
            border-left: 5px solid var(--color-gold); 
        }
        .room-details-card h4 {
            color: var(--color-navy);
            font-size: 1.8rem;
        }
        .price-display {
             color: var(--color-gold); 
             font-weight: 700;
             font-size: 1.5rem;
        }
        
        /* Input & Form */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border-color: #ddd;
        }
        .form-control:focus, .form-select:focus {
             box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
             border-color: var(--color-gold);
        }

        /* QRIS Info */
        #qris_info { 
            border: 2px dashed var(--color-gold); 
            padding: 20px; 
            border-radius: 15px;
            background: #fffbef;
        }
        #qris_info .fw-bold {
            color: var(--color-navy) !important;
        }
        
        /* Primary Button */
        .btn-submit { 
            background-color: var(--color-navy); 
            border-color: var(--color-navy); 
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-submit:hover { 
            background-color: var(--color-gold); 
            border-color: var(--color-gold); 
            color: var(--color-navy);
            box-shadow: 0 5px 15px rgba(30, 61, 115, 0.3);
        }
    </style>
</head>
<body>
    
<div class="container mt-4">
    <a href="rooms.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Kamar</a>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="booking-card p-4">
                <h3 class="mb-4 text-gold"><i class="fas fa-calendar-check me-2"></i> Konfirmasi Pemesanan</h3>
                <hr>

                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-<?= $_SESSION['msg']['type'] ?> alert-dismissible fade show">
                        <?= $_SESSION['msg']['text'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['msg']); ?>
                <?php endif; ?>

                <div class="room-details-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h4 class="mb-1"><?= htmlspecialchars($room['type']) ?></h4>
                            <p class="lead price-display mb-0">$<?= number_format($room['price'], 2) ?> <small class="text-muted">/ malam</small></p>
                            <p class="small text-muted mt-2"><?= htmlspecialchars($room['description'] ?? 'Deskripsi singkat kamar.') ?></p>
                            <p class="small text-muted">**PENTING:** Ini adalah mode demonstrasi tanpa penyimpanan database.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    
                    <h5 class="mt-4 mb-3" style="color: var(--color-navy);"><i class="fas fa-clock me-2"></i> Durasi & Detail Tamu</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="check_in" class="form-label">Check-in</label>
                            <input type="date" name="check_in" id="check_in" class="form-control" 
                                   required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($check_in) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="check_out" class="form-label">Check-out</label>
                            <input type="date" name="check_out" id="check_out" class="form-control" 
                                   required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($check_out) ?>">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Jumlah Tamu</label>
                            <select class="form-select">
                                <option>2 Adult(s), 0 Child, 1 Room</option>
                                <option>3 Adult(s), 0 Child, 1 Room</option>
                            </select>
                        </div>

                        <div class="col-12">
                             <div class="alert alert-info text-center" id="price_summary" style="font-weight: 600;">
                                Pilih tanggal untuk melihat Total Harga.
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3" style="color: var(--color-navy);"><i class="fas fa-credit-card me-2"></i> Metode Pembayaran</h5>
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash_option" value="cash" required <?= ($payment_method == 'cash' || empty($payment_method)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="cash_option">
                                <i class="fas fa-money-bill-wave me-1"></i> Cash (Bayar di Tempat - Status **Confirmed**)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="qris_option" value="qris" required <?= ($payment_method == 'qris') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="qris_option">
                                <i class="fas fa-qrcode me-1"></i> QRIS (Transfer Bank - Status **Pending Approval**)
                            </label>
                        </div>
                    </div>

                    <div id="qris_section" class="mb-4" style="display: <?= ($payment_method == 'qris') ? 'block' : 'none' ?>;">
                        <div id="qris_info" class="text-center mb-3">
                            <p class="mb-1 fw-bold" style="font-size: 1.1rem;">Scan untuk Bayar</p>
                            <p class="small text-muted">Pastikan Anda membayar sebesar total harga yang tertera.</p>
                            
                            
                            
                            <p class="mt-2 mb-0 fw-bold">TOTAL HARGA AKAN TERTERA DI ATAS</p>
                        </div>
                        
                        <label for="proof_image" class="form-label">Upload Bukti Pembayaran (JPG/PNG - Max 5MB)</label>
                        <input type="file" name="proof_image" id="proof_image" class="form-control" accept="image/jpeg,image/png">
                        <p class="form-text text-muted small">Wajib diisi jika Anda memilih metode pembayaran QRIS.</p>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="book" class="btn btn-submit btn-lg">
                            <i class="fas fa-check-circle me-2"></i> Selesaikan Pemesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // include '../include/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const qrisOption = document.getElementById('qris_option');
    const cashOption = document.getElementById('cash_option');
    const qrisSection = document.getElementById('qris_section');
    const priceSummary = document.getElementById('price_summary');
    const roomPrice = <?= $room['price'] ?>; // Mengambil harga dari PHP

    function toggleQrisSection() {
        qrisSection.style.display = qrisOption.checked ? 'block' : 'none';
        const proofImageInput = document.getElementById('proof_image');
        // Hanya tambahkan/hapus required, file tidak di-upload dalam mode ini
        if (qrisOption.checked) {
            proofImageInput.setAttribute('required', 'required');
        } else {
            proofImageInput.removeAttribute('required');
            proofImageInput.value = ''; 
        }
    }

    function calculatePrice() {
        const checkIn = checkInInput.value;
        const checkOut = checkOutInput.value;

        if (checkIn && checkOut) {
            const dateIn = new Date(checkIn);
            const dateOut = new Date(checkOut);
            
            const diffTime = Math.abs(dateOut - dateIn);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (dateOut <= dateIn) {
                 priceSummary.innerHTML = 'Tanggal check-out harus setelah check-in.';
                 priceSummary.classList.remove('alert-success');
                 priceSummary.classList.add('alert-info');
                 return;
            }

            const totalPrice = roomPrice * diffDays;
            
            // Mengubah format ke Dolar ($)
            priceSummary.innerHTML = `Durasi: **${diffDays} malam** ($${roomPrice.toFixed(2)} x ${diffDays}) = **TOTAL HARGA: $${totalPrice.toFixed(2)}**`;
            priceSummary.classList.remove('alert-info');
            priceSummary.classList.add('alert-success');
        } else {
             priceSummary.innerHTML = 'Pilih tanggal untuk melihat Total Harga.';
             priceSummary.classList.remove('alert-success');
             priceSummary.classList.add('alert-info');
        }
    }

    // Event Listeners
    qrisOption.addEventListener('change', toggleQrisSection);
    cashOption.addEventListener('change', toggleQrisSection);
    checkInInput.addEventListener('change', calculatePrice);
    checkOutInput.addEventListener('change', calculatePrice);

    // Jalankan saat load
    toggleQrisSection();
    calculatePrice();
});
</script>
</body>
</html>