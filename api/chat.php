<?php
// File: api/chat.php

require_once '../config.php';
require_once '../db_connection.php'; 

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['error' => 'Pesan tidak boleh kosong.']);
    exit;
}

$final_prompt = $user_message; // Default prompt

// =================================================================
// LOGIKA RAG VERSI 2.0: PENCARIAN DINAMIS & AMAN
// =================================================================

// 1. Definisikan kata kunci yang dikenali dan kolom database yang sesuai
$keyword_map = [
    'agama' => 'agama',
    'beragama' => 'agama',
    'jurusan' => 'jurusan',
    'prodi' => 'jurusan',
    'pangkat' => 'pangkat',
    'golongan' => 'golongan',
    'jenis kelamin' => 'jenis_kelamin',
    'laki-laki' => 'jenis_kelamin',
    'perempuan' => 'jenis_kelamin',
    'kepala sekolah' => 'kelompok_mata_pelajaran',
    'kepsek' => 'kelompok_mata_pelajaran'
];

$search_column = null;
$search_value = null;

// 2. Deteksi kata kunci dan ekstrak nilainya dari pesan pengguna
foreach ($keyword_map as $keyword => $column) {
    if (stripos($user_message, $keyword) !== false) {
        $search_column = $column;
        // Ambil kata setelah kata kunci sebagai nilai pencarian
        $parts = explode($keyword, $user_message, 2);
        $value_candidate = trim($parts[1]);

        if ($keyword == 'laki-laki' || $keyword == 'perempuan') {
            $search_value = str_replace('-', ' ', $keyword);
        } elseif (!empty($value_candidate)) {
             // Ambil kata pertama setelah keyword, atau beberapa kata jika relevan
            $value_parts = explode(' ', $value_candidate);
            $search_value = $value_parts[0]; 
            // Untuk kasus seperti "pendidikan agama islam", kita gabungkan beberapa kata
            if (strtolower($search_value) == 'pendidikan' && isset($value_parts[1], $value_parts[2])) {
                $search_value .= ' ' . $value_parts[1] . ' ' . $value_parts[2];
            }
        }
        break;
    }
}

// 3. Jika kata kunci ditemukan, lakukan pencarian dinamis ke database
if ($search_column && $search_value) {
    // Keamanan: Hanya pilih kolom yang aman untuk ditampilkan
    $safe_columns = "name, nip, gelar, mata_pelajaran, kelompok_mata_pelajaran, pangkat, golongan, agama, jenis_kelamin";
    
    $stmt = $conn->prepare("SELECT {$safe_columns} FROM guru WHERE {$search_column} LIKE ?");
    $like_value = "%" . $search_value . "%";
    $stmt->bind_param("s", $like_value);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 4. Jika ada hasil dari database, buat konteks untuk AI
    if (count($results) > 0) {
        $context = "Ditemukan " . count($results) . " data pegawai yang cocok dengan kriteria '" . htmlspecialchars($search_value) . "'. Rangkum data berikut dalam format daftar atau poin yang rapi:\n\n";
        $count = 1;
        foreach ($results as $guru) {
            $context .= $count . ". Nama: " . ($guru['name'] ?? '-') . ", NIP: " . ($guru['nip'] ?: '-') . ", Jabatan: " . ($guru['kelompok_mata_pelajaran'] ?? '-') . "\n";
            $count++;
        }

        $final_prompt = "Anda adalah asisten AI kepegawaian. Jawab pertanyaan pengguna HANYA berdasarkan DATA KONTEKS yang saya berikan. Jangan menambah informasi lain.\n\n" .
                        "DATA KONTEKS:\n---\n" . $context . "\n---\n\n" .
                        "Pertanyaan Pengguna: \"" . $user_message . "\"\n\n" .
                        "Jawaban Anda (dalam format daftar yang rapi):";
    }
}


// --- Kirim prompt ke Gemini (logika ini tetap sama) ---
$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent?key=' . GEMINI_API_KEY;
$data = ['contents' => [['parts' => [['text' => $final_prompt]]]]];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    $result = json_decode($response, true);
    $bot_reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses balasan saat ini.';
    echo json_encode(['reply' => $bot_reply]);
} else {
    echo json_encode(['error' => 'Gagal terhubung ke layanan AI. Kode: ' . $http_code, 'details' => json_decode($response)]);
}
?>