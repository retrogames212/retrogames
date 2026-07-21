<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: error?code=401");
    exit;
}

// Menangkap dari POST (Utama) atau GET (Cadangan)
$romFile = isset($_POST['rom']) ? $_POST['rom'] : (isset($_GET['rom']) ? $_GET['rom'] : '');
$romFileCleaned = str_replace(['../', '..\\'], '', $romFile);
$baseRomDir = __DIR__ . '/roms/';
$fullRomPath = $baseRomDir . $romFileCleaned;

if (empty($romFileCleaned) || !file_exists($fullRomPath) || !is_file($fullRomPath)) {
    header("Location: error?code=404");
    exit;
}

// Deteksi ekstensi berkas untuk parameter WebMSX
$extension = strtolower(pathinfo($romFileCleaned, PATHINFO_EXTENSION));
$paramName = 'ROM';
if ($extension === 'dsk') { $paramName = 'DISK'; } 
elseif ($extension === 'cas') { $paramName = 'TAPE'; }

$romUrl = '/roms/' . $romFileCleaned;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playing MSX Game</title>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        iframe { width: 100%; height: 100%; border: none; }
        .back-btn { 
            position: fixed; 
            top: 10px; 
            left: 10px; 
            z-index: 9999; 
            background: rgba(0,0,0,0.8); 
            color: #fff; 
            border: 1px solid #fff; 
            padding: 6px 14px; 
            border-radius: 4px; 
            text-decoration: none; 
            font-size: 13px; 
            font-weight: bold; 
            font-family: sans-serif;
        }
        .back-btn:hover { background: #ff4757; border-color: #ff4757; }
    </style>
</head>
<body>

    <a href="home" class="back-btn">⬅ Kembali</a>

    <iframe src="/webmsx/index.html?<?php echo $paramName; ?>=<?php echo urlencode($romUrl); ?>&MACHINE=MSX2" allowfullscreen></iframe>

    <script>
        // Opsional: Membersihkan history state browser agar ketika di-refresh tidak minta kirim ulang form
        if (window.history.replaceState) {
            window.history.replaceState(null, null, '/play');
        }
    </script>
</body>
</html>