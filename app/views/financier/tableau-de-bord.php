<?php
require __DIR__ . '/_nav.php';

$db = db();
$revMois  = (float)$db->query("SELECT COALESCE(SUM(montant),0) FROM transactions WHERE statut = 'Payé' AND strftime('%Y-%m', date_t) = strftime('%Y-%m','now')")->fetchColumn();
if ($revMois == 0) $revMois = (float)$db->query("SELECT COALESCE(SUM(montant),0) FROM transactions WHERE statut = 'Payé' AND strftime('%Y-%m', date_t) = (SELECT strftime('%Y-%m', MAX(date_t)) FROM transactions WHERE statut='Payé')")->fetchColumn();
$attente  = $db->query("SELECT COALESCE(SUM(montant),0) s, COUNT(*) n FROM transactions WHERE statut = 'En attente'")->fetch();
$impaye   = $db->query("SELECT COALESCE(SUM(montant),0) s, COUNT(*) n FROM transactions WHERE statut = 'Impayé'")->fetch();
$paye     = $db->query("SELECT COALESCE(SUM(montant),0) s, COUNT(*) n FROM transactions WHERE statut = 'Payé'")->fetch();
$nbColisAttente = (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut = 'En attente'")->fetchColumn();
$factEcheance = (int)$db->query("SELECT COUNT(*) FROM factures WHERE statut = 'En attente'")->fetchColumn();
$devisAttente = (int)$db->query("SELECT COUNT(*) FROM commandes WHERE statut = 'Devis transmis au SF'")->fetchColumn();
$aPayer = (int)$db->query("SELECT COUNT(*) FROM commandes WHERE statut = 'Réception confirmée'")->fetchColumn();

page_head('Tableau de bord', 'financier', 'dashboard', $NAV, 'SERVICE FINANCIER');
?>
<div class="page-header"><h1>Service Financier - Suivi Colis</h1><p>IUT de Villetaneuse</p></div>

<div class="stats-grid">
  <div class="stat-card">
    <div><div class="label">Revenus du mois</div><div class="value" style="color:var(--green);font-size:22px;"><?= eur0($revMois) ?></div></div>
    <div class="stat-icon green"><?= icon('euro', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Paiements en attente</div><div class="value" style="color:var(--orange);font-size:22px;"><?= eur0((float)$attente['s']) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $attente['n'] ?> transactions</div></div>
    <div class="stat-icon orange"><?= icon('clock', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Factures impayées</div><div class="value" style="color:var(--red);font-size:22px;"><?= eur0((float)$impaye['s']) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $impaye['n'] ?> transactions</div></div>
    <div class="stat-icon orange"><?= icon('alert', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Transactions réussies</div><div class="value" style="font-size:22px;"><?= eur0((float)$paye['s']) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $paye['n'] ?> transactions</div></div>
    <div class="stat-icon blue"><?= icon('check', 18) ?></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
  <div class="card" style="margin-bottom:0;">
    <div class="card-title" style="margin-bottom:8px;">Statistiques des colis</div>
    <div class="list-item">
      <div class="li-icon" style="background:#dbeafe;color:var(--blue);"><?= icon('box', 16) ?></div>
      <div class="li-info"><div class="li-title">Colis en attente de paiement</div>
      <div class="li-meta"><?= $nbColisAttente ?> colis</div></div>
      <div style="font-weight:700;color:var(--navy);font-size:13px;"><?= eur0((float)$attente['s']) ?></div>
    </div>
    <div class="list-item">
      <div class="li-icon" style="background:#dcfce7;color:var(--green);"><?= icon('check', 16) ?></div>
      <div class="li-info"><div class="li-title">Paiements encaissés</div>
      <div class="li-meta"><?= $paye['n'] ?> transactions</div></div>
      <div style="font-weight:700;color:var(--navy);font-size:13px;"><?= eur0((float)$paye['s']) ?></div>
    </div>
    <div class="list-item">
      <div class="li-icon" style="background:#dcfce7;color:var(--green);"><?= icon('trend', 16) ?></div>
      <div class="li-info"><div class="li-title">Volume total traité</div>
      <div class="li-meta"><?= $paye['n'] + $attente['n'] + $impaye['n'] ?> transactions</div></div>
      <div style="font-weight:700;color:var(--navy);font-size:13px;"><?= eur0((float)$paye['s'] + (float)$attente['s'] + (float)$impaye['s']) ?></div>
    </div>
  </div>
  <div class="card" style="margin-bottom:0;">
    <div class="card-title" style="margin-bottom:12px;">Actions rapides</div>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <a class="btn btn-gold" style="justify-content:center;" href="devis.php"><?= icon('check', 14) ?> Valider les devis en attente</a>
      <a class="btn btn-primary" style="justify-content:center;" href="transactions.php?nouvelle=1"><?= icon('euro', 14) ?> Enregistrer un paiement</a>
      <a class="btn btn-primary" style="justify-content:center;" href="rapports.php"><?= icon('chart', 14) ?> Consulter les rapports</a>
    </div>
    <div class="note-blue" style="margin-top:14px;">
      <strong style="display:block;margin-bottom:2px;">À traiter</strong>
      <?= $devisAttente ?> devis en attente de validation ·
      <?= $aPayer ?> fournisseur(s) à payer (réception confirmée) ·
      <?= $factEcheance ?> facture(s) en attente
    </div>
  </div>
</div>
<?php page_foot();
