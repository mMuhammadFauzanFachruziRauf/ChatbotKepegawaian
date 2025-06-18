<?php
session_start();
require '../db_connection.php'; 

$error = ''; $success = ''; $logout_message = '';
if (isset($_SESSION['success_message'])) { $success = $_SESSION['success_message']; unset($_SESSION['success_message']); }
if (isset($_SESSION['logout_message'])) { $logout_message = $_SESSION['logout_message']; unset($_SESSION['logout_message']); }

if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'guru') {
        header('Location: ../guru/index.php');
    } else {
        header('Location: ../petugas/dashboardAdmin.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = $_POST['user_type'] ?? null;
    $credential = trim($_POST['credential'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($userType) || empty($credential) || empty($password)) {
        $error = "Semua kolom wajib diisi.";
    } else {
        if ($userType === 'guru') {
            $stmt = $conn->prepare("SELECT id, name, email, password FROM guru WHERE email = ?");
            $stmt->bind_param("s", $credential);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = ['id' => $user['id'], 'email' => $user['email'], 'name' => $user['name'] ?? ''];
                $_SESSION['user_type'] = 'guru';
                header('Location: ../guru/index.php');
                exit;
            } else {
                $error = "Email atau password Guru tidak valid.";
            }
        } 
        // Logika untuk Petugas dan Admin digabung di sini
        elseif ($userType === 'petugas') { 
            $stmt = $conn->prepare("SELECT id, username, password, role FROM petugas WHERE username = ?");
            $stmt->bind_param("s", $credential);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username']];
                $_SESSION['user_type'] = $user['role']; // Peran diambil dari DB
                header('Location: ../petugas/dashboardAdmin.php');
                exit;
            } else {
                $error = "Username atau password tidak valid.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI Kepegawaian SMKN 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .radio-hidden { position: absolute; opacity: 0; width: 0; height: 0; } </style>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md p-8 space-y-6 bg-slate-800 rounded-xl shadow-2xl border border-slate-700">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-white">Selamat Datang Kembali</h1>
            <p class="text-gray-400 mt-2">Login untuk mengakses dashboard Anda.</p>
        </div>

        <?php if (!empty($success)): ?><div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg text-center"><p><?php echo htmlspecialchars($success); ?></p></div><?php endif; ?>
        <?php if (!empty($logout_message)): ?><div class="bg-sky-500/20 border border-sky-500 text-sky-300 px-4 py-3 rounded-lg text-center"><p><?php echo htmlspecialchars($logout_message); ?></p></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center"><p><?php echo htmlspecialchars($error); ?></p></div><?php endif; ?>

        <form id="loginForm" method="POST" action="login.php" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Login sebagai</label>
                <div id="userTypeContainer" class="grid grid-cols-2 gap-2">
                    <div>
                        <input type="radio" name="user_type" id="type_guru" value="guru" class="radio-hidden peer" checked>
                        <label for="type_guru" class="block w-full text-center text-sm p-2 rounded-md border border-slate-600 cursor-pointer transition-colors peer-checked:bg-blue-600 peer-checked:border-blue-500 peer-checked:text-white hover:bg-slate-700">Guru</label>
                    </div>
                    <div>
                        <input type="radio" name="user_type" id="type_petugas" value="petugas" class="radio-hidden peer">
                        <label for="type_petugas" class="block w-full text-center text-sm p-2 rounded-md border border-slate-600 cursor-pointer transition-colors peer-checked:bg-blue-600 peer-checked:border-blue-500 peer-checked:text-white hover:bg-slate-700">Petugas</label>
                    </div>
                    </div>
            </div>

            <div>
                <label for="credential" class="block text-sm font-medium text-gray-300" id="label-credential">Email</label>
                <input type="email" id="credential" name="credential" required autocomplete="email" class="mt-1 block w-full bg-slate-700 border-slate-600 text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <div class="relative mt-1">
                    <input type="password" id="password" name="password" required autocomplete="current-password" class="block w-full bg-slate-700 border-slate-600 text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm">
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-white">
                        <svg id="icon-eye" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg id="icon-eye-slash" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-blue-500 transition-colors">Login</button>
            </div>
        </form>

        <div class="text-center text-sm text-gray-400">
            <p>Belum punya akun? <a href="signup.php" class="font-medium text-blue-400 hover:text-blue-500">Daftar sebagai Guru</a></p>
            <p class="mt-2"><a href="../index.php" class="hover:underline">Kembali ke Beranda</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userTypeContainer = document.getElementById('userTypeContainer');
            const credentialInput = document.getElementById('credential');
            const credentialLabel = document.getElementById('label-credential');
            
            function updateUserType() {
                const selectedType = userTypeContainer.querySelector('input[name="user_type"]:checked').value;
                if (selectedType === 'guru') {
                    credentialLabel.textContent = 'Email';
                    credentialInput.type = 'email';
                } else { // Ini akan berlaku untuk 'petugas'
                    credentialLabel.textContent = 'Username';
                    credentialInput.type = 'text';
                }
            }
            userTypeContainer.addEventListener('change', updateUserType);
            updateUserType();

            // ... (script untuk toggle password tetap sama) ...
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const iconEye = document.getElementById('icon-eye');
            const iconEyeSlash = document.getElementById('icon-eye-slash');
            togglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                iconEye.classList.toggle('hidden', isPassword);
                iconEyeSlash.classList.toggle('hidden', !isPassword);
            });
        });
    </script>
</body>
</html>