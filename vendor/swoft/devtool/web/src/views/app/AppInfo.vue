<template>
  <v-layout row wrap>
    <v-flex xs12>
      <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    </v-flex>
    <v-flex d-flex xs12 md4>
      <v-card>
        <v-card-title class="title grey lighten-3">{{ $t('App.someTips') }}</v-card-title>
        <v-card-text>
          <p>{{ $t('App.getRealPath') }}: <span class="el-tag el-tag--success">\Swoft::getAlias('@root/public')</span></p>
        </v-card-text>
      </v-card>
    </v-flex>
    <v-flex d-flex xs12 md8>
      <v-card>
        <v-card-title class="title blue lighten-5">{{ $t('App.pathAliases') }}</v-card-title>
        <table class="table">
          <thead>
            <tr>
              <th>{{ $t('App.alias') }}</th>
              <th>{{ $t('App.value') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(val, name) in aliases" :key="name">
              <td>{{ name }}</td>
              <td><span class="el-tag">{{ val }}</span></td>
            </tr>
          </tbody>
        </table>
      </v-card>
    </v-flex>

  </v-layout>
</template>

<script>
  import {VDataTable} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import {getAppAliases} from '../../libs/api-services'

  export default {
    name: 'app-info',
    components: {VDataTable, ...VCard},
    data() {
      return {
        aliases: {}
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
        getAppAliases().then(({data}) => {
          this.aliases = data

          console.log(data)
        })
      }
    }
  }
</script>

<style scoped>

</style>
