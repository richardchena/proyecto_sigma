import Vue from 'vue'
import VueRouter from 'vue-router'
//import Home from '../views/Home.vue'
import Inicio from '../views/Inicio.vue'
import Error from '../views/Error.vue'
import History from '../views/History.vue'
import InicioAdmin from '../views/InicioAdmin.vue'
import HistoricoAdmin from '../views/HistoricoAdmin.vue'
import VistaMetrica from '../views/VistaMetrica.vue'
import login from '../views/Login.vue'
//import App from '../App.vue'

Vue.use(VueRouter)

const routes = [
  /*{
    path: '/',
    redirect: { name:'inicio' }
  },*/
  {
    path: '/login',
    name: 'login',
    component: login
  },
  {
    path: '/inicio',
    name: 'inicio',
    component: Inicio
  },
  {
    path: '/admin',
    name: 'inicioadmin',
    component: InicioAdmin
  },
  {
    path: '/history',
    name: 'history',
    component: History,
    meta: {
      title: 'Historial'
    },
  },
  {
    path: '/history_admin',
    name: 'history_admin',
    component: HistoricoAdmin
  },
  {
    path: '/metrica',
    name: 'metrica',
    component: VistaMetrica
  },
  {
    path: '/error',
    name: 'error',
    component: Error
  },
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
