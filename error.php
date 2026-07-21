<?php
if (isset($_GET['code'])) {
    if ($_GET['code'] == 'offline') {
        $status = "OFFLINE";
        $message = "CONNECTION LOST.<br>INTERNET CONNECTION LOST. CHECK YOUR ROUTER OR DATA ALLOWANCE.";
    } else {
        $status = intval($_GET['code']);
    }
} else {
    $status = isset($_SERVER['REDIRECT_STATUS']) ? $_SERVER['REDIRECT_STATUS'] : 404;
}

$error_messages = [
    400 => "BAD REQUEST.<br>THE INPUT YOU SUBMITTED IS IN THE WRONG FORMAT.",
    401 => "UNAUTHORIZED.<br>ILLEGAL LOGIN! YOU HAVEN'T INSERTED COINS (YOU AREN'T LOGGED IN).",
    403 => "FORBIDDEN.<br>RESTRICTED AREA! YOU DO NOT HAVE ACCESS HERE.",
    404 => "PAGE NOT FOUND.<br>PAGE NOT FOUND / FILE MISSING.",
    405 => "METHOD NOT ALLOWED.<br>ILLEGAL MOVE! DIRECT ACCESS IS NOT PERMITTED.",
    408 => "REQUEST TIMEOUT.<br>YOU ARE AFK! THE BROWSER IS TAKING TOO LONG TO RESPOND.",
    419 => "PAGE EXPIRED.<br>SESSION EXPIRED! PLEASE REFRESH THE PAGE.",
    429 => "TOO MANY REQUESTS.<br>SPAM DETECTED! YOU ARE ON COOLDOWN.",
    500 => "INTERNAL SERVER ERROR.<br>SYSTEM CRASH! ADMIN IS REFILLING THE SERVER'S HEALTH.",
    502 => "BAD GATEWAY.<br>THE SERVER NETWORK IS CONGESTED.",
    503 => "SERVICE UNAVAILABLE.<br>SERVER OVERLOAD! TOO MANY PLAYERS.",
    504 => "GATEWAY TIMEOUT.<br>SLOW NETWORK! THE MAIN SERVER IS TAKING TOO LONG TO RESPOND."
];

if (!isset($message)) {
    $message = isset($error_messages[$status]) ? $error_messages[$status] : "UNKNOWN ERROR [Code: $status].<br>A SYSTEM ERROR HAS OCCURRED.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $status; ?> - GAME OVER</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #0b0b1a; color: #fff; font-family: 'Press Start 2P', monospace;
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            overflow-x: hidden; flex-direction: column; text-align: center; padding: 20px;
        }
        .container { max-width: 500px; width: 100%; z-index: 1; }
        
        .error-code { font-size: 3.5rem; color: #ff0055; text-shadow: 3px 3px 0px #00ffff; margin-bottom: 10px; }
        .game-over { font-size: 1.2rem; color: #ffcc00; margin-bottom: 20px; }
        .message { font-size: 0.7rem; line-height: 1.6; color: #a0a0c0; margin-bottom: 30px; }
        
        .ghost { 
            width: 50px; height: 50px; background: #00ffff; margin: 0 auto 30px auto; 
            animation: float 2s ease-in-out infinite; 
            clip-path: polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 85% 100%, 70% 85%, 55% 100%, 45% 100%, 30% 85%, 15% 100%, 0% 80%, 0% 20%);
        }

        .btn-restart { 
            display: block; width: 100%; padding: 15px; font-family: 'Press Start 2P', monospace; 
            font-size: 0.8rem; color: #0b0b1a; background-color: #00ff66; 
            border: 3px solid #fff; text-decoration: none;
        }

        @media screen and (max-width: 480px) {
            .error-code { font-size: 2.5rem; }
            .game-over { font-size: 1rem; }
            .container { padding: 10px; }
        }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code" id="error-title"><?php echo $status; ?></div>
        <div class="game-over" id="game-status">GAME OVER</div>
        <div class="ghost"></div>
        <p class="message" id="error-message"><?php echo $message; ?></p>
        <a href="home" class="btn-restart">RESTART</a>
    </div>

    <script>
        if (!navigator.onLine) {
            document.getElementById('error-title').innerText = "OFFLINE";
            document.getElementById('game-status').innerText = "CONNECTION LOST";
            document.getElementById('error-message').innerHTML = "INTERNET CONNECTION LOST.<br>CHECK YOUR ROUTER OR DATA ALLOWANCE.";
        }
        window.addEventListener('offline', function() {
            document.getElementById('error-title').innerText = "OFFLINE";
            document.getElementById('game-status').innerText = "CONNECTION LOST";
            document.getElementById('error-message').innerHTML = "INTERNET CONNECTION LOST.<br>CHECK YOUR ROUTER OR DATA ALLOWANCE.";
        });
    </script>
</body>
</html>