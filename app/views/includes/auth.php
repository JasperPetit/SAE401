<?php
/* ============================================================
   AUTHENTIFICATION / SESSIONS - SAE COLIS
   ============================================================ */
require_once __DIR__ . '/functions.php';

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

/* Protège une page : redirige vers la sélection de profil si
   non connecté, ou si le rôle ne correspond pas. */
function require_role(string $role): array {
    $u = current_user();
    if (!$u || $u['role'] !== $role) {
        header('Location: ' . rtrim(dirname($_SERVER['PHP_SELF'], 2), '/') . '/index.php');
        exit;
    }
    return $u;
}

function login(string $role): bool {
    $st = db()->prepare('SELECT * FROM utilisateurs WHERE role = ? LIMIT 1');
    $st->execute([$role]);
    $u = $st->fetch();
    if ($u) {
        session_regenerate_id(true);
        $_SESSION['user'] = $u;
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    session_destroy();
}
