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
      path: '/user-profiles',
      name: 'users',
      component: () => import('@/views/users/UserProfile.vue'),
    },
    {
      path: '/discover-games',
      name: 'discover-games',
      component: () => import('@/views/users/DiscoverGames.vue'),
    },
    {
      path: '/manage-games',
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
