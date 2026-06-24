<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();
/* Le compte de démo est rattaché au département Informatique (id 1) */
$DEPT_ID = 1;
$dept = $db->query("SELECT * FROM departements WHERE id = $DEPT_ID")->fetch();

/* --- Confirmer la réception d'un colis (action clé du processus :
       le département prévient le SF pour éviter les retards de paiement) --- */
if (isset($_POST['confirmer_colis'])) {
    $cid = (int)$_POST['confirmer_colis'];
    $db->prepare("UPDATE colis SET statut = 'Livré', date_maj = datetime('now','localtime') WHERE id = ? AND departement_id = ?")
       ->execute([$cid, $DEPT_ID]);
    $db->prepare("INSERT INTO colis_historique (colis_id, statut, lieu, note, date_h)
                  VALUES (?,'Livré',?, 'Réception confirmée par le département', datetime('now','localtime'))")
       ->execute([$cid, 'Dépt. ' . $dept['nom']]);
    /* La commande liée passe en "Réception confirmée" -> le SF sait qu'il peut payer */
    $db->exec("UPDATE commandes SET statut = 'Réception confirmée'
               WHERE departement_id = $DEPT_ID AND statut = 'Livraison en cours'");
    flash('Réception confirmée. Le service financier est informé et peut procéder au paiement du fournisseur.');
    header('Location: accueil.php');
    exit;
}

/* Budget consommé = commandes engagées (devis validé et au-delà) */
$consomme = (float)$db->query("SELECT COALESCE(SUM(total),0) FROM commandes
    WHERE departement_id = $DEPT_ID AND statut NOT IN ('Devis transmis au SF','Devis refusé')")->fetchColumn();
$restant = (float)$dept['budget_alloue'] - $consomme;
$pct = $dept['budget_alloue'] > 0 ? min(100, round($consomme / $dept['budget_alloue'] * 100)) : 0;

$nbDevis   = (int)$db->query("SELECT COUNT(*) FROM commandes WHERE departement_id = $DEPT_ID AND statut = 'Devis transmis au SF'")->fetchColumn();
$nbEnCours = (int)$db->query("SELECT COUNT(*) FROM commandes WHERE departement_id = $DEPT_ID AND statut IN ('Devis validé','Bon de commande signé','Livraison en cours')")->fetchColumn();

/* Colis arrivés à confirmer */
$aConfirmer = $db->query("SELECT * FROM colis WHERE departement_id = $DEPT_ID AND statut IN ('En cours','En attente') ORDER BY date_maj DESC")->fetchAll();

$recents = $db->query(
    "SELECT c.reference, c.statut, h.lieu, h.date_h
     FROM colis c LEFT JOIN colis_historique h ON h.colis_id = c.id
     WHERE c.departement_id = $DEPT_ID
       AND h.id = (SELECT MAX(id) FROM colis_historique WHERE colis_id = c.id)
     ORDER BY h.date_h DESC LIMIT 3")->fetchAll();

page_head('Accueil', 'demandeur', 'accueil', $NAV, 'DÉPT. ' . strtoupper($dept['nom']));
?>
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div><h1>Département <?= e($dept['nom']) ?></h1><p>IUT de Villetaneuse — Suivi des commandes et du budget</p></div>
  <a class="btn btn-gold" href="nouvelle-commande.php"><?= icon('plus', 14) ?> Déposer un devis</a>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
  <div class="stat-card">
    <div><div class="label">Budget alloué</div><div class="value" style="font-size:21px;"><?= eur0((float)$dept['budget_alloue']) ?></div></div>
    <div class="stat-icon blue"><?= icon('euro', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Budget engagé</div><div class="value" style="font-size:21px;color:var(--orange);"><?= eur0($consomme) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $pct ?> % du budget</div></div>
    <div class="stat-icon orange"><?= icon('cart', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Budget restant</div><div class="value" style="font-size:21px;color:<?= $restant < 0 ? 'var(--red)' : 'var(--green)' ?>;"><?= eur0($restant) ?></div></div>
    <div class="stat-icon green"><?= icon('check', 18) ?></div>
  </div>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:10px;">Consommation du budget</div>
  <div class="prog-bar" style="height:10px;">
    <div style="width:<?= $pct ?>%;background:<?= $pct > 90 ? 'var(--red)' : ($pct > 70 ? 'var(--orange)' : 'var(--green)') ?>;"></div>
  </div>
  <p style="font-size:12px;color:var(--text-light);margin-top:8px;">
    <?= eur0($consomme) ?> engagés sur <?= eur0((float)$dept['budget_alloue']) ?>
    — <?= $nbDevis ?> devis en attente de validation, <?= $nbEnCours ?> commande(s) en cours.</p>
</div>

<?php if ($aConfirmer): ?>
<div class="card" style="border-left:3px solid var(--gold);">
  <div class="card-title" style="margin-bottom:4px;">Colis à réceptionner</div>
  <p style="font-size:12px;color:var(--text-light);margin-bottom:8px;">
    Confirmez la réception pour informer le service financier (et éviter tout retard de paiement au fournisseur).</p>
  <?php foreach ($aConfirmer as $c): ?>
  <div class="list-item">
    <div class="li-icon"><?= icon('box', 16) ?></div>
    <div class="li-info"><div class="li-title"><?= e($c['reference']) ?> — <?= e($c['expediteur']) ?></div>
    <div class="li-meta"><?= e($c['destination']) ?></div></div>
    <div class="li-actions">
      <?= badge($c['statut']) ?>
      <form method="post" style="display:inline;">
        <?= csrf_field() ?>
        <button class="btn btn-gold btn-sm" name="confirmer_colis" value="<?= $c['id'] ?>">Confirmer la réception</button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
  <div class="card" style="margin-bottom:0;">
    <div class="card-title" style="margin-bottom:8px;">Activité récente</div>
    <?php if (!$recents): ?><p class="empty-state">Aucune activité récente.</p><?php endif; ?>
    <?php foreach ($recents as $r): ?>
    <div class="list-item">
      <div class="li-info"><?= badge($r['statut']) ?>
        <div class="li-title" style="margin-top:6px;">Colis <?= e($r['reference']) ?><?= $r['lieu'] ? ' — ' . e($r['lieu']) : '' ?></div>
        <div class="li-meta"><?= dateheure_fr($r['date_h']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="card" style="margin-bottom:0;">
    <div class="card-title" style="margin-bottom:12px;">Actions rapides</div>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <a class="btn btn-gold" style="justify-content:center;" href="nouvelle-commande.php">Déposer un devis</a>
      <a class="btn btn-primary" style="justify-content:center;" href="colis.php"><?= icon('box', 14) ?> Suivre un colis</a>
      <a class="btn btn-primary" style="justify-content:center;" href="mes-commandes.php"><?= icon('cart', 14) ?> Mes commandes</a>
    </div>
  </div>
</div>
<?php page_foot();
