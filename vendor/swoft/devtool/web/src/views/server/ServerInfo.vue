<template>
  <div>
    <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    <v-card>
      <v-card-title class="title grey lighten-3">
        Php {{ $t('Index.extensions') }} <small> ({{ $t('App.total')}}: <code>{{ extList.length }}</code>)</small>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text class="pa-2">
        <v-chip label outline v-for="name in extList" :key="name">{{ name }}</v-chip>
      </v-card-text>
    </v-card>

    <v-subheader><h2>Swoole {{ $t('Index.information') }}</h2></v-subheader>
    <v-layout>
      <v-flex
        xs12
        sm6
      >
        <v-card>
          <v-card-title class="title grey lighten-3">Enable</v-card-title>
          <v-divider></v-divider>
          <v-list dense>
            <v-list-tile v-for="(item, index) in swooleInfo.enable" :key="index">
              <v-list-tile-content>{{ item.name }}</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ item.value }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>

      <v-flex
        xs12
        sm6
      >
        <v-card>
          <v-card-title class="title grey lighten-3">Directive</v-card-title>
          <v-divider></v-divider>
          <v-list dense>
            <v-list-tile v-for="(item, index) in swooleInfo.directive" :key="index">
              <v-list-tile-content>{{ item.name }}</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ item.value }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
  import {VChip} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import {getSwooleInfo, getPhpExtList} from '../../libs/api-services'

  export default {
    name: 'ServerInfo',
    components: {VChip, ...VCard},
    data() {
      return {
        extList: [],
        swooleInfo: []
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
        getSwooleInfo().then(({data}) => {
          this.swooleInfo = data
        })
        getPhpExtList().then(({data}) => {
          this.extList = data
        })
      }
    }
  }
</script>

<style scoped>

</style>
