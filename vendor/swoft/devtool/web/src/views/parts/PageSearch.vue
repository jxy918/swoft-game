<template>
  <div>
    <v-select
      :items="states"
      v-model="a1"
      label="Search"
      autocomplete
    ></v-select>
  </div>
</template>

<script>
  export default {
    name: 'page-search'
  }
</script>

<style scoped>

</style>
