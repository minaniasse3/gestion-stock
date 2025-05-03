<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Système de Gestion de Stock</title>
        
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
                    <ul class="navbar-nav ms-auto">
            @if (Route::has('login'))
                    @auth
                                <li class="nav-item">
                                    <a href="{{ url('/dashboard') }}" class="nav-link">Tableau de bord</a>
                                </li>
                                <li class="nav-item">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="nav-link btn btn-link">Déconnexion</button>
                                    </form>
                                </li>
                    @else
                                <li class="nav-item">
                                    <a href="{{ route('login') }}" class="nav-link">Connexion</a>
                                </li>
                        @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a href="{{ route('register') }}" class="nav-link">S'inscrire</a>
                                    </li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-5">
            @auth
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
            @else
                <div class="row">
                    <div class="col-md-6 offset-md-3 text-center py-5">
                        <div class="card dashboard-card">
                            <div class="card-body p-5">
                                <h1 class="display-4 mb-4">Gestion de Stock</h1>
                                <p class="lead">Un système complet pour gérer votre inventaire, suivre les produits et optimiser votre stock.</p>
                                <div class="mt-5">
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg mx-2">
                                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                                    </a>
                                    @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg mx-2">
                                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                                    </a>
                                    @endif
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            @endauth
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
