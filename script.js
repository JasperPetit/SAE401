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
    const head = { x: snake[0].x, y: snake[0].y };

    // Déplacer la tête du serpent
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

    // Vérifier les collisions
    const hitWall = head.x < 0 || head.x >= canvas.width || head.y < 0 || head.y >= canvas.height;
    const hitSelf = snake.some((segment, index) => index !== 0 && segment.x === head.x && segment.y === head.y);

    if (hitWall || hitSelf) {
        gameOver();
        return;
    }

    // Ajouter la nouvelle tête
    snake.unshift(head);

    // Vérifier si le serpent a mangé la nourriture
    if (head.x === food.x && head.y === food.y) {
        score += 10;
        scoreDisplay.textContent = `Score: ${score}`;
        food = generateFood(); // Générer une nouvelle nourriture
        // Augmenter la vitesse du jeu après chaque nourriture mangée (optionnel)
        gameSpeed = Math.max(50, gameSpeed - 5); // Vitesse minimale de 50ms
        clearInterval(gameInterval);
        startGameLoop();
    } else {
        // Supprimer la queue si pas de nourriture mangée
        snake.pop();
    }

    draw();
}

function gameOver() {
    clearInterval(gameInterval);
    alert(`Fin du jeu ! Votre score est de : ${score}`);
    startButton.textContent = 'Recommencer le jeu';
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

// Initialiser le jeu une première fois pour afficher le serpent et la nourriture
// sans démarrer le mouvement tant que le bouton n'est pas cliqué.
// Ou laisser le bouton "Commencer" faire la première initialisation.
// Pour l'instant, on attend le clic sur le bouton pour initializeGame.
// On dessine juste une fois au chargement pour montrer l'état initial.
initializeGame(); // Pour avoir le serpent et la nourriture affichés au départ.
clearInterval(gameInterval); // S'assure que le jeu ne démarre pas auto.
startButton.textContent = 'Commencer le jeu'; // S'assure du bon texte
