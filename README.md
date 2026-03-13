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

---

## 📂 Structure du projet

Voici un aperçu rapide de l'organisation des fichiers :

```text
.
├── composer.json        # Gestion des dépendances PHP
├── public/              # Dossier racine du serveur web
│   ├── assets/          # Ressources statiques compilées (CSS, JS, Images, PDF)
│   └── index.php        # Point d'entrée de l'application
├── scss/                # Sources des styles (Sass)
│   ├── abstact/         # Variables, thèmes (Light/Dark) et mixins
│   ├── components/      # Éléments réutilisables (Badge, Terminal)
│   ├── layout/          # Structure de base et Responsive
│   └── main.scss        # Fichier maître qui compile vers public/assets/css
├── src/                 # Architecture Modulaire PHP
│   ├── components/      # Fragments de pages (Navbar, Footer, Modales)
│   ├── database/        # Connexion et requêtes à la base de données
│   ├── sections/        # Sections de la page principale (Hero, Projets, Contact)
│   └── config.php       # Configuration globale de l'application
└── README.md            # Documentation
```
