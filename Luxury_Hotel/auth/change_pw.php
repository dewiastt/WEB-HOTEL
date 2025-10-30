<?php
session_start();
require '../db/database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = $success = '';

if ($_POST) {
    $current = $_POST['current'] ?? '';
    $new     = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Ambil data user
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!password_verify($current, $user['password'])) {
        $error = "Password lama salah!";
    } elseif ($new !== $confirm) {
        $error = "Password baru tidak cocok!";
    } elseif (strlen($new) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $user_id]);
        $success = "Password berhasil diubah! Silakan login ulang.";
        session_destroy();
        header("Refresh: 3; url=login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.6)), url('../assets/foto/hotel.jpg') center/cover; min-height: 100vh; display: flex; align-items: center; }
        .card { max-width: 450px; margin: auto; padding: 2.5rem; border-radius: 16px; background: rgba(255,255,255,0.95); box-shadow: 0 15px 40px rgba(0,0,0,0.2); }
        .btn-gold { background: #d4af37; color: white; border-radius: 50px; }
        .btn-gold:hover { background: #b8962e; }
    </style>
</head>
<body>
    <div class="card">
        <h3 class="text-center text-gold mb-4">Ganti Password</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="mb-3">
                <input type="password" name="current" class="form-control" placeholder="Password Lama" required>
            </div>
            <div class="mb-3">
                <input type="password" name="new" class="form-control" placeholder="Password Baru" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm" class="form-control" placeholder="Konfirmasi Password Baru" required>
            </div>
            <button type="submit" class="btn btn-gold w-100">Ganti Password</button>
        </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="../<?= $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/rooms.php' ?>" class="text-muted">
                Kembali
            </a>
        </div>
    </div>
</body>
</html>