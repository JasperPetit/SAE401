<?php
$db = new PDO('sqlite:' . __DIR__ . '/data/database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$numero = 'CMD-007';
$query = $db->prepare("DELETE FROM Commande WHERE NumeroBonCommande = ?");
try {
    $res = $query->execute([$numero]);
    echo "Deleted: " . $res . "\n";
    echo "Rows affected: " . $query->rowCount() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
