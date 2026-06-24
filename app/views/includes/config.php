<?php
/* ============================================================
   CONFIGURATION GÉNÉRALE - SAE COLIS
   Université Sorbonne Paris Nord - IUT de Villetaneuse
   ============================================================ */
session_start();

define('BASE_PATH', dirname(__DIR__));
define('DB_FILE', BASE_PATH . '/data/colis.db');

/* ---------- Connexion PDO SQLite (singleton) ---------- */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        /* Vérification : l'extension SQLite doit être activée
           (sur XAMPP : décommenter extension=pdo_sqlite dans php.ini) */
        if (!extension_loaded('pdo_sqlite')) {
            http_response_code(500);
            exit('<h2>Extension PHP manquante : pdo_sqlite</h2>
                  <p>Ouvrez votre fichier <code>php.ini</code> et décommentez (retirez le ;) les lignes :</p>
                  <pre>extension=pdo_sqlite
extension=sqlite3</pre>
                  <p>Puis redémarrez le serveur (Apache sur XAMPP).</p>');
        }
        /* Le dossier data/ peut être absent (les ZIP ignorent les dossiers
           vides) : on le crée automatiquement si besoin */
        $dir = dirname(DB_FILE);
        if (!is_dir($dir) && !@mkdir($dir, 0777, true)) {
            http_response_code(500);
            exit('<h2>Impossible de créer le dossier data/</h2>
                  <p>Créez manuellement un dossier <code>data</code> à la racine de
                  <code>site-colis/</code> et vérifiez les droits d\'écriture.</p>');
        }
        if (!is_writable($dir)) {
            http_response_code(500);
            exit('<h2>Le dossier data/ n\'est pas accessible en écriture</h2>
                  <p>Le site doit pouvoir y créer la base <code>colis.db</code>.
                  Clic droit sur le dossier &rarr; Propriétés &rarr; décochez « Lecture seule »
                  (ou <code>chmod 777 data</code> sous Linux/Mac).</p>');
        }
        $init = !file_exists(DB_FILE);
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
        if ($init) { init_schema($pdo); seed_data($pdo); }
    }
    return $pdo;
}

/* ---------- Création des tables ---------- */
function init_schema(PDO $pdo): void {
    $pdo->exec("
    CREATE TABLE utilisateurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        email TEXT,
        role TEXT NOT NULL CHECK(role IN ('admin','postier','financier','demandeur'))
    );
    CREATE TABLE fournisseurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        categorie TEXT,
        email TEXT, tel TEXT, adresse TEXT,
        specialites TEXT,
        note REAL DEFAULT 4.0,
        nb_commandes INTEGER DEFAULT 0
    );
    CREATE TABLE departements (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        budget_alloue REAL DEFAULT 0
    );
    CREATE TABLE commandes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        reference TEXT UNIQUE NOT NULL,
        date_creation TEXT NOT NULL,
        demandeur TEXT NOT NULL,
        service TEXT,
        departement_id INTEGER REFERENCES departements(id),
        fournisseur_id INTEGER REFERENCES fournisseurs(id),
        lieu_livraison TEXT,
        statut TEXT DEFAULT 'Devis transmis au SF',
        notes TEXT,
        total REAL DEFAULT 0
    );
    CREATE TABLE commande_articles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        commande_id INTEGER NOT NULL REFERENCES commandes(id) ON DELETE CASCADE,
        nom TEXT NOT NULL, reference TEXT,
        quantite INTEGER DEFAULT 1, prix REAL DEFAULT 0
    );
    CREATE TABLE colis (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        reference TEXT UNIQUE NOT NULL,
        tracking TEXT UNIQUE NOT NULL,
        expediteur TEXT, destinataire TEXT NOT NULL,
        destination TEXT, type TEXT DEFAULT 'Standard',
        departement_id INTEGER REFERENCES departements(id),
        poids REAL, statut TEXT DEFAULT 'En attente',
        date_maj TEXT
    );
    CREATE TABLE colis_historique (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        colis_id INTEGER NOT NULL REFERENCES colis(id) ON DELETE CASCADE,
        statut TEXT NOT NULL, lieu TEXT, note TEXT, date_h TEXT NOT NULL
    );
    CREATE TABLE transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        reference TEXT UNIQUE NOT NULL,
        date_t TEXT NOT NULL, client TEXT NOT NULL,
        type TEXT, montant REAL NOT NULL,
        statut TEXT DEFAULT 'En attente'
    );
    CREATE TABLE factures (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        numero TEXT UNIQUE NOT NULL,
        date_emission TEXT NOT NULL, client TEXT NOT NULL,
        echeance TEXT, montant REAL NOT NULL,
        statut TEXT DEFAULT 'En attente'
    );
    CREATE TABLE parametres (
        cle TEXT PRIMARY KEY, valeur TEXT
    );");
}

/* ---------- Données de démonstration ---------- */
function seed_data(PDO $pdo): void {
    $pdo->exec("INSERT INTO utilisateurs (nom, email, role) VALUES
        ('Omar Msa', 'admin@sorbonne-paris-nord.fr', 'admin'),
        ('Hela Addar', 'finance@sorbonne-paris-nord.fr', 'financier'),
        ('Agent Postal', 'postal@sorbonne-paris-nord.fr', 'postier'),
        ('Jean Dupont', 'dept-info@sorbonne-paris-nord.fr', 'demandeur')");

    /* Départements de l'IUT avec leur budget alloué */
    $pdo->exec("INSERT INTO departements (nom, budget_alloue) VALUES
        ('Informatique', 25000),
        ('Science des Données', 18000),
        ('GEII', 22000),
        ('R&T', 20000),
        ('GEA', 15000)");

    $pdo->exec("INSERT INTO fournisseurs (nom, categorie, email, tel, adresse, specialites, note, nb_commandes) VALUES
        ('Bureau Plus', 'Fournitures de bureau', 'contact@bureauplus.fr', '01 48 26 30 40', '15 Rue de la République, 93430 Villetaneuse', 'Papeterie,Mobilier,Consommables', 4.5, 28),
        ('TechSupply', 'Matériel informatique', 'ventes@techsupply.fr', '01 48 26 31 50', '28 Avenue Jean Jaurès, 93300 Aubervilliers', 'Ordinateurs,Périphériques,Logiciels,Réseau', 4.8, 38),
        ('Office Depot', 'Fournitures de bureau', 'pro@officedepot.fr', '01 48 26 32 60', '45 Boulevard de la Liberté, 93200 Saint-Denis', 'Fournitures,Mobilier,Imprimerie', 4.2, 17),
        ('LabEquip', 'Équipement de laboratoire', 'contact@labequip.fr', '01 48 26 33 70', '12 Rue des Sciences, 93430 Villetaneuse', 'Matériel scientifique,Consommables,Chimie', 4.6, 22)");

    /* Cycle de vie d'une commande (processus décrit par M. Butelle) :
       Devis transmis au SF -> Devis validé (SF) -> Bon de commande signé (directeur)
       -> Livraison en cours -> Réception confirmée (département) -> Fournisseur payé */
    $pdo->exec("INSERT INTO commandes (reference, date_creation, demandeur, service, departement_id, fournisseur_id, lieu_livraison, statut, total) VALUES
        ('CMD-2025-001', '2025-10-10', 'Jean Dupont', 'IUT Villetaneuse', 1, 1, 'Bureau 203, Bâtiment A', 'Livraison en cours', 245.50),
        ('CMD-2025-002', '2025-10-09', 'Marie Martin', 'IUT Villetaneuse', 2, 2, 'Salle 105, Bâtiment B', 'Devis validé', 1250.00),
        ('CMD-2025-003', '2025-10-08', 'Pierre Bernard', 'IUT Villetaneuse', 3, 3, 'Secrétariat Général', 'Devis transmis au SF', 487.20),
        ('CMD-2025-004', '2025-10-07', 'Sophie Dubois', 'IUT Villetaneuse', 1, 1, 'Bureau 110, Bâtiment C', 'Réception confirmée', 320.00),
        ('CMD-2025-005', '2025-09-25', 'Jean Dupont', 'IUT Villetaneuse', 1, 2, 'Bureau 203, Bâtiment A', 'Fournisseur payé', 890.00)");

    $pdo->exec("INSERT INTO commande_articles (commande_id, nom, reference, quantite, prix) VALUES
        (1, 'Ramette papier A4', 'BP-A4-500', 10, 4.55),
        (1, 'Stylos bille bleus (x50)', 'BP-STY-50', 4, 12.50),
        (2, 'Écran 24 pouces', 'TS-EC24', 5, 250.00),
        (3, 'Chaise de bureau', 'OD-CHB-12', 12, 40.60),
        (4, 'Classeurs A4 (x20)', 'BP-CLA-20', 8, 40.00)");

    /* Chaque colis est rattaché à un département destinataire :
       le service postal retrouve facilement où livrer, même si le
       bon de livraison papier est détérioré */
    $colis = [
        ['CP2024-11-001','TRK123456789','Bureau Plus','Jean Dupont','Dépt. Informatique - Bureau 203','Standard',1,1.2,'Livré'],
        ['CP2024-11-002','TRK987654321','TechSupply','Marie Martin','Dépt. Science des Données - Salle 105','Express',2,3.5,'En cours'],
        ['CP2024-11-003','TRK456789123','Office Depot','Pierre Bernard','Dépt. GEII - Secrétariat','Standard',3,0.8,'En attente'],
        ['CP2024-11-004','TRK789123456','LabEquip','Marie Chen','Dépt. R&T - Labo 2','Standard',4,2.1,'En cours'],
        ['CP2024-11-005','TRK321654987','Bureau Plus','Ahmed Ben Ali','Dépt. GEA - Bureau 12','Express',5,1.9,'Livré'],
        ['CP2024-11-006','TRK654987321','TechSupply','Lucie Bernard','Dépt. Informatique - Salle TP3','Standard',1,4.2,'En attente'],
    ];
    $st = $pdo->prepare("INSERT INTO colis (reference, tracking, expediteur, destinataire, destination, type, departement_id, poids, statut, date_maj)
                         VALUES (?,?,?,?,?,?,?,?,?,datetime('now','localtime'))");
    foreach ($colis as $c) { $st->execute($c); }

    /* Historique du premier colis (timeline de la maquette) */
    $pdo->exec("INSERT INTO colis_historique (colis_id, statut, lieu, note, date_h) VALUES
        (1, 'En transit', 'Dépôt national', 'Le colis a quitté le dépôt', '2024-11-27 08:00'),
        (1, 'Arrivé au centre de tri', 'Centre de tri Paris Nord', 'Le colis est arrivé au centre de tri', '2024-11-27 18:45'),
        (1, 'En cours de livraison', 'Centre de tri Villetaneuse', 'Le colis est en cours de livraison', '2024-11-28 10:15'),
        (1, 'Livré', 'IUT Villetaneuse - Bureau 203', 'Colis livré et signé', '2024-11-28 14:30'),
        (2, 'En cours de livraison', 'Centre de tri Villetaneuse', 'Le colis est en cours de livraison', '2024-11-28 10:15'),
        (3, 'En attente', 'Service postal IUT', 'Colis enregistré', '2024-11-28 09:00')");

    $pdo->exec("INSERT INTO transactions (reference, date_t, client, type, montant, statut) VALUES
        ('TRX-2024-1156', '2024-11-28', 'Département Informatique', 'Colis express', 450, 'Payé'),
        ('TRX-2024-1155', '2024-11-28', 'Service RH', 'Colis standard', 280, 'En attente'),
        ('TRX-2024-1154', '2024-11-27', 'Laboratoire Recherche', 'Colis prioritaire', 890, 'Payé'),
        ('TRX-2024-1153', '2024-11-27', 'Bibliothèque Universitaire', 'Colis standard', 125, 'Impayé'),
        ('TRX-2024-1152', '2024-11-26', 'Service Communication', 'Colis express', 340, 'Payé'),
        ('TRX-2024-1151', '2024-10-15', 'Département GEA', 'Colis standard', 220, 'Payé'),
        ('TRX-2024-1150', '2024-09-12', 'Service Scolarité', 'Colis express', 510, 'Payé'),
        ('TRX-2024-1149', '2024-08-20', 'Département Info', 'Colis standard', 180, 'Payé'),
        ('TRX-2024-1148', '2024-07-10', 'Direction', 'Colis prioritaire', 760, 'Payé'),
        ('TRX-2024-1147', '2024-06-05', 'Service RH', 'Colis express', 430, 'Payé')");

    $pdo->exec("INSERT INTO factures (numero, date_emission, client, echeance, montant, statut) VALUES
        ('FACT-2024-0456', '2024-11-28', 'Département Informatique', '2024-11-28', 450, 'Payée'),
        ('FACT-2024-0455', '2024-11-27', 'Service RH', '2024-12-04', 280, 'En attente'),
        ('FACT-2024-0454', '2024-11-26', 'Laboratoire Recherche', '2024-11-26', 890, 'Payée'),
        ('FACT-2024-0452', '2024-11-24', 'Service Communication', '2024-11-24', 340, 'Payée'),
        ('FACT-2024-0451', '2024-11-23', 'Direction Générale', '2024-11-30', 1250, 'En attente')");

    $pdo->exec("INSERT INTO parametres (cle, valeur) VALUES
        ('notif_email','1'),('notif_sms','0'),('notif_push','1'),('notif_livraison','1')");
}
