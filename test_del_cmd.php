<?php
require 'app/Models/CommandeModel.php';
require 'app/Models/ColisModel.php';
$db = new PDO('sqlite:data/database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$colisModel = new \App\Models\ColisModel($db);
$commandeModel = new \App\Models\CommandeModel($db);

$num = 'CMD-005'; // Test with a known commande
echo "Attempting to delete $num\n";

try {
    $colisModel->deleteColisParCommande($num);
    echo "Deleted colis successfully.\n";
    
    $commandeModel->deleteCommande($num);
    echo "Deleted commande successfully.\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
