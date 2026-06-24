<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bordereau - <?= htmlspecialchars($colis['NumeroBonDeCommande'] ?? 'Inconnu') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            body { margin: 0; padding: 0; background: #fff; }
            @page { size: auto; margin: 0mm; }
        }
        body { font-family: 'Roboto', sans-serif; background: #e2e8f0; display: flex; justify-content: center; padding: 20px; }
        .etiquette { width: 100%; max-width: 500px; background: #fff; border: 2px solid #000; padding: 20px; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; font-weight: bold; }
        .logo { font-size: 1.2rem; }
        .infos-grid { display: flex; gap: 15px; margin-bottom: 20px; }
        .case { flex: 1; border: 1px solid #000; padding: 10px; text-align: center; }
        .titre-section { font-size: 0.75rem; text-transform: uppercase; color: #555; margin-bottom: 5px; }
        .destinataire { border: 2px solid #000; padding: 15px; margin-bottom: 20px; }
        .adresse-grosse { font-size: 1.4rem; font-weight: bold; margin-top: 10px; line-height: 1.4; }
        .footer { text-align: center; border-top: 2px solid #000; padding-top: 15px; }
        .code-barres { font-family: 'Libre Barcode 39', cursive; font-size: 60px; line-height: 1; }
        .numero-lisible { font-size: 1.2rem; font-weight: bold; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="etiquette">
        <div class="header">
            <div class="logo">SERVICE POSTAL USPN</div>
            <div class="date">Date : <?= htmlspecialchars($colis['Date_'] ?? date('d/m/Y')) ?></div>
        </div>

        <div class="infos-grid">
            <div class="case">
                <div class="titre-section">Poids</div>
                <strong><?= htmlspecialchars($colis['Poids'] ?? 'Standard') ?> KG</strong>
            </div>
            <div class="case">
                <div class="titre-section">Format</div>
                <strong>STANDARD</strong>
            </div>
        </div>

        <div class="destinataire">
            <div class="titre-section">Adresse de livraison / Destinataire :</div>
            <div class="adresse-grosse">
                <?= htmlspecialchars($colis['AdresseArivee'] ?? 'Non spécifiée') ?>
            </div>
        </div>

        <div class="footer">
            <div class="code-barres">*<?= htmlspecialchars($colis['NumeroBonDeCommande'] ?? '0000') ?>*</div>
            <div class="numero-lisible"><?= htmlspecialchars($colis['NumeroBonDeCommande'] ?? '0000') ?></div>
        </div>
    </div>

    <script>
        // Lance l'impression automatiquement dès l'ouverture de la page
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>