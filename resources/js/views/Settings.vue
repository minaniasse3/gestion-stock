<template>
  <div>
    <div class="header-container">
      <h2 class="mb-4">
        <i class="fas fa-cog me-2"></i>Paramètres
      </h2>
      <p class="text-muted">Personnalisez votre expérience de l'application</p>
    </div>

    <div class="row">
      <div class="col-md-3 mb-4">
        <div class="list-group">
          <a href="#" 
            v-for="(section, index) in sections" 
            :key="index"
            @click.prevent="activeSection = section.id"
            :class="['list-group-item list-group-item-action', { active: activeSection === section.id }]">
            <i :class="['me-2', section.icon]"></i>{{ section.name }}
          </a>
        </div>
      </div>
      
      <div class="col-md-9">
        <!-- Interface utilisateur -->
        <div v-if="activeSection === 'ui'" class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Interface utilisateur</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label d-block">Thème de l'application</label>
              <div class="btn-group" role="group">
                <input type="radio" class="btn-check" id="theme-light" v-model="settings.theme" value="light" autocomplete="off">
                <label class="btn btn-outline-primary" for="theme-light">Clair</label>
                
                <input type="radio" class="btn-check" id="theme-dark" v-model="settings.theme" value="dark" autocomplete="off">
                <label class="btn btn-outline-primary" for="theme-dark">Sombre</label>
                
                <input type="radio" class="btn-check" id="theme-system" v-model="settings.theme" value="system" autocomplete="off">
                <label class="btn btn-outline-primary" for="theme-system">Système</label>
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Taille de la police</label>
              <select class="form-select" v-model="settings.fontSize">
                <option value="sm">Petite</option>
                <option value="md">Moyenne</option>
                <option value="lg">Grande</option>
              </select>
            </div>
            
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" id="compact-mode" v-model="settings.compactMode">
              <label class="form-check-label" for="compact-mode">Mode compact</label>
            </div>
          </div>
        </div>
        
        <!-- Notifications -->
        <div v-if="activeSection === 'notifications'" class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Notifications</h5>
          </div>
          <div class="card-body">
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" id="email-notifications" v-model="settings.emailNotifications">
              <label class="form-check-label" for="email-notifications">Recevoir des notifications par email</label>
            </div>
            
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" id="stock-alerts" v-model="settings.stockAlerts">
              <label class="form-check-label" for="stock-alerts">Alertes de stock faible</label>
            </div>
            
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" id="activity-alerts" v-model="settings.activityAlerts">
              <label class="form-check-label" for="activity-alerts">Alertes d'activité</label>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Fréquence des résumés</label>
              <select class="form-select" v-model="settings.digestFrequency">
                <option value="daily">Quotidien</option>
                <option value="weekly">Hebdomadaire</option>
                <option value="monthly">Mensuel</option>
                <option value="never">Jamais</option>
              </select>
            </div>
          </div>
        </div>
        
        <!-- Confidentialité -->
        <div v-if="activeSection === 'privacy'" class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Confidentialité et sécurité</h5>
          </div>
          <div class="card-body">
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" id="two-factor-auth" v-model="settings.twoFactorAuth">
              <label class="form-check-label" for="two-factor-auth">Authentification à deux facteurs</label>
              <div v-if="settings.twoFactorAuth" class="alert alert-info mt-2">
                <i class="fas fa-info-circle me-2"></i>Cette fonctionnalité sera bientôt disponible.
              </div>
            </div>
            
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" id="activity-logging" v-model="settings.activityLogging">
              <label class="form-check-label" for="activity-logging">Journalisation de l'activité</label>
            </div>
            
            <div class="card bg-light mb-3">
              <div class="card-body">
                <h6 class="card-title">Sessions actives</h6>
                <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                  <div>
                    <i class="fas fa-desktop me-2"></i>
                    <strong>Windows PC</strong> - Chrome
                    <div class="text-muted small">Cet appareil • {{ new Date().toLocaleDateString() }}</div>
                  </div>
                  <div class="ms-auto">
                    <span class="badge bg-success me-2">Actif</span>
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <div>
                    <i class="fas fa-mobile-alt me-2"></i>
                    <strong>iPhone</strong> - Safari
                    <div class="text-muted small">Paris, France • {{ new Date(Date.now() - 3*24*60*60*1000).toLocaleDateString() }}</div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-sign-out-alt me-1"></i>Déconnecter
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="alert alert-danger">
              <h6><i class="fas fa-exclamation-triangle me-2"></i>Zone de danger</h6>
              <p class="mb-2">Ces actions sont irréversibles. Soyez prudent.</p>
              <button class="btn btn-danger me-2" @click="confirmDeleteData">
                <i class="fas fa-trash-alt me-1"></i>Supprimer mes données
              </button>
              <button class="btn btn-outline-danger" @click="confirmDeleteAccount">
                <i class="fas fa-user-times me-1"></i>Supprimer mon compte
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Settings',
  data() {
    return {
      activeSection: 'ui',
      sections: [
        { id: 'ui', name: 'Interface utilisateur', icon: 'fas fa-palette' },
        { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell' },
        { id: 'privacy', name: 'Confidentialité', icon: 'fas fa-shield-alt' }
      ],
      settings: {
        // UI settings
        theme: 'light',
        fontSize: 'md',
        compactMode: false,
        
        // Notification settings
        emailNotifications: true,
        stockAlerts: true,
        activityAlerts: true,
        digestFrequency: 'weekly',
        
        // Privacy settings
        twoFactorAuth: false,
        activityLogging: true
      }
    };
  },
  mounted() {
    this.loadSettings();
  },
  methods: {
    loadSettings() {
      // Dans une application réelle, nous ferions un appel API
      // const response = await axios.get('/api/settings');
      // this.settings = response.data;
      
      // Pour l'exemple, nous récupérons les paramètres du localStorage ou utilisons les valeurs par défaut
      const savedSettings = localStorage.getItem('userSettings');
      if (savedSettings) {
        this.settings = JSON.parse(savedSettings);
      }
    },
    async saveSettings() {
      try {
        // Dans une application réelle, nous ferions un appel API
        // await axios.put('/api/settings', this.settings);
        
        // Pour l'exemple, nous sauvegardons les paramètres dans le localStorage
        localStorage.setItem('userSettings', JSON.stringify(this.settings));
        
        this.$emit('showAlert', 'Paramètres enregistrés avec succès!', 'success');
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement des paramètres:', error);
        this.$emit('showAlert', 'Erreur lors de l\'enregistrement des paramètres', 'danger');
      }
    },
    confirmDeleteData() {
      if (confirm('Êtes-vous sûr de vouloir supprimer toutes vos données ? Cette action est irréversible.')) {
        // Dans une application réelle, nous ferions un appel API
        // await axios.delete('/api/user/data');
        
        this.$emit('showAlert', 'Vos données ont été supprimées avec succès', 'success');
      }
    },
    confirmDeleteAccount() {
      if (confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
        // Dans une application réelle, nous ferions un appel API
        // await axios.delete('/api/user/account');
        
        // Déconnexion
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        localStorage.removeItem('userSettings');
        
        this.$emit('showAlert', 'Votre compte a été supprimé avec succès', 'success');
        this.$router.push('/login');
      }
    }
  },
  watch: {
    settings: {
      handler() {
        this.saveSettings();
      },
      deep: true
    }
  }
};
</script>

<style scoped>
.list-group-item.active {
  background-color: #007bff;
  border-color: #007bff;
}
</style> 