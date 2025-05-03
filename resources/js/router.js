import { createRouter, createWebHistory } from 'vue-router';

// Importation des vues
import Home from './views/Home.vue';
import ProductsList from './views/ProductsList.vue';
import ProductForm from './views/ProductForm.vue';
import SuppliersList from './views/SuppliersList.vue';
import Dashboard from './views/Dashboard.vue';
import Login from './views/Login.vue';
import Register from './views/Register.vue';
import NotFound from './views/NotFound.vue';
import Statistics from './views/Statistics.vue';
import Reports from './views/Reports.vue';
import Users from './views/Users.vue';
import Profile from './views/Profile.vue';
import Settings from './views/Settings.vue';

// Définition des routes
const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/products',
        name: 'products',
        component: ProductsList,
        meta: { requiresAuth: true }
    },
    {
        path: '/products/create',
        name: 'product-create',
        component: ProductForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/products/:id/edit',
        name: 'product-edit',
        component: ProductForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/suppliers',
        name: 'suppliers',
        component: SuppliersList,
        meta: { requiresAuth: true }
    },
    {
        path: '/statistics',
        name: 'statistics',
        component: Statistics,
        meta: { requiresAuth: true }
    },
    {
        path: '/reports',
        name: 'reports',
        component: Reports,
        meta: { requiresAuth: true }
    },
    {
        path: '/users',
        name: 'users',
        component: Users,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        path: '/settings',
        name: 'settings',
        component: Settings,
        meta: { requiresAuth: true }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guestOnly: true }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { guestOnly: true }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: NotFound
    }
];

// Création du routeur
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const isAuthenticated = !!token;
    
    let user = { role: '' };
    try {
        const userData = localStorage.getItem('user');
        if (userData) {
            user = JSON.parse(userData);
        }
    } catch (error) {
        console.error('Erreur lors du parsing des données utilisateur dans le routeur:', error);
        // En cas d'erreur de parsing, supprimons les données corrompues
        localStorage.removeItem('user');
        localStorage.removeItem('token');
    }
    
    console.log('Navigation vers:', to.path, 'Auth:', isAuthenticated, 'Role:', user.role);
    
    // Redirection pour les routes protégées
    if (to.meta.requiresAuth && !isAuthenticated) {
        console.log('Redirection vers la page de connexion (auth requise)');
        next({ name: 'login' });
    } 
    // Vérification des permissions admin
    else if (to.meta.requiresAdmin && user.role !== 'admin') {
        console.log('Redirection vers le dashboard (admin requis)');
        next({ name: 'dashboard' });
    }
    // Redirection pour les routes guest-only
    else if (to.meta.guestOnly && isAuthenticated) {
        console.log('Redirection vers la page d\'accueil (déjà authentifié)');
        next({ name: 'dashboard' });
    } 
    else {
        next();
    }
});

export default router; 