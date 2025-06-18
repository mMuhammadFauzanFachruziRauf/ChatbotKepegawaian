<?php
session_start();
require '../db_connection.php'; 

if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'admin') { die("Akses ditolak."); }
$pageTitle = 'Manajemen Petugas';
$admin_id = $_SESSION['user']['id'];

// Logika Tambah Petugas (dengan tambahan 'role')
if (isset($_POST['tambah_petugas'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role']; // Mengambil role dari form

    if (empty($username) || empty($password) || empty($role)) {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
    } elseif (!in_array($role, ['admin', 'petugas'])) {
        $_SESSION['error_message'] = "Peran tidak valid.";
    } else {
        // ... (logika cek duplikat username tetap sama) ...
        $stmt_check = $conn->prepare("SELECT id FROM petugas WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $_SESSION['error_message'] = "Username sudah digunakan.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            // Query INSERT sekarang menyertakan kolom 'role'
            $stmt_insert = $conn->prepare("INSERT INTO petugas (username, password, role) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $username, $hashed_password, $role);
            if ($stmt_insert->execute()) {
                $_SESSION['success_message'] = "Akun berhasil ditambahkan.";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan akun.";
            }
        }
    }
    header("Location: manajemen_petugas.php");
    exit();
}

// ... (logika Hapus dan ambil notifikasi tetap sama) ...

// Ambil semua data petugas untuk ditampilkan, sekarang dengan role
$petugas_list = $conn->query("SELECT id, username, role FROM petugas ORDER BY username ASC")->fetch_all(MYSQLI_ASSOC);

require_once 'layout/header.php';
require_once 'layout/sidebar.php';
?>
<div class="flex-1 flex flex-col">
    <main class="p-6 flex-grow">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 bg-slate-800 p-6 rounded-lg shadow-lg h-fit">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Tambah Akun Baru</h3>
                <form action="manajemen_petugas.php" method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300">Username (Email)</label>
                        <input type="email" name="username" id="username" required class="mt-1 w-full bg-slate-700 ...">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">Password Awal</label>
                        <input type="password" name="password" id="password" required class="mt-1 w-full bg-slate-700 ...">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-300">Peran (Role)</label>
                        <select name="role" id="role" required class="mt-1 w-full bg-slate-700 ...">
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="text-right">
                        <button type="submit" name="tambah_petugas" class="bg-blue-600 ...">Tambah</button>
                    </div>
                </form>
            </div>
            <div class="lg:col-span-2 bg-slate-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Daftar Akun Petugas & Admin</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full ...">
                        <thead class="..."><th class="px-4 py-2">Username</th><th class="px-4 py-2">Peran</th><th class="px-4 py-2 text-center">Aksi</th></thead>
                        <tbody>
                            <?php foreach ($petugas_list as $petugas): ?>
                            <tr class="border-b border-slate-700">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($petugas['username']); ?></td>
                                <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded-full <?php echo $petugas['role'] == 'admin' ? 'bg-red-500/30 text-red-300' : 'bg-sky-500/30 text-sky-300'; ?>"><?php echo htmlspecialchars($petugas['role']); ?></span></td>
                                <td class="px-4 py-2 text-center whitespace-nowrap">
                                    <?php if ($petugas['id'] == $admin_id): ?>
                                        <span class="font-medium text-gray-600 cursor-not-allowed" title="Anda tidak dapat menghapus akun sendiri">Hapus</span>
                                    <?php else: ?>
                                        <a href="manajemen_petugas.php?action=delete_petugas&id=<?php echo $petugas['id']; ?>" onclick="return confirm('Yakin ingin menghapus akun ini?')" class="font-medium text-red-500 hover:underline">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php require_once 'layout/footer.php'; ?>