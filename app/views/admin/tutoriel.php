<?php
require __DIR__ . '/_nav.php';
page_head('Tutoriel', 'admin', 'tutoriel', $NAV, 'ADMINISTRATEUR');

$tutos = [
    ['file', 'Guide de démarrage rapide', 'Apprenez les bases du système de suivi de colis en 5 minutes', 'Article · 5 min', true],
    ['play', 'Comment créer un bon de commande', 'Tutoriel vidéo complet sur la création et la gestion des commandes', 'Vidéo · 8 min', false],
    ['scan', 'Scanner et enregistrer un colis', "Guide pratique pour scanner et enregistrer l'arrivée d'un colis", 'Article · 3 min', false],
    ['users', 'Gérer les fournisseurs', 'Comment ajouter et gérer vos fournisseurs dans le système', 'Vidéo · 6 min', false],
];
$faq = [
    ['Comment suivre un colis en attente ?', "Accédez à la page Colis et utilisez la recherche par numéro de tracking pour suivre votre colis en temps réel."],
    ['Qui peut créer un bon de commande ?', 'Les demandeurs créent les commandes ; les administrateurs les valident et font évoluer leur statut.'],
    ['Comment recevoir des notifications ?', 'Configurez vos préférences dans Administration > Paramètres de notifications. Elles sont enregistrées en base de données.'],
];
?>
<div class="page-header"><h1>Centre d'aide et Tutoriels</h1><p>Apprenez à utiliser toutes les fonctionnalités du système</p></div>

<div class="card">
  <div class="card-title" style="margin-bottom:16px;">Tutoriels recommandés</div>
  <div class="tutorial-grid">
    <?php foreach ($tutos as [$ic, $t, $d, $m, $nouveau]): ?>
    <div class="tuto-card">
      <div class="tuto-icon"><?= icon($ic, 16) ?></div>
      <div>
        <?php if ($nouveau): ?><span class="tuto-badge">Nouveau</span><?php endif; ?>
        <div class="tuto-title"><?= e($t) ?></div>
        <div class="tuto-desc"><?= e($d) ?></div>
        <div class="tuto-meta"><?= e($m) ?></div>
        <div class="tuto-footer"><a class="btn btn-outline btn-sm" href="#">Ouvrir</a></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:12px;">Questions fréquentes (FAQ)</div>
  <?php foreach ($faq as [$q, $r]): ?>
  <details class="faq-item" style="display:block;">
    <summary class="faq-q" style="cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center;">
      <?= e($q) ?> <?= icon('chev', 13) ?></summary>
    <p style="font-size:12.5px;color:var(--text-light);margin-top:8px;"><?= e($r) ?></p>
  </details>
  <?php endforeach; ?>
</div>

<div style="text-align:center;margin-top:24px;">
  <p style="font-size:12px;color:var(--text-light);">Vous ne trouvez pas de réponse à votre question ?</p>
  <a class="btn btn-gold" style="margin-top:10px;" href="mailto:support@sorbonne-paris-nord.fr">Contacter le support</a>
</div>
<?php page_foot();
