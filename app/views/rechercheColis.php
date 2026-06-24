<?php
$texte_saisi = $_GET['champ_recherche'] ?? null;
$commande_trouvee = null; 

if ($texte_saisi && isset($this) && method_exists($this, 'rechercherRapide')) {
    $commande_trouvee = $this->rechercherRapide();
}
?>

<div style="position: relative; display: inline-block;">
    <form action="index.php" method="GET" style="display: flex; gap: 10px;">
        <input type="hidden" name="action" value="<?= htmlspecialchars($_GET['action'] ?? 'accueil') ?>">
        <input type="text" name="champ_recherche" class="search-input" style="margin-bottom: 0; width: 250px; padding: 8px 12px;" placeholder="Chercher un n°..." value="<?= htmlspecialchars($texte_saisi ?? '') ?>">
        <button type="submit" class="btn btn-blue" style="padding: 8px 12px;"><i class="fas fa-search"></i></button>
    </form>

    <?php if ($texte_saisi !== null): ?>
        <div style="position: absolute; top: 110%; left: 0; background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 300px; z-index: 100;">
            <?php if ($commande_trouvee): 
                $statut = $commande_trouvee['statut'] ?? '';
                $badge = ($statut == 'livré') ? 'badge-success' : (($statut == 'retard') ? 'badge-danger' : 'badge-warning');
            ?>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 10px;">
                    <strong>#<?= htmlspecialchars($commande_trouvee['NumeroBonDeCommande']) ?></strong>
                    <a href="index.php?action=<?= htmlspecialchars($_GET['action'] ?? 'accueil') ?>" style="color: var(--text-muted);"><i class="fas fa-times"></i></a>
                </div>
                <p style="font-size: 0.9rem; margin-bottom: 5px;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($commande_trouvee['AdresseArivee']) ?></p>
                <p style="font-size: 0.9rem;">Statut : <span class="badge <?= $badge ?>"><?= htmlspecialchars($statut) ?></span></p>
            <?php else: ?>
                <div style="display: flex; justify-content: space-between; color: var(--danger);">
                    <span><i class="fas fa-exclamation-circle"></i> Introuvable</span>
                    <a href="index.php?action=<?= htmlspecialchars($_GET['action'] ?? 'accueil') ?>" style="color: var(--danger);"><i class="fas fa-times"></i></a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>