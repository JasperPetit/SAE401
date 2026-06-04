const cells = document.querySelectorAll('.cell');
const gameInfo = document.getElementById('game-info');
const restartButton = document.getElementById('restart-button');
const modePvEEasyButton = document.getElementById('mode-pve-easy');
const modePvEHardButton = document.getElementById('mode-pve-hard');
const modePvPButton = document.getElementById('mode-pvp');

let board = ['', '', '', '', '', '', '', '', ''];
let currentPlayer = 'X';
let gameActive = false;
let gameMode = ''; // 'PvP', 'PvE-Easy', 'PvE-Hard'

const winningConditions = [
    [0, 1, 2],
    [3, 4, 5],
    [6, 7, 8],
    [0, 3, 6],
    [1, 4, 7],
    [2, 5, 8],
    [0, 4, 8],
    [2, 4, 6]
];

function handleCellClick(clickedCellEvent) {
    const clickedCell = clickedCellEvent.target;
    const clickedCellIndex = parseInt(clickedCell.dataset.cellIndex);

    if (board[clickedCellIndex] !== '' || !gameActive) {
        return;
    }

    handlePlayerMove(clickedCell, clickedCellIndex);
    checkResult();

    if (gameActive && currentPlayer === 'O' && (gameMode === 'PvE-Easy' || gameMode === 'PvE-Hard')) {
        setTimeout(handleAIMove, 500); // Délai pour que l'IA joue
    }
}

function handlePlayerMove(cell, index) {
    board[index] = currentPlayer;
    cell.textContent = currentPlayer;
    cell.classList.add(currentPlayer.toLowerCase());
}

function checkResult() {
    let roundWon = false;
    for (let i = 0; i < winningConditions.length; i++) {
        const winCondition = winningConditions[i];
        let a = board[winCondition[0]];
        let b = board[winCondition[1]];
        let c = board[winCondition[2]];

        if (a === '' || b === '' || c === '') {
            continue;
        }
        if (a === b && b === c) {
            roundWon = true;
            break;
        }
    }

    if (roundWon) {
        gameInfo.textContent = `Le joueur ${currentPlayer} a gagné !`;
        gameActive = false;
        return;
    }

    let roundDraw = !board.includes('');
    if (roundDraw) {
        gameInfo.textContent = `Match nul !`;
        gameActive = false;
        return;
    }

    changePlayer();
}

function changePlayer() {
    currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
    gameInfo.textContent = `C'est au tour du joueur ${currentPlayer}`;
}

function handleRestartGame() {
    board = ['', '', '', '', '', '', '', '', ''];
    currentPlayer = 'X';
    gameActive = true;
    gameInfo.textContent = `C'est au tour du joueur ${currentPlayer}`;
    cells.forEach(cell => {
        cell.textContent = '';
        cell.classList.remove('x', 'o');
    });
}

// Logique de l'IA (mode facile et difficile)
function handleAIMove() {
    let availableCells = [];
    for (let i = 0; i < board.length; i++) {
        if (board[i] === '') {
            availableCells.push(i);
        }
    }

    let bestMove;
    if (gameMode === 'PvE-Hard') {
        bestMove = getBestMove(board, 'O');
    } else { // PvE-Easy
        bestMove = availableCells[Math.floor(Math.random() * availableCells.length)];
    }

    if (bestMove !== undefined && board[bestMove] === '') {
        const cellToPlay = cells[bestMove];
        handlePlayerMove(cellToPlay, bestMove);
        checkResult();
    }
}

// Algorithme Minimax pour l'IA difficile
function getBestMove(currentBoard, player) {
    let bestScore = (player === 'O') ? -Infinity : Infinity;
    let move = null;

    for (let i = 0; i < currentBoard.length; i++) {
        if (currentBoard[i] === '') {
            currentBoard[i] = player;
            let score = minimax(currentBoard, 0, false);
            currentBoard[i] = ''; // annuler le coup

            if (player === 'O' && score > bestScore) {
                bestScore = score;
                move = i;
            } else if (player === 'X' && score < bestScore) {
                bestScore = score;
                move = i;
            }
        }
    }
    return move;
}

function minimax(currentBoard, depth, isMaximizingPlayer) {
    let score = evaluateBoard(currentBoard);

    if (score !== null) {
        return score;
    }

    let availableMoves = [];
    for (let i = 0; i < currentBoard.length; i++) {
        if (currentBoard[i] === '') {
            availableMoves.push(i);
        }
    }

    if (availableMoves.length === 0) { // Égalité
        return 0;
    }

    if (isMaximizingPlayer) { // IA joue (O)
        let bestScore = -Infinity;
        for (let i = 0; i < availableMoves.length; i++) {
            currentBoard[availableMoves[i]] = 'O';
            let currentScore = minimax(currentBoard, depth + 1, false);
            currentBoard[availableMoves[i]] = '';
            bestScore = Math.max(bestScore, currentScore);
        }
        return bestScore;
    } else { // Joueur humain joue (X)
        let bestScore = Infinity;
        for (let i = 0; i < availableMoves.length; i++) {
            currentBoard[availableMoves[i]] = 'X';
            let currentScore = minimax(currentBoard, depth + 1, true);
            currentBoard[availableMoves[i]] = '';
            bestScore = Math.min(bestScore, currentScore);
        }
        return bestScore;
    }
}

function evaluateBoard(currentBoard) {
    for (let i = 0; i < winningConditions.length; i++) {
        const [a, b, c] = winningConditions[i];
        if (currentBoard[a] === currentBoard[b] && currentBoard[b] === currentBoard[c]) {
            if (currentBoard[a] === 'O') return 10; // IA (O) gagne
            if (currentBoard[a] === 'X') return -10; // Joueur (X) gagne
        }
    }
    return null; // Pas de gagnant
}


// Gestion des modes de jeu
function setGameMode(mode) {
    gameMode = mode;
    handleRestartGame(); // Réinitialise le jeu à chaque changement de mode
    if (gameMode === 'PvP') {
        gameInfo.textContent = `Mode Joueur vs Joueur. C'est au tour du joueur X`;
    } else if (gameMode === 'PvE-Easy') {
        gameInfo.textContent = `Mode Joueur vs IA (Facile). C'est au tour du joueur X`;
    } else if (gameMode === 'PvE-Hard') {
        gameInfo.textContent = `Mode Joueur vs IA (Difficile). C'est au tour du joueur X`;
    }
}

// Initialisation des écouteurs d'événements
cells.forEach(cell => cell.addEventListener('click', handleCellClick));
restartButton.addEventListener('click', handleRestartGame);
modePvEEasyButton.addEventListener('click', () => setGameMode('PvE-Easy'));
modePvEHardButton.addEventListener('click', () => setGameMode('PvE-Hard'));
modePvPButton.addEventListener('click', () => setGameMode('PvP'));

// Démarre le jeu en mode par défaut (PvP) au chargement
setGameMode('PvP');
