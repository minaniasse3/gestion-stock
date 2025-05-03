<template>
  <div>
    <div class="header-container">
      <h2 class="mb-4">
        <i class="fas fa-file-alt me-2"></i>Rapports
      </h2>
      <p class="text-muted">Générez et consultez les rapports de votre activité</p>
    </div>

    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Générer un rapport</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="generateReport">
              <div class="row mb-3">
                <div class="col-md-4">
                  <label for="reportType" class="form-label">Type de rapport</label>
                  <select id="reportType" class="form-select" v-model="newReport.type" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="inventory">Inventaire</option>
                    <option value="sales">Ventes</option>
                    <option value="purchases">Achats</option>
                    <option value="lowStock">Produits en rupture</option>
                    <option value="suppliers">Fournisseurs</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="startDate" class="form-label">Date de début</label>
                  <input type="date" id="startDate" class="form-control" v-model="newReport.startDate" required>
                </div>
                <div class="col-md-4">
                  <label for="endDate" class="form-label">Date de fin</label>
                  <input type="date" id="endDate" class="form-control" v-model="newReport.endDate" required>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="format" class="form-label">Format</label>
                  <select id="format" class="form-select" v-model="newReport.format" required>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Options</label>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="includeCharts" v-model="newReport.includeCharts">
                    <label class="form-check-label" for="includeCharts">
                      Inclure les graphiques
                    </label>
                  </div>
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-file-download me-1"></i>Générer
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Rapports récents</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Nom du rapport</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Format</th>
                    <th>Créé par</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(report, index) in reports" :key="index">
                    <td>{{ report.name }}</td>
                    <td>{{ getReportTypeName(report.type) }}</td>
                    <td>{{ report.period }}</td>
                    <td>
                      <span class="badge" :class="getFormatBadgeClass(report.format)">
                        {{ report.format.toUpperCase() }}
                      </span>
                    </td>
                    <td>{{ report.createdBy }}</td>
                    <td>{{ report.createdAt }}</td>
                    <td>
                      <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary" @click="downloadReport(report)">
                          <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="deleteReport(report.id)">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Reports',
  data() {
    return {
      newReport: {
        type: '',
        startDate: '',
        endDate: '',
        format: 'pdf',
        includeCharts: true
      },
      reports: []
    };
  },
  mounted() {
    this.fetchReports();
  },
  methods: {
    async fetchReports() {
      try {
        // Dans une application réelle, décommentez cette ligne
        // const response = await axios.get('/api/reports');
        // this.reports = response.data;
        
        // Pour l'exemple, nous utilisons des données statiques
        this.reports = [
          {
            id: 1,
            name: 'Inventaire Avril 2023',
            type: 'inventory',
            period: '01/04/2023 - 30/04/2023',
            format: 'pdf',
            createdBy: 'Admin',
            createdAt: '30/04/2023'
          },
          {
            id: 2,
            name: 'Ventes Q1 2023',
            type: 'sales',
            period: '01/01/2023 - 31/03/2023',
            format: 'excel',
            createdBy: 'Gestionnaire',
            createdAt: '05/04/2023'
          },
          {
            id: 3,
            name: 'Produits en rupture',
            type: 'lowStock',
            period: '15/04/2023',
            format: 'csv',
            createdBy: 'Système',
            createdAt: '15/04/2023'
          },
          {
            id: 4,
            name: 'Achats Mars 2023',
            type: 'purchases',
            period: '01/03/2023 - 31/03/2023',
            format: 'pdf',
            createdBy: 'Admin',
            createdAt: '02/04/2023'
          },
          {
            id: 5,
            name: 'Liste des fournisseurs',
            type: 'suppliers',
            period: '01/01/2023 - 31/03/2023',
            format: 'excel',
            createdBy: 'Admin',
            createdAt: '15/03/2023'
          }
        ];
      } catch (error) {
        console.error('Erreur lors de la récupération des rapports:', error);
        this.$emit('showAlert', 'Erreur lors du chargement des rapports', 'danger');
      }
    },
    async generateReport() {
      try {
        // Dans une application réelle, envoyez les données à l'API
        // const response = await axios.post('/api/reports', this.newReport);
        
        // Simulation d'un temps de téléchargement
        this.$emit('showAlert', 'Génération du rapport en cours...', 'info');
        
        setTimeout(() => {
          this.$emit('showAlert', 'Rapport généré avec succès!', 'success');
          
          // Ajouter le rapport à la liste des rapports récents
          const today = new Date();
          const formattedDate = today.toLocaleDateString('fr-FR');
          
          this.reports.unshift({
            id: this.reports.length + 1,
            name: `${this.getReportTypeName(this.newReport.type)} ${formattedDate}`,
            type: this.newReport.type,
            period: `${this.newReport.startDate} - ${this.newReport.endDate}`,
            format: this.newReport.format,
            createdBy: 'Admin',
            createdAt: formattedDate
          });
          
          // Réinitialiser le formulaire
          this.newReport = {
            type: '',
            startDate: '',
            endDate: '',
            format: 'pdf',
            includeCharts: true
          };
        }, 2000);
      } catch (error) {
        console.error('Erreur lors de la génération du rapport:', error);
        this.$emit('showAlert', 'Erreur lors de la génération du rapport', 'danger');
      }
    },
    downloadReport(report) {
      this.$emit('showAlert', `Téléchargement du rapport '${report.name}' en cours...`, 'info');
      
      // Simuler un téléchargement
      setTimeout(() => {
        this.$emit('showAlert', 'Rapport téléchargé avec succès!', 'success');
      }, 1500);
    },
    deleteReport(reportId) {
      if (confirm('Êtes-vous sûr de vouloir supprimer ce rapport?')) {
        try {
          // Dans une application réelle, envoyez une requête DELETE à l'API
          // await axios.delete(`/api/reports/${reportId}`);
          
          // Supprimer le rapport de la liste
          this.reports = this.reports.filter(report => report.id !== reportId);
          
          this.$emit('showAlert', 'Rapport supprimé avec succès', 'success');
        } catch (error) {
          console.error('Erreur lors de la suppression du rapport:', error);
          this.$emit('showAlert', 'Erreur lors de la suppression du rapport', 'danger');
        }
      }
    },
    getReportTypeName(type) {
      const typeMap = {
        'inventory': 'Inventaire',
        'sales': 'Ventes',
        'purchases': 'Achats',
        'lowStock': 'Produits en rupture',
        'suppliers': 'Fournisseurs'
      };
      
      return typeMap[type] || type;
    },
    getFormatBadgeClass(format) {
      const classMap = {
        'pdf': 'bg-danger',
        'excel': 'bg-success',
        'csv': 'bg-primary'
      };
      
      return classMap[format] || 'bg-secondary';
    }
  }
};
</script>

<style scoped>
.badge {
  padding: 0.5em 0.75em;
}
</style> 