<?php
require __DIR__ . '/_nav.php';

$db = db();
$num = trim($_GET['num'] ?? '');
$colis = null; $histo = [];

if ($num !== '') {
    $st = $db->prepare("SELECT * FROM colis WHERE tracking = ? OR reference = ? LIMIT 1");
    $st->execute([$num, $num]);
    $colis = $st->fetch();
} else {
    /* Par défaut : premier colis livré (démo, comme la maquette) */
    $colis = $db->query("SELECT * FROM colis WHERE statut = 'Livré' ORDER BY id LIMIT 1")->fetch();
}
if ($colis) {
    $st = $db->prepare("SELECT * FROM colis_historique WHERE colis_id = ? ORDER BY date_h DESC");
    $st->execute([$colis['id']]);
    $histo = $st->fetchAll();
}

page_head('Suivi de Colis', 'demandeur', 'colis', $NAV, 'DÉPT. INFORMATIQUE');
?>
<div class="page-header"><h1>Suivi de Colis</h1><p>Suivez vos colis en temps réel</p></div>

<form method="get" class="search-row">
  <div class="search-wrap"><?= icon('search', 14) ?>
    <input class="search-input" name="num" value="<?= e($num) ?>" placeholder="Entrez un numéro de suivi (ex : TRK123456789 ou CP2024-11-001)..."></div>
  <button class="btn btn-primary" type="submit">Rechercher</button>
</form>

<?php if ($num !== '' && !$colis): ?>
<div class="flash flash-error">Aucun colis trouvé pour « <?= e($num) ?> ». Vérifiez le numéro de suivi.</div>
<?php endif; ?>

<?php if ($colis): ?>
<div class="two-col">
  <div class="card" style="margin-bottom:0;">
    <div class="card-title" style="margin-bottom:14px;">Historique du colis</div>
    <?php if (!$histo): ?><p class="empty-state">Aucun évènement enregistré pour ce colis.</p><?php endif; ?>
    <?php foreach ($histo as $i => $h):
        $done = $h['statut'] === 'Livré';
        $ic = $done ? 'check' : (str_contains($h['statut'], 'livraison') || $h['statut'] === 'En transit' ? 'truck' : 'box');
    ?>
    <div class="tl-item">
      <span class="tl-dot<?= $done ? ' done' : '' ?>"><?= icon($ic, 15) ?></span>
      <div style="flex:1;">
        <div class="tl-title<?= $done ? ' green' : '' ?>"><?= e($h['statut']) ?></div>
        <div class="tl-place"><?= icon('pin', 12) ?> <?= e($h['lieu']) ?></div>
        <div class="tl-note"><?= e($h['note']) ?></div>
      </div>
      <div class="tl-time"><?= date_fr($h['date_h']) ?><br><?= date('H:i', strtotime($h['date_h'])) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <div>
    <div class="card" style="margin-bottom:14px;">
      <div class="card-title" style="margin-bottom:12px;">Informations du colis</div>
      <div style="display:flex;flex-direction:column;gap:11px;font-size:12.5px;">
        <div><div style="color:var(--text-light);font-size:11px;">Référence</div><strong style="color:var(--navy);"><?= e($colis['reference']) ?></strong></div>
        <div><div style="color:var(--text-light);font-size:11px;">Numéro de suivi</div><strong><?= e($colis['tracking']) ?></strong></div>
        <div><div style="color:var(--text-light);font-size:11px;">Expéditeur</div><strong><?= e($colis['expediteur'] ?? '-') ?></strong></div>
        <div><div style="color:var(--text-light);font-size:11px;">Destinataire</div><strong><?= e($colis['destinataire']) ?></strong></div>
        <div><div style="color:var(--text-light);font-size:11px;">Destination</div><strong><?= e($colis['destination']) ?></strong></div>
        <div><div style="color:var(--text-light);font-size:11px;margin-bottom:4px;">Statut actuel</div><?= badge($colis['statut']) ?></div>
      </div>
    </div>
    <?php if ($colis['statut'] !== 'Livré'): ?>
    <div class="note-blue">
      <strong style="display:flex;align-items:center;gap:6px;color:var(--navy);"><?= icon('box', 14) ?> Livraison prévue</strong>
      <span style="display:block;margin-top:4px;">Votre colis sera livré prochainement. Suivez son avancement ici.</span>
    </div>
    <?php else: ?>
    <div class="flash flash-success" style="margin:0;">Ce colis a bien été livré.</div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
<?php page_foot();
