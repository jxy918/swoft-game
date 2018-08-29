// functions

const SPECIAL_CHARS_REGEXP = /([:\-_]+(.))/g
const MOZ_HACK_REGEXP = /^moz([A-Z])/

function camelCase(name) {
  return name.replace(SPECIAL_CHARS_REGEXP, function (_, separator, letter, offset) {
    return offset ? letter.toUpperCase() : letter
  }).replace(MOZ_HACK_REGEXP, 'Moz$1')
}

function jsonEncode(obj) {
  return JSON.stringify(obj)
}

function jsonDecode(str) {
  return JSON.parse(str)
}

let util = {
  // number
  toFixed(num, decimals = 2) {
    return (num * 1).toFixed(decimals)
  },
  timestamp () {
    return Date.parse(new Date()) / 1000
  },
  // string
  lcFirst(str) {
    return str.substr(0, 1).toLowerCase() + str.slice(1)
  },
  ucFirst(str) {
    return str.substr(0, 1).toUpperCase() + str.slice(1)
  },
  trim(str) {
    return str.replace(/(^\s*)|(\s*$)/g, '')
  },
  ltrim(str) {
    return str.replace(/(^\s*)/g, '')
  },
  rtrim(str) {
    return str.replace(/(\s*$)/g, '')
  },
  // other
  bindLeaveTips() {
    window.onbeforeunload = function () {
      return '您输入的内容尚未保存，确定离开此页面吗？'
    }
  },
  unbindLeaveTips() {
    window.onbeforeunload = null
  },
  getQueryStringByName: function (name) {
    let reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i')
    let r = window.location.search.substr(1).match(reg)
    let context = ''

    if (r != null) {
      context = r[2]
    }

    reg = null
    r = null

    return context == null || context === '' || context === 'undefined' ? '' : context
  },
  formatDate: {
    format: function (date, pattern = 'yyyy-MM-dd') {
      function strPad(str, padLen = 2) {
        if (str.length < padLen) {
          str = '0' + str
        }

        return str
      }

      return pattern.replace(/([yMdhsm])(\1*)/g, function ($0) {
        switch ($0.charAt(0)) {
          case 'y':
            return strPad(date.getFullYear(), $0.length)
          case 'M':
            return strPad(date.getMonth() + 1, $0.length)
          case 'd':
            return strPad(date.getDate(), $0.length)
          case 'w':
            return date.getDay() + 1
          case 'h':
            return strPad(date.getHours(), $0.length)
          case 'm':
            return strPad(date.getMinutes(), $0.length)
          case 's':
            return strPad(date.getSeconds(), $0.length)
        }
      })
    },
    parse: function (dateString, pattern) {
      let matchs1 = pattern.match(/([yMdhsm])(\1*)/g)
      let matchs2 = dateString.match(/(\d)+/g)

      if (matchs1.length === matchs2.length) {
        let _date = new Date(1970, 0, 1)

        for (let i = 0; i < matchs1.length; i++) {
          let _int = parseInt(matchs2[i])
          let sign = matchs1[i]
          switch (sign.charAt(0)) {
            case 'y':
              _date.setFullYear(_int)
              break
            case 'M':
              _date.setMonth(_int - 1)
              break
            case 'd':
              _date.setDate(_int)
              break
            case 'h':
              _date.setHours(_int)
              break
            case 'm':
              _date.setMinutes(_int)
              break
            case 's':
              _date.setSeconds(_int)
              break
          }
        }
        return _date
      }

      return null
    }
  }
}

util.title = function (title) {
  title = title ? title + ' - Swoft dev' : 'Swoft DEV'
  window.document.title = title
}

// 从数组中筛选含搜索字段的新数组
util.search = function (array, searchKey) {
  return array.filter(obj => {
    return Object.keys(obj).some(key => {
      return String(obj[key]).toLowerCase().indexOf(searchKey.toLowerCase()) > -1
    })
  })
}

// getStyle
util.getStyle = function (element, styleName) {
  if (!element || !styleName) return null
  styleName = camelCase(styleName)
  if (styleName === 'float') {
    styleName = 'cssFloat'
  }
  try {
    const computed = document.defaultView.getComputedStyle(element, '')
    return element.style[styleName] || computed ? computed[styleName] : null
  } catch (e) {
    return element.style[styleName]
  }
}

util.getLangageCookie = function (name, defaultValue) {
  let reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)')
  var arr = document.cookie.match(reg)
  if (arr) {
    return unescape(arr[2])
  } else {
    return defaultValue
  }
}

const localStorage = window.localStorage

export const cache = {
  set: function (key, value) {
    localStorage.setItem(key, value)
  },
  get: function (key) {
    return localStorage.getItem(key)
  },
  sets: function (data) {
    // data.forEach(item => this.set(item.key, item.value))
    Object.keys(data).forEach(key => this.set(key, data[key]))
  },
  gets: function (keys) {
    return keys.map(key => this.get(key))
  },
  setJson: function (key, obj) {
    localStorage.setItem(key, jsonEncode(obj))
  },
  getJson: function (key) {
    return jsonDecode(this.get(key))
  },
  del: function (key) {
    return localStorage.removeItem(key)
  },
  key: function (index) {
    return localStorage.key(index)
  },
  has: function (key) {
    return this.get(key) !== null
  },
  len: function () {
    return localStorage.length
  },
  clear: function (keys) {
    if (keys) {
      keys.forEach(key => this.del(key))
    } else {
      localStorage.clear()
    }
  }
}

export default util
