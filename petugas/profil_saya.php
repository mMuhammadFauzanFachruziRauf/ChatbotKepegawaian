<?php
session_start();
require '../db_connection.php'; 

if (!isset($_SESSION['user']) || !in_array($_SESSION['user_type'], ['admin', 'petugas'])) {
    header("Location: ../login.php");
    exit();
}
$pageTitle = 'Profil Saya';

$user_id = $_SESSION['user']['id'];
$username = $_SESSION['user']['username'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logika untuk Ganti Password
    if (isset($_POST['ganti_password'])) {
        $password_lama = $_POST['password_lama'];
        $password_baru = $_POST['password_baru'];
        $konfirmasi_password = $_POST['konfirmasi_password'];

        $stmt = $conn->prepare("SELECT password FROM petugas WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !password_verify($password_lama, $user['password'])) {
            $error = "Password lama yang Anda masukkan salah.";
        } elseif (strlen($password_baru) < 8) {
            $error = "Password baru minimal harus 8 karakter.";
        } elseif ($password_baru !== $konfirmasi_password) {
            $error = "Konfirmasi password baru tidak cocok.";
        } else {
            $hashed_password = password_hash($password_baru, PASSWORD_BCRYPT);
            $stmt_update = $conn->prepare("UPDATE petugas SET password = ? WHERE id = ?");
            $stmt_update->bind_param("si", $hashed_password, $user_id);
            if ($stmt_update->execute()) {
                $success = "Password berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui password.";
            }
        }
    }
}

require_once 'layout/header.php';
require_once 'layout/sidebar.php';
?>

<main class="p-6 flex-grow">
    <h2 class="text-2xl font-bold text-white mb-6"><?php echo $pageTitle; ?></h2>

    <?php if ($success): ?><div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $success; ?></p></div><?php endif; ?>
    <?php if ($error): ?><div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center mb-6"><p><?php echo $error; ?></p></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Ganti Password</h3>
            <form action="profil_saya.php" method="POST" class="space-y-4">
                <div>
                    <label for="password_lama" class="block text-sm font-medium text-gray-300">Password Lama</label>
                    <input type="password" name="password_lama" id="password_lama" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="password_baru" class="block text-sm font-medium text-gray-300">Password Baru</label>
                    <input type="password" name="password_baru" id="password_baru" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="konfirmasi_password" class="block text-sm font-medium text-gray-300">Konfirmasi Password Baru</label>
                    <input type="password" name="konfirmasi_password" id="konfirmasi_password" required class="mt-1 w-full bg-slate-700 border-slate-600 rounded-md py-2 px-3 text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="text-right">
                    <button type="submit" name="ganti_password" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Simpan Password</button>
                </div>
            </form>
        </div>

        <div class="bg-slate-800 p-6 rounded-lg shadow-lg h-fit">
             <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 mb-4">Informasi Akun</h3>
             <dl class="text-sm">
                <div class="flex justify-between py-2"><dt class="text-gray-400">Username (Email)</dt><dd class="text-white font-medium"><?php echo htmlspecialchars($username); ?></dd></div>
                <div class="flex justify-between py-2"><dt class="text-gray-400">Peran / Role</dt><dd class="text-white font-medium"><span class="px-2 py-1 text-xs rounded-full <?php echo $_SESSION['user_type'] == 'admin' ? 'bg-red-500/30 text-red-300' : 'bg-sky-500/30 text-sky-300'; ?>"><?php echo htmlspecialchars($_SESSION['user_type']); ?></span></dd></div>
             </dl>
             <p class="text-xs text-gray-500 mt-4">Untuk mengubah username, silakan hubungi admin utama.</p>
        </div>
    </div>
</main>

<?php
require_once 'layout/footer.php';
?>