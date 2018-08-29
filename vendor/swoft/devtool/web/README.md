# devtool

> A Vue.js project

## 一些改动

文件 `vendor/swoft/devtool/web/config/index.js`

- 增加了开发时的代理配置

```json
    proxyTable: {
      '/api': {
        target: 'http://127.0.0.1:9088',
        pathRewrite: {'^/api': '/__devtool'},
        changeOrigin: true
      }
    }
```

- 资源发布目录做了调整:  `./static` -> `./devtool/static`

方便打包后直接拷贝到项目目录的 public 目录下

- 如果想使用本地字体 `npm install material-design-icons -S`

## Build Setup

``` bash
# install dependencies
npm install

# serve with hot reload at localhost:8080
npm run dev

# build for production with minification
npm run build

# build for production and view the bundle analyzer report
npm run build --report
```

For a detailed explanation on how things work, check out the [guide](http://vuejs-templates.github.io/webpack/) and [docs for vue-loader](http://vuejs.github.io/vue-loader).
