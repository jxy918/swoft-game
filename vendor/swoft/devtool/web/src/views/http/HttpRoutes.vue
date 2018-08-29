<template>
  <div class="mb-2">
    <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    <v-tabs color="blue lighten-5">
      <v-tabs-slider color="blue"></v-tabs-slider>
      <v-tab href="#tab-1">
        <strong>{{ $t('App.staticRoutes') }}</strong>
      </v-tab>
      <v-tab href="#tab-2">
        <strong>{{ $t('App.dynamicRoutes') }}({{ $t('App.regular') }})</strong>
      </v-tab>

      <v-tab-item id="tab-1">
        <v-card>
          <v-card-title class="pt-1">
            <v-spacer></v-spacer>
            <v-text-field
              append-icon="search"
              :label="$t('App.search')"
              single-line
              hide-details
              v-model="stSearch"
            ></v-text-field>
          </v-card-title>
          <v-divider></v-divider>
          <v-data-table
            :headers="stHeaders"
            :items="staticList"
            :search="stSearch"
            :rows-per-page-items="pageOpts"
            :rows-per-page-text="$t('App.rowsPerPage')"
            disable-initial-sort
            class="elevation-1"
          >
            <template slot="items" slot-scope="props">
              <td>{{ props.item.path }}</td>
              <td>{{ props.item.method }}</td>
              <td><span class="el-tag">{{ props.item.handler }}</span></td>
            </template>
            <template slot="no-data">
              <v-alert :value="true" color="info" icon="info">
                Sorry, nothing to display here :(
              </v-alert>
            </template>
            <template slot="footer">
              <td colspan="100%">
                <strong>This is an extra footer</strong>
              </td>
            </template>
            <v-alert slot="no-results" :value="true" color="error" icon="warning">
              Your search for "{{ stSearch }}" found no results.
            </v-alert>
          </v-data-table>
        </v-card>
      </v-tab-item>
      <v-tab-item id="tab-2">
        <v-card>
          <v-card-title class="pt-1">
            <v-spacer></v-spacer>
            <v-text-field
              append-icon="search"
              label="Search"
              single-line
              hide-details
              v-model="rgSearch"
            ></v-text-field>
          </v-card-title>
          <v-divider></v-divider>
          <v-data-table
            :headers="rgHeaders"
            :items="regularList"
            :search="rgSearch"
            :rows-per-page-items="pageOpts"
            disable-initial-sort
            class="elevation-1"
          >
            <template slot="items" slot-scope="props">
              <td>{{ props.item.original }}</td>
              <td>{{ props.item.methods }}</td>
              <td><code>{{ props.item.handler }}</code></td>
            </template>
            <template slot="no-data">
              <v-alert :value="true" color="info" icon="info">
                Sorry, nothing to display here :(
              </v-alert>
            </template>
            <template slot="footer">
              <td colspan="100%">
                <strong>This is an extra footer</strong>
              </td>
            </template>
            <v-alert slot="no-results" :value="true" color="error" icon="warning">
              Your search for "{{ stSearch }}" found no results.
            </v-alert>
          </v-data-table>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
  import {VAlert, VDataTable} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import * as VTabs from 'vuetify/es5/components/VTabs'
  import {getHttpRoutes} from '@/libs/api-services'

  function formatStaticRoutes(routes, app) {
    for (let path in routes) {
      let val = routes[path]

      for (let method in val) {
        let item = {
          path: path
        }

        item.method = method
        item.handler = val[method].handler
        app.staticList.push(item)
      }
    }
  }

  function formatRegularRoutes(routes, app) {
    for (let fstNode in routes) {
      let items = routes[fstNode]

      for (let k in items) {
        let methods = items[k].methods

        app.regularList.push({
          first: fstNode,
          methods: methods.substr(0, methods.length - 1),
          regex: items[k].regex,
          original: items[k].original,
          handler: items[k].handler,
          option: items[k].option
        })
      }
    }
  }

  export default {
    name: 'httpRoutes',
    components: {VAlert, ...VCard, ...VTabs, VDataTable},
    data() {
      let That = this
      return {
        stSearch: '',
        rgSearch: '',
        rawData: [],

        // table settings
        pageOpts: [10, 25, {'text': 'All', 'value': -1}],

        // table headers
        stHeaders: [{
          text: That.$t('App.uriPath'),
          align: 'left',
          sortable: false,
          value: 'path'
        }, {
          text: That.$t('App.method'),
          align: 'left',
          value: 'method'
        }, {
          text: That.$t('App.routeHandler'),
          align: 'left',
          value: 'handler'
        }],
        rgHeaders: [{
          text: 'Uri Pattern',
          sortable: false,
          value: 'original'
        }, {
          text: 'Allowed Methods',
          value: 'methods'
        }, {
          text: That.$t('App.routeHandler'),
          value: 'handler'
        }],

        // data list
        staticList: [],
        cacheList: [],
        regularList: [],
        vagueList: []
      }
    },
    created() {
      this.fetchList()
    },
    mounted() {
    },
    computed: {},
    methods: {
      fetchList() {
        getHttpRoutes().then(({data}) => {
          console.log(data)
          this.rawData = data

          formatStaticRoutes(data.static, this)
          formatRegularRoutes(data.regular, this)

          this.cacheList = data.cached
          this.vagueList = data.vague
        })
      }
    }
  }
</script>

<style scoped>

</style>
