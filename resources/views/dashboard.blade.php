<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord - Système de Gestion de Stock</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        .dashboard-card {
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .header-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-boxes me-2"></i>Gestion de Stock
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="{{ url('/dashboard') }}" class="nav-link active">
                            <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/products') }}" class="nav-link">
                            <i class="fas fa-box me-1"></i>Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/suppliers') }}" class="nav-link">
                            <i class="fas fa-truck me-1"></i>Fournisseurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/reports') }}" class="nav-link">
                            <i class="fas fa-file-alt me-1"></i>Rapports
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name ?? 'Utilisateur' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mon profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="header-container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>Tableau de bord</h1>
                    <p class="text-muted">Bienvenue dans votre système de gestion de stock</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ url('/products/create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouveau Produit
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card mb-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary text-white">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Produits</h6>
                            <h3 class="mb-0">256</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card mb-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success text-white">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">En Stock</h6>
                            <h3 class="mb-0">184</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card mb-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning text-white">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Stock Faible</h6>
                            <h3 class="mb-0">15</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card mb-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger text-white">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">À Commander</h6>
                            <h3 class="mb-0">57</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon text-primary">
                            <i class="fas fa-box"></i>
                        </div>
                        <h3 class="card-title">Produits</h3>
                        <p class="card-text">Gérez votre inventaire de produits, ajoutez de nouveaux articles et mettez à jour les stocks.</p>
                        <a href="{{ url('/products') }}" class="btn btn-outline-primary mt-3">
                            Accéder aux produits
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon text-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="card-title">Statistiques</h3>
                        <p class="card-text">Consultez les rapports et les statistiques concernant vos mouvements de stock.</p>
                        <a href="{{ url('/statistics') }}" class="btn btn-outline-success mt-3">
                            Voir les statistiques
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon text-info">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="card-title">Utilisateurs</h3>
                        <p class="card-text">Gérez les utilisateurs et leurs permissions dans le système.</p>
                        <a href="{{ url('/users') }}" class="btn btn-outline-info mt-3">
                            Gérer les utilisateurs
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4 mt-4">
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon text-warning">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3 class="card-title">Fournisseurs</h3>
                        <p class="card-text">Gérez vos fournisseurs et suivez les commandes fournisseurs.</p>
                        <a href="{{ url('/suppliers') }}" class="btn btn-outline-warning mt-3">
                            Gérer les fournisseurs
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon text-danger">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="card-title">Rapports</h3>
                        <p class="card-text">Générez et téléchargez des rapports de stock détaillés.</p>
                        <a href="{{ url('/reports') }}" class="btn btn-outline-danger mt-3">
                            Générer des rapports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">© {{ date('Y') }} Système de Gestion de Stock | Tous droits réservés</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 