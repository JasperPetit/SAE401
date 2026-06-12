<?php
require_once dirname(__DIR__) . '/includes/layout.php';
$USER = require_role('demandeur');
$NAV = [
    ['accueil.php', 'home', 'Accueil', 'accueil'],
    ['nouvelle-commande.php', 'file', 'Déposer un devis', 'commande'],
    ['mes-commandes.php', 'cart', 'Mes commandes', 'mescommandes'],
    ['colis.php', 'box', 'Colis', 'colis'],
    ['fournisseurs.php', 'users', 'Fournisseurs', 'fournisseurs'],
    ['tutoriel.php', 'book', 'Tutoriel', 'tutoriel'],
];
