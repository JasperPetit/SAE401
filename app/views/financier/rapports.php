<?php
require __DIR__ . '/_nav.php';

$db = db();

/* --- Revenus par mois (6 derniers mois avec données) --- */
$rows = $db->query("SELECT strftime('%Y-%m', date_t) AS mois, SUM(montant) AS total
                    FROM transactions WHERE statut = 'Payé'
                    GROUP BY mois ORDER BY mois DESC LIMIT 6")->fetchAll();
$rows = array_reverse($rows);
$moisFr = [1=>'Janv','Févr','Mars','Avr','Mai','Juin','Juil','Août','Sept','Oct','Nov','Déc'];
$labels = []; $revenus = [];
foreach ($rows as $r) {
    $labels[] = $moisFr[(int)substr($r['mois'], 5, 2)];
    $revenus[] = (float)$r['total'];
}
$couts = array_map(fn($v) => round($v * 0.46), $revenus); // estimation coûts (démo)
$maxV = max(array_merge($revenus, $couts, [1])) * 1.25;

/* --- Répartition par type --- */
$types = $db->query("SELECT type, COUNT(*) n FROM transactions GROUP BY type ORDER BY n DESC")->fetchAll();
$totalT = max(1, array_sum(array_column($types, 'n')));

/* --- Indicateurs --- */
$totRev   = (float)$db->query("SELECT COALESCE(SUM(montant),0) FROM transactions WHERE statut = 'Payé'")->fetchColumn();
$nTrx     = max(1, (int)$db->query("SELECT COUNT(*) FROM transactions")->fetchColumn());
$nPaye    = (int)$db->query("SELECT COUNT(*) FROM transactions WHERE statut = 'Payé'")->fetchColumn();
$valMoy   = $totRev / max(1, $nPaye);
$tauxPaye = round($nPaye / $nTrx * 100, 1);
$totCouts = array_sum($couts);
$marge    = $totRev - $totCouts;

/* --- SVG bar chart --- */
$W = 540; $H = 240; $PL = 52; $PB = 30; $PT = 12;
$ph = $H - $PB - $PT;
$svg = '';
for ($g = 0; $g <= 4; $g++) {
    $y = $H - $PB - $ph * $g / 4;
    $v = (int)($maxV * $g / 4);
    $svg .= '<line x1="' . $PL . '" y1="' . round($y) . '" x2="' . ($W - 8) . '" y2="' . round($y) . '" stroke="#eef2f7"/>';
    $svg .= '<text x="' . ($PL - 7) . '" y="' . round($y + 4) . '" text-anchor="end" font-size="9" fill="#94a3b8">' . ($v >= 1000 ? round($v / 1000) . 'k' : $v) . '</text>';
}
$n = max(1, count($labels));
$slot = ($W - $PL - 14) / $n;
foreach ($labels as $i => $m) {
    $cx = $PL + $slot * $i + $slot / 2;
    $hr = $ph * $revenus[$i] / $maxV;
    $hc = $ph * $couts[$i] / $maxV;
    $svg .= '<rect x="' . round($cx - 20) . '" y="' . round($H - $PB - $hr) . '" width="17" height="' . round($hr) . '" rx="2" fill="#2a3257"/>';
    $svg .= '<rect x="' . round($cx + 2) . '" y="' . round($H - $PB - $hc) . '" width="17" height="' . round($hc) . '" rx="2" fill="#2f9e6e"/>';
    $svg .= '<text x="' . round($cx) . '" y="' . ($H - 10) . '" text-anchor="middle" font-size="10" fill="#64748b">' . $m . '</text>';
}
$barchart = '<svg viewBox="0 0 ' . $W . ' ' . $H . '" style="width:100%;height:auto;">' . $svg . '</svg>';

/* --- SVG pie chart --- */
$colors = ['#2a3257', '#2f9e6e', '#c8933a', '#4a7fb5'];
$pie = ''; $a = -M_PI / 2; $legend = [];
foreach ($types as $i => $t) {
    $frac = $t['n'] / $totalT;
    $a1 = $a + $frac * 2 * M_PI;
    $x0 = 105 + 82 * cos($a);  $y0 = 105 + 82 * sin($a);
    $x1 = 105 + 82 * cos($a1); $y1 = 105 + 82 * sin($a1);
    $large = ($a1 - $a) > M_PI ? 1 : 0;
    $col = $colors[$i % 4];
    if ($frac >= 0.999) { $pie .= '<circle cx="105" cy="105" r="82" fill="' . $col . '"/>'; }
    else { $pie .= '<path d="M 105 105 L ' . round($x0,1) . ' ' . round($y0,1) . ' A 82 82 0 ' . $large . ' 1 ' . round($x1,1) . ' ' . round($y1,1) . ' Z" fill="' . $col . '"/>'; }
    $legend[] = [$t['type'], round($frac * 100), $col];
    $a = $a1;
}
$piechart = '<svg viewBox="0 0 210 210" style="width:180px;height:180px;">' . $pie . '</svg>';

page_head('Rapports', 'financier', 'rapports', $NAV, 'SERVICE FINANCIER');
?>
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div><h1>Rapports Financiers</h1><p>Analyses et statistiques calculées en temps réel sur la base de données</p></div>
  <a class="btn btn-gold" href="transactions.php?export=1"><?= icon('dl', 14) ?> Exporter les données</a>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
  <div class="stat-card">
    <div><div class="label">Revenus encaissés</div><div class="value" style="color:var(--green);font-size:20px;"><?= eur0($totRev) ?></div></div>
    <div class="stat-icon green"><?= icon('trend', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Coûts estimés</div><div class="value" style="color:var(--red);font-size:20px;"><?= eur0((float)$totCouts) ?></div></div>
    <div class="stat-icon orange"><?= icon('chart', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Marge bénéficiaire</div><div class="value" style="font-size:20px;"><?= eur0($marge) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $totRev > 0 ? round($marge / $totRev * 100, 1) : 0 ?> % de marge</div></div>
    <div class="stat-icon blue"><?= icon('cal', 18) ?></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
  <div class="card" style="margin-bottom:0;">
    <div class="card-title" style="margin-bottom:10px;">Revenus et Coûts Mensuels</div>
    <?= $barchart ?>
    <div style="display:flex;justify-content:center;gap:18px;font-size:11.5px;margin-top:6px;">
      <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#2a3257;margin-right:5px;"></span>Revenus (€)</span>
      <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#2f9e6e;margin-right:5px;"></span>Coûts (€)</span>
    </div>
  </div>
  <div class="card" style="margin-bottom:0;">
    <div class="card-title" style="margin-bottom:10px;">Répartition par Type de Service</div>
    <div style="display:flex;justify-content:center;padding:6px 0;"><?= $piechart ?></div>
    <div style="display:flex;justify-content:center;gap:14px;font-size:11.5px;flex-wrap:wrap;">
      <?php foreach ($legend as [$t, $pct, $col]): ?>
      <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:<?= $col ?>;margin-right:5px;"></span><?= e($t) ?> : <?= $pct ?> %</span>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:6px;">Indicateurs Clés</div>
  <div class="prog-row">
    <div style="display:flex;justify-content:space-between;font-size:12.5px;"><span>Valeur moyenne par transaction payée</span><strong style="color:var(--navy);"><?= eur0($valMoy) ?></strong></div>
    <div class="prog-bar"><div style="width:<?= min(100, $valMoy / 10) ?>%;background:#2a3257;"></div></div>
  </div>
  <div class="prog-row">
    <div style="display:flex;justify-content:space-between;font-size:12.5px;"><span>Taux de transactions payées</span><strong style="color:var(--navy);"><?= $tauxPaye ?> %</strong></div>
    <div class="prog-bar"><div style="width:<?= $tauxPaye ?>%;background:#16a34a;"></div></div>
  </div>
  <div class="prog-row">
    <div style="display:flex;justify-content:space-between;font-size:12.5px;"><span>Marge bénéficiaire</span><strong style="color:var(--navy);"><?= $totRev > 0 ? round($marge / $totRev * 100, 1) : 0 ?> %</strong></div>
    <div class="prog-bar"><div style="width:<?= $totRev > 0 ? round($marge / $totRev * 100) : 0 ?>%;background:#c8933a;"></div></div>
  </div>
</div>
<?php page_foot();
