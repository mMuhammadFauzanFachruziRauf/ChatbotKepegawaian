<?php
session_start();
require '../db_connection.php'; 

// Keamanan: Pastikan user sudah login dan merupakan admin/petugas
if (!isset($_SESSION['user']) || !in_array($_SESSION['user_type'], ['admin', 'petugas'])) {
    header("Location: ../login.php");
    exit();
}
$pageTitle = 'Edit Data Guru';

// Ambil & Validasi ID Guru dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manajemen_guru.php");
    exit();
}
$guru_id_to_edit = $_GET['id'];

$error = '';
$success = '';

// Ambil pesan notifikasi dari session jika ada
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Logika untuk menangani form submission (ada 2 form: profil dan password)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Jika tombol 'simpan_profil' ditekan
    if (isset($_POST['simpan_profil'])) {
        // Ambil semua data dari form profil
        $name = trim($_POST['name']);
        $nip = trim($_POST['nip']);
        $mata_pelajaran_id = $_POST['mata_pelajaran_id'];
        // ... (dan seterusnya untuk semua field)
        $gelar = trim($_POST['gelar']);
        $kelompok_mapel = trim($_POST['kelompok_mata_pelajaran']);
        $jurusan = trim($_POST['jurusan']);
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $agama = trim($_POST['agama']);
        $jenjang = trim($_POST['jenjang']);
        $pangkat = trim($_POST['pangkat']);
        $golongan = trim($_POST['golongan']);
        $alamat = trim($_POST['alamat']);
        $no_hp = trim($_POST['no_hp']);

        // Query UPDATE untuk data profil
        $query = "UPDATE guru SET name=?, nip=?, mata_pelajaran=?, kelompok_mata_pelajaran=?, gelar=?, jurusan=?, jenis_kelamin=?, agama=?, jenjang=?, pangkat=?, golongan=?, alamat=?, no_hp=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssssssssi", $name, $nip, $mata_pelajaran_id, $kelompok_mapel, $gelar, $jurusan, $jenis_kelamin, $agama, $jenjang, $pangkat, $golongan, $alamat, $no_hp, $guru_id_to_edit);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data profil guru berhasil diperbarui.";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui profil.";
        }
        header("Location: detail_guru.php?id=" . $guru_id_to_edit);
        exit();
    }

    // Jika tombol 'reset_password' ditekan
    if (isset($_POST['reset_password'])) {
        $new_password = $_POST['new_password'];
        if (empty($new_password) || strlen($new_password) < 8) {
            $_SESSION['error_message'] = "Password baru minimal harus 8 karakter.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE guru SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $guru_id_to_edit);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Password guru berhasil direset.";
            } else {
                $_SESSION['error_message'] = "Gagal mereset password.";
            }
        }
        header("Location: edit_guru.php?id=" . $guru_id_to_edit);
        exit();
    }
}

// Ambil data guru yang akan diedit untuk ditampilkan di form
$stmt_guru = $conn->prepare("SELECT * FROM guru WHERE id = ?");
$stmt_guru->bind_param("i", $guru_id_to_edit);
$stmt_guru->execute();
$guru = $stmt_guru->get_result()->fetch_assoc();

if (!$guru) {
    die("Data guru tidak ditemukan.");
}

// Ambil semua data mata pelajaran untuk dropdown
$mapel_list = $conn->query("SELECT id, nama_mapel FROM guru_mapel ORDER BY nama_mapel ASC")->fetch_all(MYSQLI_ASSOC);

require_once 'layout/header.php';
require_once 'layout/sidebar.php';
?>

<div class="flex-1 flex flex-col">
    <header class="bg-slate-800/50 backdrop-blur-lg shadow-sm p-4 flex items-center justify-between sticky top-0 z-20">
        <button id="menu-toggle" class="md:hidden text-white"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
        <h1 class="text-xl font-semibold text-white"><?php echo $pageTitle; ?></h1>
        <div class="w-10"></div>
    </header>

    <main class="p-6 flex-grow">
        <div class="mb-6"><a href="detail_guru.php?id=<?php echo $guru['id']; ?>" class="flex items-center gap-2 text-sm text-sky-400 hover:text-sky-300"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>Kembali ke Detail Guru</a></div>
        
        <?php if ($success): ?><div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $success; ?></p></div><?php endif; ?>
        <?php if ($error): ?><div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $error; ?></p></div><?php endif; ?>

        <form method="POST" action="edit_guru.php?id=<?php echo $guru['id']; ?>" class="space-y-6">
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Edit Informasi Guru: <?php echo htmlspecialchars($guru['name']); ?></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="name" class="block text-sm font-medium text-gray-300">Nama Lengkap</label><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($guru['name']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="nip" class="block text-sm font-medium text-gray-300">NIP</label><input type="text" name="nip" id="nip" value="<?php echo htmlspecialchars($guru['nip']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    
                    <div>
                        <label for="mata_pelajaran_id" class="block text-sm font-medium text-gray-300">Mata Pelajaran Utama</label>
                        <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                            <?php foreach ($mapel_list as $mapel): ?>
                                <option value="<?php echo $mapel['id']; ?>" <?php echo ($guru['mata_pelajaran'] == $mapel['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mapel['nama_mapel']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div><label for="kelompok_mata_pelajaran" class="block text-sm font-medium text-gray-300">Kelompok Mapel</label><input type="text" name="kelompok_mata_pelajaran" id="kelompok_mata_pelajaran" value="<?php echo htmlspecialchars($guru['kelompok_mata_pelajaran']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="pangkat" class="block text-sm font-medium text-gray-300">Pangkat</label><input type="text" name="pangkat" id="pangkat" value="<?php echo htmlspecialchars($guru['pangkat']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="golongan" class="block text-sm font-medium text-gray-300">Golongan</label><input type="text" name="golongan" id="golongan" value="<?php echo htmlspecialchars($guru['golongan']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="gelar" class="block text-sm font-medium text-gray-300">Gelar</label><input type="text" name="gelar" id="gelar" value="<?php echo htmlspecialchars($guru['gelar']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="jenjang" class="block text-sm font-medium text-gray-300">Jenjang</label><input type="text" name="jenjang" id="jenjang" value="<?php echo htmlspecialchars($guru['jenjang']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="jurusan" class="block text-sm font-medium text-gray-300">Program Studi/Jurusan</label><input type="text" name="jurusan" id="jurusan" value="<?php echo htmlspecialchars($guru['jurusan']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="agama" class="block text-sm font-medium text-gray-300">Agama</label><input type="text" name="agama" id="agama" value="<?php echo htmlspecialchars($guru['agama']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="jenis_kelamin" class="block text-sm font-medium text-gray-300">Jenis Kelamin</label><select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"><option value="Laki-Laki" <?php echo ($guru['jenis_kelamin'] ?? '') == 'Laki-Laki' ? 'selected' : ''; ?>>Laki-Laki</option><option value="Perempuan" <?php echo ($guru['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option></select></div>
                    <div><label for="no_hp" class="block text-sm font-medium text-gray-300">Nomor Handphone</label><input type="text" name="no_hp" id="no_hp" value="<?php echo htmlspecialchars($guru['no_hp']); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div class="md:col-span-2"><label for="alamat" class="block text-sm font-medium text-gray-300">Alamat</label><textarea name="alamat" id="alamat" rows="3" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($guru['alamat']); ?></textarea></div>
                </div>
            </div>
            <div class="flex justify-end gap-4"><button type="submit" name="simpan_profil" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Simpan Perubahan</button></div>
        </form>

        <form method="POST" action="edit_guru.php?id=<?php echo $guru['id']; ?>" class="mt-6">
             <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Reset Password</h3>
                <p class="text-sm text-gray-400 mb-4">Masukkan password baru untuk guru ini. Guru akan perlu menggunakan password baru ini saat login berikutnya.</p>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-300">Password Baru</label>
                    <input type="password" name="new_password" id="new_password" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="text-right mt-4">
                    <button type="submit" name="reset_password" class="bg-amber-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-amber-700 transition-colors">Reset Password</button>
                </div>
            </div>
        </form>
    </main>
</div>

<?php require_once 'layout/footer.php'; ?>