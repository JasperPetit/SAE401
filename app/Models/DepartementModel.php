<?php 
namespace app\Models;
use PDO;

class departementModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllDepartements(){
        $sql = "SELECT * FROM Departement";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

}


?>