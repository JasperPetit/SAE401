const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const scoreDisplay = document.getElementById('score');
const startButton = document.getElementById('startButton');

const gridSize = 20; // Taille d'un segment de serpent et de la nourriture
let snake;
let food;
let direction;
let score;
let gameInterval;
let gameSpeed = 100; // Millisecondes par frame

function initializeGame() {
    snake = [
        { x: 10 * gridSize, y: 10 * gridSize } // Position initiale du serpent
    ];
    food = generateFood();
    direction = 'right'; // Direction initiale
    score = 0;
    scoreDisplay.textContent = `Score: ${score}`;
    gameSpeed = 100; // Réinitialise la vitesse
    if (gameInterval) clearInterval(gameInterval); // Arrête tout intervalle précédent
    startButton.textContent = 'Recommencer';
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

    // Dessiner le serpent
    for (let i = 0; i < snake.length; i++) {
        ctx.fillStyle = (i === 0) ? 'green' : 'lime'; // Tête verte, corps vert clair
        ctx.strokeStyle = 'darkgreen';
        ctx.fillRect(snake[i].x, snake[i].y, gridSize, gridSize);
        ctx.strokeRect(snake[i].x, snake[i].y, gridSize, gridSize);
    }

    // Dessiner la nourriture
    ctx.fillStyle = 'red';
    ctx.strokeStyle = 'darkred';
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
    alert(`La glissade est terminée ! Votre score est de : ${score} points.`);
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
initializeGame(); // Pour avoir le pingouin et la nourriture affichés au départ.
clearInterval(gameInterval); // S'assure que le jeu ne démarre pas automatiquement.
startButton.textContent = 'Démarrer la Glissade'; // S'assure du bon texte
draw(); // Dessine l'état initial avec les flocons statiques au début
