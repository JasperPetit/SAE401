<?php
require_once dirname(__DIR__) . '/includes/layout.php';
$USER = require_role('postier');
$NAV = [
    ['tableau-de-bord.php', 'home', 'Tableau de bord', 'dashboard'],
    ['scanner.php', 'scan', 'Scanner un colis', 'scanner'],
    ['nouvel-envoi.php', 'send', 'Nouvel envoi', 'envoi'],
    ['suivi-colis.php', 'box', 'Suivi des colis', 'suivi'],
];
