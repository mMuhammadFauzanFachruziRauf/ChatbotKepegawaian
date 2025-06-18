<?php
session_start();

// Pengecekan Sesi & Tipe Pengguna
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'guru') {
    header('Location: ../login/login.php');
    exit;
}

require '../db_connection.php';
$pageTitle = 'Sertifikasi & Pelatihan'; // Judul untuk halaman ini

$userId = $_SESSION['user']['id'];
$email = $_SESSION['user']['email'];
$error = '';
$success = '';

// Logika untuk Menghapus Data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    
    // Keamanan: Cek dulu apakah data yang akan dihapus benar-benar milik user yang login
    $stmt_check = $conn->prepare("SELECT sertifikat, email FROM riwayat_pelatihan WHERE id = ?");
    $stmt_check->bind_param("i", $delete_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $item_to_delete = $result_check->fetch_assoc();

    if ($item_to_delete && $item_to_delete['email'] === $email) {
        // Hapus file fisik dari server jika ada. Path disesuaikan.
        if (!empty($item_to_delete['sertifikat']) && file_exists($item_to_delete['sertifikat'])) {
            unlink($item_to_delete['sertifikat']);
        }

        // Hapus record dari database
        $stmt_delete = $conn->prepare("DELETE FROM riwayat_pelatihan WHERE id = ?");
        $stmt_delete->bind_param("i", $delete_id);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Data pelatihan berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus data.";
        }
        header("Location: sertifikasi.php");
        exit();
    }
}


// Logika untuk Menambah Data Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pelatihan'])) {
    $nama_pelatihan = trim($_POST['nama_pelatihan']);
    $lama_pelatihan = trim($_POST['lama_pelatihan']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_berakhir = $_POST['tanggal_berakhir'];
    
    $sertifikat_path_db = null;

    if (empty($nama_pelatihan) || empty($lama_pelatihan) || empty($tanggal_mulai) || empty($tanggal_berakhir)) {
        $error = "Semua kolom teks wajib diisi.";
    } else {
        if (!empty($_FILES['sertifikat']['name'])) {
            // =================================================================
            // PERBAIKAN 1: Path upload diubah (tanpa ../)
            // =================================================================
            $uploadDir = "uploads/sertifikat/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = basename($_FILES['sertifikat']['name']);
            $fileType = mime_content_type($_FILES['sertifikat']['tmp_name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if ($fileExt === "pdf" && $fileType === "application/pdf") {
                $unique_filename = time() . "_" . $fileName;
                $targetFilePath = $uploadDir . $unique_filename;
                
                if (move_uploaded_file($_FILES['sertifikat']['tmp_name'], $targetFilePath)) {
                    // Path yang disimpan ke DB juga disesuaikan
                    $sertifikat_path_db = $targetFilePath;
                } else {
                     $error = "Gagal mengunggah file.";
                }
            } else {
                $error = "Hanya file dengan format PDF yang diperbolehkan.";
            }
        } else {
            $error = "File sertifikat wajib diunggah.";
        }
    }
    
    if (empty($error) && $sertifikat_path_db) {
        $stmt_insert = $conn->prepare("INSERT INTO riwayat_pelatihan (email, nama_pelatihan, lama_pelatihan, tanggal_mulai, tanggal_berakhir, sertifikat) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("ssssss", $email, $nama_pelatihan, $lama_pelatihan, $tanggal_mulai, $tanggal_berakhir, $sertifikat_path_db);
        if ($stmt_insert->execute()) {
            $_SESSION['success_message'] = "Data pelatihan baru berhasil ditambahkan.";
        } else {
            $_SESSION['error_message'] = "Gagal menyimpan data ke database.";
        }
        header("Location: sertifikasi.php");
        exit();
    }
}

// Ambil pesan notifikasi dari session
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Ambil semua data riwayat pelatihan untuk ditampilkan
$stmt_fetch = $conn->prepare("SELECT * FROM riwayat_pelatihan WHERE email = ? ORDER BY tanggal_mulai DESC");
$stmt_fetch->bind_param("s", $email);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$trainings = $result->fetch_all(MYSQLI_ASSOC);


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
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg text-center mb-6">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center mb-6">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-slate-800 p-6 rounded-lg shadow-lg mb-8">
            <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Tambah Riwayat Pelatihan Baru</h3>
            <form action="sertifikasi.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_pelatihan" class="block text-sm font-medium text-gray-300">Nama Pelatihan</label>
                        <input type="text" name="nama_pelatihan" id="nama_pelatihan" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="lama_pelatihan" class="block text-sm font-medium text-gray-300">Lama Pelatihan (Contoh: 1 Bulan)</label>
                        <input type="text" name="lama_pelatihan" id="lama_pelatihan" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-300">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="tanggal_berakhir" class="block text-sm font-medium text-gray-300">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" id="tanggal_berakhir" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="sertifikat" class="block text-sm font-medium text-gray-300">Upload Sertifikat (Hanya PDF)</label>
                        <input type="file" name="sertifikat" id="sertifikat" accept=".pdf" required class="mt-1 block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"/>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" name="tambah_pelatihan" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Tambah Pelatihan</button>
                </div>
            </form>
        </div>

        <div>
            <h3 class="text-xl font-bold text-white mb-4">Riwayat Pelatihan Anda</h3>
            <div class="space-y-4">
                <?php if (empty($trainings)): ?>
                    <div class="bg-slate-800 p-8 rounded-lg text-center text-gray-400">Anda belum menambahkan riwayat pelatihan.</div>
                <?php else: ?>
                    <?php foreach ($trainings as $train): ?>
                        <div class="bg-slate-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="flex-shrink-0 text-blue-400">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-white"><?php echo htmlspecialchars($train['nama_pelatihan']); ?></h4>
                                <p class="text-sm text-gray-400">
                                    <span class="font-semibold">Durasi:</span> <?php echo htmlspecialchars($train['lama_pelatihan']); ?> | 
                                    <span class="font-semibold">Periode:</span> <?php echo date('d M Y', strtotime($train['tanggal_mulai'])) . " - " . date('d M Y', strtotime($train['tanggal_berakhir'])); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-2 mt-2 sm:mt-0">
                                <?php if (!empty($train['sertifikat']) && file_exists($train['sertifikat'])): ?>
                                    <a href="<?php echo htmlspecialchars($train['sertifikat']); ?>" target="_blank" class="text-sm bg-slate-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-slate-500 transition-colors">Lihat PDF</a>
                                <?php endif; ?>
                                <a href="sertifikasi.php?action=delete&id=<?php echo $train['id']; ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini? File sertifikat yang terhubung juga akan dihapus secara permanen.');"
                                   class="text-sm bg-red-600 text-white font-semibold px-3 py-1.5 rounded-md hover:bg-red-700 transition-colors">
                                   Hapus
                                </a>
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