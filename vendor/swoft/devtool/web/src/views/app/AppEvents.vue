<template>
  <div>
    <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    <v-layout>
      <v-flex d-flex xs12 sm5>
        <v-card>
          <v-card-title class="title grey lighten-3">
            {{ $t('App.listenedEvents') }} ({{ $t('App.total') }}: <span class="el-tag el-tag--danger">{{ events.length }}</span>)
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-2">
            <v-chip label outline v-for="name in events" :key="name" @click="fetchListeners(name)">
              {{ name }}
            </v-chip>
          </v-card-text>
        </v-card>
      </v-flex>

      <v-flex d-flex xs12 sm7>
        <v-card>
          <v-card-title class="title green lighten-5">
            {{ $t('App.ListenersEvent') }}: <span class="el-tag el-tag--danger">{{ selected }}</span> ({{ $t('App.total') }}: <span class="el-tag el-tag--danger">{{ eventListeners.length }}</span>)
          </v-card-title>
          <v-divider></v-divider>
          <v-list dense class="pa-2">
            <v-list-tile v-for="name in eventListeners" :key="name">
              <v-list-tile-content><span class="el-tag">{{ name }}</span></v-list-tile-content>
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
  import {getAppEvents} from '../../libs/api-services'

  export default {
    name: 'AppEvents',
    components: {VChip, ...VCard},
    data() {
      return {
        selected: '(please select a event)',
        events: [],
        eventListeners: [],
        allListeners: {}
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
        getAppEvents().then(({data}) => {
          this.events = data
        })
      },
      fetchListeners(name) {
        this.selected = name

        // has cache
        if (this.allListeners.hasOwnProperty(name)) {
          this.eventListeners = this.allListeners[name]

          return
        }

        getAppEvents(name).then(({data}) => {
          this.allListeners[name] = data
          this.eventListeners = data
        })
      }
    }
  }
</script>

<style scoped>

</style>
