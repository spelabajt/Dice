<?php
session_start();

$dice_count = (isset($_GET['dice']) && is_numeric($_GET['dice'])) ? (int)$_GET['dice'] : 1;
$reps_total = (isset($_GET['reps']) && is_numeric($_GET['reps'])) ? (int)$_GET['reps'] : 1;
$users = isset($_SESSION['butterflies']) ? $_SESSION['butterflies'] : [];

if (empty($users)) {
    header("Location: s1.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Butterfly Sanctuary - Game</title>
    <style>
        :root {
            --pastel-purple: #d896ff;
            --blue: #a3e4ff;
            --glass: rgba(0, 0, 0, 0.85);
        }

        *, *::before, *::after { box-sizing: border-box; outline: none; border: none; }

        body {
            background: url("../img/background.jpg") no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            
            /* CENTRIRANJE CELEGA TELESA */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Navpično centriranje */
            align-items: center;     /* Vodoravno centriranje */
            min-height: 100vh;       /* Celotna višina okna */
        }

        .game-container {
            display: flex;
            flex-wrap: wrap; 
            justify-content: center; /* Centriranje kartic v vrsti */
            align-items: center;
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            margin: auto; /* Dodatno zagotovilo za sredino */
        }

        .player-card {
            background: var(--glass);
            border: 2px solid var(--pastel-purple);
            border-radius: 20px;
            width: 280px; 
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
            transition: transform 0.3s, opacity 0.5s;
        }

        .dice-area {
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 8px;
        }

        .dice-area img {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
            filter: drop-shadow(0 0 6px var(--pastel-purple));
        }

        .player-name {
            font-size: 1.5rem;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 12px;
            text-shadow: 0 0 8px var(--pastel-purple);
        }

        .stats-box {
            display: flex;
            justify-content: space-around;
            background: rgba(0,0,0,0.5);
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .stat-val { font-size: 1.5rem; font-weight: bold; color: var(--blue); }
        .stat-label { font-size: 0.65rem; text-transform: uppercase; opacity: 0.8; }

        .btn-roll {
            background: linear-gradient(45deg, #a044ff, #d896ff);
            color: white;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 900;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .btn-roll:hover:not(:disabled) { transform: scale(1.03); }
        .btn-roll:disabled { background: #333; opacity: 0.6; cursor: not-allowed; }

        #finish-container {
			margin-top: -250px;
			margin-bottom: 200px;
            display: none;
            text-align: center;
        }

        .btn-finish {
            background: #a3e4ff;
            color: #000;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 900;
            text-decoration: none;
            text-transform: uppercase;
            box-shadow: 0 0 20px rgba(163, 228, 255, 0.5);
            cursor: pointer;
        }

        .btn-back {
            margin-top: 20px;
            color: white;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 0.8rem;
            opacity: 0.6;
        }
        .btn-back:hover { opacity: 1; }
    </style>
</head>
<body>

    <div class="game-container">
        <?php foreach ($users as $index => $name): ?>
            <div class="player-card" id="player-<?php echo $index; ?>">
                <div class="dice-area" id="dice-area-<?php echo $index; ?>">
                    <span style="opacity: 0.2; font-size: 0.9rem;">READY</span>
                </div>

                <div class="player-name"><?php echo htmlspecialchars($name); ?></div>

                <div class="stats-box">
                    <div class="stat-item">
                        <span class="stat-val" id="score-<?php echo $index; ?>">0</span>
                        <span class="stat-label">Points</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-val" id="reps-<?php echo $index; ?>"><?php echo $reps_total; ?></span>
                        <span class="stat-label">Reps left</span>
                    </div>
                </div>

                <button class="btn-roll" onclick="rollDice(<?php echo $index; ?>, <?php echo $dice_count; ?>)" id="btn-<?php echo $index; ?>">Roll Dice</button>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="finish-container">
        <form action="s3.php" method="POST" id="finish-form">
            <input type="hidden" name="final_data" id="final_data">
            <button type="button" class="btn-finish" onclick="submitResults()">GO TO PODIUM →</button>
        </form>
    </div>

    <a href="s1.php" class="btn-back">← Back</a>

    <script>
    function rollDice(playerIndex, diceCount) {
        const btn = document.getElementById(`btn-${playerIndex}`);
        const diceArea = document.getElementById(`dice-area-${playerIndex}`);
        const scoreEl = document.getElementById(`score-${playerIndex}`);
        const repsEl = document.getElementById(`reps-${playerIndex}`);

        let currentReps = parseInt(repsEl.innerText);
        if (currentReps <= 0) return;

        btn.disabled = true;
        btn.innerText = "...";

        let animations = "";
        for (let i = 0; i < diceCount; i++) {
            animations += `<img src="../img/kocka.gif?t=${Date.now() + i}" alt="Roll">`;
        }
        diceArea.innerHTML = animations;

        setTimeout(() => {
            let totalTurnValue = 0;
            let resultsHtml = "";

            for (let i = 0; i < diceCount; i++) {
                const result = Math.floor(Math.random() * 6) + 1;
                totalTurnValue += result;
                resultsHtml += `<img src="../img/k${result}.jpg" alt="${result}">`;
            }
            
            diceArea.innerHTML = resultsHtml;
            scoreEl.innerText = parseInt(scoreEl.innerText) + totalTurnValue;
            
            currentReps--;
            repsEl.innerText = currentReps;

            if (currentReps > 0) {
                btn.disabled = false;
                btn.innerText = "Roll Dice";
            } else {
                btn.innerText = "DONE";
                btn.style.background = "#222";
                document.getElementById(`player-${playerIndex}`).style.opacity = "0.5";
            }

            checkAllDone();
        }, 1200); 
    }

    function checkAllDone() {
        const reps = document.querySelectorAll('.stat-val[id^="reps-"]');
        let allDone = true;
        reps.forEach(r => {
            if (parseInt(r.innerText) > 0) allDone = false;
        });

        if (allDone) {
            document.getElementById('finish-container').style.display = 'block';
        }
    }

    function submitResults() {
        let results = [];
        <?php foreach ($users as $index => $name): ?>
            results.push({
                name: "<?php echo addslashes($name); ?>",
                score: parseInt(document.getElementById("score-<?php echo $index; ?>").innerText)
            });
        <?php endforeach; ?>
        
        document.getElementById('final_data').value = JSON.stringify(results);
        document.getElementById('finish-form').submit();
    }
    </script>
</body>
</html>