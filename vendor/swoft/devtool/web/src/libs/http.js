import axios from 'axios'
import config from '../config'
import { ACCESS_TOKEN } from './constants'

// set base Url
axios.defaults.baseURL = config.baseUri

// Add a request interceptor
axios.interceptors.request.use(function (config) {
  console.log('load ...')

  // config.headers['X-Requested-With'] = 'XMLHttpRequest'

  if (ACCESS_TOKEN) {
    config.headers['Authorization'] = `Bearer ${ACCESS_TOKEN}`
  }

  return config
}, function (error) {
  // iView.LoadingBar.error()
  return Promise.reject(error)
})

// Add a response interceptor
axios.interceptors.response.use(function (response) {
  // iView.LoadingBar.finish()
  console.log('load ok')

  return response
}, function (error) {
  // iView.LoadingBar.error()
  console.log('load fail')

  return Promise.reject(error)
})

export const ajax = axios.create({
  // baseURL: ajaxUrl,
  timeout: 30000,
  params: {
    _time: Date.parse(new Date()) / 1000
  },
  headers: {
    'X-Swoft-Devtool': '1.0.0',
    'X-Requested-With': 'XMLHttpRequest'
  }
})
