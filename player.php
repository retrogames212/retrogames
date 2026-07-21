<?php
header("Cross-Origin-Opener-Policy: same-origin");
header("Cross-Origin-Embedder-Policy: credentialless");
?>

<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: error?code=401");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: error?code=405");
    exit;
}

$romFile = isset($_POST['rom']) ? $_POST['rom'] : '';
$system = isset($_POST['system']) ? $_POST['system'] : '';
$biosFile = isset($_POST['bios']) ? $_POST['bios'] : '';

$romFileCleaned = str_replace(['../', '..\\'], '', $romFile);
$baseRomDir = __DIR__ . '/roms/';
$fullRomPath = $baseRomDir . $romFileCleaned;

if (empty($romFileCleaned) || empty($system) || !file_exists($fullRomPath) || !is_file($fullRomPath)) {
    header("Location: error?code=404");
    exit;
}

// Cek apakah ini game MSX (berdasarkan core fmsx/bluemsx atau folder msx)
$isMSX = ($system === 'msx' || $system === 'fmsx' || $system === 'bluemsx' || strpos($romFileCleaned, 'msx/') === 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Playing Retro Game</title>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; font-family: sans-serif; overflow: hidden; }
        *, *::before, *::after { box-sizing: border-box; }

        .back-btn { 
            position: fixed; 
            top: 10px; 
            left: 10px; 
            z-index: 999999; 
            background: rgba(0,0,0,0.8); 
            color: #fff; 
            border: 1px solid #fff; 
            padding: 6px 14px; 
            border-radius: 4px; 
            text-decoration: none; 
            font-size: 13px; 
            font-weight: bold; 
        }
        .back-btn:hover { background: #ff4757; border-color: #ff4757; }
        
        #preroll-screen { 
            position: fixed; 
            inset: 0; /* Mengisi seluruh layar */
            background: #111; 
            z-index: 99999; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            padding: 20px;
        }

        .ad-box { 
            width: 100%; 
            max-width: 600px; 
            max-height: 60vh;
            background: #222; 
            border: 1px dashed #444; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 20px 0; 
            padding: 10px;
            overflow: auto;
        }

        .skip-btn { 
            background: #555; 
            color: #aaa; 
            border: none; 
            padding: 15px 40px; 
            font-size: 18px; 
            font-weight: bold; 
            border-radius: 6px; 
            cursor: not-allowed; 
            transition: 0.3s; 
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .skip-btn.active { background: #2ed573; color: white; cursor: pointer; }
        .skip-btn.active:hover { background: #26af5f; transform: scale(1.05); }

        /* WADAH EMULATORJS */
        #game-container {
            width: 100vw;
            height: 100vh;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        #game { width: 100%; height: 100%; }

        @media screen and (max-width: 480px) {
            .ad-box { max-height: 40vh; margin: 15px 0; }
            .skip-btn { width: 90%; padding: 15px; font-size: 16px; }
        }
    </style>
</head>
<body>

    <a href="home" class="back-btn">⬅ Kembali</a>

    <div id="preroll-screen">
        <h3 style="margin-bottom: 0;">Menyiapkan Emulator...</h3>
        <p style="color: #888; font-size: 14px; text-align: center;">Game akan siap setelah iklan di bawah selesai dibuka</p>
        
        <div class="ad-box">
            <span style="color: #666; width: 100%;">
            <?php
            echo '<div id="container-0524f9df72294ab0e7c386b845f22017" style="width: 100%; min-height: 80px;"></div>';
            $joss1 = 'PHNjcmlwdCBhc3luYz0iYXN5bmMiIGRhdGEtY2Zhc3luYz0iZmFsc2UiIHNyYz0iaHR0cHM6Ly9leGFtaW5lcmFzaHRyYXlxdWl6bWFzdGVyLmNvbS8wNTI0ZjlkZjcyMjk0YWIwZTdjMzg2Yjg0NWYyMjAxNy9pbnZva2UuanMiPjwvc2NyaXB0Pg==';
            echo base64_decode($joss1);
            ?>
            </span>
        </div>

        <button id="skip-button" class="skip-btn" disabled>Tunggu 5s...</button>
    </div>

    <!-- WADAH EMULATOR (BARU AKTIF SETELAH SKIP) -->
    <div id="game-container">
        <div id="game"></div>
    </div>

    <script>
        const romFile = <?php echo json_encode($romFileCleaned); ?>;
        const system = <?php echo json_encode($system); ?>;
        const biosFile = <?php echo json_encode($biosFile); ?>;
        const isMSX = <?php echo json_encode($isMSX); ?>;

        let timeLeft = 5;
        const skipBtn = document.getElementById('skip-button');
        
        const countdown = setInterval(() => {
            timeLeft--;
            skipBtn.innerText = `Tunggu ${timeLeft}s...`;
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                skipBtn.innerText = "LEWATI IKLAN & MAIN 🎮";
                skipBtn.removeAttribute('disabled');
                skipBtn.classList.add('active');
            }
        }, 1000);

        skipBtn.addEventListener('click', () => {
            if (timeLeft <= 0) {
                document.getElementById('preroll-screen').remove();
                
                if (isMSX) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/play';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'rom';
                    input.value = romFile;
                    
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                    } else {
                    document.getElementById('game-container').style.display = 'block';

                    EJS_player = '#game';
                    EJS_pathtodata = '/data/'; 
                    EJS_gameUrl = window.location.origin + '/roms/' + romFile; 
                    
                    EJS_core = system; 
                    EJS_startOnLoaded = true;
                    EJS_Language = 'en';
                    EJS_threads = true; 
                    
                    EJS_config = { 
                        "pcsx_rearmed_skip_bios": "disabled",
                        "pcsx_rearmed_async_cdrom": "enabled",
                        "pcsx_rearmed_spu_interpolation": "simple",
                        "pcsx_rearmed_spu_reverb": "disabled",
                        "pcsx_rearmed_frameskip": "auto",
                        "pcsx_rearmed_neon_enhancement": "disabled"
                    };
                    
                    if (biosFile !== '') {
                        var biosName = biosFile.substring(biosFile.lastIndexOf('/') + 1).toLowerCase();
                        if (biosName === 'scph5501.bin') {
                            var proxyUrl = window.location.origin + '/bios?file=' + biosName;
                            EJS_biosUrl = {}; EJS_biosUrl[biosName] = proxyUrl;
                        } else {
                            EJS_biosUrl = window.location.origin + '/' + biosFile;
                        }
                    }
                    if (romFile.toLowerCase().endsWith('.zip')) { EJS_isZip = true; }

                    const script = document.createElement('script');
                    script.src = "/data/loader.js";
                    document.body.appendChild(script);
                }
            }
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, 'home');
        }
    </script>
    
    <?php
    $joss2 = 'PHNjcmlwdCBzcmM9Imh0dHBzOi8vZXhhbWluZXJhc2h0cmF5cXVpem1hc3Rlci5jb20vZjYvMGQvMTMvZjYwZDEzOTY0MzI3NzRmMTUyZjE1ZGQwMzMwOGEwNTYuanMiPjwvc2NyaXB0Pg==';
    echo base64_decode($joss2);
    
    $joss3 = 'PHNjcmlwdCBzcmM9Imh0dHBzOi8vZXhhbWluZXJhc2h0cmF5cXVpem1hc3Rlci5jb20vOGIvODIvNmQvOGI4MjZkZTIzMTMzMTlkNDczYTVhYTNjZGYwNjU5OWIuanMiPjwvc2NyaXB0Pg==';
    echo base64_decode($joss3);
    ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function pemicuLandscapeSaja() {
        const apakahGawai = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (apakahGawai) {
            if (screen.orientation && screen.orientation.lock) {
                screen.orientation.lock('landscape').catch(function(error) {
                    console.log("Rotasi otomatis gagal: ", error);
                });
            }
        }
    }
    document.body.addEventListener('click', pemicuLandscapeSaja, { once: true });
    document.body.addEventListener('touchstart', pemicuLandscapeSaja, { once: true });
});

function cekKoneksiInternet() {
    if (!navigator.onLine) { window.location.href = "error?code=offline"; }
}
window.addEventListener('offline', cekKoneksiInternet);
</script>
</body>
</html>