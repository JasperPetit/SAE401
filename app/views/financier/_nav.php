<?php
require_once dirname(__DIR__) . '/includes/layout.php';
$USER = require_role('financier');
$NAV = [
    ['tableau-de-bord.php', 'grid', 'Tableau de bord', 'dashboard'],
    ['devis.php', 'check', 'Validation des devis', 'devis'],
    ['transactions.php', 'card', 'Transactions', 'transactions'],
    ['factures.php', 'file', 'Factures', 'factures'],
    ['rapports.php', 'chart', 'Rapports', 'rapports'],
];
