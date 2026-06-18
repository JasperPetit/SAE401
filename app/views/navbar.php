<?php
/* ============================================================
   GABARIT COMMUN (SIDEBAR + TOPBAR + HELPER ICÔNES)
   ============================================================ */

// 1. Définition du Helper d'icônes SVG intégrées (tracés de la maquette pro)
if (!function_exists('icon')) {
    function icon(string $name, int $s = 16): string {
        static $P = [
            'home' => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/>',
            'cart' => '<circle cx="9" cy="20" r="1.6"/><circle cx="17.5" cy="20" r="1.6"/><path d="M2.5 3.5h2.6l2.4 12h11l2-8.5H6"/>',
            'box' => '<path d="M12 2.7 20.5 7v10L12 21.3 3.5 17V7Z"/><path d="M3.7 7.2 12 11.5l8.3-4.3"/><path d="M12 11.5V21"/>',
            'users' => '<circle cx="9" cy="8" r="3.4"/><path d="M2.8 20c.7-3.4 3.2-5.2 6.2-5.2s5.5 1.8 6.2 5.2"/><path d="M16 5.2a3.4 3.4 0 0 1 0 5.9"/><path d="M18.2 14.9c1.8.8 2.8 2.5 3.1 5.1"/>',
            'cog' => '<circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v2.4M12 18.8v2.4M21.2 12h-2.4M5.2 12H2.8M18.5 5.5l-1.7 1.7M7.2 16.8l-1.7 1.7M18.5 18.5l-1.7-1.7M7.2 7.2 5.5 5.5"/>',
            'book' => '<path d="M12 5.5C10.5 4.2 8.4 3.5 5.5 3.5c-1 0-1.9.1-2.7.3v15.4c.8-.2 1.7-.3 2.7-.3 2.9 0 5 .7 6.5 2 1.5-1.3 3.6-2 6.5-2 1 0 1.9.1 2.7.3V3.8c-.8-.2-1.7-.3-2.7-.3-2.9 0-5 .7-6.5 2Z"/><path d="M12 5.5V21"/>',
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
            'dl' => '<path d="M12 3.5v11M7.5 10.5 12 15l4.5-4.5"/><path d="M4 17.5v2a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 20 19.5v-2"/>',
            'eye' => '<path d="M2.5 12S6 5.8 12 5.8 21.5 12 21.5 12 18 18.2 12 18.2 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.8"/>',
            'plus' => '<path d="M12 5v14M5 12h14"/>',
            'filter' => '<path d="M3.5 5h17l-6.6 7.8v5.4L10 20.5v-7.7Z"/>',
            'trash' => '<path d="M4 6.5h16"/><path d="M9.5 6.5V4.2a1.2 1.2 0 0 1 1.2-1.2h2.6a1.2 1.2 0 0 1 1.2 1.2v2.3"/><path d="M6 6.5 7 19.8a1.6 1.6 0 0 0 1.6 1.5h6.8a1.6 1.6 0 0 0 1.6-1.5l1-13.3"/><path d="M10 11v6M14 11v6"/>',
            'chev' => '<path d="m9 5.5 6.5 6.5L9 18.5"/>',
            'star' => '<path d="m12 2.8 2.8 5.8 6.4.9-4.6 4.5 1.1 6.3L12 17.3l-5.7 3 1.1-6.3L2.8 9.5l6.4-.9Z"/>',
            'bell' => '<path d="M18 9a6 6 0 0 0-12 0c0 5-2 6-2 6h16s-2-1-2-6"/><path d="M10.3 19.5a2 2 0 0 0 3.4 0"/>',
            'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m20.5 20.5-4.9-4.9"/>',
            'logout' => '<path d="M9.5 21H5.2A1.7 1.7 0 0 1 3.5 19.3V4.7A1.7 1.7 0 0 1 5.2 3h4.3"/><path d="m15.5 16.5 4.5-4.5-4.5-4.5"/><path d="M20 12H9.5"/>',
            'file' => '<path d="M14 2.8H6.5A1.7 1.7 0 0 0 4.8 4.5v15A1.7 1.7 0 0 0 6.5 21.2h11a1.7 1.7 0 0 0 1.7-1.7V8Z"/><path d="M14 2.8V8h5.2"/><path d="M8.5 12.5h7M8.5 16h7"/>',
        ];
        return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($P[$name] ?? '') . '</svg>';
    }
}

// 2. Récupération du rôle et de l'action actuelle pour la gestion active des liens
$role_session = $_SESSION['role'] ?? 'demandeur';
$action_actuelle = $_GET['action'] ?? 'accueil';
$nom_complet = $_SESSION['nom_complet'] ?? 'Utilisateur Inconnu';

// 3. Définition dynamique des menus selon le profil
$menu_items = [];
$badge_text = '';

if ($role_session === 'Administrateur') {
    $badge_text = 'ADMINISTRATEUR';
    $menu_items = [
        ['index.php?action=accueil', 'home', 'Accueil', 'accueil'],
        ['index.php?action=afficherCommande', 'cart', 'Commandes', 'afficherCommande'],
        ['index.php?action=afficherColis', 'box', 'Colis', 'afficherColis'],
        ['index.php?action=afficherFournisseur', 'users', 'Fournisseurs', 'afficherFournisseur'],
        ['index.php?action=pageAdmin', 'cog', 'Administration', 'pageAdmin'],
    ];
} elseif ($role_session === 'Service_Postal') {
    $badge_text = 'SERVICE POSTAL';
    $menu_items = [
        ['index.php?action=accueil', 'home', 'Tableau de bord', 'accueil'],
        ['index.php?action=afficherColis', 'scan', 'Scanner un colis', 'afficherColis'],
        ['index.php?action=nouveau', 'send', 'Nouvel envoi', 'nouveau'],
        ['index.php?action=suivi', 'box', 'Suivi des colis', 'suivi'],
    ];
} elseif ($role_session === 'Service_Financier') {
    $badge_text = 'SERVICE FINANCIER';
    $menu_items = [
        ['index.php?action=pageServiceFinancierDevis', 'check', 'Validation devis', 'pageServiceFinancierDevis'],
        ['index.php?action=afficherFournisseur', 'users', 'Fournisseurs', 'afficherFournisseur'],
    ];
} else { // Profil par défaut : Demandeur
    $badge_text = 'DÉPT. INFORMATIQUE';
    $menu_items = [
        ['index.php?action=accueil', 'home', 'Accueil', 'accueil'],
        ['index.php?action=formulaireDevis', 'file', 'Déposer un devis', 'formulaireDevis'],
        ['index.php?action=pageInfosDevis', 'cart', 'Mes demandes', 'pageInfosDevis'],
        ['index.php?action=afficherColis', 'box', 'Suivre un colis', 'afficherColis'],
    ];
}
?>

<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="public/images/logo.jpeg" alt="Université Sorbonne Paris Nord">
    <div class="sidebar-subtitle">IUT de Villetaneuse</div>
  </div>
  <nav class="nav">
    <?php foreach ($menu_items as [$href, $ic, $lbl, $id_menu]): ?>
        <a class="nav-item<?= $action_actuelle === $id_menu ? ' active' : '' ?>" href="<?= $href ?>">
            <?= icon($ic) ?> <span><?= $lbl ?></span>
        </a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div style="font-size:11px;color:rgba(255,255,255,.7);margin-bottom:6px;"><?= htmlspecialchars($_SESSION['nom_complet'] ?? 'Utilisateur USPN') ?></div>
    <span class="role-badge"><?= $badge_text ?></span>
    <a href="index.php?action=deconnexion" class="logout-link"><?= icon('logout', 13) ?> Se déconnecter</a>
  </div>
</aside>

<div class="topbar">
  <div class="topbar-title">Suivi Colis <span>IUT de Villetaneuse</span></div>
  <div class="topbar-right">
    <span style="font-size:12px;color:var(--text-light);"><?= htmlspecialchars($_SESSION['utilisateur_id'] ?? 'cas@uspn.fr') ?></span>
    <button class="btn-icon" title="Notifications"><?= icon('bell') ?></button>
  </div>
</div>