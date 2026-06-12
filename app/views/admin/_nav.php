<?php
require_once dirname(__DIR__) . '/includes/layout.php';
$USER = require_role('admin');
$NAV = [
    ['accueil.php', 'home', 'Accueil', 'accueil'],
    ['commandes.php', 'cart', 'Mes commandes', 'commandes'],
    ['colis.php', 'box', 'Colis', 'colis'],
    ['fournisseurs.php', 'users', 'Fournisseurs', 'fournisseurs'],
    ['administration.php', 'cog', 'Administration', 'administration'],
    ['tutoriel.php', 'book', 'Tutoriel', 'tutoriel'],
];
