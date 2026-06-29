<?php
$checks = [];

$phpOk = version_compare(PHP_VERSION, '8.0.0', '>=');
$checks[] = [
    'group' => 'PHP',
    'label' => 'Versi PHP',
    'status' => $phpOk ? 'ok' : 'fail',
    'details' => PHP_VERSION . ' (minimal 8.0.0)'
];

$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'gd'];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    $checks[] = [
        'group' => 'PHP',
        'label' => 'Ekstensi ' . $ext,
        'status' => $loaded ? 'ok' : 'fail',
        'details' => $loaded ? 'Terpasang' : 'Tidak terpasang'
    ];
}

$dbStatus = 'fail';
$dbDetails = '';
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
    try {
        $database = new Database();
        $conn = $database->getConnection();
        if ($conn) {
            $dbStatus = 'ok';
            $dbDetails = 'Koneksi berhasil';
        } else {
            $dbDetails = 'getConnection() mengembalikan null';
        }
    } catch (Throwable $e) {
        $dbDetails = 'Gagal konek: ' . $e->getMessage();
    }
} else {
    $dbDetails = 'File config database.php tidak ditemukan';
}

$checks[] = [
    'group' => 'Database',
    'label' => 'Koneksi Database',
    'status' => $dbStatus,
    'details' => $dbDetails
];

$uploadDirs = [
    'admin/uploads/',
    'admin/uploads/news/',
    'admin/uploads/academic/',
    'admin/uploads/innovations/',
    'admin/uploads/profile/',
    'admin/uploads/transparency/'
];

foreach ($uploadDirs as $rel) {
    $path = dirname(__DIR__) . '/' . $rel;
    $exists = is_dir($path);
    $writable = $exists && is_writable($path);
    $status = $exists && $writable ? 'ok' : ($exists ? 'warn' : 'fail');
    $detailParts = [];
    $detailParts[] = $exists ? 'Folder ada' : 'Folder tidak ada';
    if ($exists) {
        $detailParts[] = $writable ? 'Dapat ditulis' : 'Tidak dapat ditulis';
    }
    $checks[] = [
        'group' => 'Filesystem',
        'label' => $rel,
        'status' => $status,
        'details' => implode(' | ', $detailParts)
    ];
}

$overallStatus = 'ok';
foreach ($checks as $c) {
    if ($c['status'] === 'fail') {
        $overallStatus = 'fail';
        break;
    }
    if ($c['status'] === 'warn' && $overallStatus !== 'fail') {
        $overallStatus = 'warn';
    }
}

function badgeClass($status)
{
    if ($status === 'ok') {
        return 'bg-green-100 text-green-800 border-green-300';
    }
    if ($status === 'warn') {
        return 'bg-yellow-100 text-yellow-800 border-yellow-300';
    }
    return 'bg-red-100 text-red-800 border-red-300';
}

function badgeText($status)
{
    if ($status === 'ok') {
        return 'OK';
    }
    if ($status === 'warn') {
        return 'Peringatan';
    }
    return 'Gagal';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preflight Check - Admin SDIP Tunas Bangsa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center py-10">
    <div class="max-w-4xl w-full mx-auto bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Preflight Check Environment</h1>
                <p class="text-sm text-slate-600">Verifikasi cepat sebelum deploy ke server produksi</p>
            </div>
            <div class="px-3 py-1 rounded-full text-xs font-semibold border <?php echo badgeClass($overallStatus); ?>">
                Status: <?php echo badgeText($overallStatus); ?>
            </div>
        </div>

        <div class="px-6 py-4 text-sm text-slate-600 border-b border-slate-100 bg-slate-50/50 flex flex-wrap gap-4">
            <div>
                <span class="font-medium">Versi PHP Server:</span>
                <span class="ml-1 text-slate-800"><?php echo htmlspecialchars(PHP_VERSION); ?></span>
            </div>
            <div>
                <span class="font-medium">Server Software:</span>
                <span class="ml-1 text-slate-800"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Tidak diketahui'); ?></span>
            </div>
            <div>
                <span class="font-medium">Path Project:</span>
                <span class="ml-1 text-slate-800"><?php echo htmlspecialchars(dirname(__DIR__)); ?></span>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Kategori</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Item</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($checks as $check): ?>
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap text-slate-700"><?php echo htmlspecialchars($check['group']); ?></td>
                            <td class="px-3 py-2 whitespace-nowrap text-slate-800 font-medium"><?php echo htmlspecialchars($check['label']); ?></td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?php echo badgeClass($check['status']); ?>">
                                    <?php echo badgeText($check['status']); ?>
                                </span>
                            </td>
                            <td class="px-3 py-2 text-slate-700"><?php echo htmlspecialchars($check['details']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-xs text-slate-500">
                <p>Jika ada status "Gagal", periksa konfigurasi server Anda sebelum membuka situs untuk publik.</p>
                <p class="mt-1">Skrip ini dapat dihapus setelah deployment berhasil.</p>
            </div>
        </div>
    </div>
</body>
</html>

