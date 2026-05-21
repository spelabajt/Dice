<?php
session_start();
if (isset($_GET['reset'])) { 
    $_SESSION['butterflies'] = []; 
    header("Location: s1.php?reset_session=1"); 
    exit; 
}

$results = isset($_POST['final_data']) ? json_decode($_POST['final_data'], true) : [];
if (empty($results)) { header("Location: s1.php"); exit; }

usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
$top3 = array_slice($results, 0, 3);
$others = array_slice($results, 3);
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Victory - Podium</title>
    <style>
        :root { 
            --p-purple: #d896ff; 
            --blue: #a3e4ff; 
            --glass: rgba(0, 0, 0, 0.85);
            --gold: #ffd700;
            --silver: #e0e0e0;
            --bronze: #cd7f32;
        }

        body {
            background: url("../img/background.jpg") no-repeat center center fixed; 
            background-size: cover;
            color: white; 
            font-family: 'Segoe UI', sans-serif; 
            display: flex; 
            flex-direction: column;
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0;
            padding: 40px 20px;
        }

        h1 {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 15px;
            font-weight: 900;
            color: #ffffff;
            text-shadow: 0 0 15px #d896ff, 0 0 30px #d896ff, 0 0 50px #be61ff;
            transition: all 0.8s ease;
            font-size: 4.5rem;
            margin-bottom: 60px;
            user-select: none;
        }

        .podium-wrapper {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 20px;
            margin-bottom: 50px;
            height: 350px;
            width: 100%;
        }

        .podium-card {
            background: var(--glass);
            border: 2px solid var(--p-purple);
            border-radius: 25px 25px 15px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 25px;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.7);
            animation: fadeIn 0.8s ease;
            position: relative;
            overflow: hidden; /* Da konfeti ne gledajo čez robove */
        }

       .rank-1 { 
			height: 320px; 
			order: 2; 
			width: 220px; 
			border-color: var(--gold) !important; 
			box-shadow: 0 0 40px rgba(255, 215, 0, 0.4);
			
			/* Sprememba tukaj: */
			background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url("../img/konfeti.gif");
			background-size: cover;
			background-position: center;
			background-blend-mode: normal; /* Normalno prikazovanje GIF-a */
		}
        .rank-2 { height: 250px; order: 1; width: 190px; border-color: var(--silver) !important; }
        .rank-3 { height: 200px; order: 3; width: 190px; border-color: var(--bronze) !important; }

        .name-display { color: #f0c3ff; font-weight: bold; font-size: 1.5rem; margin: 10px 0; z-index: 2; }
        
        .score-container { display: flex; align-items: baseline; gap: 5px; z-index: 2; }
        .score-display { color: var(--blue); font-size: 2.2rem; font-weight: 900; line-height: 1; }
        .points-label { color: var(--blue); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }

        .others-list { width: 100%; max-width: 500px; margin-top: 20px; }
        .rank-item { 
            display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; 
            background: rgba(255,255,255,0.05); margin-bottom: 10px; border-radius: 15px;
            border: 1px solid rgba(216, 150, 255, 0.2);
        }

        .btn-home {
            background: linear-gradient(45deg, #a044ff, #d896ff);
            color: white; text-decoration: none; padding: 18px 50px;
            border-radius: 50px; font-weight: 900; text-transform: uppercase;
            letter-spacing: 2px; transition: 0.4s; margin-top: 30px;
        }
        .btn-home:hover { transform: scale(1.1); box-shadow: 0 0 30px var(--p-purple); background: white; color: black; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <h1>Victory</h1>

    <div class="podium-wrapper">
        <?php foreach ($top3 as $i => $p): 
            $rank = $i + 1;
            $icons = ['🥇', '🥈', '🥉'];
        ?>
            <div class="podium-card rank-<?php echo $rank; ?>">
                <div style="font-size: 2.5rem; z-index: 2;"><?php echo $icons[$i]; ?></div>
                <img src="../img/butterfly.png" style="width: 50px; margin: 10px 0; z-index: 2;" alt="">
                <div class="name-display"><?php echo htmlspecialchars($p['name']); ?></div>
                <div class="score-container">
                    <span class="score-display"><?php echo $p['score']; ?></span>
                    <span class="points-label">pts</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($others)): ?>
        <div class="others-list">
            <?php foreach ($others as $i => $p): ?>
                <div class="rank-item">
                    <span>#<?php echo $i + 4; ?> <?php echo htmlspecialchars($p['name']); ?></span>
                    <div style="display: flex; align-items: baseline; gap: 4px;">
                        <span style="color: var(--blue); font-weight: bold; font-size: 1.2rem;"><?php echo $p['score']; ?></span>
                        <span style="color: var(--blue); font-size: 0.7rem; opacity: 0.7;">pts</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="?reset=1" class="btn-home">Play Again</a>

</body>
</html>