<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();
$trouve = null; $histo = [];

/* --- Scan (recherche) --- */
$code = trim($_POST['code'] ?? $_GET['code'] ?? '');
if ($code !== '') {
    /* Recherche souple (M. Butelle) : retrouver le departement destinataire
       meme si le bon de livraison papier est deteriore, avec quelques infos. */
    $st = $db->prepare("SELECT c.*, d.nom AS dept FROM colis c
                        LEFT JOIN departements d ON d.id = c.departement_id
                        WHERE c.tracking = ? OR c.reference = ?
                           OR c.destinataire LIKE ? OR d.nom LIKE ?
                        LIMIT 1");
    $st->execute([$code, $code, "%$code%", "%$code%"]);
    $trouve = $st->fetch();
    if (!$trouve) flash("Aucun colis trouvé pour « $code ». Essayez avec le nom du destinataire ou du département.", 'error');
}

/* --- Simuler un scan : prend un colis non livré au hasard --- */
if (isset($_POST['simuler'])) {
    $trouve = $db->query("SELECT c.*, d.nom AS dept FROM colis c LEFT JOIN departements d ON d.id = c.departement_id WHERE c.statut != 'Livré' ORDER BY RANDOM() LIMIT 1")->fetch();
    if (!$trouve) $trouve = $db->query("SELECT c.*, d.nom AS dept FROM colis c LEFT JOIN departements d ON d.id = c.departement_id ORDER BY RANDOM() LIMIT 1")->fetch();
    $code = $trouve['tracking'] ?? '';
}

/* --- Mise à jour de statut après scan --- */
if (isset($_POST['maj_statut'], $_POST['colis_id'])) {
    $cid = (int)$_POST['colis_id'];
    $nouveau = $_POST['maj_statut'];
    $valides = ['En attente', 'En transit', 'En cours', 'En livraison', 'Livré'];
    if (in_array($nouveau, $valides, true)) {
        $db->prepare("UPDATE colis SET statut = ?, date_maj = datetime('now','localtime') WHERE id = ?")->execute([$nouveau, $cid]);
        $lieux = ['En attente' => 'Service postal IUT', 'En transit' => 'Dépôt national',
                  'En cours' => 'Centre de tri Villetaneuse', 'En livraison' => 'En tournée',
                  'Livré' => 'Remis au destinataire'];
        $db->prepare("INSERT INTO colis_historique (colis_id, statut, lieu, note, date_h)
                      VALUES (?,?,?,?,datetime('now','localtime'))")
           ->execute([$cid, $nouveau, $lieux[$nouveau], 'Statut mis à jour par le service postal']);
        flash("Statut du colis mis à jour : $nouveau");
        $st = $db->prepare("SELECT c.*, d.nom AS dept FROM colis c LEFT JOIN departements d ON d.id = c.departement_id WHERE c.id = ?"); $st->execute([$cid]);
        $trouve = $st->fetch();
        $code = $trouve['tracking'];
    }
}

if ($trouve) {
    $st = $db->prepare("SELECT * FROM colis_historique WHERE colis_id = ? ORDER BY date_h DESC LIMIT 4");
    $st->execute([$trouve['id']]);
    $histo = $st->fetchAll();
}

page_head('Scanner un colis', 'postier', 'scanner', $NAV, 'SERVICE POSTAL');
?>
<div class="page-header"><h1>Scanner un colis</h1><p>Bon de livraison détérioré ? Retrouvez le département destinataire avec quelques informations seulement</p></div>

<div class="scan-grid">
  <div class="card">
    <div class="tabs"><span class="on"><?= icon('scan', 13) ?>&nbsp; Scanner</span><span>Saisie manuelle</span></div>
    <div class="scan-zone"><span class="cam"><?= icon('camera', 24) ?></span><span class="line"></span></div>
    <form method="post" style="margin-top:14px;">
      <?= csrf_field() ?>
      <button class="btn btn-gold" name="simuler" value="1" style="width:100%;justify-content:center;">Simuler un scan</button>
    </form>
    <form method="post" style="margin-top:10px;display:flex;gap:8px;">
      <?= csrf_field() ?>
      <input class="f-input" name="code" value="<?= e($code) ?>" placeholder="N° de suivi, référence, destinataire ou département...">
      <button class="btn btn-primary" type="submit">Rechercher</button>
    </form>
    <p style="font-size:11.5px;color:var(--text-light);text-align:center;margin-top:8px;">Positionnez le code-barres devant la caméra</p>
  </div>

  <div class="card" style="min-height:330px;<?= $trouve ? '' : 'display:flex;align-items:center;justify-content:center;' ?>">
    <?php if (!$trouve): ?>
    <div style="text-align:center;color:var(--text-light);">
      <span style="display:inline-flex;color:#cbd5e1;"><?= icon('scan', 40) ?></span>
      <p style="font-size:12.5px;margin-top:12px;">Scannez un colis pour voir les détails</p>
    </div>
    <?php else: ?>
    <div class="card-header"><div class="card-title">Colis <?= e($trouve['reference']) ?></div><?= badge($trouve['statut']) ?></div>
    <div style="background:var(--navy);border-radius:8px;padding:14px 16px;margin-bottom:14px;">
      <div style="font-size:10px;letter-spacing:1px;color:rgba(255,255,255,.6);text-transform:uppercase;">Livrer au département</div>
      <div style="font-size:18px;font-weight:700;color:#fff;margin-top:2px;"><?= e($trouve['dept'] ?? 'Inconnu') ?></div>
      <div style="font-size:12px;color:rgba(255,255,255,.75);margin-top:2px;"><?= icon('pin', 12) ?> <?= e($trouve['destination']) ?></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px;font-size:12.5px;margin-bottom:14px;">
      <div><div style="color:var(--text-light);font-size:11px;">Numéro de suivi</div><strong style="color:var(--navy);"><?= e($trouve['tracking']) ?></strong></div>
      <div><div style="color:var(--text-light);font-size:11px;">Destinataire</div><strong><?= e($trouve['destinataire']) ?></strong></div>
      <div><div style="color:var(--text-light);font-size:11px;">Destination</div><strong><?= e($trouve['destination']) ?></strong></div>
      <div><div style="color:var(--text-light);font-size:11px;">Type / Poids</div><strong><?= e($trouve['type']) ?> — <?= number_format((float)$trouve['poids'], 1, ',') ?> kg</strong></div>
    </div>
    <form method="post" style="display:flex;gap:8px;align-items:end;">
      <?= csrf_field() ?>
      <input type="hidden" name="colis_id" value="<?= $trouve['id'] ?>">
      <div style="flex:1;"><label class="f-label">Mettre à jour le statut</label>
        <select class="f-input" name="maj_statut">
          <?php foreach (['En attente', 'En transit', 'En cours', 'En livraison', 'Livré'] as $s): ?>
          <option <?= $trouve['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select></div>
      <button class="btn btn-gold" type="submit">Valider</button>
    </form>
    <?php if ($histo): ?>
    <div class="divider"></div>
    <div style="font-size:11px;color:var(--text-light);margin-bottom:8px;font-weight:600;">DERNIERS ÉVÈNEMENTS</div>
    <?php foreach ($histo as $h): ?>
    <div style="display:flex;justify-content:space-between;font-size:12px;padding:5px 0;border-bottom:1px solid var(--border);">
      <span><?= e($h['statut']) ?> — <?= e($h['lieu']) ?></span>
      <span style="color:var(--text-light);"><?= dateheure_fr($h['date_h']) ?></span>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php page_foot();
