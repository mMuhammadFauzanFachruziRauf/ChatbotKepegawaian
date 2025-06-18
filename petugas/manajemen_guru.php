<?php
session_start();
require '../db_connection.php'; 

// Keamanan: Pastikan user sudah login dan merupakan admin/petugas
if (!isset($_SESSION['user']) || !in_array($_SESSION['user_type'], ['admin', 'petugas'])) {
    header("Location: ../login.php");
    exit();
}
$pageTitle = 'Manajemen Guru';
$error = '';
$success = '';

// =================================================================
// FITUR BARU: Logika untuk Menghapus Data Guru
// =================================================================
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);

    // 1. Ambil path foto sebelum menghapus record dari DB
    $stmt_photo = $conn->prepare("SELECT photo FROM guru WHERE id = ?");
    $stmt_photo->bind_param("i", $delete_id);
    $stmt_photo->execute();
    $photo_result = $stmt_photo->get_result()->fetch_assoc();
    $stmt_photo->close();

    // 2. Hapus record guru dari database
    $stmt_delete = $conn->prepare("DELETE FROM guru WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);

    if ($stmt_delete->execute()) {
        // 3. Jika record DB berhasil dihapus, hapus file fotonya dari server
        if ($photo_result && !empty($photo_result['photo'])) {
            $file_path_from_guru_dir = $photo_result['photo']; // e.g., 'uploads/profile_xyz.jpg'
            $actual_file_path = __DIR__ . '/' . $file_path_from_guru_dir; // Path dari folder /petugas ke /guru/uploads/...

            // Path yang benar dari /petugas adalah ../guru/
            $correct_server_path = realpath(__DIR__ . '/../guru/' . $photo_db_path);

            if (file_exists($correct_server_path)) {
                unlink($correct_server_path);
            }
        }
        $_SESSION['success_message'] = "Data guru beserta semua file terkait telah berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data guru.";
    }
    $stmt_delete->close();
    header("Location: manajemen_guru.php");
    exit();
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


// Logika untuk Pencarian
$search_query = $_GET['search'] ?? '';
$sql = "
    SELECT g.id, g.name, g.nip, g.email, gm.nama_mapel 
    FROM guru g
    LEFT JOIN guru_mapel gm ON g.mata_pelajaran = gm.id
";
$params = [];
$types = "";

if (!empty($search_query)) {
    $sql .= " WHERE g.name LIKE ? OR g.nip LIKE ? OR gm.nama_mapel LIKE ?";
    $search_term = "%" . $search_query . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}
$sql .= " ORDER BY g.name ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$guru_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


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
        
        <?php if ($success): ?><div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $success; ?></p></div><?php endif; ?>
        <?php if ($error): ?><div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $error; ?></p></div><?php endif; ?>
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h2 class="text-2xl font-bold text-white mb-4 sm:mb-0">Daftar Guru Terdaftar</h2>
            <form method="GET" action="manajemen_guru.php" class="flex">
                <input type="text" name="search" placeholder="Cari nama, NIP, mapel..." value="<?php echo htmlspecialchars($search_query); ?>" class="bg-slate-700 border-slate-600 rounded-l-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-r-md hover:bg-blue-700 transition-colors">Cari</button>
            </form>
        </div>

        <div class="bg-slate-800 p-2 sm:p-4 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-300">
                    <thead class="text-xs text-gray-400 uppercase bg-slate-700">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-3">NIP</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Mata Pelajaran Utama</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($guru_list)): ?>
                            <tr class="bg-slate-800 border-b border-slate-700">
                                <td colspan="5" class="px-6 py-4 text-center">Data tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($guru_list as $guru): ?>
                                <tr class="bg-slate-800 border-b border-slate-700 hover:bg-slate-700/50">
                                    <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                                        <?php echo htmlspecialchars($guru['name']); ?>
                                    </th>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($guru['nip'] ?: '-'); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($guru['email']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($guru['nama_mapel'] ?? '-'); ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="detail_guru.php?id=<?php echo $guru['id']; ?>" class="font-medium text-blue-400 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php
require_once 'layout/footer.php';
?>