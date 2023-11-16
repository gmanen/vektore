import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router'
import routes from '~pages'

const newRoutes = [
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('../views/Front/Dashboard.vue'),
  },
  {
    path: '/document/list',
    name: 'DocumentList',
    component: () => import('@/views/Document/List.vue'),
  },
  {
    path: '/document/creation',
    name: 'DocumentCreate',
    component: () => import('@/views/Document/Create.vue'),
  }
]

// merge routes and newRoutes
routes.push(...newRoutes)

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    ...setupLayouts(routes),
  ],
})

router.beforeEach((to, from, next) => {
  next()

  const token = localStorage.getItem('token')
  const tokenExpiration = localStorage.getItem('token_expiration')
  const isTokenExpired = Date.now() > tokenExpiration
  const isAllowed = token && !isTokenExpired

  if ((to.name === 'Contact' ||
      to.name === 'LoginCasCallback'
  ) && !isAllowed) {
    next()
  } else if (to.path !== '/connexion-cas' && !isAllowed) {
    next('/connexion-cas')
  } else {
    next()
  }
})

export default router
