<template>
  <div>
    <v-subheader><h2>{{ $t(this.$route.name) }}</h2></v-subheader>
    <v-layout row>
      <v-flex
        xs12
        sm6
        md4
        lg3
      >
        <v-card>
          <v-card-title class="title grey lighten-3">Server Stats</v-card-title>
          <v-divider></v-divider>
          <v-list dense>
            <v-list-tile v-for="(item, index) in stats" :key="index">
              <v-list-tile-content>{{ index }}</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ item }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
  import * as VCard from 'vuetify/es5/components/VCard'
  import {getServerStats} from '../../libs/api-services'

  export default {
    name: 'ServerInfo',
    components: {...VCard},
    data() {
      return {
        stats: []
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
        getServerStats().then(({data}) => {
          console.log(data)
          this.stats = data
        })
      }
    }
  }
</script>

<style scoped>

</style>
