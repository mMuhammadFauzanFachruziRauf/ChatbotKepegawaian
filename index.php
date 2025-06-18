<?php
// Memulai sesi untuk manajemen login
session_start();

// Cek jika pengguna sudah login, arahkan ke dashboard
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'guru') {
        header('Location: guru/index.php');
        exit;
    } else { // Untuk admin dan petugas
        header('Location: admin/dashboardAdmin.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Kepegawaian - SMKN 1 Percut Sei Tuan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #0f172a; /* bg-slate-900 */
        }
        .hero-button-glow {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5), 0 0 30px rgba(59, 130, 246, 0.3);
        }
        /* Style untuk background grid */
        .background-grid {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.15;
            background-image: linear-gradient(white 1px, transparent 1px), linear-gradient(to right, white 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(ellipse 80% 50% at 50% 0%, black, transparent 70%);
        }
    </style>
</head>
<body class="text-gray-300">
    <div class="relative overflow-x-hidden">
        <div class="background-grid"></div>
        <div class="relative z-10">
            <header class="bg-slate-900/60 backdrop-blur-lg shadow-lg sticky top-0 z-50 border-b border-slate-800">
                <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
                    <a href="#" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                            S
                        </div>
                        <span class="text-xl font-bold text-white">SI Kepegawaian</span>
                    </a>
                    <div>
                        <a href="login/login.php" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-300">
                            Login
                        </a>
                    </div>
                </nav>
            </header>

            <main class="container mx-auto px-6 py-8">
                <section class="text-center py-20">
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-400">
                        Modernisasi Manajemen Data Kepegawaian
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-400">
                        Selamat datang di platform digital terintegrasi untuk pengelolaan data pegawai di SMK Negeri 1 Percut Sei Tuan.
                    </p>
                    <div class="mt-8">
                        <a href="login/login.php" class="hero-button-glow bg-blue-600 text-white font-bold px-8 py-3 rounded-full text-lg hover:bg-blue-700 transition-all transform hover:scale-105 duration-300">
                            Mulai Sekarang
                        </a>
                    </div>
                </section>

                <section id="about" class="mt-16">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="text-center md:text-left">
                            <h2 class="text-3xl font-bold text-white">Dari Manual ke Digital</h2>
                            <p class="mt-4 text-gray-400">
                                Sistem ini hadir sebagai solusi atas tantangan pengelolaan data manual via Microsoft Excel. Kami mentransformasi proses yang rentan akan kesalahan, duplikasi, dan kelambatan menjadi sebuah alur kerja yang efisien, aman, dan terstruktur. Tujuannya adalah untuk meningkatkan akurasi data dan meringankan beban kerja administratif, sehingga mendukung kemajuan sekolah secara keseluruhan.
                            </p>
                        </div>
                        <div class="bg-slate-800/50 p-4 rounded-xl shadow-2xl border border-slate-700">
                            <div class="flex items-center gap-1.5 mb-3">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <div class="bg-slate-900 p-4 rounded-lg h-64 flex gap-4">
                                <div class="w-1/4 bg-slate-800 rounded-md p-2 space-y-2">
                                    <div class="h-4 bg-slate-700 rounded"></div>
                                    <div class="h-4 bg-slate-700 rounded w-5/6"></div>
                                    <div class="h-4 bg-slate-700 rounded w-4/6"></div>
                                </div>
                                <div class="w-3/4 bg-slate-800 rounded-md p-2 space-y-2">
                                     <div class="h-6 bg-slate-700 rounded w-1/3"></div>
                                     <div class="h-10 bg-slate-700 rounded"></div>
                                     <div class="h-10 bg-slate-700 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="mt-24">
                    <h2 class="text-3xl font-bold text-center text-white mb-12">Fitur Unggulan Sistem</h2>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-slate-800 p-6 rounded-lg shadow-lg hover:shadow-blue-500/20 hover:-translate-y-1 transition-all duration-300 border border-slate-700">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-600 text-white mb-4"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg></div>
                            <h3 class="text-lg font-semibold text-white">Database Terpusat</h3><p class="mt-2 text-gray-400 text-sm">Pengelolaan data oleh Admin menjadi lebih mudah dan akurat dalam satu platform yang terintegrasi.</p>
                        </div>
                        <div class="bg-slate-800 p-6 rounded-lg shadow-lg hover:shadow-blue-500/20 hover:-translate-y-1 transition-all duration-300 border border-slate-700">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-600 text-white mb-4"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                            <h3 class="text-lg font-semibold text-white">Akses Mandiri Pegawai</h3><p class="mt-2 text-gray-400 text-sm">Memberikan kemandirian bagi setiap pegawai untuk memperbarui profil dan data pribadinya kapan saja.</p>
                        </div>
                        <div class="bg-slate-800 p-6 rounded-lg shadow-lg hover:shadow-blue-500/20 hover:-translate-y-1 transition-all duration-300 border border-slate-700">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-600 text-white mb-4"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg></div>
                            <h3 class="text-lg font-semibold text-white">Chatbot Berbasis RAG</h3><p class="mt-2 text-gray-400 text-sm">Teknologi pencarian inovatif untuk menemukan data pegawai secara akurat menggunakan bahasa alami.</p>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="bg-slate-900 mt-20 border-t border-slate-800">
                <div class="container mx-auto px-6 py-6 text-center text-gray-500">
                    <p>&copy; <?php echo date("Y"); ?> - Sistem Informasi Kepegawaian SMKN 1 Percut Sei Tuan.</p>
                    <p class="text-sm mt-1">Dikembangkan oleh: Muhammad Fauzan Fachruzi Rauf</p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>