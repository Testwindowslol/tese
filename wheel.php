<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('@/header.php');

$paginaname = 'Fortune Wheel';

if (!isset($_SESSION['ID'])) {
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            text-align: center;
        }

        .wheel-container {
            position: relative;
            width: 400px;
            height: 400px;
            margin: 50px auto;
        }

        .wheel {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(#45007b, #070d19);
            transition: transform 4s ease-in-out;
            position: absolute;
        }

        .segment {
            position: absolute;
            width: 100%;
            height: 100%;
            clip-path: polygon(50% 0%, 50% 50%, 50% 100%);
            transform: rotate(30deg);
        }

        .reward {
            position: absolute;
            width: 200px;
            text-align: center;
            white-space: nowrap;
            transform: translateX(-50%);
        }

        #spin-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #45007b;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        h1 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="page-content">
        <div class="alert alert-fill-primary">
            <span data-feather="gift" class="icon-md text-light mr-2"></span>
            <span><?php echo $paginaname ?></span>
        </div>
        <button type="submit" class="btn btn-primary" id="spin-button">Spin Now</button>
    </div>

    <div class="wheel-container">
        <div id="wheel-container">
            <canvas id="canvas" width="400" height="400"></canvas>
        </div>
    </div>
    <h1 id="result"></h1>
</div>

<script src="js/winwheel.js"></script>
<script src="js/TweenMax.min.js"></script>
<script>
    const result = document.getElementById('result');
    const spinButton = document.getElementById('spin-button');
    let spinning = false;
    let rotationAngle = 0; // Pour stocker la rotation actuelle

    const segments = [
        { fillStyle: "#45007b", text: "Gold Plan" },
        { fillStyle: "#070d19", text: "Nothing" },
        { fillStyle: "#45007b", text: "Home2 Plan" },
        { fillStyle: "#070d19", text: "Nothing" },
        { fillStyle: "#45007b", text: "Diamond Plan" },
        { fillStyle: "#070d19", text: "Nothing" },
        { fillStyle: "#45007b", text: "5$ balance" },
        { fillStyle: "#070d19", text: "Nothing" },
        { fillStyle: "#45007b", text: "10$ balance" },
        { fillStyle: "#070d19", text: "Nothing" },
        { fillStyle: "#45007b", text: "15$ balance" },
        { fillStyle: "#070d19", text: "Nothing" }
    ];

    const theWheel = new Winwheel({
        'numSegments': segments.length,
        'segments': segments,
        'animation': {
            'type': 'spinToStop',
            'duration': 5,
            'spins': 8,
            'autoDraw': false // Désactivez le dessin automatique
        }
    });

    theWheel.draw(false);

    spinButton.addEventListener('click', () => {
        if (!spinning) {
            spinning = true;
            spinButton.disabled = true;

            // Réglez la vitesse de rotation constante (sens horaire)
            theWheel.animation.spins = 8;
            rotationAngle += 360; // Ajoutez un tour complet
            theWheel.animation.direction = 'clockwise'; // Réglez la direction du sens horaire
            theWheel.animation.stopAngle = rotationAngle;

            theWheel.startAnimation();

            setTimeout(() => {
                result.innerHTML = `<h3>You win: ${theWheel.getIndicatedSegment().text}</h3>`;
                spinButton.disabled = false;
                spinning = false;
            }, 6000);
        }
    });
</script>
</body>
</html>