<template>
  <table class="table">
    <thead>
      <tr><slot name="header"></slot></tr>
    </thead>
    <tbody>
      <slot></slot>
    </tbody>
  </table>
</template>

<script>
  import {VDataTable} from 'vuetify'

  export default {
    name: 'simple-table',
    props: {
      transparent: Boolean
    },
    components: {VDataTable}
  }
</script>

<style scoped>
</style>
