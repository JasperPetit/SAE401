<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();

/* --- Ajouter un fournisseur --- */
$erreurs = [];
if (isset($_POST['ajout'])) {
    $nom = trim($_POST['nom'] ?? '');
    $cat = trim($_POST['categorie'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $spec = trim($_POST['specialites'] ?? '');
    if ($nom === '') $erreurs[] = 'Le nom du fournisseur est obligatoire.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = 'Email invalide.';
    if (!$erreurs) {
        $db->prepare("INSERT INTO fournisseurs (nom, categorie, email, tel, adresse, specialites) VALUES (?,?,?,?,?,?)")
           ->execute([$nom, $cat, $email, $tel, $adresse, $spec]);
        flash("Fournisseur « $nom » ajouté.");
        header('Location: fournisseurs.php');
        exit;
    }
}

/* --- Supprimer --- */
if (isset($_POST['suppr_id'])) {
    $db->prepare("DELETE FROM fournisseurs WHERE id = ?")->execute([(int)$_POST['suppr_id']]);
    flash('Fournisseur supprimé.');
    header('Location: fournisseurs.php');
    exit;
}

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $st = $db->prepare("SELECT * FROM fournisseurs WHERE nom LIKE ? OR categorie LIKE ? ORDER BY nom");
    $st->execute(["%$q%", "%$q%"]);
} else {
    $st = $db->query("SELECT * FROM fournisseurs ORDER BY nom");
}
$fournisseurs = $st->fetchAll();
$ouvrirForm = isset($_GET['ajouter']) || $erreurs;

page_head('Fournisseurs', 'admin', 'fournisseurs', $NAV, 'ADMINISTRATEUR');
?>
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div><h1>Fournisseurs</h1><p>Gérez vos fournisseurs et partenaires</p></div>
  <a class="btn btn-gold" href="?ajouter=1"><?= icon('plus', 14) ?> Ajouter un fournisseur</a>
</div>

<?php foreach ($erreurs as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>

<?php if ($ouvrirForm): ?>
<div class="card" style="border-left:3px solid var(--gold);">
  <div class="card-header"><div class="card-title">Nouveau fournisseur</div>
  <a class="btn btn-outline btn-sm" href="fournisseurs.php">Fermer</a></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="ajout" value="1">
    <div class="form-grid">
      <div><label class="f-label">Nom <span class="req">*</span></label><input class="f-input" name="nom" required></div>
      <div><label class="f-label">Catégorie</label><input class="f-input" name="categorie" placeholder="Ex : Fournitures de bureau"></div>
      <div><label class="f-label">Email</label><input class="f-input" type="email" name="email"></div>
      <div><label class="f-label">Téléphone</label><input class="f-input" name="tel"></div>
      <div><label class="f-label">Adresse</label><input class="f-input" name="adresse"></div>
      <div><label class="f-label">Spécialités (séparées par des virgules)</label><input class="f-input" name="specialites" placeholder="Papeterie, Mobilier"></div>
    </div>
    <div class="actions-row" style="margin-top:14px;">
      <button class="btn btn-gold" type="submit">Enregistrer</button>
    </div>
  </form>
</div>
<?php endif; ?>

<form method="get" class="search-row">
  <div class="search-wrap"><?= icon('search', 14) ?>
    <input class="search-input" name="q" value="<?= e($q) ?>" placeholder="Rechercher un fournisseur..."></div>
  <button class="btn btn-outline" type="submit">Rechercher</button>
</form>

<div class="fournisseur-grid">
<?php foreach ($fournisseurs as $f): ?>
  <div class="fournisseur-card">
    <div class="four-header">
      <div><div class="four-name"><?= e($f['nom']) ?></div>
      <div style="font-size:11.5px;color:var(--text-light);"><?= e($f['categorie']) ?></div></div>
      <span class="star-rating"><?= icon('star', 13) ?> <?= number_format((float)$f['note'], 1) ?></span>
    </div>
    <div class="four-info"><?= icon('mail', 13) ?> <?= e($f['email']) ?></div>
    <div class="four-info"><?= icon('phone', 13) ?> <?= e($f['tel']) ?></div>
    <div class="four-info"><?= icon('pin', 13) ?> <?= e($f['adresse']) ?></div>
    <div style="margin-top:8px;">
      <?php foreach (array_filter(explode(',', (string)$f['specialites'])) as $tag): ?>
      <span class="four-tag"><?= e(trim($tag)) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="four-footer">
      <span style="font-size:11.5px;color:var(--text-light);"><?= (int)$f['nb_commandes'] ?> commandes passées</span>
      <span style="display:flex;gap:6px;">
        <a class="btn btn-outline btn-sm" href="mailto:<?= e($f['email']) ?>">Contacter</a>
        <form method="post" onsubmit="return confirm('Supprimer ce fournisseur ?');" style="display:inline;">
          <?= csrf_field() ?>
          <button class="btn btn-outline btn-sm" name="suppr_id" value="<?= $f['id'] ?>" style="color:var(--red);"><?= icon('trash', 13) ?></button>
        </form>
      </span>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php page_foot();
