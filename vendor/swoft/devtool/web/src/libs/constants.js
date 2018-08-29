import { cache } from './util'

export const URI_PREFIX = '/__devtool'

// lang order: localStorage -> browser language -> default
export const LANG = cache.get('site.lang') || navigator.language || 'zh-CN'

export const ACCESS_TOKEN = 'test'
