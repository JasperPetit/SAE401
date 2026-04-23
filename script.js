const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const scoreDisplay = document.getElementById('score');
const startButton = document.getElementById('startButton');

const gridSize = 20; // Taille d'un segment de serpent et de la nourriture
let penguin; // Le "serpent" est maintenant un pingouin
let food; // La nourriture est un poisson
let direction;
let score;
let gameInterval;
let gameSpeed = 150; // Millisecondes par frame (le pingouin glisse un peu moins vite)
let snowflakes = []; // Tableau pour stocker les flocons de neige

function initializeGame() {
    penguin = [
        { x: 10 * gridSize, y: 10 * gridSize } // Position initiale du pingouin
    ];
    food = generateFood();
    direction = 'right'; // Direction initiale
    score = 0;
    scoreDisplay.textContent = `Score: ${score}`;
    gameSpeed = 150; // Réinitialise la vitesse
    if (gameInterval) clearInterval(gameInterval); // Arrête tout intervalle précédent
    startButton.textContent = 'Recommencer la glissade';

    // Initialisation des flocons de neige
    snowflakes = [];
    for (let i = 0; i < 50; i++) { // Nombre de flocons
        snowflakes.push(createSnowflake());
    }

    startGameLoop();
}

function generateFood() {
    let newFood;
    while (true) {
        newFood = {
            x: Math.floor(Math.random() * (canvas.width / gridSize)) * gridSize,
            y: Math.floor(Math.random() * (canvas.height / gridSize)) * gridSize
        };
        // S'assurer que la nourriture n'apparaît pas sur le serpent
        let collisionWithSnake = false;
        for (let i = 0; i < snake.length; i++) {
            if (snake[i].x === newFood.x && snake[i].y === newFood.y) {
                collisionWithSnake = true;
                break;
            }
        }
        if (!collisionWithSnake) {
            return newFood;
        }
    }
}

function draw() {
    // Nettoyer le canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Dessiner les flocons de neige
    ctx.fillStyle = 'rgba(255, 255, 255, 0.8)'; // Flocons blancs semi-transparents
    snowflakes.forEach(flake => {
        ctx.beginPath();
        ctx.arc(flake.x, flake.y, flake.radius, 0, Math.PI * 2);
        ctx.fill();
    });

    // Dessiner le pingouin (anciennement le serpent)
    for (let i = 0; i < penguin.length; i++) {
        // Tête du pingouin (noir/bleu foncé)
        ctx.fillStyle = (i === 0) ? '#333333' : '#FFFFFF'; // Tête sombre, corps blanc
        ctx.strokeStyle = (i === 0) ? '#000000' : '#CCCCCC'; // Bordure foncée, corps gris clair

        // Dessine la forme de base du pingouin
        ctx.fillRect(penguin[i].x, penguin[i].y, gridSize, gridSize);
        ctx.strokeRect(penguin[i].x, penguin[i].y, gridSize, gridSize);

        // Si c'est la tête, ajouter un bec (petit carré orange)
        if (i === 0) {
            ctx.fillStyle = '#FFA500'; // Orange pour le bec
            // Positionnement du bec en fonction de la direction
            let beakX = penguin[i].x;
            let beakY = penguin[i].y;
            const beakSize = gridSize / 3;

            switch (direction) {
                case 'up':
                    beakX += gridSize / 3;
                    beakY -= beakSize;
                    break;
                case 'down':
                    beakX += gridSize / 3;
                    beakY += gridSize;
                    break;
                case 'left':
                    beakX -= beakSize;
                    beakY += gridSize / 3;
                    break;
                case 'right':
                    beakX += gridSize;
                    beakY += gridSize / 3;
                    break;
            }
            ctx.fillRect(beakX, beakY, beakSize, beakSize);
        }
    }

    // Dessiner la nourriture (poisson)
    ctx.fillStyle = '#FF4500'; // Orange vif pour le poisson
    ctx.strokeStyle = '#CD3700';
    ctx.fillRect(food.x, food.y, gridSize, gridSize);
    ctx.strokeRect(food.x, food.y, gridSize, gridSize);
}

function update() {
    const head = { x: penguin[0].x, y: penguin[0].y }; // La tête du pingouin

    // Déplacer la tête du pingouin
    switch (direction) {
        case 'up':
            head.y -= gridSize;
            break;
        case 'down':
            head.y += gridSize;
            break;
        case 'left':
            head.x -= gridSize;
            break;
        case 'right':
            head.x += gridSize;
            break;
    }

    // Vérifier les collisions avec les bords de la banquise
    const hitWall = head.x < 0 || head.x >= canvas.width || head.y < 0 || head.y >= canvas.height;
    // Vérifier les collisions avec le corps du pingouin
    const hitSelf = penguin.some((segment, index) => index !== 0 && segment.x === head.x && segment.y === head.y);

    if (hitWall || hitSelf) {
        gameOver();
        return;
    }

    // Ajouter la nouvelle tête
    penguin.unshift(head);

    // Vérifier si le pingouin a mangé la nourriture (poisson)
    if (head.x === food.x && head.y === food.y) {
        score += 10; // Le score augmente de 10
        scoreDisplay.textContent = `Score: ${score}`;
        food = generateFood(); // Générer une nouvelle nourriture
        // Augmenter la vitesse de glissade légèrement
        gameSpeed = Math.max(50, gameSpeed - 5); // La vitesse minimale est 50ms
        clearInterval(gameInterval); // Arrête l'intervalle actuel
        startGameLoop(); // Redémarre avec la nouvelle vitesse
    } else {
        // Supprimer la queue si pas de nourriture mangée
        penguin.pop();
    }

    // Mettre à jour la position des flocons de neige
    snowflakes.forEach(flake => {
        flake.y += flake.speed;
        if (flake.y > canvas.height) { // Si le flocon sort de l'écran, le réinitialiser en haut
            flake.y = 0;
            flake.x = Math.random() * canvas.width;
        }
    });

    draw(); // Redessiner le jeu après mise à jour
}

function gameOver() {
    clearInterval(gameInterval);
    alert(`La glissade est terminée ! Votre score est de : ${score} poissons.`);
    startButton.textContent = 'Recommencer la glissade';
}

function changeDirection(event) {
    const keyPressed = event.key;
    const goingUp = direction === 'up';
    const goingDown = direction === 'down';
    const goingLeft = direction === 'left';
    const goingRight = direction === 'right';

    if (keyPressed === 'ArrowLeft' && !goingRight) {
        direction = 'left';
    } else if (keyPressed === 'ArrowUp' && !goingDown) {
        direction = 'up';
    } else if (keyPressed === 'ArrowRight' && !goingLeft) {
        direction = 'right';
    } else if (keyPressed === 'ArrowDown' && !goingUp) {
        direction = 'down';
    }
}

function startGameLoop() {
    gameInterval = setInterval(update, gameSpeed);
}

// Événements
document.addEventListener('keydown', changeDirection);
startButton.addEventListener('click', initializeGame);

// Initialiser le jeu une première fois pour afficher le pingouin et la nourriture (poisson)
// sans démarrer le mouvement tant que le bouton n'est pas cliqué.
// On appelle initializeGame une première fois, puis on s'assure que l'intervalle n'est pas lancé
// et que le texte du bouton est correct. Le draw() final permet d'afficher les éléments.
initializeGame();
clearInterval(gameInterval);
startButton.textContent = 'Démarrer la Glissade';
draw();
