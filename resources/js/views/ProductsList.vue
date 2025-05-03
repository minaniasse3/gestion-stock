<template>
  <div class="products-container">
    <div class="header-container d-flex justify-content-between align-items-center">
      <h2><i class="fas fa-box me-2"></i>Gestion des produits</h2>
      <button 
        class="btn btn-primary" 
        @click="showAddProductModal"
      >
        <i class="fas fa-plus-circle me-2"></i>Ajouter un produit
      </button>
    </div>

    <!-- Recherche et filtres -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
              <input 
                type="text" 
                class="form-control" 
                placeholder="Rechercher un produit..." 
                v-model="searchQuery"
                @input="searchProducts"
              >
              <button 
                class="btn btn-outline-secondary" 
                type="button"
                @click="clearSearch"
                v-if="searchQuery"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-md-end mt-3 mt-md-0">
              <select class="form-select me-2" style="width: auto;" v-model="categoryFilter">
                <option value="">Toutes catégories</option>
                <option value="Informatique">Informatique</option>
                <option value="Téléphonie">Téléphonie</option>
                <option value="Accessoires">Accessoires</option>
              </select>
              <select class="form-select" style="width: auto;" v-model="stockFilter">
                <option value="all">Tous les stocks</option>
                <option value="low">Stock bas</option>
                <option value="out">Rupture</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des produits -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center my-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des produits...</p>
        </div>

        <div v-else-if="filteredProducts.length === 0" class="text-center my-5">
          <i class="fas fa-box-open display-1 text-muted"></i>
          <p class="lead mt-3">Aucun produit trouvé</p>
          <p v-if="searchQuery || categoryFilter || stockFilter !== 'all'">
            Essayez avec d'autres critères de recherche ou 
            <button class="btn btn-link p-0" @click="resetFilters">réinitialisez les filtres</button>.
          </p>
          <p v-else>Commencez par ajouter votre premier produit.</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th>Produit</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Catégorie</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in filteredProducts" :key="product.id">
                <td>
                  <div class="d-flex align-items-center">
                    <div class="product-image me-3">
                      <img 
                        v-if="product.image" 
                        :src="'/storage/' + product.image" 
                        class="img-thumbnail" 
                        alt="Product image"
                        width="50"
                      >
                      <div v-else class="no-image">
                        <i class="fas fa-image text-muted"></i>
                      </div>
                    </div>
                    <div>
                      <div class="fw-bold">{{ product.name }}</div>
                      <small class="text-muted">{{ truncateText(product.description, 50) }}</small>
                    </div>
                  </div>
                </td>
                <td>{{ formatPrice(product.price) }}</td>
                <td>
                  <span 
                    :class="{
                      'badge bg-danger': product.quantity === 0,
                      'badge bg-warning text-dark': product.quantity > 0 && product.quantity <= 5,
                      'badge bg-success': product.quantity > 5
                    }"
                  >
                    {{ product.quantity === 0 ? 'Rupture' : product.quantity }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-info text-dark">{{ product.category }}</span>
                </td>
                <td>
                  <div class="btn-group">
                    <button 
                      class="btn btn-sm btn-outline-primary" 
                      @click="showEditProductModal(product)"
                      title="Modifier"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button 
                      class="btn btn-sm btn-outline-danger" 
                      @click="confirmDeleteProduct(product)"
                      title="Supprimer"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <nav v-if="pagination.last_page > 1" aria-label="Page navigation" class="mt-4">
          <ul class="pagination justify-content-center">
            <li :class="['page-item', { disabled: pagination.current_page === 1 }]">
              <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                <i class="fas fa-chevron-left"></i>
              </a>
            </li>
            
            <li 
              v-for="page in paginationPages" 
              :key="page" 
              :class="['page-item', { active: pagination.current_page === page }]"
            >
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

    <!-- Modals seront ajoutés dans une mise à jour ultérieure -->
  </div>
  
  <!-- Ajout du composant ProductModal -->
  <product-modal
    ref="productModal"
    :suppliers="suppliers"
    @product-added="handleProductAdded"
    @product-updated="handleProductUpdated"
    @product-deleted="handleProductDeleted"
    @error="handleError"
  />
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';
import ProductModal from '../components/ProductModal.vue';

export default {
  name: 'ProductsList',
  components: {
    ProductModal
  },
  data() {
    return {
      loading: true,
      products: [],
      filteredProducts: [],
      searchQuery: '',
      categoryFilter: '',
      stockFilter: 'all',
      searchTimeout: null,
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
      },
      suppliers: [] // Liste des fournisseurs pour le modal
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
    this.fetchProducts();
    this.fetchSuppliers();
  },
  methods: {
    async fetchProducts(page = 1) {
      this.loading = true;
      
      try {
        // Dans une application réelle, nous ferions un appel API
        // Simuler un délai réseau
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Données simulées pour la démo
        const mockProducts = [
          {
            id: 1,
            name: 'Ordinateur portable',
            description: 'Ordinateur portable haute performance avec processeur i7 et 16GB de RAM',
            price: 899.99,
            quantity: 15,
            category: 'Informatique',
            image: null,
            supplier_id: 1,
            sku: 'ORD-001'
          },
          {
            id: 2,
            name: 'Smartphone Android',
            description: 'Smartphone dernière génération avec écran AMOLED 6.5 pouces',
            price: 649.99,
            quantity: 25,
            category: 'Téléphonie',
            image: null,
            supplier_id: 2,
            sku: 'TEL-001'
          },
          {
            id: 3,
            name: 'Écran 27"',
            description: 'Écran ultra HD 4K avec taux de rafraîchissement 144Hz',
            price: 349.99,
            quantity: 8,
            category: 'Informatique',
            image: null,
            supplier_id: 3,
            sku: 'ECR-001'
          },
          {
            id: 4,
            name: 'Casque audio sans fil',
            description: 'Casque Bluetooth avec réduction de bruit active',
            price: 199.99,
            quantity: 30,
            category: 'Accessoires',
            image: null,
            supplier_id: 2,
            sku: 'ACS-001'
          },
          {
            id: 5,
            name: 'Souris sans fil',
            description: 'Souris ergonomique sans fil avec capteur de précision',
            price: 49.99,
            quantity: 0,
            category: 'Accessoires',
            image: null,
            supplier_id: 3,
            sku: 'ACS-002'
          },
          {
            id: 6,
            name: 'Clavier mécanique',
            description: 'Clavier mécanique rétroéclairé pour gaming',
            price: 129.99,
            quantity: 3,
            category: 'Accessoires',
            image: null,
            supplier_id: 1,
            sku: 'ACS-003'
          },
        ];
        
        // Pagination simulée
        this.pagination = {
          current_page: page,
          last_page: 2,
          per_page: 10,
          total: mockProducts.length
        };
        
        this.products = mockProducts;
        this.applyFilters();
      } catch (error) {
        console.error('Erreur lors de la récupération des produits:', error);
        this.$emit('showAlert', 'Erreur lors du chargement des produits', 'danger');
      } finally {
        this.loading = false;
      }
    },
    
    async fetchSuppliers() {
      try {
        // Simuler une requête API
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Données simulées pour la démo
        this.suppliers = [
          { id: 1, name: 'TechSupplies Inc.' },
          { id: 2, name: 'ElectroVendor' },
          { id: 3, name: 'GadgetWorld' }
        ];
      } catch (error) {
        console.error('Erreur lors de la récupération des fournisseurs:', error);
      }
    },
    
    applyFilters() {
      let filtered = [...this.products];
      
      // Appliquer le filtre de recherche
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(p => 
          p.name.toLowerCase().includes(query) || 
          (p.description && p.description.toLowerCase().includes(query)) ||
          (p.sku && p.sku.toLowerCase().includes(query))
        );
      }
      
      // Appliquer le filtre de catégorie
      if (this.categoryFilter) {
        filtered = filtered.filter(p => p.category === this.categoryFilter);
      }
      
      // Appliquer le filtre de stock
      if (this.stockFilter === 'low') {
        filtered = filtered.filter(p => p.quantity > 0 && p.quantity <= 5);
      } else if (this.stockFilter === 'out') {
        filtered = filtered.filter(p => p.quantity === 0);
      }
      
      this.filteredProducts = filtered;
    },
    
    searchProducts() {
      // Debounce pour éviter trop d'appels lors de la frappe
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.applyFilters();
      }, 300);
    },
    
    clearSearch() {
      this.searchQuery = '';
      this.applyFilters();
    },
    
    resetFilters() {
      this.searchQuery = '';
      this.categoryFilter = '';
      this.stockFilter = 'all';
      this.applyFilters();
    },
    
    changePage(page) {
      if (page < 1 || page > this.pagination.last_page) return;
      this.fetchProducts(page);
    },
    
    truncateText(text, length) {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    },
    
    formatPrice(price) {
      return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(price);
    },
    
    showAddProductModal() {
      this.$refs.productModal.openAddModal();
    },
    
    showEditProductModal(product) {
      this.$refs.productModal.openEditModal(product);
    },
    
    confirmDeleteProduct(product) {
      this.$refs.productModal.openDeleteModal(product);
    },
    
    handleProductAdded(product) {
      // Ajouter le produit à la liste
      this.products.unshift(product);
      this.applyFilters();
      this.$emit('showAlert', `Le produit ${product.name} a été ajouté avec succès`, 'success');
    },
    
    handleProductUpdated(product) {
      // Mettre à jour le produit dans la liste
      const index = this.products.findIndex(p => p.id === product.id);
      if (index !== -1) {
        this.products[index] = product;
        this.applyFilters();
        this.$emit('showAlert', `Le produit ${product.name} a été mis à jour avec succès`, 'success');
      }
    },
    
    handleProductDeleted(product) {
      // Supprimer le produit de la liste
      this.products = this.products.filter(p => p.id !== product.id);
      this.applyFilters();
      this.$emit('showAlert', `Le produit ${product.name} a été supprimé avec succès`, 'success');
    },
    
    handleError(message) {
      this.$emit('showAlert', message, 'danger');
    }
  }
};
</script>

<style scoped>
.products-container {
  margin-bottom: 30px;
}

.no-image {
  width: 50px;
  height: 50px;
  background-color: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.table th {
  font-weight: 600;
}

.table td {
  vertical-align: middle;
}

.btn-group .btn {
  padding: 0.25rem 0.5rem;
}
</style> 