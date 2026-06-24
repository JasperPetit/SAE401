<?php
$db = new PDO('sqlite:' . __DIR__ . '/data/database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $db->query("SELECT * FROM StatutCommande");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt = $db->query("SELECT * FROM Commande");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
