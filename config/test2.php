<?php
$path = realpath(__DIR__ . "/../data/database.db");
echo "Chemin : " . $path . "<br>";
echo "Fichier existe : " . (file_exists($path) ? "OUI" : "NON") . "<br>";
try {
    $db = new PDO("sqlite:" . $path);
    echo "Connexion OK";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
