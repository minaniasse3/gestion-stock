<template>
  <div class="register-container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Inscription</h4>
          </div>
          <div class="card-body">
            <div v-if="error" class="alert alert-danger">
              {{ error }}
            </div>
            <div v-if="validationErrors.length > 0" class="alert alert-danger">
              <ul class="mb-0">
                <li v-for="(errorMsg, index) in validationErrors" :key="index">{{ errorMsg }}</li>
              </ul>
            </div>
            <form @submit.prevent="register">
              <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="name" 
                  v-model="form.name" 
                  required
                  placeholder="Votre nom"
                >
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  v-model="form.email" 
                  required
                  placeholder="Votre adresse email"
                >
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  v-model="form.password" 
                  required
                  placeholder="Votre mot de passe"
                >
              </div>
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password_confirmation" 
                  v-model="form.password_confirmation" 
                  required
                  placeholder="Confirmer votre mot de passe"
                >
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  {{ loading ? 'Inscription en cours...' : 'S\'inscrire' }}
                </button>
              </div>
            </form>
            <div class="text-center mt-3">
              <p>Vous avez déjà un compte ? <router-link to="/login">Connectez-vous</router-link></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      error: null,
      validationErrors: [],
      loading: false
    };
  },
  methods: {
    async register() {
      this.loading = true;
      this.error = null;
      this.validationErrors = [];
      
      console.log('Envoi des données:', this.form); // Afficher les données envoyées
      
      try {
        const response = await axios.post('/auth/register', this.form);
        console.log('Réponse du serveur:', response.data); // Afficher la réponse
        
        if (response.data && response.data.token) {
          localStorage.setItem('token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          
          // Configurer Axios avec le token
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
          
          // Émettre un événement pour mettre à jour l'état d'authentification
          this.$emit('showAlert', 'Inscription réussie!', 'success');
          
          // Rediriger vers la page des produits
          this.$router.push('/dashboard');
        } else {
          // Si la réponse ne contient pas de token
          throw new Error('Réponse d\'inscription invalide');
        }
      } catch (error) {
        console.error('Erreur d\'inscription:', error);
        console.log('Détails de l\'erreur:', error.response ? error.response.data : 'Pas de détails');
        
        if (error.response && error.response.data) {
          if (error.response.data.message) {
            this.error = error.response.data.message;
          }
          
          if (error.response.data.errors) {
            // Gérer les erreurs de validation
            const errorsObj = error.response.data.errors;
            for (const field in errorsObj) {
              if (Array.isArray(errorsObj[field])) {
                errorsObj[field].forEach(message => {
                  this.validationErrors.push(message);
                });
              } else {
                this.validationErrors.push(errorsObj[field]);
              }
            }
            
            if (this.validationErrors.length === 0) {
              this.error = 'Validation échouée. Veuillez vérifier vos informations.';
            }
          }
        } else {
          this.error = 'Une erreur est survenue lors de l\'inscription.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.register-container {
  margin-top: 2rem;
}
</style> 