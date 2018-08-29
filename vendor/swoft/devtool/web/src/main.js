// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import Util from './libs/util'
import {
  Vuetify,
  VApp,
  VDivider,
  VNavigationDrawer,
  VFooter,
  VList,
  VBtn,
  VIcon,
  VGrid,
  VToolbar,
  VTextField,
  VSubheader,
  transitions,
  VMenu
} from 'vuetify'
import '../node_modules/vuetify/src/stylus/app.styl'

// json view
import TreeView from 'vue-json-tree-view'

// VueI18n
import VueI18n from 'vue-i18n'
import messages from './locale/index'

// Helpers
// import colors from 'vuetify/es5/util/colors'

// console.log(colors.teal)
Vue.use(TreeView)
Vue.use(VueI18n)
Vue.use(Vuetify, {
  components: {
    VApp,
    VDivider,
    VNavigationDrawer,
    VFooter,
    VList,
    VBtn,
    VIcon,
    VGrid,
    VToolbar,
    VTextField,
    VSubheader,
    transitions,
    VMenu
  }
})

Vue.config.productionTip = false

const i18n = new VueI18n({
  locale: Util.getLangageCookie('PLAY_LANG', 'zh'), // 语言标识
  messages
})

router.beforeEach((to, from, next) => {
  // iView.LoadingBar.start()
  Util.title(to.meta.title ? to.meta.title : i18n.t(to.name))
  next()
})

router.afterEach((to, from, next) => {
  // iView.LoadingBar.finish()
  // window.scrollTo(0, 0)
  console.log('after route each')
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  i18n,
  components: { App },
  template: '<App/>'
})
