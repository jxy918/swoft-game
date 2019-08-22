<template>
  <div class="dashboard">
    <v-jumbotron dark class="main-container">
      <v-container fill-height>
        <v-layout align-center>
          <v-flex>
            <h3 class="display-3">{{ $t('Index.title') }}</h3>
            <span class="subheading">
              {{ $t('Index.subheading') }}
            </span>
            <v-divider class="my-3"></v-divider>
            <div class="title mb-3">{{ $t('Index.features') }}</div>
            <v-btn large color="primary" class="mx-0" :href="github" target="_blank">Github</v-btn>
            <v-btn large color="success" class="mx-0" :href="document" target="_blank">{{ $t('Index.document') }}</v-btn>
          </v-flex>
        </v-layout>
      </v-container>
    </v-jumbotron>

      <v-flex xs12 md12 md12 style="padding-top:15px;">
        <v-card>
          <v-card-title class="title grey lighten-3"> {{ $t('Index.application') }} </v-card-title>
          <v-divider></v-divider>
            <v-list dense>
            
            <div class="dashboard-list">
              <v-list-tile v-for="item in app1" :key="item.href">
                <v-list-tile-content>{{ $t(item.title) }}</v-list-tile-content>
                <v-list-tile-content class="align-end">
                  <v-btn small outline color="blue" :to="item.href" style="padding: 5px;">
                    {{ $t('Index.view') }}&nbsp;<v-icon>remove_red_eye</v-icon>
                  </v-btn>
                </v-list-tile-content>
              </v-list-tile>
            </div>

            <div class="dashboard-list">
              <v-list-tile v-for="item in app2" :key="item.href">
                <v-list-tile-content>{{ $t(item.title) }}</v-list-tile-content>
                <v-list-tile-content class="align-end">
                  <v-btn small outline color="blue" :to="item.href">
                    {{ $t('Index.view') }}&nbsp;<v-icon>remove_red_eye</v-icon>
                  </v-btn>
                </v-list-tile-content>
              </v-list-tile>
            </div>

            <div class="dashboard-list">
              <v-list-tile v-for="item in app3" :key="item.href">
                <v-list-tile-content>{{ $t(item.title) }}</v-list-tile-content>
                <v-list-tile-content class="align-end">
                  <v-btn small outline color="blue" :to="item.href">
                    {{ $t('Index.view') }}&nbsp;<v-icon>remove_red_eye</v-icon>
                  </v-btn>
                </v-list-tile-content>
              </v-list-tile>
            </div>
            <p style="clear:both"></p>
          </v-list>
        </v-card>
      </v-flex>


    <v-layout row wrap style="padding-top:15px;">
      <v-flex
        d-flex
        xs12
        sm6
        md6
      >
        <v-card>
          <v-card-title class="title grey lighten-3"> {{ $t('Index.environment') }} </v-card-title>
          <v-divider></v-divider>
          <v-list dense class="pa-2">
            <v-list-tile v-for="(val, key) in env" :key="key">
              <v-list-tile-content>{{ key }}</v-list-tile-content>
              <!-- <v-list-tile-content class="align-end"></v-list-tile-content> -->
              {{ val }}
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>

      <v-flex d-flex xs12 sm6 md3>
        <v-card>
          <v-card-title class="title grey lighten-3"> {{ $t('Index.server') }} </v-card-title>
          <v-divider></v-divider>
          <v-list dense class="pa-2">
            <v-list-tile v-for="item in server" :key="item.href">
              <v-list-tile-content>{{ $t(item.title) }}</v-list-tile-content>
              <v-list-tile-content class="align-end">
                <v-btn small outline color="blue" :to="item.href">
                  {{ $t('Index.view') }}&nbsp;<v-icon>remove_red_eye</v-icon>
                </v-btn>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>

      <v-flex d-flex xs12 sm6 md3>
        <v-card>
          <v-card-title class="title grey lighten-3"> {{ $t('Index.tools') }} </v-card-title>
          <v-divider></v-divider>
          <v-list dense class="pa-2">
            <v-list-tile v-for="item in tools" :key="item.href">
              <v-list-tile-content>{{ $t(item.title) }}</v-list-tile-content>
              <v-list-tile-content class="align-end">
                <v-btn  flat  outline color="blue" :to="item.href">
                  {{ $t('Index.view') }}&nbsp;<v-icon>remove_red_eye</v-icon>
                </v-btn>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>

    </v-layout>

  </div>
</template>

<script>
  import {VJumbotron, VDataTable} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import {URI_PREFIX} from '../libs/constants'
  import {getBasicEnv} from '../libs/api-services'

  export default {
    name: 'dashboard',
    components: {VJumbotron, VDataTable, ...VCard},
    data: () => ({
      env: {},
      basePath: '',
      uriPrefix: URI_PREFIX,
      github: 'https://github.com/swoft-cloud/swoft',
      document: 'https://doc.swoft.org',
      app1: [{
        title: 'Index.information',
        href: URI_PREFIX + '/app/info',
        icon: 'info'
      }, {
        title: 'Index.applicationConfig',
        href: URI_PREFIX + '/app/config',
        icon: 'settings'
      }, {
        title: 'Index.registeredEvents',
        href: URI_PREFIX + '/app/events',
        icon: 'event'
      }],
      app2: [{
        title: 'Index.httpRoutes',
        href: URI_PREFIX + '/http/routes',
        icon: ''
      }, {
        title: 'Index.rpcRoutes',
        href: URI_PREFIX + '/rpc/routes',
        icon: ''
      }, {
        title: 'Index.webSocketRoutes',
        href: URI_PREFIX + '/ws/routes',
        icon: 'web'
      }],
      app3: [{
        title: 'Index.registeredBeans',
        href: URI_PREFIX + '/app/beans',
        icon: ''
      }, {
        title: 'Index.aopHandlers',
        href: URI_PREFIX + '/aop/handlers',
        icon: 'drag_handle'
      }, {
        title: 'Index.httpMiddleware',
        href: URI_PREFIX + '/http/middles',
        icon: 'http'
      }],
      server: [{
        title: 'Index.information',
        href: URI_PREFIX + '/server/info',
        icon: 'info'
      }, {
        title: 'Index.configuration',
        href: URI_PREFIX + '/server/config',
        icon: 'setting'
      }, {
        title: 'Index.registeredEvents',
        href: URI_PREFIX + '/server/events',
        icon: 'event'
      }, {
        title: 'Index.swooleLog',
        href: URI_PREFIX + '/swoole/logs',
        icon: 'history'
      }],
      tools: [{
        title: 'Index.codeGenerator',
        href: URI_PREFIX + '/code/gen',
        icon: 'event'
      }, {
        title: 'Index.webSocketTest',
        href: URI_PREFIX + '/ws/test',
        icon: ''
      }, {
        title: 'Index.runTracing',
        href: URI_PREFIX + '/run/trace',
        icon: 'track_changes'
      }, {
        title: 'Index.applicationLog',
        href: URI_PREFIX + '/app/logs',
        icon: 'history'
      }],
      colors: [
        'amber darken-2',
        'blue',
        'blue lighten-2',
        'blue darken-3',
        'blue accent-4',
        'brown',
        'brown darken-2',
        'cyan',
        'cyan darken-1',
        'cyan darken-3',
        'indigo',
        'indigo darken-2',
        'lime darken-2',
        'orange',
        'orange darken-2',
        'deep-orange darken-2',
        'deep-purple',
        'deep-purple darken-2',
        'purple',
        'purple darken-2',
        'yellow darken-3',
        'teal',
        'teal darken-3',
        'blue-grey',
        'blue-grey darken-2',
        'green',
        'green darken-2',
        'red darken-2',
        'pink darken-1',
        'light-blue',
        'light-blue darken-4',
        'light-green',
        'light-green darken-3'
      ],
      lorem: `Lorem ipsum dolor sit amet, mel at clita quando. Te sit oratio vituperatoribus, nam ad ipsum posidonium mediocritatem, explicari dissentiunt cu mea. Repudiare disputationi vim in, mollis iriure nec cu, alienum argumentum ius ad. Pri eu justo aeque torquatos.`
    }),
    created() {
      this.fetchList()
    },
    computed: {
    },
    methods: {
      randomColor () {
        let index = Math.floor(Math.random() * this.colors.length)

        return this.colors[index]
      },
      buildRoute (route) {
        return this.uriPrefix + route
      },
      fetchList() {
        getBasicEnv().then(({data}) => {
          this.basePath = data.basePath

          // remove key
          delete data.basePath

          this.env = data

          console.log(data)
        })
      }
    }
  }
</script>

<style lang="stylus">
.main-container
  background  url('/devtool/static/img/top-bg.png') no-repeat center center;
.dashboard-list
  width 33.333%;
  float left;
  border-left 1px solid #ddd
.dashboard-list:first-child
  border-left 1px solid transparent
</style>
