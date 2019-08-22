<template>
  <v-layout row wrap>
    <v-flex xs12>
      <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    </v-flex>
    <v-flex xs12 md4>
      <v-card>
        <v-card-text>
          hello
        </v-card-text>
      </v-card>
    </v-flex>
    <v-flex xs12 md8>
      <v-card color="amber lighten-5" class="pa-3">
        <tree-view :data="dataMap" :options="{maxDepth: 4, rootObjectKey: 'JSON'}"></tree-view>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
  import {VAlert} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import {getAopHandlers} from '../../libs/api-services'

  export default {
    name: 'aop-handlers',
    components: {VAlert, ...VCard},
    data() {
      return {
        dataMap: {}
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
        getAopHandlers().then(({data}) => {
          this.dataMap = data

          console.log(data)
        })
      }
    }
  }
</script>

<style scoped>

</style>
