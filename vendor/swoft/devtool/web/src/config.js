import merge from 'webpack-merge'

let env

if (process.env.NODE_ENV === 'development') {
  env = {
    baseUri: '/api'
  }
}

export default merge({
  baseUri: '/__devtool',
  serverHost: 'http://127.0.0.1',
  serverPort: 8086
}, env)
