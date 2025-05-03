<template>
  <div>
    <div class="header-container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1><i class="fas fa-truck me-2"></i>Fournisseurs</h1>
          <p class="text-muted">Gestion des fournisseurs et des contacts</p>
        </div>
        <div class="col-md-6 text-md-end">
          <button type="button" class="btn btn-primary" @click="showAddSupplierModal">
            <i class="fas fa-plus me-2"></i>Nouveau Fournisseur
          </button>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-primary text-white">
              <i class="fas fa-truck"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Total Fournisseurs</h6>
              <h3 class="mb-0">{{ stats.totalSuppliers }}</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-success text-white">
              <i class="fas fa-box"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Produits Associés</h6>
              <h3 class="mb-0">{{ stats.totalProducts }}</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-info text-white">
              <i class="fas fa-file-invoice"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Commandes</h6>
              <h3 class="mb-0">{{ stats.totalOrders }}</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body d-flex align-items-center">
            <div class="stat-icon bg-warning text-white">
              <i class="fas fa-clock"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">En attente</h6>
              <h3 class="mb-0">{{ stats.pendingOrders }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="table-container">
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" v-model="searchQuery" class="form-control" placeholder="Rechercher un fournisseur...">
          </div>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="btn-group" role="group">
            <button class="btn btn-outline-success" @click="exportSuppliers">
              <i class="fas fa-file-excel me-1"></i>Exporter
            </button>
            <button type="button" class="btn btn-outline-primary" @click="showImportModal">
              <i class="fas fa-file-import me-1"></i>Importer
            </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Chargement...</span>
        </div>
        <p class="mt-2">Chargement des fournisseurs...</p>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nom</th>
              <th scope="col">Contact</th>
              <th scope="col">Email</th>
              <th scope="col">Téléphone</th>
              <th scope="col">Adresse</th>
              <th scope="col">Produits</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="supplier in filteredSuppliers" :key="supplier.id">
              <th scope="row">{{ supplier.id }}</th>
              <td>{{ supplier.name }}</td>
              <td>{{ supplier.contact_person }}</td>
              <td>{{ supplier.email }}</td>
              <td>{{ supplier.phone }}</td>
              <td>{{ supplier.address ? (supplier.address.length > 30 ? supplier.address.substring(0, 30) + '...' : supplier.address) : '' }}</td>
              <td>{{ supplier.products_count }}</td>
              <td>
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm btn-info text-white" @click="viewSupplier(supplier)">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-warning text-white" @click="editSupplier(supplier)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-danger" @click="confirmDeleteSupplier(supplier)">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredSuppliers.length === 0">
              <td colspan="8" class="text-center">Aucun fournisseur trouvé</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination.last_page > 1" class="d-flex justify-content-end">
        <nav aria-label="Page navigation">
          <ul class="pagination">
            <li :class="['page-item', { disabled: pagination.current_page === 1 }]">
              <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">Précédent</a>
            </li>
            <li v-for="page in paginationArray" :key="page" :class="['page-item', { active: pagination.current_page === page }]">
              <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
            </li>
            <li :class="['page-item', { disabled: pagination.current_page === pagination.last_page }]">
              <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Suivant</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Modal Ajout/Édition Fournisseur -->
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true" ref="supplierModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="supplierModalLabel">{{ editMode ? 'Modifier le fournisseur' : 'Ajouter un fournisseur' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveSupplier">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="name" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="name" v-model="supplierForm.name" required>
                </div>
                <div class="col-md-6">
                  <label for="contact_person" class="form-label">Personne à contacter</label>
                  <input type="text" class="form-control" id="contact_person" v-model="supplierForm.contact_person">
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" v-model="supplierForm.email">
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Téléphone</label>
                  <input type="text" class="form-control" id="phone" v-model="supplierForm.phone">
                </div>
              </div>
              
              <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <textarea class="form-control" id="address" v-model="supplierForm.address" rows="3"></textarea>
              </div>
              
              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="formSubmitting">
                  <span v-if="formSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i v-else class="fas fa-save me-2"></i>{{ formSubmitting ? 'Enregistrement...' : 'Enregistrer' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal View Supplier -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true" ref="viewSupplierModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewSupplierModalLabel">Détails du fournisseur</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="viewSupplierBody">
            <div v-if="viewLoading" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
            </div>
            <div v-else>
              <div class="row">
                <div class="col-md-6">
                  <h6>Informations générales</h6>
                  <hr>
                  <p><strong>Nom:</strong> {{ currentSupplier.name }}</p>
                  <p><strong>Contact:</strong> {{ currentSupplier.contact_person }}</p>
                  <p><strong>Email:</strong> {{ currentSupplier.email }}</p>
                  <p><strong>Téléphone:</strong> {{ currentSupplier.phone }}</p>
                  <p><strong>Adresse:</strong> {{ currentSupplier.address }}</p>
                </div>
                <div class="col-md-6">
                  <h6>Statistiques</h6>
                  <hr>
                  <p><strong>Produits fournis:</strong> {{ currentSupplier.products_count }}</p>
                  <p><strong>Dernière commande:</strong> {{ currentSupplier.last_order_date || 'Aucune' }}</p>
                  <p><strong>Commandes en attente:</strong> {{ currentSupplier.pending_orders || 0 }}</p>
                  <p><strong>Montant total des commandes:</strong> {{ formatPrice(currentSupplier.total_order_amount) }}</p>
                </div>
              </div>
              <h6 class="mt-4">Produits fournis</h6>
              <hr>
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Nom du produit</th>
                      <th>Prix</th>
                      <th>Quantité</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="product in supplierProducts" :key="product.id">
                      <td>{{ product.name }}</td>
                      <td>{{ formatPrice(product.price) }}</td>
                      <td>{{ product.quantity }}</td>
                    </tr>
                    <tr v-if="supplierProducts.length === 0">
                      <td colspan="3" class="text-center">Aucun produit trouvé</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true" ref="deleteConfirmModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmer la suppression</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer le fournisseur <strong>{{ supplierToDelete?.name }}</strong> ?</p>
            <p class="text-danger">Cette action est irréversible.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" :disabled="deleteLoading" @click="deleteSupplier">
              <span v-if="deleteLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="fas fa-trash me-2"></i>Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true" ref="importModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="importModalLabel">Importer des fournisseurs</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="importSuppliers">
              <div class="mb-3">
                <label for="importFile" class="form-label">Fichier CSV</label>
                <input type="file" class="form-control" id="importFile" ref="importFile" accept=".csv">
              </div>
              <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary" :disabled="importLoading">
                  <span v-if="importLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i v-else class="fas fa-file-import me-2"></i>Importer
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'SuppliersList',
  data() {
    return {
      suppliers: [],
      supplierProducts: [],
      loading: true,
      viewLoading: false,
      formSubmitting: false,
      deleteLoading: false,
      importLoading: false,
      editMode: false,
      searchQuery: '',
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
      },
      stats: {
        totalSuppliers: 0,
        totalProducts: 0,
        totalOrders: 0,
        pendingOrders: 0
      },
      supplierForm: {
        id: null,
        name: '',
        contact_person: '',
        email: '',
        phone: '',
        address: ''
      },
      currentSupplier: {},
      supplierToDelete: null,
      modals: {
        supplier: null,
        view: null,
        delete: null,
        import: null
      }
    };
  },
  computed: {
    filteredSuppliers() {
      if (!this.searchQuery) return this.suppliers;
      
      const query = this.searchQuery.toLowerCase();
      return this.suppliers.filter(supplier => {
        return supplier.name.toLowerCase().includes(query) ||
               (supplier.contact_person && supplier.contact_person.toLowerCase().includes(query)) ||
               (supplier.email && supplier.email.toLowerCase().includes(query)) ||
               (supplier.phone && supplier.phone.toLowerCase().includes(query)) ||
               (supplier.address && supplier.address.toLowerCase().includes(query));
      });
    },
    paginationArray() {
      const array = [];
      const total = this.pagination.last_page;
      const current = this.pagination.current_page;
      
      // Always show first and last page, with ellipsis if needed
      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          array.push(i);
        }
      } else {
        array.push(1);
        
        if (current > 3) {
          array.push('...');
        }
        
        const start = Math.max(2, current - 1);
        const end = Math.min(total - 1, current + 1);
        
        for (let i = start; i <= end; i++) {
          array.push(i);
        }
        
        if (current < total - 2) {
          array.push('...');
        }
        
        array.push(total);
      }
      
      return array;
    }
  },
  mounted() {
    this.fetchSuppliers();
    this.initModals();
  },
  methods: {
    initModals() {
      this.modals = {
        supplier: new Modal(this.$refs.supplierModal),
        view: new Modal(this.$refs.viewSupplierModal),
        delete: new Modal(this.$refs.deleteConfirmModal),
        import: new Modal(this.$refs.importModal)
      };
    },
    fetchSuppliers() {
      this.loading = true;
      
      // Simulation des données - À remplacer par des appels API réels
      setTimeout(() => {
        this.suppliers = [
          { id: 1, name: 'Fournisseur A', contact_person: 'John Doe', email: 'contact@fournisseura.com', phone: '+33 1 23 45 67 89', address: '123 Rue de Paris, 75001 Paris', products_count: 15 },
          { id: 2, name: 'Fournisseur B', contact_person: 'Jane Smith', email: 'contact@fournisseurb.com', phone: '+33 9 87 65 43 21', address: '456 Avenue des Champs-Élysées, 75008 Paris', products_count: 8 },
          { id: 3, name: 'Fournisseur C', contact_person: 'Bob Johnson', email: 'contact@fournisseurc.com', phone: '+33 6 12 34 56 78', address: '789 Boulevard Haussmann, 75009 Paris', products_count: 12 }
        ];
        
        this.stats = {
          totalSuppliers: this.suppliers.length,
          totalProducts: 35,
          totalOrders: 27,
          pendingOrders: 5
        };
        
        this.pagination = {
          current_page: 1,
          last_page: 1,
          per_page: 10,
          total: this.suppliers.length
        };
        
        this.loading = false;
      }, 500);
    },
    changePage(page) {
      if (page < 1 || page > this.pagination.last_page) return;
      this.pagination.current_page = page;
      this.fetchSuppliers();
    },
    viewSupplier(supplier) {
      this.currentSupplier = supplier;
      this.viewLoading = true;
      this.modals.view.show();
      
      // Simulation des données - À remplacer par des appels API réels
      setTimeout(() => {
        this.currentSupplier = {
          ...supplier,
          last_order_date: '15/04/2023',
          pending_orders: 2,
          total_order_amount: 2500
        };
        
        this.supplierProducts = [
          { id: 1, name: 'Produit exemple 1', price: 100, quantity: 25 },
          { id: 2, name: 'Produit exemple 2', price: 75, quantity: 10 }
        ];
        
        this.viewLoading = false;
      }, 500);
    },
    showAddSupplierModal() {
      this.editMode = false;
      this.supplierForm = {
        id: null,
        name: '',
        contact_person: '',
        email: '',
        phone: '',
        address: ''
      };
      this.modals.supplier.show();
    },
    editSupplier(supplier) {
      this.editMode = true;
      this.supplierForm = { ...supplier };
      this.modals.supplier.show();
    },
    saveSupplier() {
      this.formSubmitting = true;
      
      // Simulation de l'enregistrement - À remplacer par des appels API réels
      setTimeout(() => {
        if (this.editMode) {
          const index = this.suppliers.findIndex(s => s.id === this.supplierForm.id);
          if (index !== -1) {
            this.suppliers[index] = { ...this.supplierForm };
          }
        } else {
          const newId = Math.max(...this.suppliers.map(s => s.id), 0) + 1;
          this.suppliers.push({
            ...this.supplierForm,
            id: newId,
            products_count: 0
          });
          this.stats.totalSuppliers++;
        }
        
        this.formSubmitting = false;
        this.modals.supplier.hide();
      }, 600);
    },
    confirmDeleteSupplier(supplier) {
      this.supplierToDelete = supplier;
      this.modals.delete.show();
    },
    deleteSupplier() {
      this.deleteLoading = true;
      
      // Simulation de la suppression - À remplacer par des appels API réels
      setTimeout(() => {
        const index = this.suppliers.findIndex(s => s.id === this.supplierToDelete.id);
        if (index !== -1) {
          this.suppliers.splice(index, 1);
          this.stats.totalSuppliers--;
        }
        
        this.deleteLoading = false;
        this.modals.delete.hide();
      }, 600);
    },
    showImportModal() {
      this.modals.import.show();
    },
    importSuppliers() {
      this.importLoading = true;
      
      // Simulation de l'import - À remplacer par des appels API réels
      setTimeout(() => {
        this.importLoading = false;
        this.modals.import.hide();
        
        // Affichage d'une notification
        alert('Import terminé avec succès !');
        this.fetchSuppliers();
      }, 1000);
    },
    exportSuppliers() {
      // Simulation de l'export - À remplacer par des appels API réels
      alert('Export en cours...');
    },
    formatPrice(price) {
      return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(price || 0);
    }
  }
};
</script> 