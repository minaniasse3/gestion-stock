<template>
  <div>
    <!-- Hero Section avec vidéo/image de fond pour tous les visiteurs -->
    <div class="hero-section text-center p-5 text-white rounded-3 mb-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7 text-lg-start">
            <h1 class="display-4 fw-bold mb-4"><i class="fas fa-boxes me-2"></i>Système de Gestion de Stock</h1>
            <p class="lead fs-4 mb-4">
              Une solution complète pour gérer efficacement vos produits, inventaires et fournisseurs.
            </p>
            <div class="mt-4 d-flex flex-wrap gap-2 justify-content-lg-start justify-content-center">
              <router-link v-if="!isAuthenticated" to="/login" class="btn btn-light btn-lg me-2">
                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
              </router-link>
              <router-link v-if="!isAuthenticated" to="/register" class="btn btn-outline-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Créer un compte
              </router-link>
              <router-link v-if="isAuthenticated" to="/dashboard" class="btn btn-light btn-lg me-2">
                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
              </router-link>
              <router-link v-if="isAuthenticated" to="/products" class="btn btn-outline-light btn-lg">
                <i class="fas fa-box me-2"></i>Gérer les produits
              </router-link>
            </div>
          </div>
          <div class="col-lg-5 d-none d-lg-block">
            <img src="/storage/hero-image.svg" alt="Gestion de stock" class="img-fluid mt-4 mt-lg-0" 
                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/1e88e5/FFFFFF?text=Gestion+de+Stock&font=montserrat'">
          </div>
        </div>
      </div>
    </div>

    <!-- Message de bienvenue pour les utilisateurs authentifiés -->
    <div v-if="isAuthenticated" class="card bg-light mb-4 border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="d-md-flex align-items-center justify-content-between">
          <div>
            <h2 class="mb-1">Bienvenue, {{ user.name }} !</h2>
            <p class="text-muted mb-md-0">Voici un aperçu de votre système de gestion de stock</p>
          </div>
          <div class="mt-3 mt-md-0">
            <router-link to="/dashboard" class="btn btn-primary me-2">
              <i class="fas fa-tachometer-alt me-2"></i>Mon tableau de bord
            </router-link>
            <router-link to="/profile" class="btn btn-outline-secondary">
              <i class="fas fa-user-circle me-2"></i>Mon profil
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques rapides pour les utilisateurs authentifiés -->
    <div v-if="isAuthenticated" class="row mb-5">
      <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                <i class="fas fa-box text-primary fa-2x"></i>
              </div>
              <div>
                <h6 class="text-muted mb-1">Produits</h6>
                <h3 class="mb-0">{{ stats.products }}</h3>
                <span v-if="stats.productsChange" class="badge mt-2" :class="stats.productsChange > 0 ? 'bg-success' : 'bg-danger'">
                  <i :class="stats.productsChange > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ Math.abs(stats.productsChange) }}% ce mois
                </span>
              </div>
            </div>
          </div>
          <div class="card-footer bg-white border-0 p-0">
            <router-link to="/products" class="btn btn-sm btn-light w-100 rounded-0 rounded-bottom py-2">
              <i class="fas fa-list me-1"></i> Voir tous les produits
            </router-link>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                <i class="fas fa-truck text-success fa-2x"></i>
              </div>
              <div>
                <h6 class="text-muted mb-1">Fournisseurs</h6>
                <h3 class="mb-0">{{ stats.suppliers }}</h3>
                <span v-if="stats.suppliersChange" class="badge mt-2" :class="stats.suppliersChange > 0 ? 'bg-success' : 'bg-danger'">
                  <i :class="stats.suppliersChange > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ Math.abs(stats.suppliersChange) }}% ce mois
                </span>
              </div>
            </div>
          </div>
          <div class="card-footer bg-white border-0 p-0">
            <router-link to="/suppliers" class="btn btn-sm btn-light w-100 rounded-0 rounded-bottom py-2">
              <i class="fas fa-list me-1"></i> Voir tous les fournisseurs
            </router-link>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
              </div>
              <div>
                <h6 class="text-muted mb-1">Stock faible</h6>
                <h3 class="mb-0">{{ stats.lowStock }}</h3>
                <span v-if="stats.lowStockChange" class="badge mt-2" :class="stats.lowStockChange < 0 ? 'bg-success' : 'bg-danger'">
                  <i :class="stats.lowStockChange < 0 ? 'fas fa-arrow-down' : 'fas fa-arrow-up'"></i>
                  {{ Math.abs(stats.lowStockChange) }}% ce mois
                </span>
              </div>
            </div>
          </div>
          <div class="card-footer bg-white border-0 p-0">
            <button @click="showLowStockProducts" class="btn btn-sm btn-light w-100 rounded-0 rounded-bottom py-2">
              <i class="fas fa-search me-1"></i> Voir les détails
            </button>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                <i class="fas fa-users text-info fa-2x"></i>
              </div>
              <div>
                <h6 class="text-muted mb-1">Utilisateurs</h6>
                <h3 class="mb-0">{{ stats.users }}</h3>
                <span v-if="stats.usersChange" class="badge mt-2" :class="stats.usersChange > 0 ? 'bg-success' : 'bg-danger'">
                  <i :class="stats.usersChange > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ Math.abs(stats.usersChange) }}% ce mois
                </span>
              </div>
            </div>
          </div>
          <div class="card-footer bg-white border-0 p-0">
            <router-link v-if="hasAdminAccess" to="/users" class="btn btn-sm btn-light w-100 rounded-0 rounded-bottom py-2">
              <i class="fas fa-list me-1"></i> Gérer les utilisateurs
            </router-link>
            <span v-else class="btn btn-sm btn-light w-100 disabled rounded-0 rounded-bottom py-2">
              <i class="fas fa-lock me-1"></i> Accès restreint
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Cartes de fonctionnalités principales -->
    <h2 class="mb-4" v-if="isAuthenticated">Accès rapide</h2>
    <div class="row g-4 mb-5">
      <!-- Gestion des Produits - visible pour tous les utilisateurs authentifiés -->
      <div class="col-md-4" v-if="isAuthenticated">
        <div class="card h-100 dashboard-card">
          <div class="card-body text-center pt-4">
            <div class="card-icon text-primary">
              <i class="fas fa-box"></i>
            </div>
            <h5 class="card-title">Gestion des Produits</h5>
            <p class="card-text">
              Ajoutez, modifiez et suivez vos produits. Gardez un œil sur les niveaux de stock.
            </p>
            <router-link to="/products" class="btn btn-primary mt-2">
              <i class="fas fa-arrow-right me-2"></i>Gérer les produits
            </router-link>
          </div>
        </div>
      </div>

      <!-- Gestion des Fournisseurs - visible pour tous les utilisateurs authentifiés -->
      <div class="col-md-4" v-if="isAuthenticated">
        <div class="card h-100 dashboard-card">
          <div class="card-body text-center pt-4">
            <div class="card-icon text-success">
              <i class="fas fa-truck"></i>
            </div>
            <h5 class="card-title">Gestion des Fournisseurs</h5>
            <p class="card-text">
              Enregistrez vos fournisseurs et leurs coordonnées pour un suivi efficace.
            </p>
            <router-link to="/suppliers" class="btn btn-success mt-2">
              <i class="fas fa-arrow-right me-2"></i>Gérer les fournisseurs
            </router-link>
          </div>
        </div>
      </div>

      <!-- Statistiques - visible pour les managers et admins -->
      <div class="col-md-4" v-if="isAuthenticated && hasManagerAccess">
        <div class="card h-100 dashboard-card">
          <div class="card-body text-center pt-4">
            <div class="card-icon text-info">
              <i class="fas fa-chart-line"></i>
            </div>
            <h5 class="card-title">Statistiques</h5>
            <p class="card-text">
              Visualisez l'état de votre inventaire avec des graphiques et des rapports détaillés.
            </p>
            <router-link to="/statistics" class="btn btn-info text-white mt-2">
              <i class="fas fa-arrow-right me-2"></i>Voir les statistiques
            </router-link>
          </div>
        </div>
      </div>

      <!-- Profil utilisateur - visible pour tous les utilisateurs authentifiés, remplace stats pour utilisateurs standards -->
      <div class="col-md-4" v-if="isAuthenticated && !hasManagerAccess">
        <div class="card h-100 dashboard-card">
          <div class="card-body text-center pt-4">
            <div class="card-icon text-info">
              <i class="fas fa-user-circle"></i>
            </div>
            <h5 class="card-title">Profil Utilisateur</h5>
            <p class="card-text">
              Gérez vos informations personnelles et mettez à jour votre mot de passe.
            </p>
            <router-link to="/profile" class="btn btn-info text-white mt-2">
              <i class="fas fa-arrow-right me-2"></i>Mon profil
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Fonctionnalités avancées pour administrateurs et managers -->
    <div v-if="isAuthenticated && (hasAdminAccess || hasManagerAccess)" class="mb-5">
      <h2 class="mb-4">Fonctionnalités avancées</h2>
      <div class="row">
        <!-- Rapports - visible pour les managers et admins -->
        <div class="col-md-6" v-if="hasManagerAccess">
          <div class="card h-100 dashboard-card">
            <div class="card-body text-center pt-4">
              <div class="card-icon text-warning">
                <i class="fas fa-file-alt"></i>
              </div>
              <h5 class="card-title">Rapports</h5>
              <p class="card-text">
                Générez des rapports personnalisés pour analyser vos données d'inventaire.
              </p>
              <router-link to="/reports" class="btn btn-warning text-white mt-2">
                <i class="fas fa-arrow-right me-2"></i>Créer des rapports
              </router-link>
            </div>
          </div>
        </div>

        <!-- Gestion des Utilisateurs - visible uniquement pour les admins -->
        <div class="col-md-6" v-if="hasAdminAccess">
          <div class="card h-100 dashboard-card">
            <div class="card-body text-center pt-4">
              <div class="card-icon text-danger">
                <i class="fas fa-users"></i>
              </div>
              <h5 class="card-title">Gestion des Utilisateurs</h5>
              <p class="card-text">
                Administrez les accès utilisateurs et gérez les permissions.
              </p>
              <router-link to="/users" class="btn btn-danger mt-2">
                <i class="fas fa-arrow-right me-2"></i>Gérer les utilisateurs
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section pour les utilisateurs non authentifiés -->
    <div v-if="!isAuthenticated">
      <!-- Fonctionnalités principales -->
      <h2 class="text-center mb-4">Pourquoi choisir notre solution ?</h2>
      <div class="row mb-5">
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm dashboard-card">
            <div class="card-body text-center p-4">
              <div class="feature-icon bg-primary bg-opacity-10 text-primary mx-auto mb-4">
                <i class="fas fa-boxes"></i>
              </div>
              <h4>Gestion simplifiée</h4>
              <p class="text-muted">Suivez facilement vos stocks, produits et fournisseurs depuis une interface intuitive.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm dashboard-card">
            <div class="card-body text-center p-4">
              <div class="feature-icon bg-success bg-opacity-10 text-success mx-auto mb-4">
                <i class="fas fa-chart-line"></i>
              </div>
              <h4>Analyses détaillées</h4>
              <p class="text-muted">Générez des rapports précis et visualisez des statistiques pour des décisions éclairées.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm dashboard-card">
            <div class="card-body text-center p-4">
              <div class="feature-icon bg-info bg-opacity-10 text-info mx-auto mb-4">
                <i class="fas fa-bell"></i>
              </div>
              <h4>Alertes intelligentes</h4>
              <p class="text-muted">Recevez des alertes pour les stocks bas et suivez les mouvements en temps réel.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- À propos du système -->
      <div class="card mb-5 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-4">
          <h3 class="mb-0">À propos du système de gestion de stock</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
              <h5 class="fw-bold mb-3"><i class="fas fa-star me-2 text-warning"></i>Fonctionnalités principales</h5>
              <ul class="list-group list-group-flush">
                <li class="list-group-item border-0 ps-0"><i class="fas fa-check-circle text-success me-2"></i>Gestion complète des produits et stocks</li>
                <li class="list-group-item border-0 ps-0"><i class="fas fa-check-circle text-success me-2"></i>Suivi des fournisseurs et des commandes</li>
                <li class="list-group-item border-0 ps-0"><i class="fas fa-check-circle text-success me-2"></i>Tableaux de bord et visualisations</li>
                <li class="list-group-item border-0 ps-0"><i class="fas fa-check-circle text-success me-2"></i>Rapports personnalisables</li>
                <li class="list-group-item border-0 ps-0"><i class="fas fa-check-circle text-success me-2"></i>Gestion des utilisateurs et des rôles</li>
              </ul>
            </div>
            <div class="col-lg-6">
              <h5 class="fw-bold mb-3"><i class="fas fa-users me-2 text-primary"></i>Types d'utilisateurs</h5>
              <div class="d-flex mb-3 align-items-center">
                <span class="badge bg-danger me-3 p-2"><i class="fas fa-user-shield"></i></span>
                <div>
                  <h6 class="mb-1">Administrateur</h6>
                  <p class="text-muted mb-0 small">Accès complet au système, gestion des utilisateurs et configuration.</p>
                </div>
              </div>
              <div class="d-flex mb-3 align-items-center">
                <span class="badge bg-warning text-dark me-3 p-2"><i class="fas fa-user-tie"></i></span>
                <div>
                  <h6 class="mb-1">Gestionnaire</h6>
                  <p class="text-muted mb-0 small">Accès aux statistiques, rapports et gestion des stocks.</p>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge bg-info text-dark me-3 p-2"><i class="fas fa-user"></i></span>
                <div>
                  <h6 class="mb-1">Utilisateur</h6>
                  <p class="text-muted mb-0 small">Consultation des produits et fournisseurs.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Call-to-action final -->
      <div class="text-center py-5">
        <h2 class="mb-4">Prêt à optimiser votre gestion de stock ?</h2>
        <div class="d-flex justify-content-center gap-3">
          <router-link to="/login" class="btn btn-primary btn-lg px-4 me-2">
            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
          </router-link>
          <router-link to="/register" class="btn btn-outline-primary btn-lg px-4">
            <i class="fas fa-user-plus me-2"></i>Créer un compte
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data() {
    return {
      isAuthenticated: false,
      user: {},
      stats: {
        products: 0,
        suppliers: 0,
        lowStock: 0,
        users: 0,
        productsChange: 4.2,
        suppliersChange: 0,
        lowStockChange: -12.5,
        usersChange: 16.7
      }
    };
  },
  computed: {
    hasAdminAccess() {
      return this.user && this.user.role === 'admin';
    },
    hasManagerAccess() {
      return this.user && (this.user.role === 'admin' || this.user.role === 'manager');
    }
  },
  mounted() {
    this.checkAuth();
    if (this.isAuthenticated) {
      this.fetchStats();
    }
  },
  methods: {
    checkAuth() {
      const token = localStorage.getItem('token');
      if (token) {
        const userData = localStorage.getItem('user');
        if (userData) {
          this.user = JSON.parse(userData);
          this.isAuthenticated = true;
        }
      }
    },
    fetchStats() {
      // Simuler des données statistiques pour la démo
      // Dans une application réelle, cela serait récupéré via une API
      setTimeout(() => {
        this.stats = {
          products: 128,
          suppliers: 15,
          lowStock: 8,
          users: 6,
          productsChange: 4.2,
          suppliersChange: 0,
          lowStockChange: -12.5,
          usersChange: 16.7
        };
      }, 500);
    },
    showLowStockProducts() {
      // Dans une application réelle, rediriger vers la page des produits avec un filtre
      this.$router.push({
        path: '/products',
        query: { filter: 'low-stock' }
      });
    }
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #1e88e5 0%, #1a237e 100%);
  padding: 80px 0;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.1)' fill-rule='evenodd'/%3E%3C/svg%3E");
  opacity: 0.5;
}

.card-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.dashboard-card {
  transition: transform 0.3s, box-shadow 0.3s;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dashboard-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
}
</style> 