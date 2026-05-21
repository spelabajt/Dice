<?php
session_start();

// 1. Reset seje ob prihodu iz začetne strani (index.html)
if (isset($_GET['reset_session'])) {
    $_SESSION['butterflies'] = [];
    header("Location: s1.php"); // Očistimo URL
    exit;
}

// Inicializacija seje, če še ne obstaja
if (!isset($_SESSION['butterflies'])) {
    $_SESSION['butterflies'] = [];
}

// 2. Brisanje posameznega uporabnika
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    if (isset($_SESSION['butterflies'][$id])) {
        unset($_SESSION['butterflies'][$id]);
        $_SESSION['butterflies'] = array_values($_SESSION['butterflies']);
    }
    header("Location: s1.php?adding=true");
    exit;
}

// 3. Dodajanje uporabnika preko POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $name = trim(htmlspecialchars($_POST['username']));
    if ($name !== "" && count($_SESSION['butterflies']) < 6) {
        $_SESSION['butterflies'][] = $name;
    }
    header("Location: s1.php?adding=true");
    exit;
}

// 4. Ročni gumb za reset vseh podatkov
if (isset($_POST['reset_all'])) {
    session_destroy();
    header("Location: s1.php");
    exit;
}

$count = count($_SESSION['butterflies']);
$show_input = isset($_GET['adding']) && $count < 6;
$is_active = ($count > 0 || $show_input);
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Butterfly Sanctuary</title>
    <style>
        :root {
            --bright-neon: #f0c3ff; 
            --pastel-purple: #d896ff;
            --blue: #a3e4ff;
            --glass: rgba(0, 0, 0, 0.8);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            outline: none !important;
            border: none !important;
            -webkit-tap-highlight-color: transparent;
        }

        body, html { 
            height: 100%; 
            margin: 0; 
        }

        body {
            background: url("../img/background.jpg") no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Dinamično pozicioniranje z večjim paddingom zgoraj */
            justify-content: <?php echo $is_active ? 'flex-start' : 'center'; ?>;
            min-height: 100vh;
            padding: <?php echo $is_active ? '80px 20px' : '0'; ?>;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-x: hidden;
        }

        .header-section { text-align: center; z-index: 10; }

        h1 {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 15px;
            font-weight: 900;
            color: #ffffff;
            text-shadow: 
                0 0 15px #d896ff,
                0 0 30px #d896ff,
                0 0 50px #be61ff;
            transition: all 0.8s ease;
            /* Povečane dimenzije naslova */
            font-size: <?php echo $is_active ? '4.5rem' : '7.5rem'; ?>;
            margin-bottom: <?php echo $is_active ? '50px' : '40px'; ?>;
            user-select: none;
        }

        .btn-add {
            background: linear-gradient(45deg, #a044ff, #d896ff);
            color: white !important;
            font-weight: bold;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            transition: all 0.7s ease;
            padding: <?php echo $is_active ? '15px 50px' : '22px 80px'; ?>;
            font-size: <?php echo $is_active ? '1.2rem' : '1.8rem'; ?>;
            margin-bottom: 30px;
        }

        .btn-add:hover {
            box-shadow: 0 0 30px #d896ff;
            transform: scale(1.05);
            background: white;
            color: black !important;
        }

        .butterfly-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px; /* Večji razmik med karticami */
            width: 100%;
            max-width: 1200px;
            margin-top: 40px;
        }

        .butterfly-card {
            background: var(--glass);
            border: 2px solid var(--pastel-purple) !important;
            padding: 25px;
            border-radius: 25px;
            width: 220px; 
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.7);
            animation: fadeIn 0.6s ease;
            position: relative;
        }

        .card-butterfly-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .remove-btn {
            position: absolute;
            top: 12px;
            right: 15px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }

        .remove-btn:hover {
            color: #ff4d4d;
            transform: scale(1.3);
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .name-display { 
            color: #f0c3ff; 
            font-weight: bold; 
            font-size: 1.6rem; 
            margin-bottom: 5px; 
        }

        .game-config-box {
            background: var(--glass);
            border: 2px solid var(--blue) !important;
            padding: 35px 60px;
            border-radius: 30px;
            margin-top: 70px; /* Velik odmik od igralcev */
            display: flex;
            align-items: center;
            gap: 40px;
            backdrop-filter: blur(15px);
            box-shadow: 0 0 40px rgba(163, 228, 255, 0.15);
        }

        .config-label { color: var(--blue); font-weight: bold; font-size: 1rem; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 2px;}

        .btn-start {
            background: #d896ff;
            color: #000;
            padding: 15px 45px;
            border-radius: 15px;
            font-weight: 900;
            cursor: pointer;
            font-size: 1.3rem;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .btn-start:hover { 
            background: #ffffff;
            box-shadow: 0 0 30px #ffffff;
            transform: translateY(-3px);
        }

        .btn-clear-all {
            background: none;
            border: 1px solid rgba(255,255,255,0.2) !important;
            color: rgba(255,255,255,0.4);
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 50px;
            transition: 0.3s;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        .btn-clear-all:hover { color: #ffb7ff; border-color: #ffb7ff !important; }

        input[type="text"] {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--pastel-purple) !important;
            background: rgba(255,255,255,0.05);
            color: white;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 15px;
        }

        select, input[type="number"] {
            background: #000;
            color: white;
            border: 1px solid var(--blue) !important;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header-section">
        <a href="s1.php" style="text-decoration: none;"><h1>Butterfly</h1></a>
        <a href="?adding=true" class="btn-add">ADD PLAYER</a>
    </div>

    <?php if ($is_active): ?>
    <div class="butterfly-container">
        <?php foreach ($_SESSION['butterflies'] as $index => $user): ?>
            <div class="butterfly-card">
                <a href="?remove=<?php echo $index; ?>" class="remove-btn" title="Remove player">&times;</a>
                <img src="../img/butterfly.png" alt="Butterfly" class="card-butterfly-img">
                <div class="name-display"><?php echo $user; ?></div>
                <div style="color: var(--blue); font-size: 0.85rem; font-weight: bold; letter-spacing: 3px;">BUTTERFLY</div>
            </div>
        <?php endforeach; ?>

        <?php if ($show_input): ?>
            <div class="butterfly-card" style="border-color: var(--blue) !important; width: 260px;">
                <form method="post" action="s1.php">
                    <input type="text" name="username" required autofocus placeholder="Username">
                    <button type="submit" style="width: 100%; background: var(--blue); color: #000; font-weight: 900; padding: 15px; border-radius: 12px; cursor: pointer; text-transform: uppercase;">SAVE</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($count >= 3): ?>
        <form action="s2.php" method="GET">
            <div class="game-config-box">
                <div>
                    <div class="config-label">DICE</div>
                    <select name="dice">
                        <option value="1">1 Die</option>
                        <option value="2">2 Dice</option>
                        <option value="3">3 Dice</option>
                    </select>
                </div>
                <div>
                    <div class="config-label">REPS</div>
                    <input type="number" name="reps" value="1" min="1" max="10" style="width: 80px;">
                </div>
                <button type="submit" class="btn-start">START GAME</button>
            </div>
        </form>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="reset_all" class="btn-clear-all">Clear All Data</button>
    </form>
    <?php endif; ?>

</body>
</html>