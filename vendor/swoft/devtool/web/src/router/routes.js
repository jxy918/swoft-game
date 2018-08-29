import { URI_PREFIX } from '../libs/constants'

export default [{
  path: URI_PREFIX,
  name: 'Index.dashboard',
  component: () => import('@/views/Dashboard.vue')
}, {
  path: URI_PREFIX + '/http/routes',
  name: 'Index.httpRoutes',
  component: () => import('../views/http/HttpRoutes.vue')
}, {
  path: URI_PREFIX + '/http/middles',
  name: 'Index.httpMiddleware',
  component: () => import('../views/http/HttpMiddleware.vue')
}, {
  path: URI_PREFIX + '/app/info',
  name: 'Index.applicationInfo',
  component: () => import('../views/app/AppInfo.vue')
}, {
  path: URI_PREFIX + '/app/config',
  name: 'Index.applicationConfig',
  component: () => import('../views/app/AppConfig.vue')
}, {
  path: URI_PREFIX + '/app/events',
  name: 'Index.applicationEvents',
  component: () => import('../views/app/AppEvents.vue')
}, {
  path: URI_PREFIX + '/app/beans',
  name: 'Index.applicationBeans',
  component: () => import('../views/app/AppBeans.vue')
}, {
  path: URI_PREFIX + '/app/components',
  name: 'Index.appComponents',
  component: () => import('../views/app/AppComponents.vue')
}, {
  path: URI_PREFIX + '/aop/handlers',
  name: 'Index.aopHandlers',
  component: () => import('../views/app/AopHandlers.vue')
}, {
  path: URI_PREFIX + '/connection/pools',
  name: 'Index.connectionPools',
  component: () => import('../views/app/ConnectionPools.vue')
}, {
  path: URI_PREFIX + '/server/info',
  name: 'Index.serverInfo',
  component: () => import('../views/server/ServerInfo.vue')
}, {
  path: URI_PREFIX + '/server/stats',
  name: 'Index.serverStats',
  component: () => import('../views/server/ServerStats.vue')
}, {
  path: URI_PREFIX + '/server/config',
  name: 'Index.serverConfig',
  component: () => import('../views/server/ServerConfig.vue')
}, {
  path: URI_PREFIX + '/server/events',
  name: 'Index.serverEvents',
  component: () => import('../views/server/ServerEvents.vue')
}, {
  path: URI_PREFIX + '/swoole/logs',
  name: 'Index.swooleLogs',
  component: () => import('../views/server/SwooleLog.vue')
}, {
  path: URI_PREFIX + '/ws/routes',
  name: 'Index.webSocketRoutes',
  component: () => import('../views/ws/WsRoutes.vue')
}, {
  path: URI_PREFIX + '/ws/test',
  name: 'Index.webSocketTest',
  component: () => import('../views/ws/WsTest.vue')
}, {
  path: URI_PREFIX + '/rpc/routes',
  name: 'Index.rpcRoutes',
  component: () => import('../views/rpc/RpcRoutes.vue')
}, {
  path: URI_PREFIX + '/rpc/middles',
  name: 'Index.rpcMiddleware',
  component: () => import('../views/rpc/RpcMiddleware.vue')
}, {
  path: URI_PREFIX + '/code/gen',
  name: 'Index.classGenerator',
  component: () => import('../views/gen/ClassGen.vue')
}, {
  path: URI_PREFIX + '/run/trace',
  name: 'Index.runTracing',
  component: () => import('../views/app/RunTrace.vue')
}, {
  path: URI_PREFIX + '/app/logs',
  name: 'Index.applicationLogs',
  component: () => import('../views/app/AppLog.vue')
}, {
  path: URI_PREFIX + '/about',
  name: 'Index.about',
  component: () => import('../views/pages/About.vue')
}, {
  path: '*',
  redirect: URI_PREFIX
}]
