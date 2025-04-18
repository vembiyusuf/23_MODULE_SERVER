import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: () => import('@/views/auth/Signin.vue'),
    },
    {
      path: '/signup',
      name: 'signup',
      component: () => import('@/views/auth/Signup.vue'),
    },
    {
      path: '/users',
      name: 'users',
      component: () => import('@/views/users/UserProfile.vue'),
    },
    {
      path: '/discovergames',
      name: 'discover-games',
      component: () => import('@/views/users/DiscoverGame.vue'),
    },
    {
      path: '/managegames',
      name: 'manage-games',
      component: () => import('@/views/users/ManageGame.vue'),
    },
    {
      path: '/admins',
      name: 'admins',
      component: () => import('@/views/admins/ListAdmin.vue'),
    },
    {
      path: '/admins/listusers',
      name: 'listusers',
      component: () => import('@/views/admins/ListUser.vue'),
    },
  ],
});


export default router;
