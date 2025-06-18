<?php 
session_start();
require '../db_connection.php'; 

if (!isset($_SESSION['user']) || !in_array($_SESSION['user_type'], ['admin', 'petugas'])) {
    header("Location: ../login.php");
    exit();
}
$pageTitle = 'Dashboard';
$user_role = $_SESSION['user_type'];
$username = $_SESSION['user']['username'];

// Query untuk widget
$total_guru = $conn->query("SELECT COUNT(id) as total FROM guru")->fetch_assoc()['total'];
$total_petugas = 0;
if ($user_role === 'admin') {
    $total_petugas = $conn->query("SELECT COUNT(id) as total FROM petugas")->fetch_assoc()['total'];
}

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
        
        <div class="relative">
            <button id="user-menu-button" class="flex items-center gap-2">
                <span class="text-sm font-medium text-white hidden sm:block"><?php echo htmlspecialchars($username); ?></span>
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-slate-800 rounded-md shadow-lg py-1 z-30 border border-slate-700">
                <a href="profil_saya.php" class="block px-4 py-2 text-sm text-gray-300 hover:bg-slate-700">Profil Saya</a>
                <a href="logout.php" class="block px-4 py-2 text-sm text-red-400 hover:bg-slate-700">Logout</a>
            </div>
        </div>
    </header>

    <main class="p-6 flex-grow">
        <h2 class="text-2xl font-bold text-white mb-6">Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg flex items-center gap-4">
                <div class="bg-blue-600/30 p-3 rounded-full"><svg class="h-6 w-6 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 004.77-9.582M12 13a4 4 0 110-8 4 4 0 010 8z" /></svg></div>
                <div>
                    <p class="text-sm text-gray-400">Total Guru Terdaftar</p>
                    <p class="text-2xl font-bold text-white"><?php echo $total_guru; ?></p>
                </div>
            </div>
            <?php if ($user_role === 'admin'): ?>
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg flex items-center gap-4">
                <div class="bg-amber-600/30 p-3 rounded-full"><svg class="h-6 w-6 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
                <div>
                    <p class="text-sm text-gray-400">Total Akun Petugas</p>
                    <p class="text-2xl font-bold text-white"><?php echo $total_petugas; ?></p>
                </div>
            </div>
            <?php endif; ?>
            <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                <h4 class="text-sm font-semibold text-white mb-3">Jalan Pintas</h4>
                <div class="space-y-2">
                    <a href="manajemen_guru.php" class="block w-full text-left bg-slate-700 hover:bg-slate-600 p-3 rounded-md transition-colors">Kelola Data Guru</a>
                    <a href="manajemen_mapel.php" class="block w-full text-left bg-slate-700 hover:bg-slate-600 p-3 rounded-md transition-colors">Kelola Mata Pelajaran</a>
                    <?php if ($user_role === 'admin'): ?>
                    <a href="manajemen_petugas.php" class="block w-full text-left bg-slate-700 hover:bg-slate-600 p-3 rounded-md transition-colors">Kelola Akun Petugas</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
// Memuat footer
require_once 'layout/footer.php';
?>