<template>
  <v-footer height="auto" class="grey darken-3">
    <v-layout row wrap justify-center>
      <v-btn
        color="white"
        flat
        v-for="item in links"
        :key="item.link"
        :href="item.link"
        target="_blank"
      >
        {{ item.title }}
      </v-btn>
      <v-flex xs12 py-3 text-xs-center white--text>
        &copy;2018 — <strong>Swoft</strong>
      </v-flex>
    </v-layout>
    <v-fab-transition>
      <v-btn
        color="blue"
        fab
        dark
        small
        bottom
        fixed
        right
        v-show="1"
        @click="$vuetify.goTo('#app-wrapper', gotoOpts)"
        ref="button"
      >
        <v-icon>keyboard_arrow_up</v-icon>
      </v-btn>
    </v-fab-transition>
  </v-footer>
</template>

<script>
  export default {
    data: () => ({
      links: [{
        title: 'Home',
        icon: 'home',
        link: 'https://swoft.org'
      }, {
        title: 'Github',
        icon: 'home',
        link: 'https://github.com/swoft-cloud/swoft'
      }, {
        title: 'Issues',
        icon: 'home',
        link: 'https://github.com/swoft-cloud/swoft/issues'
      }, {
        title: 'About Us',
        icon: 'contract',
        link: 'https://github.com/orgs/swoft-cloud/people'
      }],
      gotoOpts: {
        duration: 300,
        offset: 0,
        easing: 'easeInOutCubic'
      }
    })
  }
</script>

<style lang="stylus" scoped>
</style>
