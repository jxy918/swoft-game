<template>
  <v-app id="app-wrapper">
    <!-- left sidebar -->
    <v-navigation-drawer
      persistent
      :mini-variant="miniVariant"
      :clipped="clipped"
      v-model="drawer"
      enable-resize-watcher
      fixed
      app
    >
      <v-toolbar flat class="transparent">
        <v-list class="pa-0">
          <v-list-tile avatar>
            <v-list-tile-avatar>
              <img src="@/assets/swoft-logo-sm.png"  alt="logo">
            </v-list-tile-avatar>
            <v-list-tile-content>
              Swoft Dev
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-toolbar>
      <v-divider></v-divider>
      <v-list>
        <v-list-group
          v-model="item.active"
          v-for="item in items"
          :key="item.title"
          :prepend-icon="item.icon"
          :append-icon="item.subs ? 'keyboard_arrow_down' : ''"
          no-action
        >
          <v-list-tile slot="activator" :to="item.href ? uriPrefix + item.href : ''" exact>
            <v-list-tile-content>
              <v-list-tile-title>{{ $t(item.title) }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
          <v-list-tile
            v-for="subItem in item.subs"
            :key="subItem.title"
            @click=""
            v-if="item.subs"
            :to="uriPrefix + subItem.href "
          >
            <v-list-tile-action v-if="subItem.icon">
              <v-icon>{{ subItem.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>{{ $t(subItem.title) }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>

    <!-- top menu -->
    <v-toolbar
      app
      color="primary theme--dark"
      :clipped-left="clipped"
    >
      <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
      <!--<v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon v-html="miniVariant ? 'chevron_right' : 'chevron_left'"></v-icon>
      </v-btn>-->
      <!--<v-btn icon @click.stop="fixed = !fixed">-->
        <!--<v-icon>remove</v-icon>-->
      <!--</v-btn>-->
      <v-toolbar-title v-text="title"></v-toolbar-title>
      <v-spacer></v-spacer>

      <v-menu offset-y>
        <v-btn slot="activator" flat color="theme--dark" >
          {{ selectLangage }}
        </v-btn>
        <v-list>
          <v-list-tile v-for="(item, index) in langages" :key="item.title" @click="changeLange(index)">
            <v-list-tile-title>{{ item }}</v-list-tile-title>
          </v-list-tile>
        </v-list>
      </v-menu>

      <v-spacer
      <v-select
        flat
        solo-inverted
        autocomplete
        prepend-icon="search"
        :label="$t('Index.pageSearch')"
        class="hidden-sm-and-down"
        item-text="name"
        :items="pages"
        v-model="userInput"
        @change="gotoPage"
      ></v-select>
      <v-btn icon @click.stop="rightDrawer = !rightDrawer">
        <v-icon>menu</v-icon>
      </v-btn>
    </v-toolbar>

    <!-- content -->
    <v-content>
      <v-container :fluid="false" class="content-body" grid-list-md>
        <v-breadcrumbs class="dashboard">
          <v-icon slot="divider">chevron_right</v-icon>
          <v-breadcrumbs-item :to="uriPrefix" exact>{{$t('Index.dashboard')}}</v-breadcrumbs-item>
          <v-breadcrumbs-item>{{ $t(this.$route.name) }}</v-breadcrumbs-item>
        </v-breadcrumbs>

        <v-slide-y-transition mode="out-in">
          <router-view></router-view>
        </v-slide-y-transition>
      </v-container>

      <!--footer-->
      <app-footer></app-footer>
    </v-content>

    <!--right-->
    <v-navigation-drawer
      temporary
      right
      v-model="rightDrawer"
      fixed
      app
    >
      <v-list>
        <v-list-tile @click="right = !right">
          <v-list-tile-action>
            <v-icon>compare_arrows</v-icon>
          </v-list-tile-action>
          <v-list-tile-title>Switch drawer (click me)</v-list-tile-title>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>

    <n-progress parent="#app-wrapper"></n-progress>
  </v-app>
</template>

<script>
  import sidebar from './libs/sidebar'
  import routes from './router/routes'
  import {VSelect} from 'vuetify'
  import {URI_PREFIX} from './libs/constants'
  import NProgress from './views/parts/NProgress'
  import AppFooter from './views/parts/AppFooter'
  import * as VBreadcrumbs from 'vuetify/es5/components/VBreadcrumbs'

  export default {
    name: 'App',
    components: {AppFooter, NProgress, VSelect, ...VBreadcrumbs},
    data() {
      let pages = []
      let That = this

      for (let key in routes) {
        let route = routes[key]
        if (!route.name) {
          continue
        }

        pages.push({
          path: route.path,
          name: That.$t(route.name)
        })
      }

      return {
        clipped: false,
        drawer: true,
        fixed: false,
        uriPrefix: URI_PREFIX,
        items: sidebar,
        pages: pages,
        miniVariant: false,
        right: true,
        rightDrawer: false,
        title: 'DevTool',
        userInput: null,
        selectLangage: '中文',
        langages: ['中文', 'English']
      }
    },
    methods: {
      gotoPage (item) {
        this.$router.push(item.path)
      },
      changeLange (i) {
        i === 1 ? this.$i18n.locale = 'en' : this.$i18n.locale = 'zh'
        this.selectLangage = this.langages[i]
      }
    }
  }
</script>

<style lang="stylus">
  @import "assets/style/common.styl";

  .content-body
    min-height 660px;
    background-color #f6f6f6;
  .el-tag
    background-color rgba(64,158,255,.1);
    display inline-block;
    padding 0 10px;
    height 32px;
    line-height 30px;
    font-size 12px;
    color #409eff;
    border-radius 4px;
    box-sizing border-box;
    border 1px solid rgba(64,158,255,.2);
    white-space nowrap;
  .el-tag--success
    background-color rgba(103,194,58,.1);
    border-color rgba(103,194,58,.2);
    color #67c23a;
  .el-tag--warning
    background-color rgba(230,162,60,.1);
    border-color rgba(230,162,60,.2);
    color #e6a23c;
  .el-tag--danger
      background-color hsla(0,87%,69%,.1);
      border-color hsla(0,87%,69%,.2);
      color #f56c6c;
  $mobiWidth = 768px
  @media screen and (max-width $mobiWidth - 1px)
    .breadcrumbs 
      display none !important
    .dashboard-list
      width 100% !important;
</style>
