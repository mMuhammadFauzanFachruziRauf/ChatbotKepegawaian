<?php
// Skrip ini untuk membuat akun admin utama secara paksa.
// HAPUS FILE INI SETELAH SELESAI DIGUNAKAN!

require 'db_connection.php';

// --- Konfigurasi Akun Admin Baru ---
$username = 'admin@smkn1.com';
$password_plain = 'admin12345';
$role = 'admin';

// Mulai output HTML untuk memberikan feedback ke browser
echo "<!DOCTYPE html><html lang='id'><head><title>Setup Admin</title>";
echo "<style>body { font-family: sans-serif; padding: 20px; line-height: 1.6; } .success { color: green; font-weight: bold; } .error { color: red; font-weight: bold; } .warning { color: orange; font-weight: bold; } code { background: #eee; padding: 2px 5px; border-radius: 4px; }</style>";
echo "</head><body>";
echo "<h1>Proses Pembuatan Akun Admin Utama...</h1>";

// 1. Cek apakah username sudah ada
$stmt_check = $conn->prepare("SELECT id FROM petugas WHERE username = ?");
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "<p class='warning'>PERINGATAN: Akun dengan username <code>" . htmlspecialchars($username) . "</code> sudah ada di database. Tidak ada akun baru yang dibuat.</p>";
    echo "<p>Silakan coba login menggunakan akun tersebut. Jika lupa password, hapus user ini dari phpMyAdmin dan jalankan skrip ini lagi.</p>";
} else {
    // 2. Jika belum ada, lanjutkan proses pembuatan
    $hashed_password = password_hash($password_plain, PASSWORD_BCRYPT);
    
    $stmt_insert = $conn->prepare("INSERT INTO petugas (username, password, role) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt_insert->execute()) {
        echo "<p class='success'>BERHASIL! Akun admin utama telah berhasil dibuat.</p>";
        echo "<p>Silakan login dengan data berikut:</p>";
        echo "<ul>";
        echo "<li>Username: <code>" . htmlspecialchars($username) . "</code></li>";
        echo "<li>Password: <code>" . htmlspecialchars($password_plain) . "</code></li>";
        echo "</ul>";
    } else {
        echo "<p class='error'>GAGAL! Terjadi kesalahan saat menyimpan data ke database: " . htmlspecialchars($stmt_insert->error) . "</p>";
    }
    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();

echo "<hr>";
echo "<p class='warning' style='font-size: 1.2rem;'>PENTING: SEGERA HAPUS FILE <code>create_admin.php</code> INI DARI SERVER ANDA SETELAH SELESAI!</p>";
echo "</body></html>";
?>