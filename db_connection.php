<?php
$servername = "localhost"; 
$username = "root"; // Default username XAMPP
$password = ""; // Default password XAMPP (kosong)
$dbname = "skrps_sekolah"; // Sesuaikan dengan nama database

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
