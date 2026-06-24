<?php
require 'app/Models/ColisModel.php';
$db = new PDO('sqlite:data/database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$m = new \App\Models\ColisModel($db);

$colis = $m->getListeColisComplete();
if (empty($colis)) {
    echo "No colis to delete.";
    exit;
}

$id = $colis[0]['IdColis'];
echo "Deleting colis: $id\n";
try {
    $res = $m->deleteColis($id);
    echo "Result: " . ($res ? "success" : "failure");
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
