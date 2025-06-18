<aside id="sidebar" class="bg-slate-800 w-64 min-h-screen p-4 flex-col fixed md:relative -translate-x-full md:translate-x-0 transition-transform duration-300 z-30">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">S</div>
            <span class="text-xl font-bold text-white">SI Kepegawaian</span>
        </div>
        <button id="sidebar-close" class="md:hidden text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    <nav>
        <ul>
            <?php
            $menuItems = [
                'index.php' => ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>', 'label' => 'Beranda'],
                'profileGuru.php' => ['icon' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>', 'label' => 'Profil Saya'],
                'sertifikasi.php' => ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>', 'label' => 'Sertifikasi'],
                'bahan_ajar.php' => ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>', 'label' => 'Bahan Ajar'],
                'bank_soal.php' => ['icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>', 'label' => 'Bank Soal'],
            ];
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>
            <?php foreach ($menuItems as $url => $item): ?>
                <li>
                    <a href="<?php echo $url; ?>" class="flex items-center gap-3 px-4 py-2.5 my-1 rounded-lg transition-colors 
                        <?php echo ($currentPage == $url) 
                            ? 'bg-slate-700 text-white' 
                            : 'text-gray-400 hover:bg-slate-700 hover:text-white'; ?>">
                        <?php echo $item['icon']; ?>
                        <span><?php echo $item['label']; ?></span>
                    </a>
                </li>
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