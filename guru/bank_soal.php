<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'guru') {
    header('Location: ../login/login.php');
    exit;
}

require '../db_connection.php';
$pageTitle = 'Bank Soal Pribadi';

$guru_id = $_SESSION['user']['id'];
$error = '';
$success = '';

// Logika Hapus File
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    $stmt_check = $conn->prepare("SELECT path_file, id_guru_uploader FROM bank_soal WHERE id = ? AND id_guru_uploader = ?");
    $stmt_check->bind_param("ii", $delete_id, $guru_id);
    $stmt_check->execute();
    $item_to_delete = $stmt_check->get_result()->fetch_assoc();

    if ($item_to_delete) {
        if (!empty($item_to_delete['path_file']) && file_exists('../' . $item_to_delete['path_file'])) {
            unlink('../' . $item_to_delete['path_file']);
        }
        $stmt_delete = $conn->prepare("DELETE FROM bank_soal WHERE id = ?");
        $stmt_delete->bind_param("i", $delete_id);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "File soal berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus data.";
        }
    } else {
        $_SESSION['error_message'] = "Anda tidak memiliki izin untuk menghapus file ini.";
    }
    header("Location: bank_soal.php");
    exit();
}

// Logika Tambah File Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_soal'])) {
    $judul_soal = trim($_POST['judul_soal']);
    $mata_pelajaran = trim($_POST['mata_pelajaran']);
    $kelas = trim($_POST['kelas']);
    $jenis_ujian = trim($_POST['jenis_ujian']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($judul_soal) || empty($mata_pelajaran) || empty($kelas) || empty($_FILES['file_soal']['name'])) {
        $error = "Judul, Mata Pelajaran, Kelas, dan File Soal wajib diisi.";
    } else {
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        $nama_file_asli = basename($_FILES['file_soal']['name']);
        $file_ext = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            $uploadDir = "../uploads/bank_soal/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $tipe_file = $_FILES['file_soal']['type'];
            $ukuran_file = $_FILES['file_soal']['size'];
            $unique_filename = uniqid($guru_id . '_soal_', true) . '.' . $file_ext;
            $path_file_server = $uploadDir . $unique_filename;
            $path_db = "uploads/bank_soal/" . $unique_filename;

            if (move_uploaded_file($_FILES['file_soal']['tmp_name'], $path_file_server)) {
                $stmt = $conn->prepare("INSERT INTO bank_soal (judul_soal, mata_pelajaran, kelas, jenis_ujian, deskripsi, nama_file_asli, path_file, tipe_file, ukuran_file, id_guru_uploader) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssii", $judul_soal, $mata_pelajaran, $kelas, $jenis_ujian, $deskripsi, $nama_file_asli, $path_db, $tipe_file, $ukuran_file, $guru_id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "File soal berhasil diunggah.";
                } else {
                    $_SESSION['error_message'] = "Gagal menyimpan data.";
                }
            } else {
                $error = "Gagal mengunggah file.";
            }
        } else {
            $error = "Tipe file tidak diizinkan. Hanya file PDF, DOC, dan DOCX.";
        }
        
        if(empty($error)) {
            header("Location: bank_soal.php");
            exit();
        }
    }
}

// Ambil pesan notifikasi
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Ambil semua data bank soal milik guru yang login
$stmt_fetch = $conn->prepare("SELECT * FROM bank_soal WHERE id_guru_uploader = ? ORDER BY tanggal_upload DESC");
$stmt_fetch->bind_param("i", $guru_id);
$stmt_fetch->execute();
$all_files = $stmt_fetch->get_result()->fetch_all(MYSQLI_ASSOC);


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
        <?php if ($success): ?><div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $success; ?></p></div><?php endif; ?>
        <?php if ($error): ?><div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $error; ?></p></div><?php endif; ?>

        <div class="bg-slate-800 p-6 rounded-lg shadow-lg mb-8">
            <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Unggah File Soal Baru</h3>
            <form action="bank_soal.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="judul_soal" class="block text-sm font-medium text-gray-300">Judul Soal/Ujian</label>
                        <input type="text" name="judul_soal" id="judul_soal" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="mata_pelajaran" class="block text-sm font-medium text-gray-300">Mata Pelajaran</label>
                        <input type="text" name="mata_pelajaran" id="mata_pelajaran" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-300">Kelas</label>
                        <input type="text" name="kelas" id="kelas" placeholder="Contoh: XII TKJ" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="jenis_ujian" class="block text-sm font-medium text-gray-300">Jenis Ujian</label>
                        <select name="jenis_ujian" id="jenis_ujian" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="UAS">UAS</option>
                            <option value="UTS">UTS</option>
                            <option value="Kuis">Kuis</option>
                            <option value="Latihan">Latihan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-300">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi" rows="2" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="file_soal" class="block text-sm font-medium text-gray-300">Pilih File Soal</label>
                        <input type="file" name="file_soal" id="file_soal" required class="mt-1 block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"/>
                        <p class="text-xs text-gray-500 mt-1">Tipe file yang diizinkan: PDF, DOC, DOCX.</p>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" name="upload_soal" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Unggah Soal</button>
                </div>
            </form>
        </div>

        <div>
            <h3 class="text-xl font-bold text-white mb-4">Koleksi Soal Pribadi Anda</h3>
            <div class="space-y-4">
                <?php if (empty($all_files)): ?>
                    <div class="bg-slate-800 p-8 rounded-lg text-center text-gray-400">Anda belum memiliki koleksi soal. Silakan unggah file soal pertama Anda.</div>
                <?php else: ?>
                    <?php 
                        // Daftar tipe file yang bisa dilihat langsung di browser
                        $viewable_types = ['application/pdf'];
                    ?>
                    <?php foreach ($all_files as $file): ?>
                        <div class="bg-slate-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="flex-shrink-0 text-sky-400"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-white"><?php echo htmlspecialchars($file['judul_soal']); ?></h4>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs mt-1 mb-2">
                                    <span class="bg-blue-900/50 text-blue-300 px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($file['mata_pelajaran']); ?></span>
                                    <span class="bg-emerald-900/50 text-emerald-300 px-2 py-0.5 rounded-full">Kelas <?php echo htmlspecialchars($file['kelas']); ?></span>
                                    <span class="bg-amber-900/50 text-amber-300 px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($file['jenis_ujian']); ?></span>
                                </div>
                                <p class="text-sm text-gray-400"><?php echo htmlspecialchars($file['deskripsi']); ?></p>
                                <p class="text-xs text-gray-500 mt-1">Diunggah pada <?php echo date('d M Y', strtotime($file['tanggal_upload'])); ?></p>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-2 mt-2 sm:mt-0">
                                <?php if (in_array($file['tipe_file'], $viewable_types)): ?>
                                    <a href="../<?php echo htmlspecialchars($file['path_file']); ?>" target="_blank" class="text-sm bg-sky-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-sky-700 transition-colors">Lihat</a>
                                <?php endif; ?>
                                
                                <a href="../<?php echo htmlspecialchars($file['path_file']); ?>" download="<?php echo htmlspecialchars($file['nama_file_asli']); ?>" class="text-sm bg-slate-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-slate-500 transition-colors">Download</a>
                                
                                <?php if ($file['id_guru_uploader'] == $guru_id): ?>
                                    <a href="bank_soal.php?action=delete&id=<?php echo $file['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus file soal ini?');" class="text-sm bg-red-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-red-700 transition-colors">Hapus</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php
require_once 'layout/footer.php';
?>