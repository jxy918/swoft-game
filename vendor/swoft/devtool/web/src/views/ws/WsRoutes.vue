<template>
  <div>
    <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    <v-card>
      <v-card-title class="pt-1">
        <v-spacer></v-spacer>
        <v-text-field
          append-icon="search"
          :label="$t('App.search')"
          single-line
          hide-details
          v-model="search"
        ></v-text-field>
      </v-card-title>
      <v-divider></v-divider>
      <v-data-table
        :headers="headers"
        :items="routes"
        :search="search"
        :rows-per-page-items="pageOpts"
        :rows-per-page-text="$t('App.rowsPerPage')"
        disable-initial-sort
        class="elevation-1"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.path }}</td>
          <td><span class="el-tag">{{ props.item.handler }}</span></td>
        </template>
        <template slot="no-data">
          <v-alert :value="true" color="info" icon="info">
            Sorry, nothing to display here :(
          </v-alert>
        </template>
        <v-alert slot="no-results" :value="true" color="error" icon="warning">
          Your search for "{{ search }}" found no results.
        </v-alert>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
  import {VAlert, VDataTable} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import {getWsRoutes} from '../../libs/api-services'

  export default {
    name: 'WsRoutes',
    components: {VAlert, ...VCard, VDataTable},
    data() {
      let That = this
      return {
        search: '',
        rawData: [],

        // table settings
        pageOpts: [10, 25, {'text': 'All', 'value': -1}],

        // table headers
        headers: [{
          text: That.$t('App.uriPath'),
          align: 'left',
          value: 'path'
        }, {
          text: That.$t('App.routeHandler'),
          align: 'left',
          value: 'handler'
        }],

        // data list
        routes: []
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
        getWsRoutes().then(({data}) => {
          console.log(data)
          this.rawData = data

          for (let path in data) {
            let item = {
              path: path
            }

            item.handler = data[path].handler
            this.routes.push(item)
          }
        })
      }
    }
  }
</script>

<style scoped>

</style>
