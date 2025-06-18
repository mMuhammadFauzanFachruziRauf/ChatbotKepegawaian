<?php
session_start();

// Pengecekan Sesi & Tipe Pengguna
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'guru') {
    header('Location: ../login/login.php');
    exit;
}

require '../db_connection.php';
$pageTitle = 'Profil Saya'; // Judul untuk halaman ini

// Pengambilan Data Pengguna dari Sesi
$userId = $_SESSION['user']['id'];

// Query yang benar: menggunakan LEFT JOIN untuk mengambil nama mata pelajaran
$stmt = $conn->prepare("
    SELECT g.*, gm.nama_mapel 
    FROM guru g
    LEFT JOIN guru_mapel gm ON g.mata_pelajaran = gm.id
    WHERE g.id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$guru = $result->fetch_assoc();

// Jika data guru tidak ditemukan, hentikan eksekusi
if (!$guru) {
    die("Data guru tidak ditemukan.");
}

// Logika path foto yang sudah kita perbaiki sebelumnya
$photo_db_path = $guru['photo'] ?? '';
$photo_url = 'uploads/default.jpg'; // Path default relatif terhadap folder /guru
if (!empty($photo_db_path)) {
    if (file_exists($photo_db_path)) {
        $photo_url = htmlspecialchars($photo_db_path);
    } elseif (file_exists('../' . $photo_db_path)) {
        $photo_url = '../' . htmlspecialchars($photo_db_path);
    }
}

// Menyiapkan semua variabel untuk ditampilkan dengan aman (hanya satu blok, tidak duplikat)
$nama = htmlspecialchars($guru['name'] ?? "Belum Diisi");
$gelar = htmlspecialchars($guru['gelar'] ?? "-");
$mata_pelajaran = htmlspecialchars($guru['nama_mapel'] ?? $guru['mata_pelajaran'] ?? "-"); 
$kelompok_mapel = htmlspecialchars($guru['kelompok_mata_pelajaran'] ?? "-");
$jurusan = htmlspecialchars($guru['jurusan'] ?? "-");
$jenis_kelamin = htmlspecialchars($guru['jenis_kelamin'] ?? "-");
$agama = htmlspecialchars($guru['agama'] ?? "-");
$jenjang = htmlspecialchars($guru['jenjang'] ?? "-");
$pangkat = htmlspecialchars($guru['pangkat'] ?? "-");
$golongan = htmlspecialchars($guru['golongan'] ?? "-");
$nip = htmlspecialchars($guru['nip'] ?? "-");
$email_utama = htmlspecialchars($guru['email'] ?? "-");
$alamat = htmlspecialchars($guru['alamat'] ?? "-");
$no_hp = htmlspecialchars($guru['no_hp'] ?? "-");


// Memuat komponen layout
require_once 'layout/header.php';
require_once 'layout/sidebar.php';
?>

<div class="flex-1 flex flex-col">

    <header class="bg-slate-800/50 backdrop-blur-lg shadow-sm p-4 flex items-center justify-between sticky top-0 z-20">
        <button id="menu-toggle" class="md:hidden text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
        <h1 class="text-xl font-semibold text-white"><?php echo $pageTitle; ?></h1>
        <div class="w-10"></div>
    </header>

    <main class="p-6 flex-grow">
        <div class="mb-6">
            <a href="index.php" class="flex items-center gap-2 text-sm text-sky-400 hover:text-sky-300">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Beranda
            </a>
        </div>
        
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg text-center mb-6">
                <p><?php echo $_SESSION['success_message']; ?></p>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-800 p-6 rounded-lg shadow-lg text-center">
                    <img src="<?php echo $photo_url; ?>" alt="Foto Profil" class="w-32 h-32 rounded-full object-cover mx-auto mb-4 border-4 border-blue-500">
                    <h2 class="text-2xl font-bold text-white"><?php echo $nama; ?></h2>
                    <p class="text-sm text-gray-400">NIP: <?php echo $nip; ?></p>
                    <a href="editProfile.php" class="mt-4 inline-block w-full bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Perbaiki Profil
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Informasi Jabatan & Akademik</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="sm:col-span-1"><dt class="text-gray-400">Pangkat</dt><dd class="text-white mt-1"><?php echo $pangkat; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Golongan</dt><dd class="text-white mt-1"><?php echo $golongan; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Mata Pelajaran</dt><dd class="text-white mt-1"><?php echo $mata_pelajaran; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Kelompok Mapel</dt><dd class="text-white mt-1"><?php echo $kelompok_mapel; ?></dd></div>
                        <div class="sm:col-span-2"><dt class="text-gray-400">Program Studi/Jurusan</dt><dd class="text-white mt-1"><?php echo $jurusan; ?></dd></div>
                    </dl>
                </div>

                <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Data Pribadi</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="sm:col-span-1"><dt class="text-gray-400">Gelar</dt><dd class="text-white mt-1"><?php echo $gelar; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Jenjang</dt><dd class="text-white mt-1"><?php echo $jenjang; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Jenis Kelamin</dt><dd class="text-white mt-1"><?php echo $jenis_kelamin; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Agama</dt><dd class="text-white mt-1"><?php echo $agama; ?></dd></div>
                        <div class="sm:col-span-2"><dt class="text-gray-400">Alamat</dt><dd class="text-white mt-1"><?php echo $alamat; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Nomor Handphone</dt><dd class="text-white mt-1"><?php echo $no_hp; ?></dd></div>
                        <div class="sm:col-span-1"><dt class="text-gray-400">Email Utama</dt><dd class="text-white mt-1"><?php echo $email_utama; ?></dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
// Memuat footer
require_once 'layout/footer.php';
?>