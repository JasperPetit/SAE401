<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();

/* --- Bascule des notifications (persistée en base) --- */
if (isset($_POST['toggle'])) {
    $cle = $_POST['toggle'];
    if (in_array($cle, ['notif_email', 'notif_sms', 'notif_push', 'notif_livraison'], true)) {
        $cur = $db->prepare("SELECT valeur FROM parametres WHERE cle = ?");
        $cur->execute([$cle]);
        $val = $cur->fetchColumn() === '1' ? '0' : '1';
        $db->prepare("INSERT INTO parametres (cle, valeur) VALUES (?,?)
                      ON CONFLICT(cle) DO UPDATE SET valeur = excluded.valeur")->execute([$cle, $val]);
        flash('Préférence mise à jour.');
    }
    header('Location: administration.php');
    exit;
}

/* --- Sauvegarde de la base (copie du fichier SQLite) --- */
if (isset($_POST['sauvegarder'])) {
    $dest = BASE_PATH . '/data/backup_' . date('Ymd_His') . '.db';
    copy(DB_FILE, $dest);
    flash('Sauvegarde créée : ' . basename($dest));
    header('Location: administration.php');
    exit;
}

$params = [];
foreach ($db->query("SELECT cle, valeur FROM parametres") as $p) { $params[$p['cle']] = $p['valeur']; }
$nbUsers = (int)$db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$users = $db->query("SELECT * FROM utilisateurs ORDER BY id")->fetchAll();
$backups = glob(BASE_PATH . '/data/backup_*.db') ?: [];

$toggles = [
    ['notif_email', 'Notifications par email'],
    ['notif_sms', 'Notifications par SMS'],
    ['notif_push', 'Notifications push'],
    ['notif_livraison', 'Alertes de livraison'],
];

page_head('Administration', 'admin', 'administration', $NAV, 'ADMINISTRATEUR');
?>
<div class="page-header"><h1>Administration</h1><p>Gérez les paramètres et configurations du système</p></div>

<div class="admin-grid">
  <div class="admin-block">
    <div class="ab-header">
      <div class="ab-icon"><?= icon('users', 16) ?></div>
      <div class="ab-title">Gestion des utilisateurs (<?= $nbUsers ?>)</div>
    </div>
    <div class="ab-desc">Comptes utilisateurs enregistrés et leurs rôles</div>
    <table style="font-size:12px;">
      <?php foreach ($users as $u): ?>
      <tr><td style="padding:6px 8px;font-weight:600;color:var(--navy);"><?= e($u['nom']) ?></td>
      <td style="padding:6px 8px;"><?= e($u['email']) ?></td>
      <td style="padding:6px 8px;"><span class="badge badge-gray"><?= e(strtoupper($u['role'])) ?></span></td></tr>
      <?php endforeach; ?>
    </table>
  </div>
  <div class="admin-block">
    <div class="ab-header">
      <div class="ab-icon"><?= icon('db', 16) ?></div>
      <div class="ab-title">Base de données</div>
    </div>
    <div class="ab-desc">Sauvegarde et maintenance de la base SQLite (<?= round(filesize(DB_FILE) / 1024) ?> Ko)</div>
    <form method="post" class="ab-actions">
      <?= csrf_field() ?>
      <button class="btn btn-gold btn-sm" name="sauvegarder" value="1">Sauvegarder maintenant</button>
    </form>
    <?php if ($backups): ?>
    <div style="font-size:11.5px;color:var(--text-light);margin-top:10px;">
      <strong>Dernières sauvegardes :</strong>
      <?php foreach (array_slice(array_reverse($backups), 0, 3) as $b): ?>
      <div><?= e(basename($b)) ?> (<?= round(filesize($b) / 1024) ?> Ko)</div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="ab-header" style="margin-bottom:4px;">
    <div class="ab-icon"><?= icon('bell', 16) ?></div>
    <div class="ab-title">Paramètres de notifications</div>
  </div>
  <div class="ab-desc" style="margin-bottom:12px;">Cliquez sur un interrupteur pour activer / désactiver (enregistré en base)</div>
  <?php foreach ($toggles as [$cle, $lbl]):
      $on = ($params[$cle] ?? '0') === '1'; ?>
  <div class="toggle-row">
    <span class="toggle-label"><?= $lbl ?></span>
    <form method="post" style="display:inline;">
      <?= csrf_field() ?>
      <button class="toggle <?= $on ? 'on' : 'off' ?>" name="toggle" value="<?= $cle ?>" style="border:none;" title="Basculer"></button>
    </form>
  </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="ab-header" style="margin-bottom:4px;">
    <div class="ab-icon"><?= icon('file', 16) ?></div>
    <div class="ab-title">Rapports et statistiques</div>
  </div>
  <div class="ab-desc" style="margin-bottom:12px;">Les rapports sont générés en temps réel par le Service Financier</div>
  <div class="ab-actions">
    <a class="btn btn-outline" href="../postier/suivi-colis.php?export=1">Export des colis (CSV)</a>
    <a class="btn btn-outline" href="colis.php">État des colis</a>
    <a class="btn btn-gold" href="commandes.php">Gérer les commandes</a>
  </div>
</div>
<?php page_foot();
