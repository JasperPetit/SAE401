<?php
require __DIR__ . '/_nav.php';

$db = db();
$nbAttente = (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut = 'En attente'")->fetchColumn();
$nbCmd     = (int)$db->query("SELECT COUNT(*) FROM commandes WHERE statut IN ('En cours','Validée')")->fetchColumn();
$dernier   = $db->query("SELECT c.tracking, h.date_h FROM colis c
                         JOIN colis_historique h ON h.colis_id = c.id AND h.statut = 'Livré'
                         WHERE c.statut = 'Livré' ORDER BY h.date_h DESC LIMIT 1")->fetch();

page_head('Accueil', 'admin', 'accueil', $NAV, 'ADMINISTRATEUR');
?>
<div class="page-header"><h1>Suivi Colis</h1><p>IUT de Villetaneuse</p></div>

<div class="stats-grid" style="grid-template-columns:repeat(2,1fr);">
  <div class="stat-card">
    <div><div class="label">Colis en attente</div><div class="value"><?= $nbAttente ?></div></div>
    <div class="stat-icon orange"><?= icon('box', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Commandes en cours</div><div class="value"><?= $nbCmd ?></div></div>
    <div class="stat-icon blue"><?= icon('cart', 18) ?></div>
  </div>
</div>

<div class="last-parcel">
  <div>
    <div class="lp-label">Dernier colis livré</div>
    <div class="lp-label" style="margin-top:8px;">Numéro de suivi</div>
    <div class="lp-tracking"><?= e($dernier['tracking'] ?? 'Aucun colis livré') ?></div>
    <?php if ($dernier): ?><div class="lp-date">Livré le <?= dateheure_fr($dernier['date_h']) ?></div><?php endif; ?>
  </div>
  <div class="stat-icon green" style="width:40px;height:40px;"><?= icon('check', 20) ?></div>
</div>

<div class="actions-row">
  <a class="btn btn-gold" href="commandes.php"><?= icon('plus', 14) ?> Voir les bons de commande</a>
  <a class="btn btn-outline" href="colis.php"><?= icon('scan', 14) ?> Gérer les colis</a>
</div>
<?php page_foot();
