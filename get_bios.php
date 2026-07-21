<?php
header("Access-Control-Allow-Origin: *");
header("Cross-Origin-Resource-Policy: cross-origin");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");

$file = isset($_GET['file']) ? $_GET['file'] : '';

$file = basename($file); 

$filepath = __DIR__ . '/bios/' . $file;

if (!empty($file) && file_exists($filepath)) {
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($filepath));
    
    // Kirim isi file ke browser
    readfile($filepath);
    exit;
} else {
    http_response_code(404);
    echo "BIOS File Not Found";
}
?>