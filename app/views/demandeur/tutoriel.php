<?php
require __DIR__ . '/_nav.php';
page_head('Tutoriel & Aide', 'demandeur', 'tutoriel', $NAV, 'DÉPT. INFORMATIQUE');

$videos = [
    ['Créer une commande', 'Apprenez à créer et soumettre une nouvelle commande', '5 min'],
    ['Suivre un colis', 'Découvrez comment suivre vos colis en temps réel', '3 min'],
    ['Gérer vos commandes', 'Consultez et filtrez vos commandes', '4 min'],
    ['Consulter les fournisseurs', 'Trouvez les informations de contact des fournisseurs', '2 min'],
];
$faq = [
    ['Comment suivre un colis ?', 'Rendez-vous sur la page Colis et saisissez votre numéro de suivi (TRK...) ou la référence du colis (CP...).'],
    ['Comment créer une commande ?', 'Cliquez sur Nouvelle commande, choisissez un fournisseur, ajoutez vos articles puis validez. Le total est calculé automatiquement.'],
    ['Que faire si mon colis est en retard ?', 'Consultez le dernier statut dans l\'historique du colis, puis contactez le support si le retard dépasse 48 h.'],
    ['Comment filtrer mes commandes ?', 'Sur la page Mes commandes, utilisez la barre de recherche et le filtre par statut.'],
    ['Comment contacter un fournisseur ?', 'Sur la page Fournisseurs, le bouton Contacter ouvre directement votre messagerie.'],
];
?>
<div class="page-header"><h1>Tutoriel &amp; Aide</h1><p>Guide d'utilisation de la plateforme</p></div>

<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title" style="margin-bottom:14px;">Vidéos tutorielles</div>
      <div class="tutorial-grid" style="margin-bottom:0;">
        <?php foreach ($videos as [$t, $d, $du]): ?>
        <div class="video-card">
          <div class="video-thumb"><?= icon('play', 30) ?></div>
          <div class="video-body"><div class="tuto-title"><?= e($t) ?></div>
          <div class="tuto-desc"><?= e($d) ?></div><div class="tuto-meta">Durée : <?= e($du) ?></div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="card">
      <div class="card-title" style="margin-bottom:8px;">Questions fréquentes</div>
      <?php foreach ($faq as $i => [$q, $r]): ?>
      <details class="faq-item" style="display:block;">
        <summary class="faq-q" style="cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center;">
          <?= e($q) ?> <?= icon('chev', 13) ?></summary>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:8px;"><?= e($r) ?></p>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title" style="display:flex;align-items:center;gap:8px;"><?= icon('book', 15) ?> Guide utilisateur</div>
      <p style="font-size:12px;color:var(--text-light);margin:8px 0 12px;">Téléchargez le guide complet d'utilisation de la plateforme</p>
      <a class="btn btn-primary" style="width:100%;justify-content:center;" href="#">Télécharger le guide (PDF)</a>
    </div>
    <div class="note-blue" style="margin-bottom:14px;">
      <strong style="color:var(--navy);">Besoin d'aide ?</strong>
      <p style="margin:6px 0 8px;">Notre équipe support est disponible pour vous aider.</p>
      <div style="display:flex;flex-direction:column;gap:6px;">
        <span><span style="color:var(--text-light);font-size:11px;display:block;">Email</span>support@sorbonne-paris-nord.fr</span>
        <span><span style="color:var(--text-light);font-size:11px;display:block;">Téléphone</span>01 48 26 30 00</span>
        <span><span style="color:var(--text-light);font-size:11px;display:block;">Horaires</span>Lun-Ven : 9h-17h</span>
      </div>
      <a class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:10px;" href="mailto:support@sorbonne-paris-nord.fr">Contacter le support</a>
    </div>
    <div class="note-gold">
      <strong>Nouveautés</strong>
      <p style="font-size:11.5px;margin-top:4px;">Version 2.1 - Novembre 2024</p>
      <ul style="font-size:11.5px;margin:6px 0 0 15px;display:flex;flex-direction:column;gap:3px;">
        <li>Nouveau système de suivi en temps réel</li>
        <li>Filtres améliorés pour les commandes</li>
        <li>Interface mobile optimisée</li>
      </ul>
    </div>
  </div>
</div>
<?php page_foot();
