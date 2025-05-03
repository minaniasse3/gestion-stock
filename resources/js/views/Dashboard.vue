<template>
  <div>
    <div class="header-container">
      <div class="row align-items-center">
        <div class="col">
          <h1><i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord</h1>
          <p class="text-muted">
            Bienvenue, <span class="fw-bold">{{ currentUser.name || 'Utilisateur' }}</span> ! 
            Voici l'aperçu général de votre stock et activités.
          </p>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-primary text-white">
              <i class="fas fa-box"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Total Produits</h6>
              <h3 class="mb-0">{{ stats.totalProducts }}</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-success text-white">
              <i class="fas fa-truck"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Fournisseurs</h6>
              <h3 class="mb-0">{{ stats.totalSuppliers }}</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-warning text-white">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Stock Faible</h6>
              <h3 class="mb-0">{{ stats.lowStock }}</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-info text-white">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Utilisateurs</h6>
              <h3 class="mb-0">{{ stats.totalUsers }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8 mb-4">
        <div class="table-container">
          <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Activités récentes</h5>
          <div v-if="isLoading" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Chargement...</span>
            </div>
          </div>
          <div v-else-if="recentActivities.length === 0" class="text-center p-4">
            <i class="fas fa-history text-muted mb-2" style="font-size: 2rem;"></i>
            <p>Aucune activité récente à afficher</p>
          </div>
          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Action</th>
                  <th>Élément</th>
                  <th>Utilisateur</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(activity, index) in recentActivities" :key="index">
                  <td>
                    <span :class="getActivityBadgeClass(activity.type)">
                      <i :class="getActivityIcon(activity.type)"></i>
                      {{ activity.action }}
                    </span>
                  </td>
                  <td>{{ activity.item }}</td>
                  <td>{{ activity.user }}</td>
                  <td>{{ activity.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="table-container">
          <h5 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Stock critique</h5>
          <div v-if="isLoading" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Chargement...</span>
            </div>
          </div>
          <div v-else-if="criticalStock.length > 0">
            <div v-for="(item, index) in criticalStock" :key="index" class="d-flex align-items-center mb-3 p-2 border-bottom">
              <div class="me-3">
                <div class="placeholder-image bg-light rounded d-flex align-items-center justify-content-center" style="height: 40px; width: 40px; font-size: 1.2rem;">
                  <i class="fas fa-box text-secondary"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ item.name }}</h6>
                <small :class="item.quantity <= 0 ? 'text-danger' : 'text-warning'">
                  <i class="fas fa-exclamation-circle me-1"></i>
                  Quantité: {{ item.quantity }} {{ item.quantity <= 0 ? '(Rupture)' : '' }}
                </small>
              </div>
              <router-link :to="'/products/' + item.id + '/edit'" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus-circle"></i>
              </router-link>
            </div>
            <div class="text-center mt-3">
              <router-link to="/products" class="btn btn-sm btn-primary">
                <i class="fas fa-boxes me-1"></i> Voir tous les produits
              </router-link>
            </div>
          </div>
          <div v-else class="text-center p-4">
            <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
            <p>Aucun produit en stock critique</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      stats: {
        totalProducts: 0,
        totalSuppliers: 0,
        lowStock: 0,
        totalUsers: 0
      },
      recentActivities: [],
      criticalStock: [],
      isLoading: true,
      currentUser: {}
    }
  },
  created() {
    this.getCurrentUser();
    this.fetchDashboardData();
  },
  methods: {
    getCurrentUser() {
      try {
        const userData = localStorage.getItem('user');
        if (userData) {
          this.currentUser = JSON.parse(userData);
        }
      } catch (error) {
        console.error('Erreur lors du chargement des données utilisateur:', error);
      }
    },
    fetchDashboardData() {
      this.isLoading = true;
      
      // Simulation d'un délai d'API
      setTimeout(() => {
        // Données de statistiques avec des valeurs réalistes
        this.stats = {
          totalProducts: 147,
          totalSuppliers: 6,
          lowStock: 8,
          totalUsers: 5
        };
        
        // Activités récentes plus détaillées et réalistes
        this.recentActivities = [
          { type: 'create', action: 'Ajout', item: 'Clavier Mécanique RGB', user: 'Jean Dupont', date: '2025-05-03 09:45' },
          { type: 'update', action: 'Mise à jour', item: 'Station d\'accueil USB-C', user: 'Admin Principal', date: '2025-05-02 16:32' },
          { type: 'stock', action: 'Entrée stock', item: 'Écran Dell UltraSharp 27"', user: 'Jean Dupont', date: '2025-05-02 11:18' },
          { type: 'stock', action: 'Sortie stock', item: 'Souris Logitech MX Master 3', user: 'Marie Lambert', date: '2025-05-01 14:55' },
          { type: 'delete', action: 'Suppression', item: 'Adaptateur HDMI défectueux', user: 'Pierre Martin', date: '2025-04-30 10:23' },
          { type: 'update', action: 'Ajustement', item: 'Inventaire mensuel', user: 'Admin Principal', date: '2025-04-30 09:05' }
        ];
        
        // Produits en stock critique plus détaillés
        this.criticalStock = [
          { id: 16, name: 'Carte graphique NVIDIA RTX 3080', quantity: 2, image: 'gpu_rtx3080.jpg' },
          { id: 17, name: 'Station d\'accueil USB-C', quantity: 1, image: 'dock_usbc.jpg' },
          { id: 8, name: 'Switch réseau Cisco 24 ports', quantity: 2, image: 'switch_cisco.jpg' },
          { id: 9, name: 'Fauteuil ergonomique Herman Miller', quantity: 1, image: 'chair_herman_miller.jpg' },
          { id: 4, name: 'Ordinateur portable ASUS ZenBook', quantity: 0, image: 'laptop_asus_zenbook.jpg' }
        ];
        
        this.isLoading = false;
      }, 800);
    },
    getActivityBadgeClass(type) {
      const classes = {
        create: 'badge bg-success',
        update: 'badge bg-info',
        delete: 'badge bg-danger',
        stock: 'badge bg-warning'
      };
      return classes[type] || 'badge bg-secondary';
    },
    getActivityIcon(type) {
      const icons = {
        create: 'fas fa-plus-circle me-1',
        update: 'fas fa-edit me-1',
        delete: 'fas fa-trash me-1',
        stock: 'fas fa-cubes me-1'
      };
      return icons[type] || 'fas fa-circle me-1';
    }
  }
}
</script>

<style scoped>
/* Add any component-specific styles here */
.placeholder-image {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
}
</style> 