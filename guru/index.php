<?php
session_start();

// Pengecekan Sesi Pengguna
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'guru') {
    header('Location: ../login/login.php');
    exit;
}

require '../db_connection.php';
$pageTitle = 'Beranda'; // Judul untuk halaman ini

// Pengambilan Data Pengguna dari Sesi
$userId = $_SESSION['user']['id'];
$email = $_SESSION['user']['email'];

// Mengambil data utama guru dari tabel 'guru'
$stmt_user = $conn->prepare("SELECT * FROM guru WHERE id = ?");
$stmt_user->bind_param("i", $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

if (!$user) {
    session_destroy();
    header('Location: ../login/login.php');
    exit;
}

// Mengambil data untuk widget: Jumlah Sertifikat
$stmt_sertifikat = $conn->prepare("SELECT COUNT(id) as total_sertifikat FROM riwayat_pelatihan WHERE email = ?");
$stmt_sertifikat->bind_param("s", $email);
$stmt_sertifikat->execute();
$result_sertifikat = $stmt_sertifikat->get_result();
$sertifikat_data = $result_sertifikat->fetch_assoc();
$jumlah_sertifikat = $sertifikat_data['total_sertifikat'] ?? 0;

// Menyiapkan variabel untuk ditampilkan dengan aman
$name = htmlspecialchars($user['name'] ?? 'Nama Belum Diisi');
$nip = htmlspecialchars($user['nip'] ?? '-');
$mata_pelajaran = htmlspecialchars($user['mata_pelajaran'] ?? '-');

// Logika path foto
$photo_db_path = $user['photo'] ?? '';
$photo_url = 'uploads/default.jpg';
if (!empty($photo_db_path)) {
    if (file_exists($photo_db_path)) {
        $photo_url = htmlspecialchars($photo_db_path);
    } elseif (file_exists('../' . $photo_db_path)) {
        $photo_url = '../' . htmlspecialchars($photo_db_path);
    }
}

// Cek kelengkapan profil untuk widget "Aksi Cepat"
$isProfileComplete = !empty($user['nip']) && !empty($user['alamat']) && !empty($user['no_hp']);


// Memuat komponen-komponen layout
require_once 'layout/header.php';
require_once 'layout/sidebar.php';
?>

<div class="flex-1 flex flex-col">
    
    <header class="bg-slate-800/50 backdrop-blur-lg shadow-sm p-4 flex items-center justify-between sticky top-0 z-20">
        <button id="menu-toggle" class="md:hidden text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
        <h1 class="text-xl font-semibold text-white"><?php echo $pageTitle; ?></h1>
        <div class="flex items-center gap-4">
            <button class="text-gray-400 hover:text-white relative">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                <span class="h-2 w-2 bg-red-500 rounded-full absolute top-0 right-0"></span>
            </button>
            <div class="flex items-center gap-3">
                <img src="<?php echo $photo_url; ?>" alt="Foto Profil" class="h-9 w-9 rounded-full object-cover">
                <span class="text-sm font-medium text-white hidden sm:block"><?php echo $name; ?></span>
            </div>
        </div>
    </header>

    <main class="p-6 flex-grow">
        <h2 class="text-2xl font-bold text-white mb-6">Selamat Datang, <?php echo $name; ?>!</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg col-span-1 md:col-span-2 flex flex-col sm:flex-row items-center gap-6">
                <img src="<?php echo $photo_url; ?>" alt="Foto Guru" class="w-24 h-24 rounded-full object-cover border-2 border-blue-500 flex-shrink-0">
                <div class="text-center sm:text-left">
                    <h3 class="text-xl font-bold text-white"><?php echo $name; ?></h3>
                    <p class="text-gray-400">NIP: <?php echo $nip; ?></p>
                    <p class="text-gray-400 mt-1">Mata Pelajaran: <?php echo $mata_pelajaran; ?></p>
                    <a href="profileGuru.php" class="mt-3 inline-block bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Lihat Profil Lengkap
                    </a>
                </div>
            </div>

            <div class="bg-slate-800 p-6 rounded-lg shadow-lg flex flex-col justify-center items-center text-center">
                <p class="text-gray-400 text-sm">Total Sertifikat</p>
                <p class="text-4xl font-extrabold text-white mt-2"><?php echo $jumlah_sertifikat; ?></p>
                <a href="sertifikasi.php" class="mt-3 text-blue-400 text-sm hover:underline">Lihat Detail</a>
            </div>

            <?php if (!$isProfileComplete): ?>
            <div class="bg-yellow-500/20 border border-yellow-500 p-6 rounded-lg shadow-lg col-span-1 md:col-span-3">
                <div class="flex items-center gap-4">
                    <svg class="h-8 w-8 text-yellow-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <div>
                        <h4 class="font-bold text-white">Profil Anda Belum Lengkap</h4>
                        <p class="text-sm text-yellow-300">Harap lengkapi data NIP, alamat, dan No. HP Anda untuk mengoptimalkan sistem.</p>
                    </div>
                    <a href="profileGuru.php" class="ml-auto flex-shrink-0 bg-yellow-400 text-slate-900 text-sm font-bold px-4 py-2 rounded-lg hover:bg-yellow-300 transition-colors">Lengkapi Sekarang</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php
// Memuat footer (yang berisi script dan penutup HTML)
require_once 'layout/footer.php';
?>