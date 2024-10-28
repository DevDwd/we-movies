# We Movies

Une application web de type "Allociné" permettant de parcourir et rechercher des films, développée avec Symfony 7.1 et l'API TMDB.

## Fonctionnalités

- Liste des genres de films
- Recherche de films avec autocomplétion
- Filtrage par genres
- Détails des films en modal
- Système de notation en 5 étoiles
- Lecture des bandes-annonces

## Prérequis

- PHP 8.3 ou supérieur
- Composer
- Node.js & NPM
- Docker & Docker Compose
- Clé API TMDB (à obtenir sur [https://www.themoviedb.org/documentation/api](https://www.themoviedb.org/documentation/api))

## Installation

1. Cloner le projet
```bash
git clone https://github.com/DevDwd/we-movies.git
cd we-movies
```

2. Installer les dépendances PHP
```bash
docker compose exec php composer install
```

3. Installer les dépendances JavaScript
```bash
docker compose exec node npm install
```

4. Compiler les assets
```bash
docker compose exec node npm run dev
```

5. Configurer les variables d'environnement
```bash
cp .env .env.local
# Éditer .env.local et ajouter votre clé API TMDB :
# TMDB_API_KEY=votre_clé_api
```

6. Lancer l'application
```bash
docker compose up -d
```

L'application sera accessible à l'adresse : http://localhost:8081

## Développement

### Qualité du code

Le projet utilise plusieurs outils pour maintenir la qualité du code :

1. PHP CS Fixer
```bash
docker compose exec php composer cs-fix
```

2. PHPStan
```bash
docker compose exec php composer phpstan
```

3. Tests unitaires
```bash
docker compose exec php composer test
```

4. Lancer tous les outils de qualité
```bash
docker compose exec php composer qa
```

### Compilation des assets

En mode développement :
```bash
docker compose exec node npm run dev
```

Pour la production :
```bash
docker compose exec node npm run build
```

## Structure du projet

```
src/
├── Application/
│   └── Service/
├── Domain/
│   └── Entity/
└── Infrastructure/
    └── TMDB/
```

## License

Ce projet est sous licence MIT.
