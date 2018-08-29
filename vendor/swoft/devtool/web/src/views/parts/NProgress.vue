<template></template>
<script>
  import nprogress from 'nprogress'

  export default {
    name: 'n-progress',
    props: {
      parent: String
    },
    created () {
      if (this.parent) {
        nprogress.configure({ parent: this.parent })
      }

      // 第一次显示进度条 是加载页面
      this.$router.beforeEach((to, from, next) => {
        nprogress.start()
        next()
      })

      this.$router.afterEach(() => {
        nprogress.done()
      })
    }
  }
</script>
<style lang="stylus" scoped>
  @import "../../../node_modules/nprogress/nprogress.css"

  #nprogress .spinner
    display none
</style>
