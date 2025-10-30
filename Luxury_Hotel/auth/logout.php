<?php
session_start();

// Hapus semua session yang sedang berjalan.
// Ini akan menghapus data login (user_id, role, dll.) untuk user maupun admin.
$_SESSION = []; 
session_destroy();

// Redirect pengguna kembali ke halaman login.
// (Asumsi file ini ada di folder 'auth', dan login.php ada di folder yang sama)
header("Location: login.php");
exit;
?>