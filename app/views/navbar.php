<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? 'Visiteur';
$action_actuelle = $_GET['action'] ?? 'accueil';
$nom_complet = $_SESSION['nom_complet'] ?? 'Utilisateur Inconnu';

$menus = [];

if ($role === 'ADMIN') {
    $menus = [
        "Tableau de bord" => ["action" => "accueil", "icon" => "fa-chart-pie"],
        "Mes commandes"   => ["action" => "afficherCommande", "icon" => "fa-file-invoice-dollar"],
        "Colis / Suivi"   => ["action" => "afficherColis", "icon" => "fa-box"],
        "Fournisseurs"    => ["action" => "afficherFournisseur", "icon" => "fa-handshake"],
        "Mes Devis"       => ["action" => "pageInfosDevis", "icon" => "fa-file-signature"],
        "Administration"  => ["action" => "pageAdmin", "icon" => "fa-user-shield"]
    ];
} elseif ($role === 'Service_Postal') {
    $menus = [
        "Tableau de bord" => ["action" => "pageTableauDeBord", "icon" => "fa-chart-pie"],
        "Nouvel envoi"    => ["action" => "nouveau", "icon" => "fa-paper-plane"],
        "Suivi des colis" => ["action" => "suivi", "icon" => "fa-box-open"],
        "Bons Commandes"  => ["action" => "afficherCommande", "icon" => "fa-file-invoice-dollar"]
    ];
} elseif ($role === 'Service_Financier') {
    $menus = [
        "Arbitrage Devis" => ["action" => "pageServiceFinancierDevis", "icon" => "fa-balance-scale"],
        "Fournisseurs"    => ["action" => "afficherFournisseur", "icon" => "fa-handshake"]
    ];
} elseif ($role === 'Demandeur') {
    $menus = [
        "Mes Projets/Devis" => ["action" => "accueil", "icon" => "fa-file-signature"],
        "Mes Commandes"     => ["action" => "afficherCommande", "icon" => "fa-file-invoice-dollar"]
    ];
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="public/images/logo.jpeg" alt="Logo USPN">
        <span>SAE Colis</span>
    </div>
    
    <ul class="sidebar-menu">
        <?php foreach ($menus as $libelle => $data): ?>
            <li class="sidebar-item <?= ($action_actuelle === $data['action']) ? 'active' : '' ?>">
                <a href="index.php?action=<?= htmlspecialchars($data['action']) ?>">
                    <i class="fas <?= htmlspecialchars($data['icon']) ?>"></i> <?= htmlspecialchars($libelle) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-footer">
        <div class="user-name"><?= htmlspecialchars($nom_complet) ?></div>
        <div class="user-role"><?= htmlspecialchars(str_replace('_', ' ', $role)) ?></div>
        <a href="index.php?action=deconnexion" class="btn-logout"><i class="fas fa-power-off"></i> Déconnexion</a>
    </div>
</div>