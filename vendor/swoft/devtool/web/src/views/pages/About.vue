<template>
  <v-layout column align-center>
    <img src="@/assets/swoft-logo-text.png" alt="swoft" class="mb-3">
    <blockquote>
      {{ $t('App.performance') }}
    </blockquote>
    <v-divider class="my-4"></v-divider>

    <v-card class="about-card">
      <v-subheader><h2>{{ $t('App.Backend') }}</h2></v-subheader>
      <v-data-table
        :items="bItems"
        hide-actions
        hide-headers
        class="elevation-1"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.name }}</td>
          <td>{{ props.item.digest }}</td>
          <td class="text-xs-center">
            <a target="_blank" :href="props.item.github">Github</a>
            <a target="_blank" :href="props.item.document">{{ $t('Index.document') }}</a>
          </td>
        </template>
      </v-data-table>

      <v-subheader><h2>{{ $t('App.Frontend') }}</h2></v-subheader>
      <v-data-table
        :items="fItems"
        hide-actions
        hide-headers
        class="elevation-1"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.name }}</td>
          <td>{{ props.item.digest }}</td>
          <td class="text-xs-center">
            <a target="_blank" :href="props.item.github">Github</a>
            <a target="_blank" :href="props.item.document">{{ $t('Index.document') }}</a>
          </td>
        </template>
      </v-data-table>
    </v-card>
  </v-layout>
</template>

<script>
  import {VDataTable} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'

  export default {
    name: 'about',
    components: {VDataTable, ...VCard},
    data() {
      return {
        bItems: [{
          name: 'Swoft',
          digest: 'backend framework base on Swoole',
          github: 'https://github.com/swoft-cloud/swoft',
          document: 'https://doc.swoft.org/'
        }, {
          name: 'Swoole',
          digest: 'php extension',
          github: 'https://github.com/swoole/swoole-src',
          document: 'https://wiki.swoole.com/'
        }],
        fItems: [{
          name: 'Vue',
          digest: 'frontend framework',
          github: 'https://github.com/vue/vue',
          document: 'https://cn.vuejs.org/'
        }, {
          name: 'Vuetify',
          digest: 'UI framework base on Vue',
          github: 'https://github.com/vuetifyjs/vuetify',
          document: 'https://vuetifyjs.com'
        }, {
          name: 'Webpack',
          digest: 'Front-end resource packaging tools',
          github: 'https://github.com/vuetifyjs/vuetify',
          document: 'https://vuetifyjs.com'
        }, {
          name: 'Material Icons',
          digest: 'Icon font by google',
          github: 'https://github.com/google/material-design-icons',
          document: 'https://material.io/icons/'
        }]
      }
    }
  }
</script>

<style>
/*.about-card {
  background-color: transparent !important;
}*/
</style>
