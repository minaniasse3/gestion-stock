# Système de Gestion de Stock

Application web complète pour la gestion d'inventaire, développée avec Laravel et Vue.js.

## Fonctionnalités

- Authentification complète avec différents rôles (admin, manager, utilisateur)
- Gestion des produits (ajout, modification, suppression, recherche)
- Suivi des fournisseurs avec coordonnées et historique
- Mouvements de stock (entrées, sorties, ajustements, retours)
- Tableau de bord avec statistiques et aperçus
- Génération de rapports (PDF, Excel, CSV)
- API RESTful complète et sécurisée

## Technologies utilisées

### Backend
- Laravel 11
- PostgreSQL
- Laravel Sanctum (authentification API)
- Base de données relationnelle avec migrations et seeders

### Frontend
- Vue.js 3
- Axios pour les appels API
- Bootstrap pour l'interface utilisateur
- Vue Router pour la navigation

## Installation

1. Cloner le dépôt
```bash
git clone https://github.com/votre-nom/gestion-stock.git
cd gestion-stock
```

2. Installer les dépendances
```bash
composer install
npm install
```

3. Configuration
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer la base de données dans le fichier .env
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gestion_stock
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

5. Exécuter les migrations et les seeders
```bash
php artisan migrate --seed
```

6. Compiler les assets
```bash
npm run dev
```

7. Démarrer l'application
```bash
php artisan serve
```

## Données de démonstration

Le système est préchargé avec des données de démonstration pour faciliter les tests :

### Utilisateurs
- Admin: admin@stockgestion.com / password123
- Manager: jean.dupont@stockgestion.com / password123
- Utilisateur: marie.lambert@stockgestion.com / password123

### Catalogue
- 15 produits variés (périphériques, ordinateurs, réseau, mobilier, etc.)
- 6 fournisseurs avec coordonnées complètes
- Historique de mouvements de stock sur plusieurs mois

## Structure de la base de données

### Tables principales
- users: Gestion des utilisateurs et des permissions
- products: Catalogue des produits en stock
- suppliers: Liste des fournisseurs
- stock_movements: Historique des mouvements (entrées, sorties, ajustements)
- activity_logs: Journal des activités système
- reports: Rapports générés par les utilisateurs

## API

L'API RESTful est accessible via le préfixe `/api`. Exemples d'endpoints:

```
GET /api/products                # Liste des produits
POST /api/products               # Création d'un produit
GET /api/products/{id}           # Détails d'un produit
PUT /api/products/{id}           # Mise à jour d'un produit
DELETE /api/products/{id}        # Suppression d'un produit

GET /api/suppliers               # Liste des fournisseurs
GET /api/dashboard/stats         # Statistiques pour le tableau de bord
GET /api/statistics/stock-evolution  # Évolution du stock dans le temps
```
- Lien Drive Video
- https://drive.google.com/file/d/1iblzpB4eD4Mptm7O2Y-MXxiYBYi9zx-c/view?usp=sharing

