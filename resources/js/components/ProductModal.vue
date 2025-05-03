<template>
  <div class="product-modal">
    <!-- Modal pour ajouter/modifier un produit -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="productModalLabel">
              <i class="fas me-2" :class="isEditMode ? 'fa-edit' : 'fa-plus-circle'"></i>
              {{ isEditMode ? 'Modifier le produit' : 'Ajouter un produit' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveProduct" id="productForm">
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
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" form="productForm" class="btn btn-primary" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="fas fa-save me-2"></i>{{ submitting ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteProductModalLabel">
              <i class="fas fa-trash me-2 text-danger"></i>Confirmer la suppression
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer le produit <strong v-if="productToDelete">{{ productToDelete.name }}</strong> ?</p>
            <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Cette action est irréversible.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" @click="deleteProduct" :disabled="deleteSubmitting">
              <span v-if="deleteSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="fas fa-trash me-2"></i>{{ deleteSubmitting ? 'Suppression...' : 'Supprimer' }}
            </button>
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
  name: 'ProductModal',
  props: {
    suppliers: {
      type: Array,
      default: () => []
    }
  },
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
      errors: {},
      submitting: false,
      deleteSubmitting: false,
      imageFile: null,
      imagePreview: null,
      isEditMode: false,
      productToDelete: null,
      modal: null,
      deleteModal: null
    };
  },
  mounted() {
    this.modal = new Modal(document.getElementById('productModal'));
    this.deleteModal = new Modal(document.getElementById('deleteProductModal'));
  },
  methods: {
    // Ouvrir le modal en mode ajout
    openAddModal() {
      this.resetForm();
      this.isEditMode = false;
      this.modal.show();
    },
    
    // Ouvrir le modal en mode édition
    openEditModal(product) {
      this.resetForm();
      this.isEditMode = true;
      this.product = { ...product };
      this.modal.show();
    },
    
    // Ouvrir le modal de confirmation de suppression
    openDeleteModal(product) {
      this.productToDelete = product;
      this.deleteModal.show();
    },
    
    // Réinitialiser le formulaire
    resetForm() {
      this.product = {
        id: null,
        name: '',
        description: '',
        price: '',
        quantity: 0,
        category: '',
        supplier_id: '',
        sku: '',
        image: null
      };
      this.errors = {};
      this.imageFile = null;
      this.imagePreview = null;
    },
    
    // Gérer l'upload d'image
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
    
    // Sauvegarder le produit (ajout ou mise à jour)
    async saveProduct() {
      this.submitting = true;
      this.errors = {};
      
      try {
        // Dans une application réelle, cela serait un appel API réel
        // Simulons le résultat d'un appel API
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Créer une copie du produit pour éviter des modifications accidentelles
        let productData = { ...this.product };
        
        // Ajout de l'id pour les nouveaux produits
        if (!this.isEditMode) {
          productData.id = Date.now(); // Simulé pour la démo
        }
        
        // Émettre l'événement pour informer le parent
        if (this.isEditMode) {
          this.$emit('product-updated', productData);
        } else {
          this.$emit('product-added', productData);
        }
        
        // Fermer le modal et réinitialiser le formulaire
        this.modal.hide();
        this.resetForm();
        
      } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error);
        
        // Simuler des erreurs de validation pour la démo
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          // Informer le parent de l'erreur
          this.$emit('error', 'Erreur lors de la sauvegarde du produit');
        }
      } finally {
        this.submitting = false;
      }
    },
    
    // Supprimer le produit
    async deleteProduct() {
      this.deleteSubmitting = true;
      
      try {
        // Dans une application réelle, cela serait un appel API réel
        // Simulons le résultat d'un appel API
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Émettre l'événement pour informer le parent
        this.$emit('product-deleted', this.productToDelete);
        
        // Fermer le modal
        this.deleteModal.hide();
        this.productToDelete = null;
        
      } catch (error) {
        console.error('Erreur lors de la suppression:', error);
        
        // Informer le parent de l'erreur
        this.$emit('error', 'Erreur lors de la suppression du produit');
      } finally {
        this.deleteSubmitting = false;
      }
    }
  }
};
</script>

<style scoped>
.modal-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.modal-footer {
  background-color: #f8f9fa;
  border-top: 1px solid #e9ecef;
}
</style> 