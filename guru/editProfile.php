<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'guru') {
    header('Location: ../login/login.php');
    exit;
}
require '../db_connection.php';
$pageTitle = 'Edit Profil';
$userId = $_SESSION['user']['id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    // ... (ambil semua data POST lainnya) ...
    $gelar = trim($_POST['gelar']);
    $jurusan = trim($_POST['jurusan']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $agama = trim($_POST['agama']);
    $jenjang = trim($_POST['jenjang']);
    $pangkat = trim($_POST['pangkat']);
    $golongan = trim($_POST['golongan']);
    $nip = trim($_POST['nip']);
    $alamat = trim($_POST['alamat']);
    $no_hp = trim($_POST['no_hp']);

    if (empty($name)) {
        $error = "Nama lengkap tidak boleh kosong.";
    } else {
        $new_photo_path_db = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['photo']['type'], $allowed_types)) {
                
                // =================================================================
                // PERBAIKAN LOKASI UPLOAD DI SINI
                // =================================================================
                $uploadDir = "uploads/"; // Path relatif dari file ini (guru/uploads/)
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $unique_filename = 'profile_' . uniqid() . '.' . $file_extension;
                $target_file = $uploadDir . $unique_filename;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    // Path yang disimpan ke DB juga disesuaikan
                    $new_photo_path_db = $target_file; 
                } else {
                    $error = "Gagal memindahkan file yang diunggah.";
                }
            } else {
                $error = "Tipe file tidak diizinkan.";
            }
        }

        if (empty($error)) {
            $query = "UPDATE guru SET name=?, gelar=?, jurusan=?, jenis_kelamin=?, agama=?, jenjang=?, pangkat=?, golongan=?, nip=?, alamat=?, no_hp=?";
            $params = [$name, $gelar, $jurusan, $jenis_kelamin, $agama, $jenjang, $pangkat, $golongan, $nip, $alamat, $no_hp];
            $types = "sssssssssss";

            if ($new_photo_path_db) {
                $query .= ", photo=?";
                $params[] = $new_photo_path_db;
                $types .= "s";
            }
            
            $query .= " WHERE id=?";
            $params[] = $userId;
            $types .= "i";

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Profil berhasil diperbarui!";
                header('Location: profileGuru.php');
                exit;
            } else {
                $error = "Gagal memperbarui profil: " . $stmt->error;
            }
        }
    }
}

$stmt_fetch = $conn->prepare("SELECT * FROM guru WHERE id = ?");
$stmt_fetch->bind_param("i", $userId);
$stmt_fetch->execute();
$guru = $stmt_fetch->get_result()->fetch_assoc();

if (!$guru) { die("Data guru tidak ditemukan."); }

// Logika path foto yang sudah benar
$photo_db_path = $guru['photo'] ?? '';
$photo_url = 'uploads/default.jpg'; // Path default relatif dari folder guru
if (!empty($photo_db_path) && file_exists($photo_db_path)) {
    $photo_url = htmlspecialchars($photo_db_path);
}

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
        <form method="POST" action="editProfile.php" enctype="multipart/form-data" class="space-y-6">
            <?php if (!empty($error)): ?><div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center"><p><?php echo htmlspecialchars($error); ?></p></div><?php endif; ?>
            
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Foto Profil</h3>
                <div class="flex items-center gap-6">
                    <img src="<?php echo $photo_url; ?>" alt="Foto Profil Saat Ini" class="w-24 h-24 rounded-full object-cover">
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-300">Ganti Foto Profil</label>
                        <input type="file" name="photo" id="photo" class="mt-1 block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"/>
                        <p class="text-xs text-gray-500 mt-1">Hanya format JPG, PNG, atau GIF.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Informasi Detail</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="name" class="block text-sm font-medium text-gray-300">Nama Lengkap</label><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($guru['name'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="nip" class="block text-sm font-medium text-gray-300">NIP</label><input type="text" name="nip" id="nip" value="<?php echo htmlspecialchars($guru['nip'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="pangkat" class="block text-sm font-medium text-gray-300">Pangkat</label><input type="text" name="pangkat" id="pangkat" value="<?php echo htmlspecialchars($guru['pangkat'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="golongan" class="block text-sm font-medium text-gray-300">Golongan</label><input type="text" name="golongan" id="golongan" value="<?php echo htmlspecialchars($guru['golongan'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="gelar" class="block text-sm font-medium text-gray-300">Gelar</label><input type="text" name="gelar" id="gelar" value="<?php echo htmlspecialchars($guru['gelar'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="jenjang" class="block text-sm font-medium text-gray-300">Jenjang</label><input type="text" name="jenjang" id="jenjang" value="<?php echo htmlspecialchars($guru['jenjang'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="jurusan" class="block text-sm font-medium text-gray-300">Program Studi/Jurusan</label><input type="text" name="jurusan" id="jurusan" value="<?php echo htmlspecialchars($guru['jurusan'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="agama" class="block text-sm font-medium text-gray-300">Agama</label><input type="text" name="agama" id="agama" value="<?php echo htmlspecialchars($guru['agama'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="jenis_kelamin" class="block text-sm font-medium text-gray-300">Jenis Kelamin</label><select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"><option value="Laki-Laki" <?php echo ($guru['jenis_kelamin'] ?? '') == 'Laki-Laki' ? 'selected' : ''; ?>>Laki-Laki</option><option value="Perempuan" <?php echo ($guru['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option></select></div>
                    <div><label for="no_hp" class="block text-sm font-medium text-gray-300">Nomor Handphone</label><input type="text" name="no_hp" id="no_hp" value="<?php echo htmlspecialchars($guru['no_hp'] ?? ''); ?>" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></div>
                    <div class="md:col-span-2"><label for="alamat" class="block text-sm font-medium text-gray-300">Alamat</label><textarea name="alamat" id="alamat" rows="3" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($guru['alamat'] ?? ''); ?></textarea></div>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="profileGuru.php" class="bg-slate-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-slate-500 transition-colors">Batal</a>
                <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </main>
</div>
<?php require_once 'layout/footer.php'; ?>