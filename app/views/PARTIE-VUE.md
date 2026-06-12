# Partie Vue — SAE Colis

Document de synthèse : périmètre de la partie **Vue** (interface utilisateur)
dans le projet, et comment elle s'articule avec le reste du code.

## 1. Fichiers relevant de la Vue

| Fichier / dossier | Rôle |
|---|---|
| `css/style.css` | Charte graphique complète et unique du site : palette aux couleurs du logo USPN (`#2a3257`), accent coloré par service, sidebar, cartes statistiques, tableaux, badges de statut, formulaires, timeline de suivi, responsive |
| `assets/logo.jpeg` | Identité visuelle de l'université, partagée par toutes les pages |
| `includes/layout.php` | Gabarit visuel commun : sidebar (logo, navigation, badge de rôle, déconnexion) + topbar. Toutes les pages l'utilisent, ce qui garantit une interface homogène |
| Le HTML de chaque page (`admin/*.php`, `financier/*.php`, `postier/*.php`, `demandeur/*.php`) | Structure d'affichage : cartes, tableaux, formulaires, badges — fidèle à la maquette Figma |
| `../maquette-services/` | Maquette statique d'origine (HTML/CSS pur), point de départ de la Vue |

## 2. Principes de design retenus

- **Une seule feuille de style** pour tout le site : cohérence garantie,
  une modification se répercute partout.
- **Identification des services par couleur d'accent** via une simple classe
  sur `<body>` (`service-admin`, `service-postier`, `service-financier`,
  `service-demandeur`) qui surcharge une variable CSS :
  doré = Administrateur, bleu = Postier, vert = Financier, violet = Demandeur.
- **Sidebar à la couleur exacte du logo** (`#2a3257`) : l'utilisateur sait
  immédiatement qu'il est sur un site de l'Université Sorbonne Paris Nord.
- **Icônes SVG inline** dessinées à la main (aucun emoji, aucune librairie
  externe) : rendu professionnel et homogène, site autonome.
- **Badges de statut codés par couleur** (vert = livré/payé, bleu = en cours,
  orange = en attente, rouge = retard/impayé) : lecture immédiate.
- **Responsive** : la mise en page s'adapte aux petits écrans.

## 3. Articulation avec le reste du projet (MVC)

```
┌─────────────┐     données      ┌──────────────────┐     HTML/CSS     ┌────────────┐
│   MODÈLE     │ ───────────────▶ │   CONTRÔLEUR      │ ───────────────▶ │    VUE      │
│ colis.db     │                  │ requêtes SQL,     │                  │ style.css   │
│ config.php   │                  │ traitements POST  │                  │ layout.php  │
└─────────────┘                  └──────────────────┘                  │ HTML pages  │
                                                                        └────────────┘
```

- La Vue **ne contient aucune logique métier** : elle reçoit des variables
  déjà préparées (listes, compteurs, statuts) et se contente de les afficher.
- Les helpers d'affichage (`badge()`, `icon()`, `eur()`, `date_fr()`)
  transforment une donnée brute en élément visuel.
- Toute donnée affichée passe par `e()` (échappement HTML) : la Vue est
  protégée contre les failles XSS.
