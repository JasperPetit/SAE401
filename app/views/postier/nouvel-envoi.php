<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exp   = trim($_POST['expediteur'] ?? '');
    $dest  = trim($_POST['destinataire'] ?? '');
    $ville = trim($_POST['destination'] ?? '');
    $dept_id = (int)($_POST['departement_id'] ?? 0);
    $type  = $_POST['type'] ?? 'Standard';
    $poids = (float)str_replace(',', '.', $_POST['poids'] ?? '0');

    if ($exp === '')   $erreurs[] = "Le nom de l'expéditeur est obligatoire.";
    if ($dest === '')  $erreurs[] = 'Le nom du destinataire est obligatoire.';
    if ($ville === '') $erreurs[] = 'La destination est obligatoire.';
    if ($poids <= 0)   $erreurs[] = 'Le poids doit être supérieur à 0.';
    if (!in_array($type, ['Standard', 'Express', 'Prioritaire'], true)) $type = 'Standard';

    if (!$erreurs) {
        $ref = next_ref('colis', 'reference', 'CP' . date('Y-m') . '-');
        $trk = 'TRK' . str_pad((string)random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        $db->prepare("INSERT INTO colis (reference, tracking, expediteur, destinataire, destination, type, departement_id, poids, statut, date_maj)
                      VALUES (?,?,?,?,?,?,?,?,'En attente',datetime('now','localtime'))")
           ->execute([$ref, $trk, $exp, $dest, $ville, $type, $dept_id ?: null, $poids]);
        $cid = (int)$db->lastInsertId();
        $db->prepare("INSERT INTO colis_historique (colis_id, statut, lieu, note, date_h)
                      VALUES (?,?,?,?,datetime('now','localtime'))")
           ->execute([$cid, 'En attente', 'Service postal IUT', 'Bon d\'expédition créé']);
        flash("Bon d'expédition $ref créé — numéro de suivi : $trk");
        header('Location: suivi-colis.php');
        exit;
    }
}

page_head('Nouvel envoi', 'postier', 'envoi', $NAV, 'SERVICE POSTAL');
?>
<div class="page-header"><h1>Nouvel envoi</h1><p>Créez un nouveau bon d'expédition</p></div>

<?php foreach ($erreurs as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>

<form method="post">
<?= csrf_field() ?>
<div class="card">
  <div class="card-title" style="margin-bottom:14px;">Informations de l'expéditeur</div>
  <div class="form-grid">
    <div><label class="f-label">Nom de l'expéditeur <span class="req">*</span></label>
      <input class="f-input" name="expediteur" value="<?= e($_POST['expediteur'] ?? '') ?>" placeholder="Ex : Service Communication" required></div>
    <div><label class="f-label">Bâtiment / Bureau</label>
      <input class="f-input" name="bureau" value="<?= e($_POST['bureau'] ?? '') ?>" placeholder="Ex : Bâtiment A - Bureau 201"></div>
  </div>
</div>
<div class="card">
  <div class="card-title" style="margin-bottom:14px;">Informations du destinataire</div>
  <div class="form-grid">
    <div><label class="f-label">Nom du destinataire <span class="req">*</span></label>
      <input class="f-input" name="destinataire" value="<?= e($_POST['destinataire'] ?? '') ?>" placeholder="Ex : Jean Martin" required></div>
    <div><label class="f-label">Département destinataire <span class="req">*</span></label>
      <select class="f-input" name="departement_id" required>
        <option value="">Sélectionner un département</option>
        <?php foreach (db()->query("SELECT id, nom FROM departements ORDER BY nom") as $d): ?>
        <option value="<?= $d['id'] ?>"><?= e($d['nom']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label class="f-label">Précision du lieu (bureau, salle) <span class="req">*</span></label>
      <input class="f-input" name="destination" value="<?= e($_POST['destination'] ?? '') ?>" placeholder="Ex : Bureau 203, Bâtiment A" required></div>
    <div><label class="f-label">Adresse complète</label>
      <input class="f-input" name="adresse" placeholder="Numéro, rue, code postal, ville"></div>
    <div><label class="f-label">Téléphone</label>
      <input class="f-input" name="tel" placeholder="Ex : 01 48 26 30 00"></div>
  </div>
</div>
<div class="card">
  <div class="card-title" style="margin-bottom:14px;">Caractéristiques du colis</div>
  <div class="form-grid">
    <div><label class="f-label">Type d'envoi <span class="req">*</span></label>
      <select class="f-input" name="type">
        <?php foreach (['Standard', 'Express', 'Prioritaire'] as $t): ?>
        <option <?= ($_POST['type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label class="f-label">Poids (kg) <span class="req">*</span></label>
      <input class="f-input" type="number" step="0.1" min="0.1" name="poids" value="<?= e($_POST['poids'] ?? '') ?>" placeholder="Ex : 1,2" required></div>
    <div><label class="f-label">Dimensions (cm)</label>
      <input class="f-input" name="dim" placeholder="Longueur x largeur x hauteur"></div>
    <div><label class="f-label">Valeur déclarée (€)</label>
      <input class="f-input" type="number" step="0.01" min="0" name="valeur" placeholder="0"></div>
  </div>
  <div style="margin-top:14px;"><label class="f-label">Instructions particulières</label>
    <textarea class="f-input" name="instructions" placeholder="Ajoutez des informations complémentaires..."></textarea></div>
</div>
<div class="actions-row">
  <button class="btn btn-gold" type="submit"><?= icon('send', 14) ?> Créer le bon d'expédition</button>
  <a class="btn btn-outline" href="tableau-de-bord.php">Annuler</a>
</div>
</form>
<?php page_foot();
