#       Projet Colis S4


## Equipe
- [**@D4CJ**](https://github.com/D4CJ) Dimitar DIMITROV
- [**@JasperPetit**](https://github.com/JasperPetit) Jasper PETIT
- [**@omar92390**](https://github.com/omar92390) Omar MSA
- [**@Luka-jev**](https://github.com/Luka-jev) Luka JEVTIC
- [**@hela170**](https://github.com/hela170) Hela ADDAR
- [**@RiyadLaroub**](https://github.com/RiyadLaroub) Riyad LAROUB

## Sommaire
- [Instruction](#Instrction)
- [Description](#Description)
- [Prérequis](#Prérequis)
- [Lancement](#Lancement)  

## Instruction
- Ne jamais toucher au main.
- Ne jamais push d'une branche vers une autre, faire un **Pull Request**
- Pour le nom des commit, mettre l'une des catégories suivante : **Ajout, Correction, Modification, Suppression (ou autre)** puis deux points et une explication des changements.
  Exemple : 
`
  Modificaiton : Modification du nom des fichiers UtilisateurModel, DevisModel, DepartementsModel
  `

- Le nom des variable et des fonctions s'écrit en **snake case** (le premier mot commence par une minuscule puis tout les mots suivants sont collé et commencent par une majuscule) :
  Exemple : `getAllFournisseurs`

- Lors de la création d'une nouvelle foncitonnalité, créer une nouvelle branche. Eviter de revenir sur une branche aprés avoir fait son pull request, autant créer une nouvelle.

## Description

Cette application web permet de gérer le flux des colis au sein de l'IUT : de la demande de devis jusqu'au suivi de livraison, en passant par la gestion des commandes, des fournisseurs et des utilisateurs.
 
Le projet est développé en **PHP** selon **MVC** (Modèle-Vue-Contrôleur), avec une base de données **SQLite** locale. Il n'y a aucune dépendance externe.

## Prérequis
 
- **PHP 8.0 ou supérieur** installé sur la machine
- L'extension **PDO SQLite** activée (incluse par défaut dans la plupart des installations PHP)
- `git` pour cloner le projet

# Lancement
 
Récupérez d'abord le projet :
 
```bash
git clone <url_du_dépôt>
cd SAE401
```
 
### Méthode rapide 
 
C'est la méthode la plus rapide pour tester l'application en local. Depuis le répertoire du projet  exécutez :
 
```bash
php -S localhost:8000
```
### Méthode serveur web 
 
Placez le dossier du projet dans le répertoire web de votre serveur (par exemple `/var/www/html/` ou `www/` sous WAMP).
 
Le fichier `.htaccess` redirige les URL vers `index.php`. Pensez à adapter la ligne `RewriteBase` au nom de votre dossier :
 
```apache
RewriteBase /Projet-Colis/
```
 
Le site est ensuite accessible via l'URL correspondant à votre dossier




