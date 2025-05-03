<template>
  <div>
    <div class="header-container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1><i class="fas fa-users me-2"></i>Gestion des utilisateurs</h1>
          <p class="text-muted">Administration des comptes utilisateurs et des permissions</p>
        </div>
        <div class="col-md-6 text-md-end">
          <button type="button" class="btn btn-primary" @click="showAddUserModal">
            <i class="fas fa-user-plus me-2"></i>Nouvel utilisateur
          </button>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Rechercher un utilisateur..." v-model="searchQuery" @input="applyFilters">
              <button class="btn btn-outline-secondary" type="button" @click="searchQuery = ''; applyFilters()" v-if="searchQuery">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="col-md-6 d-flex justify-content-md-end mt-2 mt-md-0">
            <select class="form-select" style="width: auto;" v-model="roleFilter" @change="applyFilters">
              <option value="">Tous les rôles</option>
              <option value="admin">Administrateurs</option>
              <option value="manager">Gestionnaires</option>
              <option value="user">Utilisateurs</option>
            </select>
            <select class="form-select ms-2" style="width: auto;" v-model="statusFilter" @change="applyFilters">
              <option value="">Tous les statuts</option>
              <option value="active">Actifs</option>
              <option value="inactive">Inactifs</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center my-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des utilisateurs...</p>
        </div>
        
        <div v-else-if="filteredUsers.length === 0" class="text-center my-5">
          <i class="fas fa-users-slash text-muted" style="font-size: 4rem;"></i>
          <p class="lead mt-3">Aucun utilisateur trouvé</p>
          <p v-if="searchQuery || roleFilter || statusFilter">
            Essayez de modifier vos filtres de recherche.
          </p>
        </div>
        
        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 70px;">#</th>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Rôle</th>
                  <th>Statut</th>
                  <th>Dernière connexion</th>
                  <th style="width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in filteredUsers" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span :class="getRoleBadgeClass(user.role)">
                      {{ getRoleName(user.role) }}
                    </span>
                  </td>
                  <td>
                    <span :class="getStatusBadgeClass(user.active)">
                      {{ user.active ? 'Actif' : 'Inactif' }}
                    </span>
                  </td>
                  <td>{{ user.last_login || 'Jamais' }}</td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-info text-white" @click="viewUser(user)" title="Voir">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-warning text-white" @click="editUser(user)" title="Modifier">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-sm" :class="user.active ? 'btn-danger' : 'btn-success'" @click="toggleUserStatus(user)" :title="user.active ? 'Désactiver' : 'Activer'">
                        <i :class="user.active ? 'fas fa-ban' : 'fas fa-check'"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="d-flex justify-content-end mt-3">
            <nav>
              <ul class="pagination">
                <li :class="['page-item', { disabled: pagination.current_page === 1 }]">
                  <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                    <i class="fas fa-chevron-left"></i>
                  </a>
                </li>
                <li v-for="page in paginationPages" :key="page" :class="['page-item', { active: pagination.current_page === page }]">
                  <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                </li>
                <li :class="['page-item', { disabled: pagination.current_page === pagination.last_page }]">
                  <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                    <i class="fas fa-chevron-right"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ajout/Édition Utilisateur -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true" ref="userModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="userModalLabel">{{ isEditMode ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="mb-3">
                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" v-model="userForm.name" required>
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" v-model="userForm.email" required>
              </div>
              
              <div class="mb-3">
                <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                <select class="form-select" id="role" v-model="userForm.role" required>
                  <option value="admin">Administrateur</option>
                  <option value="manager">Gestionnaire</option>
                  <option value="user">Utilisateur</option>
                </select>
              </div>
              
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="active" v-model="userForm.active">
                <label class="form-check-label" for="active">
                  Compte actif
                </label>
              </div>
              
              <div v-if="!isEditMode">
                <hr>
                <div class="mb-3">
                  <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" id="password" v-model="userForm.password" :required="!isEditMode">
                </div>
                
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" id="password_confirmation" v-model="userForm.password_confirmation" :required="!isEditMode">
                </div>
              </div>
              
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" :disabled="submitting">
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  {{ submitting ? 'Enregistrement...' : 'Enregistrer' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal View User -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true" ref="viewUserModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewUserModalLabel">Détails de l'utilisateur</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="!selectedUser" class="text-center">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div v-else>
              <div class="text-center mb-4">
                <div class="avatar-circle mx-auto mb-3" style="background-color: #007bff; width: 100px; height: 100px; border-radius: 50%; color: white; display: flex; justify-content: center; align-items: center; font-size: 2.5rem;">
                  {{ getInitials(selectedUser.name) }}
                </div>
                <h4>{{ selectedUser.name }}</h4>
                <span :class="getRoleBadgeClass(selectedUser.role)">
                  {{ getRoleName(selectedUser.role) }}
                </span>
              </div>
              
              <div class="list-group list-group-flush mb-3">
                <div class="list-group-item">
                  <small class="text-muted">Email</small>
                  <p class="mb-0">{{ selectedUser.email }}</p>
                </div>
                <div class="list-group-item">
                  <small class="text-muted">Statut</small>
                  <p class="mb-0">
                    <span :class="getStatusBadgeClass(selectedUser.active)">
                      {{ selectedUser.active ? 'Actif' : 'Inactif' }}
                    </span>
                  </p>
                </div>
                <div class="list-group-item">
                  <small class="text-muted">Dernière connexion</small>
                  <p class="mb-0">{{ selectedUser.last_login || 'Jamais connecté' }}</p>
                </div>
                <div class="list-group-item">
                  <small class="text-muted">Date de création</small>
                  <p class="mb-0">{{ selectedUser.created_at || 'Non disponible' }}</p>
                </div>
              </div>
              
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Fermer
                </button>
                <button type="button" class="btn btn-warning" @click="editUser(selectedUser)">
                  <i class="fas fa-edit me-2"></i>Modifier
                </button>
                <button 
                  v-if="selectedUser.id !== currentUser.id" 
                  type="button" 
                  :class="selectedUser.active ? 'btn btn-danger' : 'btn btn-success'" 
                  @click="toggleUserStatus(selectedUser)">
                  <i :class="selectedUser.active ? 'fas fa-ban me-2' : 'fas fa-check me-2'"></i>
                  {{ selectedUser.active ? 'Désactiver' : 'Activer' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';
import axios from 'axios';

export default {
  name: 'Users',
  data() {
    return {
      users: [],
      filteredUsers: [],
      loading: true,
      searchQuery: '',
      roleFilter: '',
      statusFilter: '',
      pagination: {
        current_page: 1,
        last_page: 1
      },
      isEditMode: false,
      userForm: {
        id: null,
        name: '',
        email: '',
        role: 'user',
        active: true,
        password: '',
        password_confirmation: ''
      },
      submitting: false,
      selectedUser: null,
      userModal: null,
      viewUserModal: null,
      currentUser: {}
    };
  },
  computed: {
    paginationPages() {
      const pages = [];
      const currentPage = this.pagination.current_page;
      const lastPage = this.pagination.last_page;
      
      // Afficher au maximum 5 pages
      let startPage = Math.max(1, currentPage - 2);
      let endPage = Math.min(lastPage, startPage + 4);
      
      // Ajuster si on est près de la fin
      if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
      }
      
      for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  mounted() {
    this.loadUsers();
    this.$nextTick(() => {
      // Initialiser les modals
      this.userModal = new Modal(this.$refs.userModal);
      this.viewUserModal = new Modal(this.$refs.viewUserModal);
    });
    
    // Récupérer l'utilisateur actuel depuis le localStorage
    try {
      const userData = localStorage.getItem('user');
      if (userData) {
        this.currentUser = JSON.parse(userData);
      }
    } catch (error) {
      console.error('Erreur lors du chargement des données utilisateur:', error);
    }
  },
  methods: {
    loadUsers() {
      this.loading = true;
      
      // Simulation de récupération de données
      setTimeout(() => {
        // Données simulées pour la démo
        this.users = [
          {
            id: 1,
            name: 'Admin User',
            email: 'admin@example.com',
            role: 'admin',
            active: true,
            last_login: '2023-05-15 10:30',
            created_at: '2023-01-01'
          },
          {
            id: 2,
            name: 'Manager User',
            email: 'manager@example.com',
            role: 'manager',
            active: true,
            last_login: '2023-05-12 14:45',
            created_at: '2023-01-15'
          },
          {
            id: 3,
            name: 'Regular User',
            email: 'user@example.com',
            role: 'user',
            active: true,
            last_login: '2023-05-10 09:15',
            created_at: '2023-02-01'
          },
          {
            id: 4,
            name: 'Inactive User',
            email: 'inactive@example.com',
            role: 'user',
            active: false,
            last_login: '2023-04-25 16:20',
            created_at: '2023-03-10'
          }
        ];
        
        this.pagination = {
          current_page: 1,
          last_page: 1
        };
        
        this.applyFilters();
        this.loading = false;
      }, 500);
    },
    
    applyFilters() {
      let filtered = [...this.users];
      
      // Filtrer par recherche
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(user => 
          user.name.toLowerCase().includes(query) || 
          user.email.toLowerCase().includes(query)
        );
      }
      
      // Filtrer par rôle
      if (this.roleFilter) {
        filtered = filtered.filter(user => user.role === this.roleFilter);
      }
      
      // Filtrer par statut
      if (this.statusFilter === 'active') {
        filtered = filtered.filter(user => user.active);
      } else if (this.statusFilter === 'inactive') {
        filtered = filtered.filter(user => !user.active);
      }
      
      this.filteredUsers = filtered;
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page;
        this.loadUsers();
      }
    },
    
    getRoleName(role) {
      const roleMap = {
        'admin': 'Administrateur',
        'manager': 'Gestionnaire',
        'user': 'Utilisateur'
      };
      return roleMap[role] || role;
    },
    
    getRoleBadgeClass(role) {
      const classMap = {
        'admin': 'badge bg-danger',
        'manager': 'badge bg-warning text-dark',
        'user': 'badge bg-info text-dark'
      };
      return classMap[role] || 'badge bg-secondary';
    },
    
    getStatusBadgeClass(isActive) {
      return isActive ? 'badge bg-success' : 'badge bg-danger';
    },
    
    getInitials(name) {
      if (!name) return '??';
      
      return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
    },
    
    resetForm() {
      this.userForm = {
        id: null,
        name: '',
        email: '',
        role: 'user',
        active: true,
        password: '',
        password_confirmation: ''
      };
      this.isEditMode = false;
    },
    
    showAddUserModal() {
      this.resetForm();
      this.userModal.show();
    },
    
    viewUser(user) {
      this.selectedUser = { ...user };
      this.viewUserModal.show();
    },
    
    editUser(user) {
      this.isEditMode = true;
      this.userForm = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        active: user.active,
        password: '',
        password_confirmation: ''
      };
      
      // Si la modal de détails est ouverte, la fermer
      if (this.viewUserModal._isShown) {
        this.viewUserModal.hide();
      }
      
      // Afficher la modal d'édition
      this.$nextTick(() => {
        this.userModal.show();
      });
    },
    
    async saveUser() {
      // Valider le formulaire
      if (!this.userForm.name || !this.userForm.email || !this.userForm.role) {
        this.$emit('showAlert', 'Veuillez remplir tous les champs obligatoires', 'danger');
        return;
      }
      
      if (!this.isEditMode && this.userForm.password !== this.userForm.password_confirmation) {
        this.$emit('showAlert', 'Les mots de passe ne correspondent pas', 'danger');
        return;
      }
      
      this.submitting = true;
      
      try {
        // Simuler un délai d'enregistrement
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        if (this.isEditMode) {
          // Mise à jour d'un utilisateur existant
          const userIndex = this.users.findIndex(u => u.id === this.userForm.id);
          if (userIndex !== -1) {
            this.users[userIndex] = {
              ...this.users[userIndex],
              name: this.userForm.name,
              email: this.userForm.email,
              role: this.userForm.role,
              active: this.userForm.active
            };
          }
          
          this.$emit('showAlert', 'Utilisateur mis à jour avec succès', 'success');
        } else {
          // Ajout d'un nouvel utilisateur
          const newUser = {
            id: this.users.length + 1,
            name: this.userForm.name,
            email: this.userForm.email,
            role: this.userForm.role,
            active: this.userForm.active,
            last_login: null,
            created_at: new Date().toISOString().split('T')[0]
          };
          
          this.users.push(newUser);
          this.$emit('showAlert', 'Utilisateur ajouté avec succès', 'success');
        }
        
        // Fermer la modal et réinitialiser le formulaire
        this.userModal.hide();
        this.resetForm();
        
        // Mettre à jour la liste filtrée
        this.applyFilters();
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de l\'utilisateur:', error);
        this.$emit('showAlert', 'Une erreur est survenue lors de l\'enregistrement', 'danger');
      } finally {
        this.submitting = false;
      }
    },
    
    async toggleUserStatus(user) {
      // Empêcher la désactivation de son propre compte
      if (user.id === this.currentUser.id) {
        this.$emit('showAlert', 'Vous ne pouvez pas modifier le statut de votre propre compte', 'warning');
        return;
      }
      
      const newStatus = !user.active;
      const action = user.active ? 'désactiver' : 'activer';
      const confirmMessage = user.active 
        ? `Êtes-vous sûr de vouloir désactiver l'utilisateur "${user.name}" ? Il ne pourra plus se connecter à l'application.` 
        : `Êtes-vous sûr de vouloir activer l'utilisateur "${user.name}" ? Il pourra à nouveau se connecter à l'application.`;
      
      if (!confirm(confirmMessage)) {
        return;
      }
      
      try {
        // Inverser le statut de l'utilisateur
        const userIndex = this.users.findIndex(u => u.id === user.id);
        if (userIndex !== -1) {
          this.users[userIndex].active = newStatus;
          
          // Si l'utilisateur est actuellement sélectionné dans la modal de détails, mettre à jour également
          if (this.selectedUser && this.selectedUser.id === user.id) {
            this.selectedUser.active = newStatus;
          }
          
          this.applyFilters();
          
          const successMessage = newStatus 
            ? `L'utilisateur "${user.name}" a été activé avec succès` 
            : `L'utilisateur "${user.name}" a été désactivé avec succès`;
          
          this.$emit('showAlert', successMessage, 'success');
          
          // Si la modal de détails est ouverte, la fermer après confirmation
          if (this.viewUserModal._isShown) {
            this.viewUserModal.hide();
          }
        }
      } catch (error) {
        console.error(`Erreur lors de la modification du statut de l'utilisateur:`, error);
        this.$emit('showAlert', 'Une erreur est survenue lors de la modification du statut', 'danger');
      }
    }
  }
};
</script>

<style scoped>
.avatar-circle {
  font-weight: bold;
}
</style> 