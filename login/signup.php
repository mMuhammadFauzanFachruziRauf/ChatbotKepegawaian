<?php
session_start();
require '../db_connection.php'; // Pastikan file ini ada

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
        $error = 'Password tidak memenuhi semua syarat yang ditentukan.';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Menggunakan Object-Oriented style agar konsisten dengan login.php
        $stmt_check = $conn->prepare("SELECT id FROM guru WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt_insert = $conn->prepare("INSERT INTO guru (email, password) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                // OPSI 2: Mengatur pesan sukses di session, bukan alert()
                $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan login untuk melanjutkan.";
                header('Location: login.php');
                exit;
            } else {
                $error = 'Gagal membuat akun. Silakan coba lagi.';
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Guru - SI Kepegawaian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md p-8 space-y-6 bg-slate-800 rounded-xl shadow-2xl border border-slate-700">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-white">Buat Akun Baru</h1>
            <p class="text-gray-400 mt-2">Daftar sebagai Pegawai / Guru</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form id="signupForm" method="POST" action="signup.php" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full bg-slate-700 border-slate-600 text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <div class="relative mt-1">
                    <input type="password" id="password" name="password" required class="block w-full bg-slate-700 border-slate-600 text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>

            <div id="password-requirements" class="space-y-1 text-xs">
                <p id="length" class="text-gray-500 transition-colors duration-300 flex items-center gap-2">
                    <svg class="icon-cross h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    <svg class="icon-check h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Minimal 8 karakter</span>
                </p>
                <p id="lowercase" class="text-gray-500 transition-colors duration-300 flex items-center gap-2">
                    <svg class="icon-cross h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    <svg class="icon-check h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Mengandung huruf kecil (a-z)</span>
                </p>
                <p id="uppercase" class="text-gray-500 transition-colors duration-300 flex items-center gap-2">
                    <svg class="icon-cross h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    <svg class="icon-check h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Mengandung huruf besar (A-Z)</span>
                </p>
                <p id="symbol" class="text-gray-500 transition-colors duration-300 flex items-center gap-2">
                    <svg class="icon-cross h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    <svg class="icon-check h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Mengandung simbol (Contoh: @, #, !)</span>
                </p>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-300">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full bg-slate-700 border-slate-600 text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm">
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-blue-500 transition-colors">
                    Daftar
                </button>
            </div>
        </form>

        <div class="text-center text-sm text-gray-400">
            <p>Sudah punya akun? <a href="login.php" class="font-medium text-blue-400 hover:text-blue-500">Login di sini</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const requirements = {
                length: document.getElementById('length'),
                lowercase: document.getElementById('lowercase'),
                uppercase: document.getElementById('uppercase'),
                symbol: document.getElementById('symbol')
            };

            const validators = {
                length: (val) => val.length >= 8,
                lowercase: (val) => /[a-z]/.test(val),
                uppercase: (val) => /[A-Z]/.test(val),
                symbol: (val) => /\W/.test(val)
            };

            function updateRequirement(element, isValid) {
                const crossIcon = element.querySelector('.icon-cross');
                const checkIcon = element.querySelector('.icon-check');
                if (isValid) {
                    element.classList.remove('text-gray-500');
                    element.classList.add('text-green-400');
                    crossIcon.classList.add('hidden');
                    checkIcon.classList.remove('hidden');
                } else {
                    element.classList.remove('text-green-400');
                    element.classList.add('text-gray-500');
                    crossIcon.classList.remove('hidden');
                    checkIcon.classList.add('hidden');
                }
            }

            passwordInput.addEventListener('input', function () {
                const password = this.value;
                for (const key in validators) {
                    updateRequirement(requirements[key], validators[key](password));
                }
            });
        });
    </script>
</body>
</html>