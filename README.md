# Portfolio de Lavieille Dylan

[![License](https://img.shields.io/github/license/lvlDylan/portfolio?style=flat-square)](LICENSE)

> **Bienvenue sur mon portfolio !** Ce projet regroupe mes travaux, mes compétences et mon parcours en tant que
> développeur. Il sert de vitrine pour mes futures collaborations.

---

## 🌐 Démo en ligne

👉 **Visitez le site ici : [https://dylanlv.dev](https://dylanlv.dev)**

---

## 🚀 À propos

Je suis **Dylan Lavieille**, un développeur passionné.
Ce portfolio a été conçu pour être performant, accessible et minimaliste afin de mettre en avant l'essentiel : mes
projets.

### ✨ Fonctionnalités du site

* **Design Responsive** : S'adapte parfaitement aux mobiles, tablettes et desktops.
* **Mode Sombre/Clair** : Thème dynamique respectant les préférences système.
* **Animations fluides** : Transitions douces pour une meilleure expérience utilisateur.

---

## 🛠️ Stack Technique

**Langages & Frameworks :**
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![Sass](https://img.shields.io/badge/Sass-CC6699?style=for-the-badge&logo=sass&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![NodeJS](https://img.shields.io/badge/Node.js-43853D?style=for-the-badge&logo=node.js&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)

**Environnement & Outils :**
![Arch Linux](https://img.shields.io/badge/Arch_Linux-1793D1?style=for-the-badge&logo=archlinux&logoColor=white)
![PhpStorm](https://img.shields.io/badge/PhpStorm-000000?style=for-the-badge&logo=phpstorm&logoColor=white)
## 🛠️ Installation et Compilation SASS

Ce projet est un site dynamique.

### 1. Prérequis

Assurez-vous d'avoir [PHP](https://php.net/) installé sur votre machine.

#### Optionnel:
*Ce projet utilise Scss pour modifier les variables Bootstrap.*
*Assurez-vous d'avoir un compilateur scss pour en profiter et [NodeJS](https://nodejs.org) pour compiler Boostrap.*

### 2. Installation

Clonez le projet et lancer le :

```bash
git clone https://github.com/lvlDylan/portfolio.git
cd portfolio
php -S localhost -t public
```

Vous pouvez désormais y accéder sur http://localhost

---

## 📂 Structure du projet

Voici un aperçu rapide de l'organisation des fichiers :

```text
.
├── assets/                 # Sources Front-end (non compilées)
│   └── scss/               # Architecture Sass (7-1 Pattern simplifié)
│       ├── abstract/       # Variables, mixins et fonctions
│       ├── base/           # Styles globaux, reset, typographie
│       ├── components/     # Éléments réutilisables (Navbar, Terminal, etc.)
│       ├── layout/         # Structure responsive (Media Queries)
│       └── main.scss       # Point d'entrée Sass (importations)
├── config/                 # Configuration de l'application
│   ├── bootstrap.php       # Initialisation (Autoload, .env, etc.)
│   └── database.php        # Configuration de la connexion PDO
├── logs/                   # Journaux d'erreurs (développement)
├── public/                 # Seul dossier accessible par le serveur web
│   ├── css/                # Fichiers CSS compilés (styles.css)
│   ├── docs/               # Documents téléchargeables (CV)
│   ├── img/                # Images et ressources statiques
│   ├── index.php           # Front Controller (Point d'entrée unique)
│   └── scripts/            # Fichiers JavaScript (Contact, animations)
├── src/                    # Cœur de l'application (Logique métier)
│   ├── Controllers/        # Contrôleurs gérant les requêtes
│   │   ├── Api/            # Endpoints pour les appels Fetch/AJAX
│   │   └── MainController.php
│   ├── Models/             # Interaction avec la base de données
│   │   ├── Api/            # Logique spécifique aux données API
│   │   └── ...             # Modèles Projects, Skills, etc.
│   ├── Services/           # Classes utilitaires (Database Singleton)
│   └── Router.php          # Gestionnaire de routes (Mapping URL -> Action)
├── views/                  # Fichiers de rendu (HTML/PHP)
│   ├── partials/           # Éléments communs (Header, Footer, Sidebar)
│   ├── sections/           # Blocs de contenu modulaires (Hero, Projects, ...)
│   └── layout.php          # Gabarit principal de l'application
├── composer.json           # Dépendances PHP (Dotenv, etc.)
└── package.json            # Dépendances Node (Sass compiler)
```
