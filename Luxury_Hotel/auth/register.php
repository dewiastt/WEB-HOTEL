<?php
session_start();
require '../db/database.php';

$success_message = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
// Tambahkan ini jika Anda menggunakan $_SESSION['error'] di logika register
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $email    = trim($_POST['email'] ?? '');

    $errors = [];

    if ($username === '' || $password === '' || $email === '') {
        $errors[] = "Semua kolom wajib diisi!";
    }
    if ($password !== $confirm) {
        $errors[] = "Password tidak cocok!";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid!";
    }

    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->execute([$username, $email]);
    if ($check->rowCount() > 0) {
        $errors[] = "Username atau email sudah digunakan!";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $hash, $email]);
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, 1.0">
    <title>Register - Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: url('../assets/foto/hotel.jpg') center/cover no-repeat; }
        .register-card { max-width: 400px; margin: 100px auto; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .btn-gold { background: #d4af37; color: white; }
        .btn-gold:hover { background: #b8962e; color: white; }
    </style>
</head>
<body>
    <div class="register-card card">
        <h3 class="text-center mb-4 text-gold">Register</h3>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-gold w-100">Register</button>
        </form>
        <hr>
        <p class="text-center small text-muted">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>
    </div>
</body>
</html>