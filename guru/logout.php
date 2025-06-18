<?php
session_start();

// 1. Membersihkan semua variabel sesi (seperti data user)
session_unset();

// 2. Menghancurkan sesi yang lama
session_destroy();

// 3. Memulai sesi baru yang bersih hanya untuk membawa pesan
session_start();
$_SESSION['logout_message'] = "Anda telah berhasil logout.";

// 4. Mengarahkan ke halaman login
header("Location: ../login/login.php");
exit();
?>