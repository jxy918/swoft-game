<template>
  <v-layout row wrap>
    <v-flex xs12>
      <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    </v-flex>
    <v-flex xs12>
      <v-card>
        <simple-table class="table-bordered">
          <template slot="header">
            <th> Pool Name </th>
            <th> Pool Class</th>
            <th> Operation</th>
          </template>
          <tr v-for="(val, name) in pools" :key="name">
            <td>{{ name }}</td>
            <td><span class="el-tag">{{ val }}</span></td>
            <td class="text-xs-center">
              <v-btn small outline color="blue" @click="fetchPoolConfig(name)">
                {{ $t('Index.view') }} &nbsp;<v-icon>remove_red_eye</v-icon>
              </v-btn>
            </td>
          </tr>
        </simple-table>
      </v-card>
    </v-flex>
    <v-flex xs12>
      <v-card color="yellow lighten-5">
        <v-card-title class="title blue lighten-4">
          Pool Config
          <small class="pl-1" v-show="select"> (For the pool: <span class="el-tag el-tag--danger">{{ select }}</span>)</small>
        </v-card-title>
        <div class="pa-3">
          <tree-view :data="dataMap" :options="{maxDepth: 3, rootObjectKey: 'Config'}"></tree-view>
        </div>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
  import * as VCard from 'vuetify/es5/components/VCard'
  import {getAppPools} from '../../libs/api-services'
  import SimpleTable from '../parts/SimpleTable'

  export default {
    name: 'connection-pools',
    components: {SimpleTable, ...VCard},
    data() {
      return {
        dataMap: {
          tips: 'Please select a pool to see config!'
        },
        select: null,
        pools: {}
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
        getAppPools().then(({data}) => {
          this.pools = data

          console.log(data)
        })
      },
      fetchPoolConfig (name) {
        this.select = name

        getAppPools(name).then(({data}) => {
          this.dataMap = data
        })
      }
    }
  }
</script>

<style scoped>

</style>
