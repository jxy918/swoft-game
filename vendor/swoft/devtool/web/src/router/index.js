import Vue from 'vue'
import Router from 'vue-router'
import Routes from './routes'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  linkActiveClass: 'active',
  scrollBehavior: () => ({ y: 0 }),
  routes: Routes
})

export default router
