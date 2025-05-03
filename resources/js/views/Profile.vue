<template>
  <div>
    <div class="header-container">
      <div class="row align-items-center">
        <div class="col">
          <h1><i class="fas fa-user-circle me-2"></i>Mon Profil</h1>
          <p class="text-muted">Gérer vos informations personnelles et paramètres de sécurité</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <!-- Informations personnelles -->
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations personnelles</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="updateProfile">
              <div class="mb-3">
                <label for="name" class="form-label">Nom complet</label>
                <input 
                  type="text" 
                  id="name" 
                  class="form-control" 
                  v-model="profileForm.name" 
                  required
                >
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input 
                  type="email" 
                  id="email" 
                  class="form-control" 
                  v-model="profileForm.email" 
                  required
                >
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Téléphone</label>
                <input 
                  type="tel" 
                  id="phone" 
                  class="form-control" 
                  v-model="profileForm.phone"
                >
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <textarea 
                  id="address" 
                  class="form-control" 
                  v-model="profileForm.address"
                  rows="3"
                ></textarea>
              </div>
              <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <input 
                  type="text" 
                  id="role" 
                  class="form-control" 
                  v-model="profileForm.role" 
                  disabled
                >
                <div class="form-text">Le rôle ne peut être modifié que par un administrateur.</div>
              </div>
              <div class="alert alert-danger" v-if="profileErrors">
                <ul class="mb-0">
                  <li v-for="(error, index) in profileErrors" :key="index">{{ error[0] }}</li>
                </ul>
              </div>
              <div class="alert alert-success" v-if="profileSuccess">
                {{ profileSuccess }}
              </div>
              <div class="d-flex justify-content-end">
                <button 
                  type="submit" 
                  class="btn btn-primary" 
                  :disabled="profileLoading"
                >
                  <span v-if="profileLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i v-else class="fas fa-save me-2"></i>
                  Enregistrer les modifications
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Changer le mot de passe -->
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Changer le mot de passe</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="updatePassword">
              <div class="mb-3">
                <label for="current_password" class="form-label">Mot de passe actuel</label>
                <input 
                  type="password" 
                  id="current_password" 
                  class="form-control" 
                  v-model="passwordForm.current_password" 
                  required
                >
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input 
                  type="password" 
                  id="password" 
                  class="form-control" 
                  v-model="passwordForm.password" 
                  required
                >
                <div class="form-text">Le mot de passe doit contenir au moins 8 caractères.</div>
              </div>
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                <input 
                  type="password" 
                  id="password_confirmation" 
                  class="form-control" 
                  v-model="passwordForm.password_confirmation" 
                  required
                >
              </div>
              <div class="alert alert-danger" v-if="passwordErrors">
                <ul class="mb-0">
                  <li v-for="(error, index) in passwordErrors" :key="index">{{ error[0] }}</li>
                </ul>
              </div>
              <div class="alert alert-success" v-if="passwordSuccess">
                {{ passwordSuccess }}
              </div>
              <div class="d-flex justify-content-end">
                <button 
                  type="submit" 
                  class="btn btn-primary" 
                  :disabled="passwordLoading"
                >
                  <span v-if="passwordLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i v-else class="fas fa-lock me-2"></i>
                  Mettre à jour le mot de passe
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Carte d'informations -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="text-center mb-4">
              <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto">
                <span>{{ userInitials }}</span>
              </div>
              <h5 class="mt-3 mb-1">{{ user.name }}</h5>
              <p class="text-muted mb-1">{{ user.email }}</p>
              <span :class="['badge', roleColor]">{{ roleLabel }}</span>
            </div>
            <hr>
            <div class="mb-3">
              <small class="text-muted d-block mb-1">Téléphone</small>
              <p class="mb-0">{{ user.phone || 'Non renseigné' }}</p>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block mb-1">Adresse</small>
              <p class="mb-0">{{ user.address || 'Non renseignée' }}</p>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block mb-1">Membre depuis</small>
              <p class="mb-0">{{ formattedDate }}</p>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block mb-1">Dernière connexion</small>
              <p class="mb-0">Aujourd'hui à 14:30</p>
            </div>
          </div>
        </div>

        <!-- Aide -->
        <div class="card bg-light">
          <div class="card-body">
            <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Aide</h6>
            <p class="card-text small">Vous pouvez mettre à jour vos informations personnelles et votre mot de passe à tout moment.</p>
            <p class="card-text small">Pour des raisons de sécurité, votre mot de passe actuel est requis pour effectuer des modifications.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserProfile',
  data() {
    return {
      user: {
        id: null,
        name: '',
        email: '',
        phone: '',
        address: '',
        role: '',
        created_at: null
      },
      profileForm: {
        name: '',
        email: '',
        phone: '',
        address: '',
        role: ''
      },
      passwordForm: {
        current_password: '',
        password: '',
        password_confirmation: ''
      },
      profileLoading: false,
      passwordLoading: false,
      profileErrors: null,
      passwordErrors: null,
      profileSuccess: null,
      passwordSuccess: null
    };
  },
  computed: {
    userInitials() {
      if (!this.user.name) return '';
      return this.user.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
    },
    roleLabel() {
      const roles = {
        'admin': 'Administrateur',
        'manager': 'Manager',
        'user': 'Utilisateur'
      };
      return roles[this.user.role] || this.user.role;
    },
    roleColor() {
      const colors = {
        'admin': 'bg-danger',
        'manager': 'bg-warning',
        'user': 'bg-info'
      };
      return colors[this.user.role] || 'bg-secondary';
    },
    formattedDate() {
      if (!this.user.created_at) return '';
      const date = new Date(this.user.created_at);
      return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
      });
    }
  },
  mounted() {
    this.fetchUserData();
  },
  methods: {
    async fetchUserData() {
      try {
        this.profileLoading = true;
        const response = await axios.get('/auth/user');
        this.user = response.data;
        
        this.profileForm.name = this.user.name;
        this.profileForm.email = this.user.email;
        this.profileForm.phone = this.user.phone || '';
        this.profileForm.address = this.user.address || '';
        this.profileForm.role = this.user.role;
      } catch (error) {
        console.error('Erreur lors de la récupération des données utilisateur:', error);
        this.$emit('showAlert', 'Erreur lors du chargement des données utilisateur', 'danger');
      } finally {
        this.profileLoading = false;
      }
    },
    
    async updateProfile() {
      this.profileLoading = true;
      this.profileErrors = null;
      this.profileSuccess = null;
      
      try {
        const response = await axios.put('/auth/profile', this.profileForm);
        
        this.user = response.data.user;
        
        this.profileSuccess = 'Vos informations personnelles ont été mises à jour avec succès !';
      } catch (error) {
        console.error('Erreur lors de la mise à jour du profil:', error);
        
        if (error.response && error.response.status === 422) {
          this.profileErrors = error.response.data.errors;
        } else {
          this.$emit('showAlert', 'Erreur lors de la mise à jour du profil', 'danger');
        }
      } finally {
        this.profileLoading = false;
      }
    },
    
    async updatePassword() {
      this.passwordLoading = true;
      this.passwordErrors = null;
      this.passwordSuccess = null;
      
      try {
        await axios.put('/auth/password', this.passwordForm);
        
        this.passwordForm = {
          current_password: '',
          password: '',
          password_confirmation: ''
        };
        
        this.passwordSuccess = 'Votre mot de passe a été mis à jour avec succès !';
      } catch (error) {
        console.error('Erreur lors de la mise à jour du mot de passe:', error);
        
        if (error.response && error.response.status === 422) {
          this.passwordErrors = error.response.data.errors;
        } else {
          this.$emit('showAlert', 'Erreur lors de la mise à jour du mot de passe', 'danger');
        }
      } finally {
        this.passwordLoading = false;
      }
    }
  }
};
</script>

<style scoped>
.user-avatar {
  width: 80px;
  height: 80px;
  font-size: 1.5rem;
  font-weight: bold;
}

.card {
  border-radius: 0.5rem;
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  margin-bottom: 1.5rem;
}

.card-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.form-control:disabled {
  background-color: #f8f9fa;
}
</style> 