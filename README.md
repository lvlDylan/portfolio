# Portfolio de Lavieille Dylan

[![License](https://img.shields.io/github/license/lvlDylan/portfolio?style=flat-square)](LICENSE)

![GitHub last commit](https://img.shields.io/github/last-commit/lvlDylan/portfolio?style=flat-square)

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
![NodeJS](https://img.shields.io/badge/Node.js-43853D?style=for-the-badge&logo=node.js&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![Sass](https://img.shields.io/badge/Sass-CC6699?style=for-the-badge&logo=sass&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

**Environnement & Outils :**
![Kubuntu](https://img.shields.io/badge/Kubuntu-0079C1?style=for-the-badge&logo=kubuntu&logoColor=white)
![WebStorm](https://img.shields.io/badge/WebStorm-000000?style=for-the-badge&logo=webstorm&logoColor=white)

## 🛠️ Installation et Compilation SASS

Ce projet est un site statique. **Node.js** est utilisé uniquement pour compiler les fichiers `.scss` en CSS.

### 1. Prérequis

Assurez-vous d'avoir [Node.js](https://nodejs.org/) installé sur votre machine.

### 2. Installation

Clonez le projet et installez les dépendances (le compilateur SASS) :

```bash
git clone https://github.com/lvlDylan/portfolio.git
cd portfolio
npm install
```

---

## 📂 Structure du projet

Voici un aperçu rapide de l'organisation des fichiers :

```text
.
├── assets/
│   ├── icons/       # Favicon et icônes SVG
│   ├── images/      # Ressources graphiques (photo de profil, etc.)
│   ├── scripts/     # Logique JS (Gestion du thème, scroll, animations)
│   ├── styles/      # CSS final compilé (ne pas modifier directement)
│   └── vendors/     # Librairies externes (ex: Typed.js)
├── scss/            # Sources des styles (Sass)
│   ├── main.scss    # Point d'entrée des styles
│   ├── _theme.scss  # Variables et mixins pour le Dark/Light mode
│   └── ...          # Autres composants (Terminal, Badges, Responsive)
├── index.html       # Structure HTML principale
├── package.json     # Scripts de build et dépendances
└── README.md        # Documentation du projet
```