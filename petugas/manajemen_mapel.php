<?php
session_start();
require '../db_connection.php'; 

// Keamanan: Pastikan user sudah login dan merupakan admin/petugas
if (!isset($_SESSION['user']) || !in_array($_SESSION['user_type'], ['admin', 'petugas'])) {
    header("Location: ../login.php");
    exit();
}
$pageTitle = 'Manajemen Mapel';

// === LOGIKA UNTUK MATA PELAJARAN UTAMA (tabel: guru_mapel) ===

// Tambah Mapel Utama
if (isset($_POST['tambah_mapel_utama'])) {
    $nama_mapel = trim($_POST['nama_mapel']);
    if (!empty($nama_mapel)) {
        $stmt = $conn->prepare("INSERT INTO guru_mapel (nama_mapel) VALUES (?)");
        $stmt->bind_param("s", $nama_mapel);
        $stmt->execute();
        $_SESSION['success_message'] = "Mata pelajaran utama berhasil ditambahkan.";
    } else {
        $_SESSION['error_message'] = "Nama mata pelajaran tidak boleh kosong.";
    }
    header("Location: manajemen_mapel.php");
    exit();
}

// Edit Mapel Utama
if (isset($_POST['edit_mapel_utama'])) {
    $id = $_POST['id'];
    $nama_mapel = trim($_POST['nama_mapel']);
    if (!empty($nama_mapel) && !empty($id)) {
        $stmt = $conn->prepare("UPDATE guru_mapel SET nama_mapel = ? WHERE id = ?");
        $stmt->bind_param("si", $nama_mapel, $id);
        $stmt->execute();
        $_SESSION['success_message'] = "Mata pelajaran utama berhasil diperbarui.";
    } else {
        $_SESSION['error_message'] = "Data tidak lengkap untuk pembaruan.";
    }
    header("Location: manajemen_mapel.php");
    exit();
}

// Hapus Mapel Utama
if (isset($_GET['action']) && $_GET['action'] == 'delete_mapel_utama' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // ON DELETE CASCADE di DB akan otomatis menghapus kelompok mapel yang terkait
    $stmt = $conn->prepare("DELETE FROM guru_mapel WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success_message'] = "Mata pelajaran utama dan kelompok terkait telah dihapus.";
    header("Location: manajemen_mapel.php");
    exit();
}


// === LOGIKA UNTUK KELOMPOK MATA PELAJARAN (tabel: kelompok_mapel) ===

// Tambah Kelompok Mapel
if (isset($_POST['tambah_kelompok'])) {
    $nama_kelompok = trim($_POST['nama_kelompok']);
    $guru_mapel_id = $_POST['guru_mapel_id'];
    if (!empty($nama_kelompok) && !empty($guru_mapel_id)) {
        $stmt = $conn->prepare("INSERT INTO kelompok_mapel (guru_mapel_id, nama_kelompok) VALUES (?, ?)");
        $stmt->bind_param("is", $guru_mapel_id, $nama_kelompok);
        $stmt->execute();
        $_SESSION['success_message'] = "Kelompok mata pelajaran berhasil ditambahkan.";
    } else {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
    }
    header("Location: manajemen_mapel.php");
    exit();
}

// Hapus Kelompok Mapel
if (isset($_GET['action']) && $_GET['action'] == 'delete_kelompok' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM kelompok_mapel WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success_message'] = "Kelompok mata pelajaran berhasil dihapus.";
    header("Location: manajemen_mapel.php");
    exit();
}

// Ambil pesan notifikasi
$success = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Ambil data untuk ditampilkan
$mapel_utama_list = $conn->query("SELECT * FROM guru_mapel ORDER BY nama_mapel ASC")->fetch_all(MYSQLI_ASSOC);
$kelompok_mapel_list = $conn->query("SELECT km.*, gm.nama_mapel FROM kelompok_mapel km LEFT JOIN guru_mapel gm ON km.guru_mapel_id = gm.id ORDER BY gm.nama_mapel, km.nama_kelompok ASC")->fetch_all(MYSQLI_ASSOC);

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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg space-y-6">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3">Mata Pelajaran Utama</h3>
                <form action="manajemen_mapel.php" method="POST">
                    <label for="nama_mapel" class="block text-sm font-medium text-gray-300">Nama Mapel Baru</label>
                    <div class="flex gap-2 mt-1">
                        <input type="text" name="nama_mapel" id="nama_mapel" required class="flex-grow bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" name="tambah_mapel_utama" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Tambah</button>
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 uppercase bg-slate-700"><tr><th class="px-4 py-2">Nama Mata Pelajaran</th><th class="px-4 py-2 text-center">Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach ($mapel_utama_list as $mapel): ?>
                            <tr class="border-b border-slate-700">
                                <td class="px-4 py-2">
                                    <span id="text-<?php echo $mapel['id']; ?>"><?php echo htmlspecialchars($mapel['nama_mapel']); ?></span>
                                    <form id="form-<?php echo $mapel['id']; ?>" action="manajemen_mapel.php" method="POST" class="hidden items-center gap-2">
                                        <input type="hidden" name="id" value="<?php echo $mapel['id']; ?>">
                                        <input type="text" name="nama_mapel" value="<?php echo htmlspecialchars($mapel['nama_mapel']); ?>" class="flex-grow bg-slate-900 border-slate-600 rounded-md py-1 px-2 text-white">
                                        <button type="submit" name="edit_mapel_utama" class="text-green-400 hover:text-green-300">Simpan</button>
                                    </form>
                                </td>
                                <td class="px-4 py-2 text-center whitespace-nowrap">
                                    <button onclick="toggleEdit(<?php echo $mapel['id']; ?>)" id="edit-btn-<?php echo $mapel['id']; ?>" class="font-medium text-sky-400 hover:underline mr-3">Edit</button>
                                    <button onclick="document.getElementById('form-<?php echo $mapel['id']; ?>').classList.add('hidden'); document.getElementById('text-<?php echo $mapel['id']; ?>').classList.remove('hidden'); this.classList.add('hidden'); document.getElementById('edit-btn-<?php echo $mapel['id']; ?>').classList.remove('hidden');" id="cancel-btn-<?php echo $mapel['id']; ?>" class="font-medium text-gray-400 hover:underline mr-3 hidden">Batal</button>
                                    <a href="manajemen_mapel.php?action=delete_mapel_utama&id=<?php echo $mapel['id']; ?>" onclick="return confirm('Yakin ingin menghapus? Menghapus ini akan menghapus semua kelompok mapel di dalamnya.')" class="font-medium text-red-500 hover:underline">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-slate-800 p-6 rounded-lg shadow-lg space-y-6">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3">Kelompok Mata Pelajaran</h3>
                <form action="manajemen_mapel.php" method="POST" class="space-y-4">
                     <div>
                        <label for="guru_mapel_id" class="block text-sm font-medium text-gray-300">Termasuk dalam Mapel Utama</label>
                        <select name="guru_mapel_id" id="guru_mapel_id" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Mapel Utama --</option>
                            <?php foreach ($mapel_utama_list as $mapel): ?>
                            <option value="<?php echo $mapel['id']; ?>"><?php echo htmlspecialchars($mapel['nama_mapel']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="nama_kelompok" class="block text-sm font-medium text-gray-300">Nama Kelompok Baru</label>
                        <input type="text" name="nama_kelompok" id="nama_kelompok" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="text-right"><button type="submit" name="tambah_kelompok" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Tambah Kelompok</button></div>
                </form>
                <div class="overflow-x-auto">
                     <table class="min-w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 uppercase bg-slate-700"><tr><th class="px-4 py-2">Nama Kelompok</th><th class="px-4 py-2">Mapel Utama</th><th class="px-4 py-2 text-center">Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach ($kelompok_mapel_list as $kelompok): ?>
                            <tr class="border-b border-slate-700">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($kelompok['nama_kelompok']); ?></td>
                                <td class="px-4 py-2 text-gray-400"><?php echo htmlspecialchars($kelompok['nama_mapel']); ?></td>
                                <td class="px-4 py-2 text-center"><a href="manajemen_mapel.php?action=delete_kelompok&id=<?php echo $kelompok['id']; ?>" onclick="return confirm('Yakin ingin menghapus kelompok ini?')" class="font-medium text-red-500 hover:underline">Hapus</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function toggleEdit(id) {
    document.getElementById('text-' + id).classList.toggle('hidden');
    document.getElementById('form-' + id).classList.toggle('hidden');
    document.getElementById('edit-btn-' + id).classList.toggle('hidden');
    document.getElementById('cancel-btn-' + id).classList.toggle('hidden');
}
</script>

<?php require_once 'layout/footer.php'; ?>