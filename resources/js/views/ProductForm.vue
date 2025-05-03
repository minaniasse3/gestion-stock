<template>
  <div>
    <div class="header-container">
      <div class="row align-items-center">
        <div class="col">
          <h1>
            <i class="fas me-2" :class="isEditMode ? 'fa-edit' : 'fa-plus-circle'"></i>
            {{ isEditMode ? 'Modifier le produit' : 'Ajouter un produit' }}
          </h1>
          <p class="text-muted">
            {{ isEditMode ? 'Modifier les informations du produit' : 'Créer un nouveau produit dans l\'inventaire' }}
          </p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="table-container">
          <form @submit.prevent="saveProduct">
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.name}" id="name" v-model="product.name" required>
                <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
              </div>
              <div class="col-md-6">
                <label for="price" class="form-label">Prix <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">FCFA</span>
                  <input type="number" class="form-control" :class="{'is-invalid': errors.price}" id="price" v-model="product.price" step="1" min="0" required>
                  <div v-if="errors.price" class="invalid-feedback">{{ errors.price[0] }}</div>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="quantity" class="form-label">Quantité en stock <span class="text-danger">*</span></label>
                <input type="number" class="form-control" :class="{'is-invalid': errors.quantity}" id="quantity" v-model="product.quantity" min="0" required>
                <div v-if="errors.quantity" class="invalid-feedback">{{ errors.quantity[0] }}</div>
              </div>
              <div class="col-md-6">
                <label for="category" class="form-label">Catégorie</label>
                <select class="form-select" :class="{'is-invalid': errors.category}" id="category" v-model="product.category">
                  <option value="" selected>Sélectionner une catégorie</option>
                  <option value="Informatique">Informatique</option>
                  <option value="Téléphonie">Téléphonie</option>
                  <option value="Accessoires">Accessoires</option>
                  <option value="Bureautique">Bureautique</option>
                </select>
                <div v-if="errors.category" class="invalid-feedback">{{ errors.category[0] }}</div>
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" :class="{'is-invalid': errors.description}" id="description" v-model="product.description" rows="3"></textarea>
              <div v-if="errors.description" class="invalid-feedback">{{ errors.description[0] }}</div>
            </div>

            <div class="mb-3">
              <label for="image" class="form-label">Image du produit</label>
              <input type="file" class="form-control" :class="{'is-invalid': errors.image}" id="image" @change="handleImageUpload" accept="image/*">
              <div class="form-text">Formats acceptés: JPG, PNG, GIF. Max: 2MB</div>
              <div v-if="errors.image" class="invalid-feedback">{{ errors.image[0] }}</div>
              
              <div v-if="imagePreview || (isEditMode && product.image)" class="mt-2">
                <img :src="imagePreview || '/storage/' + product.image" class="img-thumbnail" style="max-height: 150px;" alt="Aperçu de l'image">
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="supplier_id" class="form-label">Fournisseur</label>
                <select class="form-select" :class="{'is-invalid': errors.supplier_id}" id="supplier_id" v-model="product.supplier_id">
                  <option value="" selected>Sélectionner un fournisseur</option>
                  <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                </select>
                <div v-if="errors.supplier_id" class="invalid-feedback">{{ errors.supplier_id[0] }}</div>
              </div>
              <div class="col-md-6">
                <label for="sku" class="form-label">Référence (SKU)</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.sku}" id="sku" v-model="product.sku">
                <div v-if="errors.sku" class="invalid-feedback">{{ errors.sku[0] }}</div>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <router-link to="/products" class="btn btn-secondary me-2">Annuler</router-link>
              <button type="submit" class="btn btn-primary" :disabled="submitting">
                <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                <i v-else class="fas fa-save me-2"></i>{{ submitting ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="table-container">
          <h5><i class="fas fa-info-circle me-2"></i>Informations</h5>
          <hr>
          <p>Remplissez le formulaire avec les détails du produit. Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.</p>
          
          <h6 class="mt-4">Instructions :</h6>
          <ul class="list-group list-group-flush">
            <li class="list-group-item bg-transparent"><i class="fas fa-check-circle text-success me-2"></i>Donnez un nom clair et descriptif au produit.</li>
            <li class="list-group-item bg-transparent"><i class="fas fa-check-circle text-success me-2"></i>Entrez une description détaillée si nécessaire.</li>
            <li class="list-group-item bg-transparent"><i class="fas fa-check-circle text-success me-2"></i>Assurez-vous que le prix et la quantité sont corrects.</li>
            <li class="list-group-item bg-transparent"><i class="fas fa-check-circle text-success me-2"></i>Ajoutez une image pour faciliter l'identification.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductForm',
  data() {
    return {
      product: {
        id: null,
        name: '',
        description: '',
        price: '',
        quantity: 0,
        category: '',
        supplier_id: '',
        sku: '',
        image: null
      },
      suppliers: [],
      errors: {},
      submitting: false,
      imageFile: null,
      imagePreview: null
    };
  },
  computed: {
    isEditMode() {
      return this.$route.params.id !== undefined;
    },
    productId() {
      return this.$route.params.id;
    }
  },
  mounted() {
    this.fetchSuppliers();
    if (this.isEditMode) {
      this.fetchProduct();
    }
  },
  methods: {
    fetchSuppliers() {
      // Simulation des données - À remplacer par des appels API réels
      this.suppliers = [
        { id: 1, name: 'Fournisseur A' },
        { id: 2, name: 'Fournisseur B' },
        { id: 3, name: 'Fournisseur C' }
      ];
    },
    fetchProduct() {
      // Simulation - À remplacer par un appel API réel
      // Simule la récupération des données d'un produit existant
      setTimeout(() => {
        this.product = {
          id: this.productId,
          name: 'Produit exemple',
          description: 'Description détaillée du produit exemple',
          price: 49.99,
          quantity: 25,
          category: 'Informatique',
          supplier_id: 1,
          sku: 'PRD-001',
          image: 'products/example.jpg'
        };
      }, 300);
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (!file) {
        this.imageFile = null;
        this.imagePreview = null;
        return;
      }
      
      this.imageFile = file;
      
      // Créer un aperçu de l'image
      const reader = new FileReader();
      reader.onload = e => {
        this.imagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    saveProduct() {
      this.submitting = true;
      this.errors = {};
      
      // Création d'un objet FormData pour gérer l'upload de fichier
      const formData = new FormData();
      for (const key in this.product) {
        if (key !== 'image' || !this.isEditMode) {
          formData.append(key, this.product[key]);
        }
      }
      
      if (this.imageFile) {
        formData.append('image', this.imageFile);
      }
      
      // Simulation de l'envoi - À remplacer par un appel API réel
      setTimeout(() => {
        this.submitting = false;
        
        // Simulation d'une redirection après succès
        this.$router.push({ path: '/products' });
      }, 800);
    }
  }
};
</script> 