<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();
$fournisseurs = $db->query('SELECT id, nom FROM fournisseurs ORDER BY nom')->fetchAll();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fid     = (int)($_POST['fournisseur_id'] ?? 0);
    $service = trim($_POST['service'] ?? '');
    $lieu    = trim($_POST['lieu'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');
    $noms    = $_POST['art_nom'] ?? [];
    $refs    = $_POST['art_ref'] ?? [];
    $qtes    = $_POST['art_qte'] ?? [];
    $prix    = $_POST['art_prix'] ?? [];

    if (!$fid)               $erreurs[] = 'Veuillez sélectionner un fournisseur.';
    if ($service === '')     $erreurs[] = 'Le service est obligatoire.';
    if ($lieu === '')        $erreurs[] = 'Le lieu de livraison est obligatoire.';

    $articles = [];
    foreach ($noms as $i => $nom) {
        $nom = trim($nom);
        if ($nom === '') continue;
        $q = max(1, (int)($qtes[$i] ?? 1));
        $p = max(0, (float)str_replace(',', '.', $prix[$i] ?? '0'));
        $articles[] = [$nom, trim($refs[$i] ?? ''), $q, $p];
    }
    if (!$articles) $erreurs[] = 'Ajoutez au moins un article.';

    if (!$erreurs) {
        $ref = next_ref('commandes', 'reference', 'CMD-' . date('Y') . '-');
        $total = array_sum(array_map(fn($a) => $a[2] * $a[3], $articles));
        $db->prepare("INSERT INTO commandes (reference, date_creation, demandeur, service, departement_id, fournisseur_id, lieu_livraison, statut, notes, total)
                      VALUES (?,date('now'),?,?,1,?,?,'Devis transmis au SF',?,?)")
           ->execute([$ref, $USER['nom'], $service, $fid, $lieu, $notes, $total]);
        $cid = (int)$db->lastInsertId();
        $sa = $db->prepare('INSERT INTO commande_articles (commande_id, nom, reference, quantite, prix) VALUES (?,?,?,?,?)');
        foreach ($articles as $a) { $sa->execute([$cid, ...$a]); }
        $db->prepare('UPDATE fournisseurs SET nb_commandes = nb_commandes + 1 WHERE id = ?')->execute([$fid]);
        flash("Devis $ref transmis au service financier pour validation (" . count($articles) . ' article(s), total ' . eur($total) . ').');
        header('Location: mes-commandes.php');
        exit;
    }
}

page_head('Déposer un devis', 'demandeur', 'commande', $NAV, 'DÉPT. INFORMATIQUE');
?>
<div class="page-header"><h1>Déposer un devis</h1><p>Renseignez le devis négocié avec le fournisseur — il sera transmis au service financier pour validation</p></div>

<?php foreach ($erreurs as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>

<form method="post" id="form-commande">
<?= csrf_field() ?>
<div class="card">
  <div class="card-title" style="margin-bottom:14px;">Informations générales</div>
  <div class="form-grid">
    <div><label class="f-label">Fournisseur <span class="req">*</span></label>
      <select class="f-input" name="fournisseur_id" required>
        <option value="">Sélectionner un fournisseur</option>
        <?php foreach ($fournisseurs as $f): ?>
        <option value="<?= $f['id'] ?>" <?= (int)($_POST['fournisseur_id'] ?? 0) === (int)$f['id'] ? 'selected' : '' ?>><?= e($f['nom']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label class="f-label">Service <span class="req">*</span></label>
      <input class="f-input" name="service" value="<?= e($_POST['service'] ?? 'IUT Villetaneuse') ?>" required></div>
  </div>
  <div style="margin-top:14px;"><label class="f-label">Lieu de livraison <span class="req">*</span></label>
    <input class="f-input" name="lieu" placeholder="Ex : Bureau 203, Bâtiment A" value="<?= e($_POST['lieu'] ?? '') ?>" required></div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Articles</div>
    <button class="btn btn-primary btn-sm" type="button" onclick="ajouterArticle()"><?= icon('plus', 13) ?> Ajouter un article</button></div>
  <div id="articles">
    <div class="article-row" style="display:grid;grid-template-columns:2fr 1.4fr .7fr .9fr auto;gap:10px;align-items:end;margin-bottom:10px;">
      <div><label class="f-label">Nom de l'article</label><input class="f-input" name="art_nom[]" placeholder="Ex : Ramette papier A4"></div>
      <div><label class="f-label">Référence</label><input class="f-input" name="art_ref[]" placeholder="Réf. fournisseur"></div>
      <div><label class="f-label">Quantité</label><input class="f-input qte" type="number" name="art_qte[]" value="1" min="1"></div>
      <div><label class="f-label">Prix unitaire (€)</label><input class="f-input prix" type="number" name="art_prix[]" value="0" min="0" step="0.01"></div>
      <button class="btn btn-outline btn-sm" type="button" style="height:35px;color:var(--text-light);" onclick="retirerArticle(this)"><?= icon('trash', 14) ?></button>
    </div>
  </div>
  <div style="display:flex;justify-content:flex-end;margin-top:8px;">
    <div class="note-blue" style="min-width:150px;text-align:right;">
      <span style="font-size:11px;color:var(--text-light);display:block;">Total estimé</span>
      <strong style="color:var(--navy);font-size:15px;" id="total">0,00 €</strong>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:12px;">Informations du devis (référence fournisseur, conditions négociées...)</div>
  <textarea class="f-input" name="notes" placeholder="Ajoutez des informations complémentaires..."><?= e($_POST['notes'] ?? '') ?></textarea>
</div>

<div class="actions-row">
  <button class="btn btn-gold" type="submit"><?= icon('send', 14) ?> Transmettre le devis au service financier</button>
  <a class="btn btn-outline" href="accueil.php">Annuler</a>
</div>
</form>

<script>
function majTotal() {
  let total = 0;
  document.querySelectorAll('#articles .article-row').forEach(r => {
    const q = parseFloat(r.querySelector('.qte').value) || 0;
    const p = parseFloat(r.querySelector('.prix').value) || 0;
    total += q * p;
  });
  document.getElementById('total').textContent =
    total.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' €';
}
function ajouterArticle() {
  const ligne = document.querySelector('#articles .article-row');
  const clone = ligne.cloneNode(true);
  clone.querySelectorAll('input').forEach(i => i.value = i.classList.contains('qte') ? 1 : (i.classList.contains('prix') ? 0 : ''));
  document.getElementById('articles').appendChild(clone);
  majTotal();
}
function retirerArticle(btn) {
  const lignes = document.querySelectorAll('#articles .article-row');
  if (lignes.length > 1) btn.closest('.article-row').remove();
  else btn.closest('.article-row').querySelectorAll('input').forEach(i => i.value = i.classList.contains('qte') ? 1 : (i.classList.contains('prix') ? 0 : ''));
  majTotal();
}
document.getElementById('articles').addEventListener('input', majTotal);
majTotal();
</script>
<?php page_foot();
