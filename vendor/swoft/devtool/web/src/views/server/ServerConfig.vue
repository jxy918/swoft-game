<template>
  <div>
    <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    <v-layout row wrap>
      <v-flex
        d-flex
        xs12
        sm6
        md3
        v-for="(items, section) in server"
        :key="section"
      >
        <v-card>
          <v-card-title class="title grey lighten-3"> {{ section }} </v-card-title>
          <v-divider></v-divider>
          <v-list dense class="pa-2">
            <v-list-tile v-for="(val, key) in items" :key="key">
              <v-list-tile-content>{{ key }}</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ val }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>

    <v-subheader><h3>Swoole {{ $t('App.setting') }}</h3></v-subheader>

    <v-card>
      <table class="table">
        <thead>
        <tr>
          <th>{{ $t('App.settingName') }}</th>
          <th>{{ $t('App.settingValue') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(val, name) in swoole" :key="name">
          <td>{{ name }}</td>
          <td>{{ val }}</td>
        </tr>
        </tbody>
      </table>
    </v-card>
  </div>
</template>

<script>
  import {VAlert, VDataTable} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import {getServerConfig} from '../../libs/api-services'

  export default {
    name: 'ServerConfig',
    components: {VAlert, VDataTable, ...VCard},
    data() {
      return {
        swoole: {},
        server: {},
        // table settings
        pageOpts: [25, {'text': 'All', 'value': -1}],

        // table headers
        headers: [{
          text: 'Setting Name',
          align: 'left',
          value: 'path'
        }, {
          text: 'Setting Value',
          align: 'left',
          value: 'handler'
        }]
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
        getServerConfig().then(({data}) => {
          this.swoole = data.swoole

          console.log(data)

          this.server = {
            basic: data.basic,
            tcp: data.tcp,
            websocket: data.websocket,
            http: data.http
          }
        })
      }
    }
  }
</script>

<style scoped>

</style>
