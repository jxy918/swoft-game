// import {URI_PREFIX} from "./constants"

export default [{
  icon: 'dashboard',
  title: 'Index.dashboard',
  href: '/'
}, {
  icon: 'layers',
  title: 'Index.application',
  subs: [{
    title: 'Index.information',
    href: '/app/info'
  }, {
    title: 'Index.configuration',
    href: '/app/config'
  }, {
    title: 'Index.components',
    href: '/app/components'
  }, {
    title: 'Index.registeredEvents',
    href: '/app/events'
  }, {
    title: 'Index.registeredBeans',
    href: '/app/beans'
  }, {
    title: 'Index.connectionPools',
    href: '/connection/pools'
  }, {
    title: 'Index.aopHandlers',
    href: '/aop/handlers'
  }, {
    title: 'Index.rpcMiddleware',
    href: '/rpc/middles'
  }, {
    title: 'Index.httpMiddleware',
    href: '/http/middles'
  }]
}, {
  icon: 'language',
  title: 'Index.server',
  subs: [{
    title: 'Index.information',
    href: '/server/info'
  }, {
    title: 'Index.serverConfig',
    href: '/server/config'
  }, {
    title: 'Index.serverEvents',
    href: '/server/events'
  }, {
    title: 'Index.serverStats',
    href: '/server/stats'
  }]
}, {
  icon: 'reorder',
  title: 'Index.routes',
  subs: [{
    title: 'Index.httpRoutes',
    href: '/http/routes'
  }, {
    title: 'Index.rpcRoutes',
    href: '/rpc/routes'
  }, {
    title: 'Index.webSocketRoutes',
    href: '/ws/routes'
  }]
}, {
  icon: 'insert_drive_file',
  title: 'Index.logs',
  subs: [{
    title: 'Index.applicationLog',
    href: '/app/logs'
  }, {
    title: 'Index.swooleLog',
    href: '/swoole/logs'
  }]
}, {
  icon: 'build',
  title: 'Index.tools',
  subs: [{
    // icon: 'code',
    title: 'Index.classGenerator',
    href: '/code/gen'
  }, {
    title: 'Index.webSocketTest',
    href: '/ws/test'
  }, {
    title: 'Index.runTrace',
    href: '/run/trace'
  }]
}, {
  icon: 'info',
  title: 'Index.about',
  href: '/about'
}]
