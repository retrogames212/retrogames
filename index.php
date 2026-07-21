<?php
session_start();
define('USER_ADMIN', 'mcnafian');
define('PASS_HASH', '$2y$10$IRv.YdmD7Bro01o6OVjRUuhig4KfQ/fV8N1af4TvTx/0HsuMsR8KO');

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: /home");
    exit;
}

$users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $u = $_POST['username']; $p = $_POST['password'];
    if ($u === USER_ADMIN && password_verify($p, PASS_HASH)) {
        $_SESSION['logged_in'] = true; $_SESSION['is_admin'] = true;
    } elseif (isset($users[$u]) && $users[$u]['pass'] == $p) {
        $_SESSION['logged_in'] = true; $_SESSION['is_admin'] = false;
    } else { $error = "Login Gagal!"; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retro Game Portal</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='black' d='M11.5 12.5a.5.5 0 0 1-.5-.5V10H9v2a.5.5 0 0 1-1 0v-2H6v2a.5.5 0 0 1-1 0v-2H3.5a2.5 2.5 0 0 1-2.5-2.5V5.5A2.5 2.5 0 0 1 3.5 3h9a2.5 2.5 0 0 1 2.5 2.5V10a2.5 2.5 0 0 1-2.5 2.5zm-5-6.5A.5.5 0 0 0 6 5.5v-1a.5.5 0 0 0-1 0v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1zM10.5 7a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m1.5-1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m-1.5 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m1.5-1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1'/%3E%3C/svg%3E">
    <style>
        :root {
            --bg-main: #0a0a12;
            --bg-card: #1f1f2e;
            --text-main: #ffffff;
            --text-muted: #a1a1b3;
            --glow-nintendo: #ff2a2a;
            --glow-sega: #0088ff;
            --glow-sony: #00f0ff;
            --glow-atari: #ff9900;
            --glow-arcade: #00ec42;
        }

        body { 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            background: linear-gradient(rgba(10, 10, 18, 0.92), rgba(10, 10, 18, 0.92)), 
                        url('https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-main); 
            margin: 0; 
            padding: 20px; 
            box-sizing: border-box;
        }
        
        *, *::before, *::after { box-sizing: border-box; }
        
        .container { max-width: 1000px; margin: 0 auto; position: relative; width: 100%; }
        
        h1 { 
            text-align: center; 
            color: #fff;
            text-transform: uppercase; 
            letter-spacing: 4px; 
            margin-bottom: 5px;
            font-size: 2.6rem;
            font-weight: 900;
            text-shadow: 3px 3px 0px var(--glow-nintendo), -3px -3px 0px var(--glow-sega);
        }
        
        .subtitle { 
            text-align: center; 
            color: var(--glow-arcade); 
            margin-top: 0; 
            margin-bottom: 40px; 
            font-size: 1rem; 
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 0 8px rgba(0, 236, 66, 0.4);
        }
        
        .vendor-group { 
            background: rgba(22, 22, 34, 0.85); 
            backdrop-filter: blur(8px); 
            border-radius: 14px; 
            margin-bottom: 25px; 
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            transition: all 0.3s ease;
        }
        
        .vendor-group.v-nintendo { border: 2px solid var(--glow-nintendo); }
        .vendor-group.v-sega { border: 2px solid var(--glow-sega); }
        .vendor-group.v-sony { border: 2px solid var(--glow-sony); }
        .vendor-group.v-atari { border: 2px solid var(--glow-atari); }
        .vendor-group.v-arcade { border: 2px solid var(--glow-arcade); }
        
        .vendor-header { 
            padding: 18px 25px;
            font-size: 1.4rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
            transition: background 0.3s;
            background: rgba(0, 0, 0, 0.3);
        }
        
        .v-nintendo .vendor-header:hover { background: rgba(255, 42, 42, 0.15); color: var(--glow-nintendo); }
        .v-sega .vendor-header:hover { background: rgba(0, 136, 128, 0.15); color: var(--glow-sega); }
        .v-sony .vendor-header:hover { background: rgba(0, 240, 255, 0.15); color: var(--glow-sony); }
        .v-atari .vendor-header:hover { background: rgba(255, 153, 0, 0.15); color: var(--glow-atari); }
        .v-arcade .vendor-header:hover { background: rgba(0, 236, 66, 0.15); color: var(--glow-arcade); }

        .header-left { display: flex; align-items: center; gap: 15px; }
        .vendor-icon { width: 36px; height: 36px; object-fit: contain; }

        .vendor-header .arrow { font-size: 1.1rem; transition: transform 0.3s ease; color: var(--text-muted); }
        .vendor-content { display: none; padding: 10px 20px 25px 20px; background: rgba(0, 0, 0, 0.4); }
        
        .system-section { 
            margin: 15px 0;
            border-radius: 8px;
            overflow: hidden;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        .system-header { 
            padding: 14px 20px; 
            font-size: 1.05rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
            background: rgba(255,255,255,0.02);
        }

        .v-nintendo .system-header:hover { background: rgba(255,255,255,0.07); border-left: 5px solid var(--glow-nintendo); }
        .v-sega .system-header:hover { background: rgba(255,255,255,0.07); border-left: 5px solid var(--glow-sega); }
        .v-sony .system-header:hover { background: rgba(255,255,255,0.07); border-left: 5px solid var(--glow-sony); }
        .v-atari .system-header:hover { background: rgba(255,255,255,0.07); border-left: 5px solid var(--glow-atari); }
        .v-arcade .system-header:hover { background: rgba(255,255,255,0.07); border-left: 5px solid var(--glow-arcade); }

        .system-icon { width: 26px; height: 26px; object-fit: contain; transition: all 0.2s; }
        
        .system-header .arrow { font-size: 0.9rem; transition: transform 0.3s ease; color: var(--text-muted); }
        .system-content { display: none; background: rgba(0, 0, 0, 0.5); }
        .header-right-side { display: flex; align-items: center; }

        .game-count { 
            font-size: 0.75rem; 
            background: #000; 
            padding: 4px 12px; 
            border-radius: 20px; 
            color: #00ec42; 
            font-weight: bold;
            margin-right: 15px;
            border: 1px solid rgba(0, 236, 66, 0.3);
        }
        
        .game-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); 
            gap: 10px;
            padding: 15px;
            max-height: 600px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #444 #1a1a24;
        }
        
        .game-grid::-webkit-scrollbar { width: 6px; }
        .game-grid::-webkit-scrollbar-thumb { background-color: #444; border-radius: 10px; }
        
        .game-card { 
            background: var(--bg-card); 
            padding: 12px; 
            border-radius: 8px; 
            text-decoration: none; 
            color: var(--text-main); 
            text-align: center; 
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
            border: 1px solid rgba(255,255,255,0.1); 
            display: flex; 
            flex-direction: column; 
            justify-content: flex-start; 
            min-height: 190px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .game-card[style*="display: none"] { display: none !important; }
        
        .v-nintendo .game-card:hover { background: var(--glow-nintendo); color: #000; transform: translateY(-4px); box-shadow: 0 6px 18px rgba(255, 42, 42, 0.4); border-color: var(--glow-nintendo); }
        .v-sega .game-card:hover { background: var(--glow-sega); color: #fff; transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0, 136, 255, 0.4); border-color: var(--glow-sega); }
        .v-sony .game-card:hover { background: var(--glow-sony); color: #000; transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0, 240, 255, 0.4); border-color: var(--glow-sony); }
        .v-atari .game-card:hover { background: var(--glow-atari); color: #000; transform: translateY(-4px); box-shadow: 0 6px 18px rgba(255, 153, 0, 0.4); border-color: var(--glow-atari); }
        .v-arcade .game-card:hover { background: var(--glow-arcade); color: #000; transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0, 236, 66, 0.4); border-color: var(--glow-arcade); }
        
        .game-title { font-weight: 700; font-size: 0.95rem; margin: 0; line-height: 1.4; letter-spacing: 0.5px; }
        .empty-message { color: #666; font-style: italic; padding: 20px; text-align: center; font-size: 0.9rem; }
        .open-arrow { transform: rotate(90deg); color: #fff !important; }

        .quit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 42, 42, 0.2);
            border: 2px solid var(--glow-nintendo);
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 0 10px rgba(255, 42, 42, 0.3);
        }
        .quit-btn:hover { background: var(--glow-nintendo); color: #000; box-shadow: 0 0 18px rgba(255, 42, 42, 0.6); }
        
        .login-box {
            max-width: 400px;
            margin: 60px auto 0 auto;
            background: rgba(22, 22, 34, 0.85);
            backdrop-filter: blur(8px);
            border: 2px solid var(--glow-sega);
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
        }
        .donasi-box { 
            max-width: 400px;
            margin: 60px auto 0 auto;
            background: rgba(22, 22, 34, 0.85);
            backdrop-filter: blur(8px);
            border: 2px solid var(--glow-sega);
            border-radius: 14px;
            text-align: center;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
        }
        .login-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 25px;
            color: #fff;
            text-shadow: 0 0 8px rgba(0, 136, 255, 0.4);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #444;
            background: #1f1f2e;
            color: #fff;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-control:focus { border-color: var(--glow-sony); }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: rgba(0, 236, 66, 0.2);
            border: 2px solid var(--glow-arcade);
            color: #fff;
            border-radius: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 0 10px rgba(0, 236, 66, 0.2);
        }
        .login-btn:hover { background: var(--glow-arcade); color: #000; box-shadow: 0 0 18px rgba(0, 236, 66, 0.5); }
        .alert-error {
            background: rgba(255, 42, 42, 0.15);
            border: 1px solid var(--glow-nintendo);
            color: #fff;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        @media screen and (max-width: 768px) {
            body { padding: 8px; }
            h1 { font-size: 1.6rem; letter-spacing: 2px; }
            .subtitle { font-size: 0.85rem; margin-bottom: 20px; }
            .vendor-header { padding: 12px 15px; font-size: 1.1rem; }
            .system-header { padding: 10px 12px; font-size: 0.9rem; }
            .game-count { margin-right: 5px; padding: 2px 8px; font-size: 0.7rem; }
            
            .game-grid { 
                grid-template-columns: repeat(2, 1fr) !important; 
                gap: 8px; 
                padding: 8px; 
            }
            .game-card { min-height: 130px; padding: 8px; }
            .game-title { font-size: 0.8rem; }
            
            .quit-btn { position: relative; display: block; text-align: center; width: fit-content; margin: 0 auto 15px auto; top: 0; right: 0; }
            .login-box { margin-top: 20px; padding: 20px; width: 100%; }
        }

        @media screen and (max-width: 380px) {
            .game-grid { 
                grid-template-columns: repeat(1, 1fr) !important; 
            }
        }
    </style>
</head>
<body>

<div class="container">

    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
        <h1>🎮 Retro Station</h1>
        <p class="subtitle">Multi-Console Platform</p>

        <div class="login-box">
            <div class="login-title">Insert Coin to Play</div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert-error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Input username..." required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Input password..." required>
                </div>
                <button type="submit" name="login" class="login-btn">Start Game</button>
            </form>
        </div>
        
        <div class="donasi-box">
            <h3 style="margin-top:0;">Need Coin ?</h3>
            <a id="donasi-link" href="#" target="_blank" onclick="this.href=atob('aHR0cHM6Ly9ub3dwYXltZW50cy5pby9kb25hdGlvbj9hcGlfa2V5PTk0ZTE2Yjg0LTk0MTAtNDhhYS05NzRmLThkOGQ0N2ZiNmY0MQ==')">
                <img src="https://nowpayments.io/images/embeds/donation-button-black.svg" alt="Support" style="width: 200px; cursor: pointer;">
            </a>
            <form action="account-status" method="POST" target="_blank" style="margin-top: 15px; display: flex; justify-content: center; gap: 10px;">
            <input type="text" name="order_id" placeholder="Input Order ID" required style="padding: 10px; border-radius: 5px; border: 1px solid #444; background: #1f1f2e; color: #fff; width: 200px;">
            <button type="submit" style="padding: 10px 20px; cursor: pointer; border-radius: 5px; border: none; background: #00ec42; color: #000; font-weight: bold;">Get Account</button>
        </form>
        <p style="color: #777; font-size: 0.75rem; margin-top: 5px;">
            *Note: The minimum donation amount is determined by the blockchain network. 
            If the donation amount is less than the minimum, the transaction cannot be processed.
        </p>
        </div>

    <?php else: ?>
        <a href="?action=logout" class="quit-btn" onclick="return confirm('Are you sure to Quit Game?')">🚪 Quit Game</a>

        <h1>🎮 Retro Station</h1>
        <p class="subtitle">Multi-Console Platform</p>
        <div style="text-align: center; margin-bottom: 30px;">
            <input type="text" id="gameSearch" placeholder="Find a game title..." 
                   onkeyup="filterGames()" 
                   style="padding: 12px 20px; width: 80%; max-width: 400px; border-radius: 25px; 
                          border: 1px solid #444; background: #1f1f2e; color: #fff; outline: none;">
        </div>

        <?php
        $romsDir = 'roms';
        $allowedExtensions = ['nes', 'gba', 'gbc', 'gb', 'sfc', 'smc', 'md', 'bin', 'gen', 'sms', 'gg', 'z64', 'n64', 'v60', 'pce', 'lnx', 'ngc', 'rom', 'ngp', 'npc', 'wsc', 'j64', 'a26', 'col', 'm3u', 'a78', 'a52', 'vec', 'vb', 'iso', 'chd', 'cue', 'pbp', 'unf', 'zip', '7z'];

        // ==================== OPTIMASI RAM: SCAN GAMBAR HANYA 1 KALI DI AWAL ====================
        $allImagesCache = [];
        if (is_dir('images/')) {
            $imgDirectory = new RecursiveDirectoryIterator('images/', RecursiveDirectoryIterator::SKIP_DOTS);
            $imgIterator = new RecursiveIteratorIterator($imgDirectory);
            foreach ($imgIterator as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $ext = strtolower($fileInfo->getExtension());
                    if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                        $allImagesCache[] = [
                            'path' => '/' . str_replace('\\', '/', $fileInfo->getPathname()),
                            'name' => strtolower($fileInfo->getBasename('.' . $ext))
                        ];
                    }
                }
            }
        }

        // Fungsi baru yang super cepat karena mencari di memori RAM, bukan di Harddisk Hosting lagi
        function getGameImageLocalCached($gameFile, $allImagesCache) {
            $rawBaseName = strtolower(pathinfo($gameFile, PATHINFO_FILENAME));
            
            // Tahap 1: Cari yang nama filenya sama persis
            foreach ($allImagesCache as $img) {
                if ($img['name'] === $rawBaseName) {
                    return $img['path'];
                }
            }
            
            // Tahap 2: Pencarian kemiripan string (Partial match)
            foreach ($allImagesCache as $img) {
                if (strpos($rawBaseName, $img['name']) !== false) {
                    return $img['path'];
                }
            }
            
            return 'https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg';
        }
        // ========================================================================================

        $vendorGroups = [
            'Nintendo' => [
                'class' => 'v-nintendo',
                'icon' => 'https://raw.githubusercontent.com/libretro/retroarch-assets/master/xmb/monochrome/png/Nintendo%20-%20Nintendo%20Entertainment%20System.png',
                'systems' => [
                    'nes'         => ['name' => 'Nintendo Entertainment System (NES)', 'core' => 'fceumm', 'badge' => 'Nintendo%20-%20Nintendo%20Entertainment%20System.png'],
                    'snes'        => ['name' => 'Super Nintendo (SNES)', 'core' => 'snes9x', 'badge' => 'Nintendo%20-%20Super%20Nintendo%20Entertainment%20System.png'],
                    'n64'         => ['name' => 'Nintendo 64', 'core' => 'mupen64plus_next', 'badge' => 'Nintendo%20-%20Nintendo%2064.png'],
                    'gb'          => ['name' => 'Game Boy', 'core' => 'gambatte', 'badge' => 'Nintendo%20-%20Game%20Boy.png'],
                    'gbc'         => ['name' => 'Game Boy Color', 'core' => 'gambatte', 'badge' => 'Nintendo%20-%20Game%20Boy%20Color.png'],
                    'gba'         => ['name' => 'Game Boy Advance (GBA)', 'core' => 'mgba', 'badge' => 'Nintendo%20-%20Game%20Boy%20Advance.png'],
                    'virtualboy'  => ['name' => 'Virtual Boy', 'core' => 'beetle_vb', 'badge' => 'Nintendo%20-%20Virtual%20Boy.png']
                ]
            ],
            'Sega' => [
                'class' => 'v-sega',
                'icon' => 'https://raw.githubusercontent.com/libretro/retroarch-assets/master/xmb/monochrome/png/Sega%20-%20Mega%20Drive%20-%20Genesis.png',
                'systems' => [
                    'sega-ms'    => ['name' => 'Sega Master System', 'core' => 'genesis_plus_gx', 'badge' => 'Sega%20-%20Master%20System%20-%20Mark%20III.png'],
                    'sega-md'    => ['name' => 'Sega Mega Drive / Genesis', 'core' => 'genesis_plus_gx', 'badge' => 'Sega%20-%20Mega%20Drive%20-%20Genesis.png'],
                    'sega-cd'    => ['name' => 'Sega CD', 'core' => 'genesis_plus_gx', 'badge' => 'Sega%20-%20Mega-CD%20-%20Sega%20CD.png'],
                    'sega-32x'   => ['name' => 'Sega 32X', 'core' => 'picodrive', 'badge' => 'Sega%20-%2032X.png'],
                    'gamegear'   => ['name' => 'Sega Game Gear', 'core' => 'genesis_plus_gx', 'badge' => 'Sega%20-%20Game%20Gear.png']
                ]
            ],
            'Sony PlayStation' => [
                'class' => 'v-sony',
                'icon' => 'https://raw.githubusercontent.com/libretro/retroarch-assets/master/xmb/monochrome/png/Sony%20-%20PlayStation.png',
                'systems' => [
                    'psx'        => ['name' => 'PlayStation 1 (PSX)', 'core' => 'pcsx_rearmed', 'badge' => 'Sony%20-%20PlayStation.png']
                ]
            ],
            'Atari' => [
                'class' => 'v-atari',
                'icon' => 'https://raw.githubusercontent.com/libretro/retroarch-assets/master/xmb/monochrome/png/Atari%20-%202600.png',
                'systems' => [
                    'atari2600'    => ['name' => 'Atari 2600', 'core' => 'stella2014', 'badge' => 'Atari%20-%202600.png'],
                    'atari5200'    => ['name' => 'Atari 5200', 'core' => 'a5200', 'badge' => 'Atari%20-%205200.png'],
                    'atari7800'    => ['name' => 'Atari 7800', 'core' => 'prosystem', 'badge' => 'Atari%20-%207800.png'],
                    'atari-lynx'   => ['name' => 'Atari Lynx', 'core' => 'handy', 'badge' => 'Atari%20-%20Lynx.png'],
                    'atari-jaguar' => ['name' => 'Atari Jaguar', 'core' => 'virtualjaguar', 'badge' => 'Atari%20-%20Jaguar.png']
                ]
            ],
            'Arcade & Others' => [
                'class' => 'v-arcade',
                'icon' => 'https://raw.githubusercontent.com/libretro/retroarch-assets/master/xmb/monochrome/png/MAME.png',
                'systems' => [
                    'arcade'        => ['name' => 'MAME / Arcade', 'core' => 'mame2003_plus', 'badge' => 'MAME.png'],
                    'neogeo'        => ['name' => 'Neo Geo', 'core' => 'fbneo', 'badge' => 'SNK%20-%20Neo%20Geo.png'],
                    'neogeopocket'  => ['name' => 'Neo Geo Pocket', 'core' => 'mednafen_ngp', 'badge' => 'SNK%20-%20Neo%20Geo%20Pocket%20Color.png'],
                    'wonderswan'    => ['name' => 'WonderSwan', 'core' => 'mednafen_wswan', 'badge' => 'Bandai%20-%20WonderSwan%20Color.png'],
                    'pcengine'      => ['name' => 'PC Engine / TurboGrafx-16', 'core' => 'mednafen_pce', 'badge' => 'NEC%20-%20PC%20Engine%20-%20TurboGrafx%2016.png'],
                    'msx'           => ['name' => 'MSX Computer', 'core' => 'fmsx', 'badge' => 'Microsoft%20-%20MSX.png'],
                    'colecovision'  => ['name' => 'ColecoVision', 'core' => 'gearcoleco', 'badge' => 'Coleco%20-%20ColecoVision.png'],
                    'amiga'         => ['name' => 'Commodore Amiga', 'core' => 'puae', 'badge' => 'Commodore%20-%20Amiga.png']
                ]
            ]
        ];

        foreach ($vendorGroups as $vendorName => $vendorData) {
            $subSystems = $vendorData['systems'];
            $vendorHasFolder = false;
            foreach ($subSystems as $folderName => $info) {
                if (is_dir($romsDir . '/' . $folderName)) {
                    $vendorHasFolder = true;
                    break;
                }
            }

            if ($vendorHasFolder) {
                echo '<div class="vendor-group ' . $vendorData['class'] . '">';
                echo '<div class="vendor-header" onclick="toggleVendor(this)">';
                echo '<div class="header-left">';
                echo '<img src="' . $vendorData['icon'] . '" class="vendor-icon" alt="logo">';
                echo '<span>' . $vendorName . '</span>';
                echo '</div>';
                echo '<span class="arrow">►</span>';
                echo '</div>';
                
                echo '<div class="vendor-content">';

                foreach ($subSystems as $folderName => $info) {
                    $folderPath = $romsDir . '/' . $folderName;

                    if (is_dir($folderPath)) {
                        $validGames = [];

                        $dirIterator = new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS);
                        $iterator = new RecursiveIteratorIterator($dirIterator);

                        foreach ($iterator as $fileInfo) {
                            if ($fileInfo->isFile()) {
                                $ext = strtolower($fileInfo->getExtension());
                                if (in_array($ext, $allowedExtensions)) {
                                    $relativePath = substr($fileInfo->getPathname(), strlen($folderPath) + 1);
                                    $relativePath = str_replace('\\', '/', $relativePath);
                                    $validGames[] = $relativePath;
                                }
                            }
                        }

                        $namesToHide = [];
                        foreach ($validGames as $gFile) {
                            if (strtolower(pathinfo($gFile, PATHINFO_EXTENSION)) === 'm3u') {
                                $m3uFilePath = $folderPath . '/' . $gFile;
                                if (file_exists($m3uFilePath)) {
                                    $lines = file($m3uFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                                    foreach ($lines as $line) {
                                        $baseName = pathinfo(trim($line), PATHINFO_FILENAME);
                                        if (!empty($baseName)) {
                                            $namesToHide[] = strtolower($baseName);
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($namesToHide)) {
                            $validGames = array_filter($validGames, function($gFile) use ($namesToHide) {
                                $currentBaseName = strtolower(pathinfo($gFile, PATHINFO_FILENAME));
                                $currentExt = strtolower(pathinfo($gFile, PATHINFO_EXTENSION));
                                
                                if ($currentExt === 'm3u') {
                                    return true;
                                }
                                
                                return !in_array($currentBaseName, $namesToHide);
                            });
                            $validGames = array_values($validGames);
                        }

                        echo '<div class="system-section">';
                        echo '<div class="system-header" onclick="toggleSystem(this)">';
                        echo '<div class="header-left">';
                        echo '<img src="https://raw.githubusercontent.com/libretro/retroarch-assets/master/xmb/monochrome/png/' . $info['badge'] . '" class="system-icon" alt="icon">';
                        echo '<span>' . $info['name'] . '</span>';
                        echo '</div>';
                        echo '<div class="header-right-side"><span class="game-count">' . count($validGames) . ' ROMs</span><span class="arrow">►</span></div>';
                        echo '</div>';
                        
                        echo '<div class="system-content">';
                        echo '<div class="game-grid">';

                        if (count($validGames) > 0) {
                            foreach ($validGames as $gameFile) {
                                $cleanTitle = pathinfo($gameFile, PATHINFO_FILENAME);
                                $cleanTitle = preg_replace('/\s*\([^)]*\)/', '', $cleanTitle);
                                $cleanTitle = preg_replace('/\s*\[[^\]]*\]/', '', $cleanTitle);
                                $cleanTitle = str_replace(['_', '-'], ' ', $cleanTitle);
                                $cleanTitle = str_replace('. ', ' ', $cleanTitle);
                                $cleanTitle = trim(ucwords($cleanTitle));
                                
                                $romPath = $folderName . '/' . $gameFile;
                                
                                // Panggil fungsi cache baru yang sudah dioptimasi kencang
                                $gameImage = getGameImageLocalCached($gameFile, $allImagesCache);
                                
                                $biosPath = '';
                                $core = $info['core'];

                                // ==================== CONFIG BIOS MULTI-CONSOLE ====================
                                if ($core === 'pcsx_rearmed' || $folderName === 'psx' || $folderName === 'ps1') {
                                    $biosPath = 'bios/scph1001.bin';
                                    if (stripos($gameFile, '(J)') !== false || stripos($gameFile, '(Japan)') !== false) {
                                        if (file_exists('bios/scph5500.bin')) { $biosPath = 'bios/scph5500.bin'; }
                                    } elseif (stripos($gameFile, '(E)') !== false || stripos($gameFile, '(Europe)') !== false) {
                                        if (file_exists('bios/scph5502.bin')) { $biosPath = 'bios/scph5502.bin'; }
                                    } else {
                                        if (file_exists('bios/scph5501.bin')) { $biosPath = 'bios/scph5501.bin'; }
                                        elseif (file_exists('bios/scph1001.bin')) { $biosPath = 'bios/scph1001.bin'; }
                                        elseif (file_exists('bios/scph7001.bin')) { $biosPath = 'bios/scph7001.bin'; }
                                        elseif (file_exists('bios/scph101.bin')) { $biosPath = 'bios/scph101.bin'; }
                                    }
                                }
                                elseif ($folderName === 'sega-cd') {
                                    $biosPath = 'bios/bios_CD_U.bin';
                                    if (stripos($gameFile, '(E)') !== false || stripos($gameFile, '(Europe)') !== false) {
                                        if (file_exists('bios/bios_CD_E.bin')) { $biosPath = 'bios/bios_CD_E.bin'; }
                                    } elseif (stripos($gameFile, '(J)') !== false || stripos($gameFile, '(Japan)') !== false) {
                                        if (file_exists('bios/bios_CD_J.bin')) { $biosPath = 'bios/bios_CD_J.bin'; }
                                    } else {
                                        if (file_exists('bios/bios_CD_U.bin')) { $biosPath = 'bios/bios_CD_U.bin'; }
                                    }
                                }
                                elseif ($folderName === 'neogeo' || $folderName === 'neo-geo') {
                                    if (file_exists('bios/neogeo.zip')) { $biosPath = 'bios/neogeo.zip'; }
                                }
                                elseif ($core === 'a5200' || $folderName === 'atari5200') {
                                    if (file_exists('bios/5200.rom')) { $biosPath = 'bios/5200.rom'; } 
                                    elseif (file_exists('bios/atari5200.bin')) { $biosPath = 'bios/atari5200.bin'; }
                                }
                                elseif ($core === 'handy' || $folderName === 'atari-lynx') {
                                    if (file_exists('bios/lynxboot.img')) { $biosPath = 'bios/lynxboot.img'; }
                                }
                                elseif ($folderName === 'gamegear') {
                                    if (file_exists('bios/bios.gg')) { $biosPath = 'bios/bios.gg'; }
                                }
                                elseif ($core === 'mgba' || $folderName === 'gba') {
                                    if (file_exists('bios/gba_bios.bin')) { $biosPath = 'bios/gba_bios.bin'; }
                                }
                                elseif ($core === 'mednafen_pce' || $folderName === 'pcengine') {
                                    if (file_exists('bios/syscard3.pce')) { $biosPath = 'bios/syscard3.pce'; }
                                }
                                elseif ($core === 'gearcoleco' || $folderName === 'colecovision') {
                                    if (file_exists('bios/coleco.rom')) { $biosPath = 'bios/coleco.rom'; } 
                                    elseif (file_exists('bios/colecovision.rom')) { $biosPath = 'bios/colecovision.rom'; } 
                                    elseif (file_exists('bios/Coleco_Bios.bin')) { $biosPath = 'bios/Coleco_Bios.bin'; } 
                                    elseif (file_exists('bios/coleco_bios.bin')) { $biosPath = 'bios/coleco_bios.bin'; } 
                                    elseif (file_exists('bios/BIOS.col')) { $biosPath = 'bios/BIOS.col'; } 
                                    elseif (file_exists('bios/bios.col')) { $biosPath = 'bios/bios.col'; }
                                }
                                elseif ($core === 'fmsx' || $folderName === 'msx' || $folderName === 'msx2') {
                                    if (stripos($gameFile, '.mx2') !== false || $folderName === 'msx2') {
                                        if (file_exists('bios/MSX2.ROM')) { $biosPath = 'bios/MSX2.ROM'; }
                                    } else {
                                        if (file_exists('bios/MSX.ROM')) { $biosPath = 'bios/MSX.ROM'; }
                                    }
                                }
                                elseif ($core === 'puae' || $core === 'uae4arm' || $folderName === 'amiga') {
                                    $biosPath = 'bios/kick34005.A500'; 
                                    if (file_exists('bios/kick34005.A500')) { $biosPath = 'bios/kick34005.A500'; } 
                                    elseif (file_exists('bios/kick13.rom')) { $biosPath = 'bios/kick13.rom'; } 
                                    elseif (file_exists('bios/kick31.rom')) { $biosPath = 'bios/kick31.rom'; }
                                }

                                $biosData = '';
                                if (!empty($biosPath) && file_exists(__DIR__ . '/' . $biosPath)) {
                                    $biosData = $biosPath;
                                }
                                // ===================================================================
                                
                                echo '<form action="playing" method="POST" style="display: inline;">';
                                echo '  <input type="hidden" name="rom" value="' . htmlspecialchars($romPath) . '">';
                                echo '  <input type="hidden" name="system" value="' . htmlspecialchars($info['core']) . '">';
                                if (!empty($biosData)) {
                                    echo '  <input type="hidden" name="bios" value="' . htmlspecialchars($biosData) . '">';
                                }
                                echo '  <button type="submit" class="game-card" style="background: none; border: none; text-align: left; width: 100%; padding: 0; cursor: pointer; color: inherit; font-family: inherit; display: block;">';
                                
                                // DI SINI SUDAH DITAMBAHKAN loading="lazy" BIAR BROWSER HP TIDAK LAG SAAT MEMUAT GAMBAR
                                echo '    <img src="' . $gameImage . '" loading="lazy" style="width: 100%; height: 110px; object-fit: contain; border-radius: 4px; margin-bottom: 12px; background: rgba(0,0,0,0.2);" alt="cover">';
                                
                                echo '    <p class="game-title">' . htmlspecialchars($cleanTitle) . '</p>';
                                echo '  </button>';
                                echo '</form>';
                            }
                        } else {
                            echo '<p class="empty-message">There are no games in this folder yet..</p>';
                        }

                        echo '</div>'; 
                        echo '</div>'; 
                        echo '</div>'; 
                    }
                }

                echo '</div>'; 
                echo '</div>'; 
            }
        }
        ?>

    <?php endif; ?>
</div>

<script>
    function filterGames() {
        let input = document.getElementById('gameSearch').value.toLowerCase();
        let cards = document.getElementsByClassName('game-card');
        
        for (let i = 0; i < cards.length; i++) {
            let title = cards[i].querySelector('.game-title').innerText.toLowerCase();
            if (title.includes(input)) {
                cards[i].style.setProperty('display', 'flex', 'important');
            } else {
                cards[i].style.setProperty('display', 'none', 'important');
            }
        }

        if (input === "") {
            document.querySelectorAll('.vendor-content, .system-content').forEach(el => el.style.display = "none");
            return;
        }

        let systems = document.getElementsByClassName('system-section');
        for (let i = 0; i < systems.length; i++) {
            let content = systems[i].querySelector('.system-content');
            let hasVisibleGame = systems[i].querySelector('.game-card[style*="display: flex"]') !== null;
            
            if (hasVisibleGame) {
                content.style.display = "block";
            } else {
                content.style.display = "none";
            }
        }

        let vendors = document.getElementsByClassName('vendor-group');
        for (let i = 0; i < vendors.length; i++) {
            let content = vendors[i].querySelector('.vendor-content');
            let hasVisibleGame = vendors[i].querySelector('.game-card[style*="display: flex"]') !== null;
            
            if (hasVisibleGame) {
                content.style.display = "block";
            } else {
                content.style.display = "none";
            }
        }
    }

    function toggleVendor(headerElement) {
        const content = headerElement.nextElementSibling;
        const arrow = headerElement.querySelector('.arrow');
        content.style.display = (content.style.display === "block") ? "none" : "block";
        arrow.classList.toggle('open-arrow');
    }

    function toggleSystem(headerElement) {
        const content = headerElement.nextElementSibling;
        const arrow = headerElement.querySelector('.arrow');
        content.style.display = (content.style.display === "block") ? "none" : "block";
        arrow.classList.toggle('open-arrow');
    }
    
(function() {
    function eksekusiBlokir() {
        document.body.innerHTML = 
            '<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:#0b0b0b;color:#fff;z-index:9999999;display:flex;justify-content:center;align-items:center;flex-direction:column;font-family:sans-serif;padding:20px;text-align:center;">' +
            '<div style="background:#161616;padding:40px;border-radius:10px;max-width:450px;border:2px solid #ff4a4a;box-shadow:0 0 30px rgba(255,74,74,0.3);">' +
            '<h2 style="color:#ff4a4a;font-size:28px;margin-top:0;margin-bottom:15px;">Game Locked! &#128274;</h2>' +
            '<p style="color:#bbb;font-size:16px;line-height:1.6;margin-bottom:25px;">' +
            'Sorry Boss, the game cannot be loaded because you are using **AdBlock / uBlock**. Please disable that extension and refresh this page to play.' +
            '</p>' +
            '<button onclick="window.location.reload()" style="background:#ff4a4a;color:#fff;border:none;padding:14px 0;font-size:16px;font-weight:bold;border-radius:5px;cursor:pointer;width:100%;transition:0.2s;box-shadow:0 4px 10px rgba(255,74,74,0.4);">I have already turned it off, Refresh! &#127918;</button>' +
            '</div>' +
            '</div>';
        document.body.style.overflow = 'hidden';
    }

    function mulaiDeteksi() {
        var baitKosmetik = document.createElement('div');
        baitKosmetik.className = 'pub_300x250 ad-zone advertising_blurb banner-ads adsbox google-adsense-slot';
        baitKosmetik.id = 'bottom-ads-container';
        baitKosmetik.style.cssText = 'width:1px !important; height:1px !important; opacity:0.01 !important; position:fixed !important; top:0 !important; left:0 !important; z-index:-9999 !important; display:block !important; visibility:visible !important;';
        document.body.appendChild(baitKosmetik);

        var baitGambar = new Image();
        baitGambar.style.cssText = 'width:1px; height:1px; position:fixed; top:0; left:0; opacity:0.01;';
        
        baitGambar.onerror = function() {
            eksekusiBlokir();
        };
        baitGambar.src = 'https://googleads.g.doubleclick.net/pagead/viewthroughconversion/100000000/?value=0&guid=ON&script=0';
        document.body.appendChild(baitGambar);

        setTimeout(function() {
            var style = window.getComputedStyle(baitKosmetik);
            if (
                baitKosmetik.offsetHeight === 0 || 
                baitKosmetik.offsetWidth === 0 || 
                style.display === 'none' || 
                style.visibility === 'hidden'
            ) {
                eksekusiBlokir();
            } else {
                baitKosmetik.remove();
                baitGambar.remove();
            }
        }, 350);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mulaiDeteksi);
    } else {
        mulaiDeteksi();
    }
})();

function cekKoneksiInternet() {
    if (!navigator.onLine) {
        window.location.href = "error?code=offline";
    }
}
window.addEventListener('offline', cekKoneksiInternet);
</script>

</body>
</html>