# SAE Colis — Site PHP fonctionnel

Plateforme de gestion et de suivi des colis — Université Sorbonne Paris Nord, IUT de Villetaneuse.

## Lancement

Prérequis : PHP 8+ avec l'extension SQLite (incluse par défaut).

```bash
cd site-colis
php -S localhost:8000
```

Puis ouvrir http://localhost:8000 dans un navigateur.

La base de données SQLite (`data/colis.db`) est **créée et remplie automatiquement**
avec des données de démonstration au premier chargement. Pour repartir de zéro,
il suffit de supprimer ce fichier.

## Structure

```
site-colis/
├── index.php              Sélection du profil (connexion)
├── logout.php             Déconnexion
├── css/style.css          CSS commun (même design que la maquette)
├── assets/logo.jpeg       Logo de l'université
├── data/                  Base SQLite (générée automatiquement)
├── includes/
│   ├── config.php         Connexion BDD + schéma + données de démo
│   ├── functions.php      Helpers (échappement, CSRF, badges, icônes SVG...)
│   ├── auth.php           Sessions et contrôle d'accès par rôle
│   └── layout.php         Gabarit commun (sidebar + topbar)
├── admin/                 Espace Administrateur
├── financier/             Espace Service Financier
├── postier/               Espace Service Postal
└── demandeur/             Espace Demandeur
```

## Fonctionnalités par profil

Le site implémente le processus réel décrit par M. Butelle (8 octobre 2025) :
devis du département → validation du service financier selon le budget →
signature du bon de commande par le directeur → livraison → confirmation de
réception par le département → paiement du fournisseur.

| Profil | Fonctionnalités |
|---|---|
| **Département** (Info, SD, GEII, R&T, GEA) | **Dépôt de devis** (articles dynamiques, total auto), **consultation du budget alloué / engagé / restant** avec barre de consommation, **confirmation de réception des colis** (informe automatiquement le SF), suivi de colis (timeline), annuaire fournisseurs |
| **Service Postal** (peu d'actions, service surchargé) | Affichage clair de l'état des livraisons, **département destinataire mis en avant sur chaque colis**, **recherche souple** (tracking, nom, département) pour retrouver le destinataire **même si le bon de livraison est détérioré**, scan simulé, export CSV |
| **Service Financier** | **Validation / refus des devis avec impact budget calculé**, enregistrement de la signature du directeur, **paiement du fournisseur bloqué tant que la réception n'est pas confirmée** (anti-blacklistage), **relance des départements**, consultation des budgets de tous les départements, rapports SVG temps réel |
| **ADMIN** | Gère tout et résout les problèmes : correction du statut de n'importe quelle commande, gestion des fournisseurs et utilisateurs, sauvegarde de la base en un clic |

Note : l'authentification simule le système USPN (qui sera fourni) ; la
gestion des rôles est en place via les sessions PHP.

## Sécurité mise en œuvre

- Requêtes **préparées PDO** partout (anti-injection SQL)
- Échappement HTML systématique via `e()` (anti-XSS)
- Jetons **CSRF** sur tous les formulaires POST
- Contrôle d'accès par rôle sur chaque page (`require_role()`)
- `session_regenerate_id()` à la connexion

## Comptes de démonstration

La connexion simule le CAS universitaire : on choisit son profil sur la page
d'accueil. Chaque profil correspond à un utilisateur en base
(admin, financier, postier, demandeur).
