<?php
/* ============================================================
   FONCTIONS UTILITAIRES - SAE COLIS
   ============================================================ */
require_once __DIR__ . '/config.php';

/* Échappement HTML systématique */
function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* Jeton CSRF */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
        (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']))) {
        http_response_code(403);
        exit('Requête invalide (CSRF).');
    }
}

/* Messages flash */
function flash(string $msg, string $type = 'success'): void {
    $_SESSION['flash'][] = ['msg' => $msg, 'type' => $type];
}
function show_flash(): string {
    if (empty($_SESSION['flash'])) return '';
    $out = '';
    foreach ($_SESSION['flash'] as $f) {
        $cls = $f['type'] === 'error' ? 'flash-error' : 'flash-success';
        $out .= '<div class="flash ' . $cls . '">' . e($f['msg']) . '</div>';
    }
    unset($_SESSION['flash']);
    return $out;
}

/* Badge selon statut */
function badge(string $statut): string {
    $map = [
        'Livré' => 'badge-green',  'Livrée' => 'badge-gray',  'Payé' => 'badge-green',
        'Payée' => 'badge-green',  'Validée' => 'badge-green',
        'En cours' => 'badge-blue','En transit' => 'badge-blue','En livraison' => 'badge-blue',
        'En attente' => 'badge-orange',
        'Impayé' => 'badge-red',   'En retard' => 'badge-red', 'Annulée' => 'badge-red',
        /* Statuts du processus de commande décrit par M. Butelle */
        'Devis transmis au SF'    => 'badge-orange',
        'Devis validé'            => 'badge-blue',
        'Bon de commande signé'   => 'badge-blue',
        'Livraison en cours'      => 'badge-blue',
        'Réception confirmée'     => 'badge-green',
        'Fournisseur payé'        => 'badge-green',
        'Devis refusé'            => 'badge-red',
    ];
    $cls = $map[$statut] ?? 'badge-gray';
    return '<span class="badge ' . $cls . '">' . e($statut) . '</span>';
}

/* Format monétaire FR */
function eur(float $n): string { return number_format($n, 2, ',', ' ') . ' €'; }
function eur0(float $n): string { return number_format($n, 0, ',', ' ') . ' €'; }

/* Date FR */
function date_fr(?string $d): string {
    if (!$d) return '';
    $ts = strtotime($d);
    return $ts ? date('d/m/Y', $ts) : e($d);
}
function dateheure_fr(?string $d): string {
    if (!$d) return '';
    $ts = strtotime($d);
    return $ts ? date('d/m/Y - H:i', $ts) : e($d);
}

/* Génère la référence suivante (CMD-2025-005, CP2024-11-007...) */
function next_ref(string $table, string $col, string $prefix): string {
    $row = db()->query("SELECT $col AS r FROM $table WHERE $col LIKE '$prefix%' ORDER BY id DESC LIMIT 1")->fetch();
    if ($row && preg_match('/(\d+)$/', $row['r'], $m)) {
        $n = (int)$m[1] + 1;
        return $prefix . str_pad((string)$n, strlen($m[1]), '0', STR_PAD_LEFT);
    }
    return $prefix . '001';
}

/* Icônes SVG (mêmes tracés que la maquette) */
function icon(string $name, int $s = 16): string {
    static $P = [
        'home' => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/>',
        'cart' => '<circle cx="9" cy="20" r="1.6"/><circle cx="17.5" cy="20" r="1.6"/><path d="M2.5 3.5h2.6l2.4 12h11l2-8.5H6"/>',
        'box' => '<path d="M12 2.7 20.5 7v10L12 21.3 3.5 17V7Z"/><path d="M3.7 7.2 12 11.5l8.3-4.3"/><path d="M12 11.5V21"/>',
        'users' => '<circle cx="9" cy="8" r="3.4"/><path d="M2.8 20c.7-3.4 3.2-5.2 6.2-5.2s5.5 1.8 6.2 5.2"/><path d="M16 5.2a3.4 3.4 0 0 1 0 5.9"/><path d="M18.2 14.9c1.8.8 2.8 2.5 3.1 5.1"/>',
        'cog' => '<circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v2.4M12 18.8v2.4M21.2 12h-2.4M5.2 12H2.8M18.5 5.5l-1.7 1.7M7.2 16.8l-1.7 1.7M18.5 18.5l-1.7-1.7M7.2 7.2 5.5 5.5"/>',
        'book' => '<path d="M12 5.5C10.5 4.2 8.4 3.5 5.5 3.5c-1 0-1.9.1-2.7.3v15.4c.8-.2 1.7-.3 2.7-.3 2.9 0 5 .7 6.5 2 1.5-1.3 3.6-2 6.5-2 1 0 1.9.1 2.7.3V3.8c-.8-.2-1.7-.3-2.7-.3-2.9 0-5 .7-6.5 2Z"/><path d="M12 5.5V21"/>',
        'grid' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.4"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.4"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.4"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.4"/>',
        'card' => '<rect x="2.5" y="5" width="19" height="14" rx="2.2"/><path d="M2.5 9.5h19"/><path d="M6 14.5h4"/>',
        'file' => '<path d="M14 2.8H6.5A1.7 1.7 0 0 0 4.8 4.5v15A1.7 1.7 0 0 0 6.5 21.2h11a1.7 1.7 0 0 0 1.7-1.7V8Z"/><path d="M14 2.8V8h5.2"/><path d="M8.5 12.5h7M8.5 16h7"/>',
        'chart' => '<path d="M4 20.5h17"/><path d="M6.5 20.5v-7M11 20.5V7.5M15.5 20.5V11M20 20.5V4.5"/>',
        'scan' => '<path d="M3.5 8V5.5a2 2 0 0 1 2-2H8M16 3.5h2.5a2 2 0 0 1 2 2V8M20.5 16v2.5a2 2 0 0 1-2 2H16M8 20.5H5.5a2 2 0 0 1-2-2V16"/><path d="M3.5 12h17"/>',
        'send' => '<path d="M21.5 2.5 11 13"/><path d="M21.5 2.5 14.7 21.3l-3.7-8.3-8.3-3.7Z"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 6.5V12l3.5 2"/>',
        'truck' => '<path d="M2.5 6h11v11h-11Z"/><path d="M13.5 9.5h4l3 3.5v4h-7"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="m8 12.3 2.8 2.8L16.3 9.5"/>',
        'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V13"/><path d="M12 16.3h.01"/>',
        'euro' => '<path d="M18 5.7A7.5 7.5 0 1 0 18 18.3"/><path d="M3.8 10h9M3.8 14h9"/>',
        'pin' => '<path d="M12 21.5s7-6.1 7-11.5a7 7 0 1 0-14 0c0 5.4 7 11.5 7 11.5Z"/><circle cx="12" cy="9.8" r="2.6"/>',
        'phone' => '<path d="M21 16.6v2.6a1.8 1.8 0 0 1-2 1.8 18.5 18.5 0 0 1-8-2.9 18 18 0 0 1-5.6-5.6A18.5 18.5 0 0 1 2.5 4.4 1.8 1.8 0 0 1 4.3 2.5h2.6a1.8 1.8 0 0 1 1.8 1.5c.1.9.3 1.8.6 2.6a1.8 1.8 0 0 1-.4 1.9L7.8 9.6a14.6 14.6 0 0 0 5.6 5.6l1.1-1.1a1.8 1.8 0 0 1 1.9-.4c.8.3 1.7.5 2.6.6a1.8 1.8 0 0 1 2 1.8Z"/>',
        'mail' => '<rect x="2.5" y="4.5" width="19" height="15" rx="2"/><path d="m2.5 7.5 9.5 6 9.5-6"/>',
        'cal' => '<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M8 2.8V7M16 2.8V7M3.5 10.5h17"/>',
        'dl' => '<path d="M12 3.5v11M7.5 10.5 12 15l4.5-4.5"/><path d="M4 17.5v2a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 20 19.5v-2"/>',
        'eye' => '<path d="M2.5 12S6 5.8 12 5.8 21.5 12 21.5 12 18 18.2 12 18.2 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.8"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'filter' => '<path d="M3.5 5h17l-6.6 7.8v5.4L10 20.5v-7.7Z"/>',
        'trend' => '<path d="M3 17.5 9.5 11l4 4L21 7.5"/><path d="M15.5 7.5H21V13"/>',
        'camera' => '<path d="M3 8.2A1.8 1.8 0 0 1 4.8 6.4h2.7l1.6-2.4h5.8l1.6 2.4h2.7A1.8 1.8 0 0 1 21 8.2v9.4a1.8 1.8 0 0 1-1.8 1.8H4.8A1.8 1.8 0 0 1 3 17.6Z"/><circle cx="12" cy="12.8" r="3.4"/>',
        'trash' => '<path d="M4 6.5h16"/><path d="M9.5 6.5V4.2a1.2 1.2 0 0 1 1.2-1.2h2.6a1.2 1.2 0 0 1 1.2 1.2v2.3"/><path d="M6 6.5 7 19.8a1.6 1.6 0 0 0 1.6 1.5h6.8a1.6 1.6 0 0 0 1.6-1.5l1-13.3"/><path d="M10 11v6M14 11v6"/>',
        'play' => '<path d="M7.5 4.8v14.4L19 12Z"/>',
        'chev' => '<path d="m9 5.5 6.5 6.5L9 18.5"/>',
        'star' => '<path d="m12 2.8 2.8 5.8 6.4.9-4.6 4.5 1.1 6.3L12 17.3l-5.7 3 1.1-6.3L2.8 9.5l6.4-.9Z"/>',
        'bell' => '<path d="M18 9a6 6 0 0 0-12 0c0 5-2 6-2 6h16s-2-1-2-6"/><path d="M10.3 19.5a2 2 0 0 0 3.4 0"/>',
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m20.5 20.5-4.9-4.9"/>',
        'logout' => '<path d="M9.5 21H5.2A1.7 1.7 0 0 1 3.5 19.3V4.7A1.7 1.7 0 0 1 5.2 3h4.3"/><path d="m15.5 16.5 4.5-4.5-4.5-4.5"/><path d="M20 12H9.5"/>',
        'db' => '<ellipse cx="12" cy="5.5" rx="8" ry="3"/><path d="M4 5.5v13c0 1.7 3.6 3 8 3s8-1.3 8-3v-13"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'shield' => '<path d="M12 2.8 4.5 5.6v6c0 4.6 3.1 8 7.5 9.6 4.4-1.6 7.5-5 7.5-9.6v-6Z"/>',
    ];
    return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
         . ' stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($P[$name] ?? '') . '</svg>';
}
