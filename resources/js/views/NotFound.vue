<template>
  <div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
      <h1 class="display-1 fw-bold">404</h1>
      <p class="fs-3">
        <span class="text-danger">Oups!</span> Page non trouvée.
      </p>
      <p class="lead">
        La page que vous recherchez n'existe pas ou a été déplacée.
      </p>
      <router-link to="/" class="btn btn-primary">
        <i class="fas fa-home me-2"></i>Retour à l'accueil
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
}
</script> 