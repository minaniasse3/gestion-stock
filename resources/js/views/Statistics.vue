<template>
  <div>
    <div class="header-container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="mb-1">
            <i class="fas fa-chart-line me-2"></i>Statistiques
          </h2>
          <p class="text-muted">Aperçu global de votre inventaire</p>
        </div>
        <div class="d-flex align-items-center">
          <div class="input-group me-3">
            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
            <select class="form-select" v-model="timeRange" @change="updateStatistics">
              <option value="week">Cette semaine</option>
              <option value="month">Ce mois</option>
              <option value="quarter">Ce trimestre</option>
              <option value="year">Cette année</option>
            </select>
          </div>
          <button class="btn btn-primary" @click="refreshData">
            <i class="fas fa-sync-alt me-1"></i> Actualiser
          </button>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="stat-card card h-100 border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-primary text-white">
              <i class="fas fa-box"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">Total des produits</h6>
              <h3 class="card-title mb-0">{{ stats.totalProducts }}</h3>
              <div class="mt-2">
                <span :class="stats.productsTrend > 0 ? 'text-success' : 'text-danger'">
                  <i :class="stats.productsTrend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ Math.abs(stats.productsTrend) }}%
                </span>
                <small class="text-muted ms-1">vs période précédente</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="stat-card card h-100 border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-success text-white">
              <i class="fas fa-truck"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">Fournisseurs</h6>
              <h3 class="card-title mb-0">{{ stats.totalSuppliers }}</h3>
              <div class="mt-2">
                <span :class="stats.suppliersTrend > 0 ? 'text-success' : 'text-danger'">
                  <i :class="stats.suppliersTrend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ Math.abs(stats.suppliersTrend) }}%
                </span>
                <small class="text-muted ms-1">vs période précédente</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="stat-card card h-100 border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-danger text-white">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">Produits en rupture</h6>
              <h3 class="card-title mb-0">{{ stats.lowStockProducts }}</h3>
              <div class="mt-2">
                <span :class="stats.lowStockTrend < 0 ? 'text-success' : 'text-danger'">
                  <i :class="stats.lowStockTrend < 0 ? 'fas fa-arrow-down' : 'fas fa-arrow-up'"></i>
                  {{ Math.abs(stats.lowStockTrend) }}%
                </span>
                <small class="text-muted ms-1">vs période précédente</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="stat-card card h-100 border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-info text-white">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">Ventes</h6>
              <h3 class="card-title mb-0">{{ stats.totalSales }}</h3>
              <div class="mt-2">
                <span :class="stats.salesTrend > 0 ? 'text-success' : 'text-danger'">
                  <i :class="stats.salesTrend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ Math.abs(stats.salesTrend) }}%
                </span>
                <small class="text-muted ms-1">vs période précédente</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8 mb-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Évolution des stocks</h5>
              <div class="btn-group">
                <button type="button" class="btn btn-sm" :class="chartType === 'bar' ? 'btn-primary' : 'btn-outline-primary'" @click="chartType = 'bar'">
                  <i class="fas fa-chart-bar"></i>
                </button>
                <button type="button" class="btn btn-sm" :class="chartType === 'line' ? 'btn-primary' : 'btn-outline-primary'" @click="chartType = 'line'">
                  <i class="fas fa-chart-line"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Chargement des données...</p>
            </div>
            <div v-else class="chart-container" ref="inventoryChartContainer">
              <canvas ref="inventoryChart" height="250"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Répartition par catégorie</h5>
              <select class="form-select form-select-sm" style="width: auto;" v-model="categoryChartFilter" @change="updateCategoryChart">
                <option value="quantity">Quantité</option>
                <option value="value">Valeur (€)</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Chargement des données...</p>
            </div>
            <div v-else class="chart-container" ref="categoryChartContainer">
              <canvas ref="categoryChart" height="250"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Produits les plus vendus</h5>
              <select class="form-select form-select-sm" style="width: auto;" v-model="topProductsLimit" @change="updateTopProducts">
                <option value="5">Top 5</option>
                <option value="10">Top 10</option>
                <option value="15">Top 15</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Chargement des données...</p>
            </div>
            <div v-else class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Produit</th>
                    <th class="text-center">Quantité vendue</th>
                    <th class="text-end">Montant total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(product, index) in topProducts" :key="index">
                    <td>
                      <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">{{ index + 1 }}</span>
                        {{ product.name }}
                      </div>
                    </td>
                    <td class="text-center">{{ product.quantitySold }}</td>
                    <td class="text-end fw-bold">{{ formatCurrency(product.totalAmount) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Activité récente</h5>
              <button class="btn btn-sm btn-outline-primary" @click="exportActivities">
                <i class="fas fa-download me-1"></i> Exporter
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Chargement des données...</p>
            </div>
            <div v-else>
              <ul class="list-group list-group-flush">
                <li v-for="(activity, index) in recentActivities" :key="index" class="list-group-item">
                  <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">
                      <span :class="getActivityBadgeClass(activity.type)" class="badge me-2">
                        <i :class="getActivityIcon(activity.type)" class="me-1"></i>
                        {{ activity.type }}
                      </span>
                    </h6>
                    <small class="text-muted">{{ activity.date }}</small>
                  </div>
                  <p class="mb-1">{{ activity.description }}</p>
                  <small class="text-primary">{{ activity.user }}</small>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Statistics',
  data() {
    return {
      loading: true,
      timeRange: 'month',
      chartType: 'bar',
      categoryChartFilter: 'quantity',
      topProductsLimit: '5',
      stats: {
        totalProducts: 0,
        productsTrend: 0,
        totalSuppliers: 0,
        suppliersTrend: 0,
        lowStockProducts: 0,
        lowStockTrend: 0,
        totalSales: 0,
        salesTrend: 0
      },
      topProducts: [],
      recentActivities: [],
      charts: {
        inventory: null,
        category: null
      },
      chartData: {
        inventory: {
          labels: [],
          datasets: []
        },
        category: {
          labels: [],
          data: []
        }
      }
    };
  },
  mounted() {
    this.fetchStatistics();
  },
  methods: {
    async fetchStatistics() {
      this.loading = true;
      
      try {
        // Simulation d'un délai réseau
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Pour l'exemple, nous utilisons des données statiques mais réalistes
        this.stats = {
          totalProducts: 147,
          productsTrend: 3.7,
          totalSuppliers: 6,
          suppliersTrend: 0,
          lowStockProducts: 5,
          lowStockTrend: 25, // Valeur positive = tendance négative (plus de produits en rupture)
          totalSales: 63,
          salesTrend: 8.5
        };
        
        // Générer des topProducts en fonction de la limite avec des données plus réalistes
        // Prix convertis en Francs CFA (1 EUR ≈ 655.957 XOF)
        const allProducts = [
          { name: 'Ordinateur portable ASUS ZenBook', quantitySold: 7, totalAmount: 5970000 },
          { name: 'Souris Logitech MX Master 3', quantitySold: 12, totalAmount: 785000 },
          { name: 'Écran Dell UltraSharp 27"', quantitySold: 5, totalAmount: 1640000 },
          { name: 'Clavier sans fil Logitech MX Keys', quantitySold: 8, totalAmount: 575000 },
          { name: 'Casque Sony WH-1000XM4', quantitySold: 9, totalAmount: 1765000 },
          { name: 'Switch réseau Cisco 24 ports', quantitySold: 3, totalAmount: 688000 },
          { name: 'Disque SSD Samsung 1 To', quantitySold: 6, totalAmount: 590000 },
          { name: 'Fauteuil ergonomique Herman Miller', quantitySold: 2, totalAmount: 1180000 },
          { name: 'Routeur WiFi 6 ASUS', quantitySold: 5, totalAmount: 655000 },
          { name: 'Tablette Samsung Galaxy Tab S7', quantitySold: 4, totalAmount: 1705000 },
          { name: 'Enceinte Bluetooth JBL Charge 5', quantitySold: 6, totalAmount: 708000 },
          { name: 'Webcam Logitech StreamCam', quantitySold: 7, totalAmount: 780000 },
          { name: 'Station d\'accueil USB-C', quantitySold: 4, totalAmount: 210000 },
          { name: 'Bureau réglable en hauteur', quantitySold: 3, totalAmount: 985000 },
          { name: 'Imprimante laser Brother', quantitySold: 5, totalAmount: 820000 }
        ];
        
        this.topProducts = allProducts.slice(0, parseInt(this.topProductsLimit));
        
        // Activités récentes plus détaillées
        this.recentActivities = [
          { 
            type: 'Vente', 
            description: 'Vente de 2x Ordinateur portable ASUS ZenBook au client Entreprise ABC',
            user: 'Jean Dupont', 
            date: '03/05/2025 - 14:30' 
          },
          { 
            type: 'Ajout', 
            description: 'Ajout de 15 nouveaux produits Souris Logitech MX Master 3',
            user: 'Admin Principal', 
            date: '02/05/2025 - 10:15' 
          },
          { 
            type: 'Alerte', 
            description: 'Stock critique atteint pour "Carte graphique NVIDIA RTX 3080" (2 restants)',
            user: 'Système', 
            date: '01/05/2025 - 08:45' 
          },
          { 
            type: 'Mise à jour', 
            description: 'Ajustement de l\'inventaire après vérification physique',
            user: 'Marie Lambert', 
            date: '30/04/2025 - 16:20' 
          },
          { 
            type: 'Suppression', 
            description: 'Suppression du produit "Adaptateur HDMI défectueux" (obsolète)',
            user: 'Pierre Martin', 
            date: '28/04/2025 - 11:05' 
          }
        ];
        
        // Données pour le graphique d'évolution des stocks
        this.chartData.inventory = {
          labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai'],
          datasets: [
            {
              label: 'Entrées',
              data: [32, 45, 37, 50, 42],
              backgroundColor: 'rgba(54, 162, 235, 0.5)',
              borderColor: 'rgb(54, 162, 235)',
              borderWidth: 2,
              tension: 0.1
            },
            {
              label: 'Sorties',
              data: [28, 35, 40, 42, 63],
              backgroundColor: 'rgba(255, 99, 132, 0.5)',
              borderColor: 'rgb(255, 99, 132)',
              borderWidth: 2,
              tension: 0.1
            }
          ]
        };
      
        // Données pour le graphique de répartition par catégorie
        this.chartData.category = {
          labels: ['Périphériques', 'Ordinateurs', 'Réseau', 'Stockage', 'Mobilier', 'Audio'],
          data: this.categoryChartFilter === 'quantity' 
            ? [35, 22, 18, 30, 11, 31] // Quantités
            : [3000000, 18750000, 3600000, 2950000, 9180000, 3150000] // Valeurs en FCFA
        };
        
        this.renderSimpleCharts();
      } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        // Afficher un message d'erreur à l'utilisateur
      } finally {
        this.loading = false;
      }
    },
    
    renderSimpleCharts() {
      this.$nextTick(() => {
        // Assurer que le DOM est complètement mis à jour avant de manipuler les éléments
        setTimeout(() => {
          // Graphique d'évolution des stocks
          if (this.$refs.inventoryChartContainer) {
            this.renderInventoryChart();
          }
          
          // Graphique de répartition par catégorie
          if (this.$refs.categoryChartContainer) {
            this.renderCategoryChart();
          }
        }, 100);
      });
    },
    
    renderInventoryChart() {
      const container = this.$refs.inventoryChartContainer;
      if (!container) return;
      
      container.innerHTML = '';
      container.style.backgroundColor = '#f8f9fa';
      container.style.border = '1px solid #dee2e6';
      container.style.borderRadius = '4px';
      container.style.padding = '15px';
      
      // Titre
      const title = document.createElement('h6');
      title.textContent = 'Évolution des stocks';
      title.style.textAlign = 'center';
      title.style.marginBottom = '15px';
      container.appendChild(title);
      
      // Barre de légende
      const legend = document.createElement('div');
      legend.style.display = 'flex';
      legend.style.justifyContent = 'center';
      legend.style.marginBottom = '15px';
      
      // Entrées
      const entriesLegend = document.createElement('div');
      entriesLegend.style.display = 'flex';
      entriesLegend.style.alignItems = 'center';
      entriesLegend.style.marginRight = '15px';
      
      const entriesColor = document.createElement('div');
      entriesColor.style.width = '15px';
      entriesColor.style.height = '15px';
      entriesColor.style.backgroundColor = 'rgba(54, 162, 235, 0.7)';
      entriesColor.style.marginRight = '5px';
      
      const entriesText = document.createElement('span');
      entriesText.textContent = 'Entrées';
      
      entriesLegend.appendChild(entriesColor);
      entriesLegend.appendChild(entriesText);
      
      // Sorties
      const outputsLegend = document.createElement('div');
      outputsLegend.style.display = 'flex';
      outputsLegend.style.alignItems = 'center';
      
      const outputsColor = document.createElement('div');
      outputsColor.style.width = '15px';
      outputsColor.style.height = '15px';
      outputsColor.style.backgroundColor = 'rgba(255, 99, 132, 0.7)';
      outputsColor.style.marginRight = '5px';
      
      const outputsText = document.createElement('span');
      outputsText.textContent = 'Sorties';
      
      outputsLegend.appendChild(outputsColor);
      outputsLegend.appendChild(outputsText);
      
      legend.appendChild(entriesLegend);
      legend.appendChild(outputsLegend);
      container.appendChild(legend);
      
      // Graphique simplifié
      const chartDiv = document.createElement('div');
      chartDiv.style.height = '180px';
      chartDiv.style.display = 'flex';
      chartDiv.style.alignItems = 'flex-end';
      chartDiv.style.justifyContent = 'space-around';
      chartDiv.style.padding = '0 10px';
      
      // Données d'exemple
      const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai'];
      const entriesData = [32, 45, 37, 50, 42];
      const outputsData = [28, 35, 40, 42, 63];
      const maxValue = Math.max(...entriesData, ...outputsData);
      
      // Créer des barres pour chaque mois
      months.forEach((month, index) => {
        const monthDiv = document.createElement('div');
        monthDiv.style.display = 'flex';
        monthDiv.style.flexDirection = 'column';
        monthDiv.style.alignItems = 'center';
        monthDiv.style.flex = '1';
        
        const barGroup = document.createElement('div');
        barGroup.style.display = 'flex';
        barGroup.style.gap = '5px';
        
        // Barre d'entrées
        const entryBar = document.createElement('div');
        const entryHeight = (entriesData[index] / maxValue) * 150;
        entryBar.style.height = entryHeight + 'px';
        entryBar.style.width = '20px';
        entryBar.style.backgroundColor = 'rgba(54, 162, 235, 0.7)';
        entryBar.style.borderRadius = '2px 2px 0 0';
        entryBar.title = 'Entrées: ' + entriesData[index];
        
        // Barre de sorties
        const outputBar = document.createElement('div');
        const outputHeight = (outputsData[index] / maxValue) * 150;
        outputBar.style.height = outputHeight + 'px';
        outputBar.style.width = '20px';
        outputBar.style.backgroundColor = 'rgba(255, 99, 132, 0.7)';
        outputBar.style.borderRadius = '2px 2px 0 0';
        outputBar.title = 'Sorties: ' + outputsData[index];
        
        barGroup.appendChild(entryBar);
        barGroup.appendChild(outputBar);
        
        const monthLabel = document.createElement('div');
        monthLabel.textContent = month;
        monthLabel.style.fontSize = '12px';
        monthLabel.style.marginTop = '5px';
        
        monthDiv.appendChild(barGroup);
        monthDiv.appendChild(monthLabel);
        chartDiv.appendChild(monthDiv);
      });
      
      container.appendChild(chartDiv);
    },
    
    renderCategoryChart() {
      const container = this.$refs.categoryChartContainer;
      if (!container) return;
      
      container.innerHTML = '';
      container.style.backgroundColor = '#f8f9fa';
      container.style.border = '1px solid #dee2e6';
      container.style.borderRadius = '4px';
      container.style.padding = '15px';
      
      // Titre
      const title = document.createElement('h6');
      title.textContent = 'Répartition par catégorie';
      title.style.textAlign = 'center';
      title.style.marginBottom = '15px';
      container.appendChild(title);
      
      // Légende
      const legend = document.createElement('div');
      legend.style.display = 'flex';
      legend.style.flexDirection = 'column';
      legend.style.gap = '8px';
      
      const colors = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)'
      ];
      
      const total = this.chartData.category.data.reduce((sum, value) => sum + value, 0);
      
      this.chartData.category.labels.forEach((label, index) => {
        const legendItem = document.createElement('div');
        legendItem.style.display = 'flex';
        legendItem.style.alignItems = 'center';
        
        const colorBox = document.createElement('div');
        colorBox.style.width = '15px';
        colorBox.style.height = '15px';
        colorBox.style.backgroundColor = colors[index];
        colorBox.style.marginRight = '8px';
        
        // Calculer le pourcentage
        const percentage = ((this.chartData.category.data[index] / total) * 100).toFixed(1);
        
        const labelText = document.createElement('span');
        if (this.categoryChartFilter === 'quantity') {
          labelText.textContent = `${label}: ${this.chartData.category.data[index]} unités (${percentage}%)`;
        } else {
          labelText.textContent = `${label}: ${this.formatCurrency(this.chartData.category.data[index])} (${percentage}%)`;
        }
        
        legendItem.appendChild(colorBox);
        legendItem.appendChild(labelText);
        legend.appendChild(legendItem);
      });
      
      container.appendChild(legend);
    },
    
    updateStatistics() {
      this.fetchStatistics();
    },
    
    updateCategoryChart() {
      // Mettre à jour les données du graphique en fonction du filtre
      this.chartData.category.data = this.categoryChartFilter === 'quantity' 
        ? [35, 22, 18, 30, 11, 31] // Quantités
        : [3000000, 18750000, 3600000, 2950000, 9180000, 3150000]; // Valeurs en FCFA
      
      this.renderCategoryChart();
    },
    
    updateTopProducts() {
      // Mettre à jour le nombre de produits affichés
      const allProducts = [
        { name: 'Ordinateur portable ASUS ZenBook', quantitySold: 7, totalAmount: 5970000 },
        { name: 'Souris Logitech MX Master 3', quantitySold: 12, totalAmount: 785000 },
        { name: 'Écran Dell UltraSharp 27"', quantitySold: 5, totalAmount: 1640000 },
        { name: 'Clavier sans fil Logitech MX Keys', quantitySold: 8, totalAmount: 575000 },
        { name: 'Casque Sony WH-1000XM4', quantitySold: 9, totalAmount: 1765000 },
        { name: 'Switch réseau Cisco 24 ports', quantitySold: 3, totalAmount: 688000 },
        { name: 'Disque SSD Samsung 1 To', quantitySold: 6, totalAmount: 590000 },
        { name: 'Fauteuil ergonomique Herman Miller', quantitySold: 2, totalAmount: 1180000 },
        { name: 'Routeur WiFi 6 ASUS', quantitySold: 5, totalAmount: 655000 },
        { name: 'Tablette Samsung Galaxy Tab S7', quantitySold: 4, totalAmount: 1705000 },
        { name: 'Enceinte Bluetooth JBL Charge 5', quantitySold: 6, totalAmount: 708000 },
        { name: 'Webcam Logitech StreamCam', quantitySold: 7, totalAmount: 780000 },
        { name: 'Station d\'accueil USB-C', quantitySold: 4, totalAmount: 210000 },
        { name: 'Bureau réglable en hauteur', quantitySold: 3, totalAmount: 985000 },
        { name: 'Imprimante laser Brother', quantitySold: 5, totalAmount: 820000 }
      ];
      
      this.topProducts = allProducts.slice(0, parseInt(this.topProductsLimit));
    },
    
    refreshData() {
      this.fetchStatistics();
    },
    
    exportActivities() {
      // Simulation d'export
      this.$emit('showAlert', 'Export des activités en cours...', 'info');
      setTimeout(() => {
        this.$emit('showAlert', 'Les activités ont été exportées avec succès', 'success');
      }, 1500);
    },
    
    formatCurrency(value) {
      return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(value);
    },
    
    getActivityBadgeClass(type) {
      const classes = {
        'Ajout': 'bg-success',
        'Mise à jour': 'bg-info',
        'Vente': 'bg-primary',
        'Alerte': 'bg-danger',
        'Suppression': 'bg-dark'
      };
      return classes[type] || 'bg-secondary';
    },
    
    getActivityIcon(type) {
      const icons = {
        'Ajout': 'fas fa-plus-circle',
        'Mise à jour': 'fas fa-edit',
        'Vente': 'fas fa-shopping-cart',
        'Alerte': 'fas fa-exclamation-triangle',
        'Suppression': 'fas fa-trash'
      };
      return icons[type] || 'fas fa-circle';
    }
  }
};
</script>

<style scoped>
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

.chart-container {
  position: relative;
  height: 250px;
}

.chart-placeholder {
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  background-color: #f9f9f9;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  font-style: italic;
  color: #999;
}

.card {
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
}

.stat-card:nth-child(1):hover {
  border-left: 4px solid #1e88e5;
}

.stat-card:nth-child(2):hover {
  border-left: 4px solid #28a745;
}

.stat-card:nth-child(3):hover {
  border-left: 4px solid #dc3545;
}

.stat-card:nth-child(4):hover {
  border-left: 4px solid #17a2b8;
}

.chart-error-message {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(255, 255, 255, 0.9);
  padding: 10px 20px;
  border-radius: 5px;
  border: 1px solid #f0ad4e;
  font-size: 14px;
  color: #856404;
}

tr:hover {
  background-color: rgba(0, 123, 255, 0.03);
}
</style> 