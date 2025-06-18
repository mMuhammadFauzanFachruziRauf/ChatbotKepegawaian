<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'guru') {
    header('Location: ../login/login.php');
    exit;
}

require '../db_connection.php';
$pageTitle = 'Bahan Ajar Pribadi';

$guru_id = $_SESSION['user']['id'];
$error = '';
$success = '';

// Logika Hapus File
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    // Keamanan: Pastikan file yang dihapus adalah milik user yang login
    $stmt_check = $conn->prepare("SELECT path_file, id_guru_uploader FROM bahan_ajar WHERE id = ? AND id_guru_uploader = ?");
    $stmt_check->bind_param("ii", $delete_id, $guru_id);
    $stmt_check->execute();
    $item_to_delete = $stmt_check->get_result()->fetch_assoc();

    if ($item_to_delete) {
        // Hapus file fisik dari server
        if (!empty($item_to_delete['path_file']) && file_exists('../' . $item_to_delete['path_file'])) {
            unlink('../' . $item_to_delete['path_file']);
        }
        // Hapus record dari database
        $stmt_delete = $conn->prepare("DELETE FROM bahan_ajar WHERE id = ?");
        $stmt_delete->bind_param("i", $delete_id);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Bahan ajar berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus data dari database.";
        }
    } else {
        $_SESSION['error_message'] = "Anda tidak memiliki izin untuk menghapus file ini.";
    }
    header("Location: bahan_ajar.php");
    exit();
}

// Logika Tambah File Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_bahan_ajar'])) {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($judul) || empty($_FILES['file_ajar']['name'])) {
        $error = "Judul dan File wajib diisi.";
    } else {
        $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'txt'];
        $nama_file_asli = basename($_FILES['file_ajar']['name']);
        $file_ext = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            $uploadDir = "../uploads/bahan_ajar/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $tipe_file = $_FILES['file_ajar']['type'];
            $ukuran_file = $_FILES['file_ajar']['size'];
            $unique_filename = uniqid($guru_id . '_', true) . '.' . $file_ext;
            $path_file_server = $uploadDir . $unique_filename;
            
            $path_db = "uploads/bahan_ajar/" . $unique_filename;

            if (move_uploaded_file($_FILES['file_ajar']['tmp_name'], $path_file_server)) {
                $stmt = $conn->prepare("INSERT INTO bahan_ajar (judul, deskripsi, nama_file_asli, path_file, tipe_file, ukuran_file, id_guru_uploader) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssii", $judul, $deskripsi, $nama_file_asli, $path_db, $tipe_file, $ukuran_file, $guru_id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Bahan ajar berhasil diunggah.";
                } else {
                    $_SESSION['error_message'] = "Gagal menyimpan data ke database.";
                }
            } else {
                $error = "Gagal mengunggah file.";
            }
        } else {
            $error = "Tipe file tidak diizinkan.";
        }
        
        if(empty($error)) {
            header("Location: bahan_ajar.php");
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

// =================================================================
// PERBAIKAN UTAMA: Query diubah untuk hanya mengambil file milik guru yang login
// =================================================================
$stmt_fetch = $conn->prepare("SELECT * FROM bahan_ajar WHERE id_guru_uploader = ? ORDER BY tanggal_upload DESC");
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
            <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Unggah Bahan Ajar Baru</h3>
            <form action="bahan_ajar.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="judul" class="block text-sm font-medium text-gray-300">Judul Bahan Ajar</label>
                        <input type="text" name="judul" id="judul" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-300">Deskripsi Singkat</label>
                        <textarea name="deskripsi" id="deskripsi" rows="2" class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="file_ajar" class="block text-sm font-medium text-gray-300">Pilih File</label>
                        <input type="file" name="file_ajar" id="file_ajar" required class="mt-1 block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"/>
                        <p class="text-xs text-gray-500 mt-1">Tipe file yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG.</p>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" name="upload_bahan_ajar" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Unggah File</button>
                </div>
            </form>
        </div>

        <div>
            <h3 class="text-xl font-bold text-white mb-4">Kabinet Bahan Ajar Anda</h3>
            <div class="space-y-4">
                <?php if (empty($all_files)): ?>
                    <div class="bg-slate-800 p-8 rounded-lg text-center text-gray-400">Kabinet Anda masih kosong. Silakan unggah bahan ajar pertama Anda.</div>
                <?php else: ?>
                    <?php 
                        $viewable_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
                    ?>
                    <?php foreach ($all_files as $file): ?>
                        <div class="bg-slate-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="flex-shrink-0 text-blue-400"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg></div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-white"><?php echo htmlspecialchars($file['judul']); ?></h4>
                                <p class="text-sm text-gray-400"><?php echo htmlspecialchars($file['deskripsi']); ?></p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Tanggal Unggah: <?php echo date('d M Y, H:i', strtotime($file['tanggal_upload'])); ?> | 
                                    Ukuran: <?php echo round($file['ukuran_file'] / 1024, 2); ?> KB
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-2 mt-2 sm:mt-0">
                                <?php if (in_array($file['tipe_file'], $viewable_types)): ?>
                                    <a href="../<?php echo htmlspecialchars($file['path_file']); ?>" target="_blank" class="text-sm bg-sky-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-sky-700 transition-colors">Lihat</a>
                                <?php endif; ?>
                                
                                <a href="../<?php echo htmlspecialchars($file['path_file']); ?>" download="<?php echo htmlspecialchars($file['nama_file_asli']); ?>" class="text-sm bg-slate-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-slate-500 transition-colors">Download</a>
                                
                                <a href="bahan_ajar.php?action=delete&id=<?php echo $file['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus file ini secara permanen?');" class="text-sm bg-red-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-red-700 transition-colors">Hapus</a>
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