<template>
  <div class="app-container">
    <!-- Navbar mobile top pour les pages protégées -->
    <nav v-if="isProtectedRoute && $route.name !== 'home'" class="navbar navbar-dark bg-primary d-lg-none fixed-top">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/">
          <i class="fas fa-boxes me-2"></i>Gestion de Stock
        </router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </nav>

    <!-- Navbar horizontal pour les pages publiques (login/register/home) -->
    <nav v-if="!isProtectedRoute || $route.name === 'home'" class="navbar navbar-expand-lg navbar-dark bg-primary py-0">
      <div class="container">
        <router-link class="navbar-brand py-3" to="/">
          <i class="fas fa-boxes me-2"></i>Gestion de Stock
        </router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item" v-if="!isAuthenticated">
              <router-link class="nav-link py-3 px-4" to="/login">
                <i class="fas fa-sign-in-alt me-1"></i> Connexion
              </router-link>
            </li>
            <li class="nav-item" v-if="!isAuthenticated">
              <router-link class="nav-link py-3 px-4" to="/register">
                <i class="fas fa-user-plus me-1"></i> Inscription
              </router-link>
            </li>
            <li class="nav-item dropdown" v-if="isAuthenticated">
              <a class="nav-link dropdown-toggle py-3 px-4" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i> {{ user.name || 'Utilisateur' }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <router-link class="dropdown-item py-2" to="/dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                  </router-link>
                </li>
                <li>
                  <router-link class="dropdown-item py-2" to="/profile">
                    <i class="fas fa-user me-2"></i> Mon profil
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item py-2" href="#" @click.prevent="logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="d-flex flex-grow-1">
      <!-- Sidebar vertical uniquement pour les pages protégées -->
      <div v-if="isProtectedRoute && $route.name !== 'home'" id="sidebarMenu" class="sidebar collapse d-lg-block bg-primary text-white">
        <div class="sidebar-header p-3">
          <router-link class="text-decoration-none text-white fs-4 d-flex align-items-center" to="/">
            <i class="fas fa-boxes me-2"></i>
            <span class="sidebar-title">Gestion de Stock</span>
          </router-link>
        </div>
        <hr class="m-0 bg-light opacity-25">
        <div class="sidebar-content">
          <ul class="nav flex-column">
            <li class="nav-item" v-if="isAuthenticated">
              <router-link :to="{ name: 'dashboard' }" class="nav-link text-white py-3" :class="{ active: $route.name === 'dashboard' }">
                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
              </router-link>
            </li>
            <li class="nav-item" v-if="isAuthenticated">
              <router-link :to="{ name: 'products' }" class="nav-link text-white py-3" :class="{ active: $route.path.includes('products') }">
                <i class="fas fa-box me-2"></i>Produits
              </router-link>
            </li>
            <li class="nav-item" v-if="isAuthenticated">
              <router-link :to="{ name: 'suppliers' }" class="nav-link text-white py-3" :class="{ active: $route.path.includes('suppliers') }">
                <i class="fas fa-truck me-2"></i>Fournisseurs
              </router-link>
            </li>
            <li class="nav-item" v-if="isAuthenticated && hasManagerAccess">
              <router-link :to="{ name: 'statistics' }" class="nav-link text-white py-3" :class="{ active: $route.path.includes('statistics') }">
                <i class="fas fa-chart-line me-2"></i>Statistiques
              </router-link>
            </li>
            <li class="nav-item" v-if="isAuthenticated && hasManagerAccess">
              <router-link :to="{ name: 'reports' }" class="nav-link text-white py-3" :class="{ active: $route.path.includes('reports') }">
                <i class="fas fa-file-alt me-2"></i>Rapports
              </router-link>
            </li>
            <li class="nav-item" v-if="isAuthenticated && hasAdminAccess">
              <router-link :to="{ name: 'users' }" class="nav-link text-white py-3" :class="{ active: $route.path.includes('users') }">
                <i class="fas fa-users me-2"></i>Utilisateurs
              </router-link>
            </li>
            <hr class="my-2 bg-light opacity-25" v-if="isAuthenticated">
            <!-- User account links -->
            <li v-if="isAuthenticated" class="nav-item">
              <router-link to="/profile" class="nav-link text-white py-3 d-flex align-items-center" :class="{ active: $route.path.includes('profile') }">
                <i class="fas fa-user-circle me-2"></i>
                <div>
                  <span class="me-1">{{ user.name || 'Utilisateur' }}</span>
                </div>
              </router-link>
            </li>
            <li v-if="isAuthenticated" class="nav-item">
              <a href="#" @click.prevent="logout" class="nav-link text-white py-3">
                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Main content avec largeur adaptée selon la page -->
      <div class="main-content flex-grow-1 bg-light" :class="{ 'has-sidebar': isProtectedRoute && $route.name !== 'home' }">
        <div class="container py-4">
          <div v-if="alert.show" :class="['alert', `alert-${alert.type}`, 'alert-dismissible', 'fade', 'show']" role="alert">
            {{ alert.message }}
            <button type="button" class="btn-close" @click="alert.show = false" aria-label="Close"></button>
          </div>
          <router-view @showAlert="showAlert"></router-view>
        </div>

        <footer class="bg-white py-3 mt-5 border-top">
          <div class="container text-center">
            <p class="mb-0">© {{ new Date().getFullYear() }} Système de Gestion de Stock | Tous droits réservés</p>
          </div>
        </footer>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Dropdown, Collapse } from 'bootstrap';

export default {
  name: 'App',
  data() {
    return {
      isAuthenticated: false,
      user: {},
      alert: {
        show: false,
        type: 'success',
        message: ''
      },
      dropdowns: [],
      sidebar: null
    };
  },
  computed: {
    isProtectedRoute() {
      // Routes protégées qui auront la barre latérale
      return !['login', 'register', 'not-found'].includes(this.$route.name);
    },
    hasAdminAccess() {
      return this.user && this.user.role === 'admin';
    },
    hasManagerAccess() {
      return this.user && (this.user.role === 'admin' || this.user.role === 'manager');
    },
    getRoleName() {
      const roles = {
        'admin': 'Administrateur',
        'manager': 'Gestionnaire',
        'user': 'Utilisateur'
      };
      return roles[this.user.role] || 'Utilisateur';
    },
    getRoleBadgeClass() {
      const classes = {
        'admin': 'bg-danger',
        'manager': 'bg-warning text-dark',
        'user': 'bg-info text-dark'
      };
      return classes[this.user.role] || 'bg-secondary';
    }
  },
  watch: {
    $route(to, from) {
      // Masquer l'alerte lors du changement de route
      this.alert.show = false;
      
      // Scroll vers le haut à chaque changement de route
      window.scrollTo(0, 0);
      
      // Force reinit du collapse pour le sidebar
      this.$nextTick(() => {
        this.initBootstrapComponents();
      });
    },
    isAuthenticated(newVal) {
      if (newVal) {
        // Force refresh des composants Bootstrap quand l'authentification change
        this.$nextTick(() => {
          this.initBootstrapComponents();
        });
      }
    }
  },
  methods: {
    async checkAuth() {
      const token = localStorage.getItem('token');
      console.log('checkAuth appelé, token présent:', !!token);
      
      if (token) {
        try {
          // Configurer Axios pour utiliser le token
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
          
          // Récupérer l'utilisateur depuis le localStorage
          const userData = localStorage.getItem('user');
          console.log('Données utilisateur en localStorage:', userData);
          
          if (userData) {
            try {
              // Ajout d'un try/catch pour gérer les erreurs de parsing JSON
              this.user = JSON.parse(userData);
              console.log('Données utilisateur parsées:', this.user);
              
              // Vérifier si les données utilisateur sont valides
              if (!this.user || !this.user.role) {
                throw new Error('Données utilisateur invalides');
              }
              
              // Vérifier si l'utilisateur est actif
              if (this.user.active === false) {
                this.logout();
                this.showAlert('Votre compte a été désactivé', 'danger');
                return;
              }
              
              this.isAuthenticated = true;
              console.log('Authentification réussie, utilisateur:', this.user.name);
              
              // Réinitialiser les composants Bootstrap après confirmation d'authentification
              this.$nextTick(() => {
                this.initBootstrapComponents();
              });
            } catch (jsonError) {
              console.error('Erreur de parsing des données utilisateur:', jsonError);
              // Nettoyer les données corrompues
              localStorage.removeItem('user');
              throw new Error('Format de données utilisateur invalide');
            }
          } else {
            // Dans une application réelle, nous ferions un appel API
            // const response = await axios.get('/auth/user');
            // this.user = response.data;
            
            // Pour l'exemple, simulons un échec d'authentification
            throw new Error('Données utilisateur introuvables');
          }
        } catch (error) {
          console.error('Erreur d\'authentification:', error);
          this.isAuthenticated = false;
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          axios.defaults.headers.common['Authorization'] = '';
        }
      } else {
        // Aucun token trouvé, réinitialiser l'état
        this.isAuthenticated = false;
        this.user = {};
        axios.defaults.headers.common['Authorization'] = '';
      }
      
      return this.isAuthenticated;
    },
    async logout() {
      try {
        // Dans une application réelle, nous ferions un appel API
        // await axios.post('/api/auth/logout');
        
        // Supprimer le token et réinitialiser l'état
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        axios.defaults.headers.common['Authorization'] = '';
        this.isAuthenticated = false;
        this.user = {};
        
        this.showAlert('Vous êtes déconnecté avec succès', 'success');
        this.$router.push('/'); // Redirection vers la page d'accueil au lieu de la page de connexion
      } catch (error) {
        console.error('Erreur de déconnexion:', error);
        this.showAlert('Erreur lors de la déconnexion', 'danger');
      }
    },
    showAlert(message, type = 'success') {
      this.alert = {
        show: true,
        type,
        message
      };
      setTimeout(() => {
        this.alert.show = false;
      }, 5000);
    },
    initBootstrapComponents() {
      // Vérifier si nous sommes sur une route protégée avant d'initialiser les composants
      if (!this.isProtectedRoute) return;
      
      this.$nextTick(() => {
        // Initialisation correcte des dropdowns
        const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
        if (dropdownElementList.length > 0) {
          this.dropdowns = [...dropdownElementList].map(dropdownToggleEl => {
            return new Dropdown(dropdownToggleEl);
          });
        }
        
        // Initialiser le sidebar collapsible pour mobile
        const sidebarElement = document.getElementById('sidebarMenu');
        if (sidebarElement) {
          this.sidebar = new Collapse(sidebarElement, {
            toggle: false
          });
          
          // En desktop, afficher la barre latérale par défaut
          if (window.innerWidth >= 992 && !sidebarElement.classList.contains('show')) {
            this.sidebar.show();
          }
        }
        
        // Fermer le sidebar en mode mobile après un clic sur un lien
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
          link.addEventListener('click', () => {
            if (window.innerWidth < 992) { // 992px est le breakpoint lg
              if (this.sidebar) {
                this.sidebar.hide();
              }
            }
          });
        });
      });
    }
  },
  mounted() {
    this.checkAuth();
    
    // Écouter les événements de stockage pour mettre à jour l'interface si l'authentification change
    window.addEventListener('storage', (event) => {
      if (event.key === 'token' || event.key === 'user') {
        this.checkAuth();
      }
    });
    
    // Initialiser les composants après montage complet
    this.$nextTick(() => {
      this.initBootstrapComponents();
    });
    
    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', () => {
      if (this.isProtectedRoute && window.innerWidth >= 992) {
        const sidebarElement = document.getElementById('sidebarMenu');
        if (sidebarElement && !sidebarElement.classList.contains('show') && this.sidebar) {
          this.sidebar.show();
        }
      }
    });
  }
};
</script>

<style>
body {
  font-family: 'Figtree', sans-serif;
  background-color: #f8f9fa;
  overflow-x: hidden;
  margin: 0;
  padding: 0;
}

.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Couleur principale - Bleu (utilisation cohérente dans toute l'app) */
.bg-primary, .btn-primary {
  background-color: #1e88e5 !important;
  border-color: #1e88e5 !important;
}

.text-primary {
  color: #1e88e5 !important;
}

/* Navbar styles */
.navbar .nav-link {
  font-weight: 500;
  transition: all 0.3s;
}

.navbar .nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.navbar-brand {
  font-weight: 700;
  font-size: 1.3rem;
}

/* Sidebar styles */
.sidebar {
  width: 280px;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
  transition: all 0.3s;
  overflow-y: auto;
}

.sidebar-title {
  font-weight: bold;
}

.sidebar-content {
  height: calc(100vh - 70px);
  overflow-y: auto;
}

.sidebar .nav-link {
  padding: 0.8rem 1rem;
  color: rgba(255, 255, 255, 0.8);
  border-radius: 0.25rem;
  margin: 0.2rem 0.5rem;
  transition: all 0.3s;
  font-size: 1.05rem;
}

.sidebar .nav-link:hover {
  background-color: rgba(255, 255, 255, 0.2);
  color: #fff;
}

.sidebar .nav-link.active {
  background-color: rgba(255, 255, 255, 0.3);
  color: #fff;
  font-weight: 600;
}

/* Main content styles */
.main-content {
  min-height: 100vh;
  transition: all 0.3s;
  overflow-y: auto;
  width: 100%;
  padding-bottom: 60px; /* Pour éviter que le contenu soit caché par le footer */
}

.main-content.has-sidebar {
  width: calc(100% - 280px);
  margin-left: 280px;
}

/* Footer fixe pour le dashboard */
footer {
  width: 100%;
}

/* Mobile adjustments */
@media (max-width: 991.98px) {
  .sidebar {
    width: 100%;
    position: fixed;
    min-height: auto;
    max-height: calc(100vh - 56px);
    top: 56px;
    left: 0;
    z-index: 1000;
  }
  
  .sidebar-content {
    height: auto;
    max-height: calc(100vh - 126px);
  }
  
  .main-content {
    margin-top: 56px;
    width: 100% !important;
    margin-left: 0 !important;
  }
  
  .navbar .nav-link {
    padding: 0.5rem 1rem;
  }
  
  .dropdown-menu {
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }
}

/* Card styles */
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

/* Common component styles */
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
.table-container {
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  padding: 20px;
  margin-bottom: 20px;
}
.dropdown-menu {
  border-radius: 0.5rem;
  padding: 0.5rem 0;
}
.dropdown-item {
  padding: 0.5rem 1rem;
}
.dropdown-item:hover {
  background-color: #f8f9fa;
}
</style> 