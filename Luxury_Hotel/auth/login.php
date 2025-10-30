<?php
session_start();
require '../db/database.php';

// Kredensial Admin Hardcoded (HANYA UNTUK TESTING LOKAL!)
// Anda bisa ganti password '12345' ini sesuai kebutuhan Anda.
$HARDCODED_ADMIN_USERNAME = 'admin';
$HARDCODED_ADMIN_PASSWORD = '12345'; 

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi!";
    } else {
        // ==========================================================
        // 🚨 LOGIKA OTORISASI HARDCODED ADMIN (OVERRIDE DATABASE) 🚨
        // ==========================================================
        if ($username === $HARDCODED_ADMIN_USERNAME && $password === $HARDCODED_ADMIN_PASSWORD) {
            
            // Set SESSION agar user dikenali sebagai admin yang sudah login
            $_SESSION['user_id'] = 9999; 
            $_SESSION['username'] = $HARDCODED_ADMIN_USERNAME;
            $_SESSION['role'] = 'admin'; 
            
            // Langsung arahkan ke dashboard admin
            header("Location: ../admin/dashboard.php");
            exit;
        } 
        
        // ==========================================================
        // ✅ LOGIKA NORMAL (VERIFIKASI DATABASE) UNTUK USER LAIN ✅
        // ==========================================================
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Cek menggunakan password_verify()
        if ($user && password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/rooms.php");
            }
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hotel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: url('../assets/foto/hotel.jpg') center/cover no-repeat; }
        .login-card { max-width: 400px; margin: 100px auto; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .btn-gold { background: #d4af37; color: white; }
        .btn-gold:hover { background: #b8962e; color: white; }
    </style>
</head>
<body>
    <div class="login-card card">
        <h3 class="text-center mb-4 text-gold">Login</h3>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-gold w-100">Login</button>
        </form>
        <hr>
        <p class="text-center small text-muted">
             <a href="register.php">register</a>
        </p>
    </div>
</body>
</html>