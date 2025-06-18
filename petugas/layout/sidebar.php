<aside id="sidebar" class="bg-slate-800 w-64 min-h-screen p-4 flex-col fixed md:relative -translate-x-full md:translate-x-0 transition-transform duration-300 z-30">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">A</div>
            <span class="text-xl font-bold text-white">Panel Admin</span>
        </div>
        <button id="sidebar-close" class="md:hidden text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    <nav>
        <ul>
            <?php
            $user_role = $_SESSION['user_type'] ?? 'petugas';
            $menuItems = [
                'dashboardAdmin.php' => ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>', 'label' => 'Dashboard'],
                'manajemen_guru.php' => ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 004.77-9.582M12 13a4 4 0 110-8 4 4 0 010 8z" /></svg>', 'label' => 'Manajemen Guru'],
                'manajemen_mapel.php' => ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18" /></svg>', 'label' => 'Manajemen Mapel'],
            ];
            // Menu khusus Admin
            if ($user_role === 'admin') {
                $menuItems['manajemen_petugas.php'] = ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>', 'label' => 'Manajemen Petugas'];
            }
            
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>
            <?php foreach ($menuItems as $url => $item): ?>
                <li><a href="<?php echo $url; ?>" class="flex items-center gap-3 px-4 py-2.5 my-1 rounded-lg transition-colors <?php echo ($currentPage == $url) ? 'bg-slate-700 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'; ?>"><?php echo $item['icon']; ?><span><?php echo $item['label']; ?></span></a></li>
            <?php endforeach; ?>
            <li class="mt-8">
                <a href="logout.php" class="flex items-center gap-3 px-4 py-2.5 text-red-400 hover:bg-red-500/20 hover:text-red-300 rounded-lg transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" /></svg>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>