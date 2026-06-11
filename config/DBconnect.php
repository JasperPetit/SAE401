<?php
    try {
        $db = new PDO('sqlite:/var/www/html/Projet-Colis/data/database.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
?>