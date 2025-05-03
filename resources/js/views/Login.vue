<template>
  <div class="login-container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Connexion</h4>
          </div>
          <div class="card-body">
            <div v-if="error" class="alert alert-danger">
              {{ error }}
            </div>
            <form @submit.prevent="login">
              <!-- Champ caché pour le token CSRF -->
              <input type="hidden" name="_token" :value="csrfToken">
              
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
              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
                </button>
              </div>
            </form>
            <div class="text-center mt-3">
              <p>Vous n'avez pas de compte ? <router-link to="/register">Inscrivez-vous</router-link></p>
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
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      error: null,
      loading: false,
      csrfToken: document.head.querySelector('meta[name="csrf-token"]')?.content || ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.error = null;
      
      try {
        // Créer une instance axios sans baseURL pour appeler sanctum/csrf-cookie
        const axiosWithoutBase = axios.create({
          baseURL: '', // Pas de préfixe
          withCredentials: true
        });
        
        // Récupérer le cookie CSRF avant de s'authentifier
        await axiosWithoutBase.get('/sanctum/csrf-cookie');
        
        // Utiliser l'API réelle pour l'authentification
        const response = await axios.post('/auth/login', {
          ...this.form,
          _token: this.csrfToken  // Ajouter le token CSRF à la requête
        });
        
        const { user, token } = response.data;
        
        // Stocker les données d'authentification
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));
        
        // Configurer Axios avec le token
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        // Émettre un événement pour mettre à jour l'état d'authentification
        this.$emit('showAlert', 'Connexion réussie!', 'success');
        
        // IMPORTANT: Mise à jour de l'état d'authentification dans App.vue avant la navigation
        if (this.$root && typeof this.$root.checkAuth === 'function') {
          await this.$root.checkAuth();
        }
        
        // Après avoir mis à jour l'état, naviguer vers le dashboard
        this.$router.push('/dashboard');
      } catch (error) {
        console.error('Erreur de connexion:', error);
        
        if (error.response && error.response.status === 422) {
          // Erreur de validation
          this.error = error.response.data.message || 'Email ou mot de passe incorrect.';
        } else if (error.response && error.response.status === 419) {
          // Erreur CSRF
          this.error = 'Session expirée. Veuillez rafraîchir la page et réessayer.';
          // Essayer de récupérer un nouveau token CSRF
          this.csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
        } else if (error.response && error.response.status === 404) {
          // Erreur 404 - route non trouvée
          this.error = 'Service d\'authentification non disponible. Veuillez contacter l\'administrateur.';
        } else if (error.response && error.response.data.message) {
          this.error = error.response.data.message;
        } else {
          this.error = 'Une erreur est survenue lors de la connexion.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.login-container {
  margin-top: 2rem;
}
</style> 