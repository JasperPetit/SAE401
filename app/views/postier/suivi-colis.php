<?php
require __DIR__ . '/_nav.php';

$db = db();
$filtre = $_GET['f'] ?? 'tous';

$counts = [
    'tous'    => (int)$db->query("SELECT COUNT(*) FROM colis")->fetchColumn(),
    'attente' => (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut = 'En attente'")->fetchColumn(),
    'cours'   => (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut IN ('En cours','En transit','En livraison')")->fetchColumn(),
    'livre'   => (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut = 'Livré'")->fetchColumn(),
];

$where = match ($filtre) {
    'attente' => "WHERE c.statut = 'En attente'",
    'cours'   => "WHERE c.statut IN ('En cours','En transit','En livraison')",
    'livre'   => "WHERE c.statut = 'Livré'",
    default   => '',
};
$liste = $db->query("SELECT c.*, d.nom AS dept FROM colis c LEFT JOIN departements d ON d.id = c.departement_id $where ORDER BY c.date_maj DESC, c.id DESC")->fetchAll();

/* Export CSV */
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="colis_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Référence', 'Tracking', 'Destinataire', 'Destination', 'Type', 'Poids (kg)', 'Statut', 'Dernière maj'], ';');
    foreach ($liste as $c) {
        fputcsv($out, [$c['reference'], $c['tracking'], $c['destinataire'], $c['destination'], $c['type'], $c['poids'], $c['statut'], $c['date_maj']], ';');
    }
    fclose($out);
    exit;
}

page_head('Suivi des colis', 'postier', 'suivi', $NAV, 'SERVICE POSTAL');
$tabs = [['tous', 'Tous'], ['attente', 'En attente'], ['cours', 'En cours'], ['livre', 'Livré']];
?>
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div><h1>Suivi des colis</h1><p>Liste complète des colis en cours de traitement</p></div>
  <a class="btn btn-gold" href="?f=<?= e($filtre) ?>&export=1"><?= icon('dl', 14) ?> Exporter (CSV)</a>
</div>

<div class="tabs">
  <?php foreach ($tabs as [$k, $lbl]): ?>
  <a href="?f=<?= $k ?>" class="<?= $filtre === $k ? 'on' : '' ?>" style="text-decoration:none;color:inherit;flex:1;text-align:center;padding:7px 12px;border-radius:6px;font-size:12.5px;<?= $filtre === $k ? 'background:var(--navy);color:#fff;font-weight:600;' : '' ?>">
    <?= $lbl ?> <span class="cnt"><?= $counts[$k] ?></span></a>
  <?php endforeach; ?>
</div>

<div class="card">
  <table>
    <thead><tr><th>Numéro</th><th>Destinataire</th><th>Département destinataire</th><th>Type</th><th>Poids</th><th>Dernière maj</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if (!$liste): ?><tr><td colspan="8" class="empty-state">Aucun colis dans cette catégorie.</td></tr><?php endif; ?>
    <?php foreach ($liste as $c): ?>
    <tr>
      <td style="font-weight:600;color:var(--navy);"><?= e($c['reference']) ?></td>
      <td><?= e($c['destinataire']) ?></td>
      <td><span class="badge badge-blue" style="font-size:10.5px;">Dépt. <?= e($c['dept'] ?? '?') ?></span><br><span style="font-size:11px;color:var(--text-light);"><?= e($c['destination']) ?></span></td>
      <td><?= e($c['type']) ?></td>
      <td><?= number_format((float)$c['poids'], 1, ',') ?> kg</td>
      <td><?= dateheure_fr($c['date_maj']) ?></td>
      <td><?= badge($c['statut']) ?></td>
      <td><a class="btn btn-outline btn-sm" href="scanner.php?code=<?= urlencode($c['tracking']) ?>">Détails</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php page_foot();
