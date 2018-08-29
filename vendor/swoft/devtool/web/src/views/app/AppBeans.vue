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
        :items="dataList"
        :search="search"
        :rows-per-page-items="pageOpts"
        :rows-per-page-text="$t('App.rowsPerPage')"
        class="elevation-1"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.index }}</td>
          <td><span class="el-tag">{{ props.item.class }}</span></td>
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
  import {getAppBeans} from '../../libs/api-services'

  export default {
    name: 'app-beans',
    components: {VAlert, ...VCard, VDataTable},
    data() {
      let That = this
      return {
        search: '',

        // table settings
        pageOpts: [10, 25, {'text': 'All', 'value': -1}],

        // table headers
        headers: [{
          text: That.$t('App.number'),
          sortable: false,
          value: 'number'
        }, {
          text: That.$t('App.class'),
          sortable: false,
          value: 'class'
        }],

        // data list
        dataList: []
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
        getAppBeans().then(({data}) => {
          console.log(data)

          for (let index in data) {
            this.dataList.push({
              index: index,
              class: data[index]
            })
          }
        })
      }
    }
  }
</script>

<style scoped>

</style>
