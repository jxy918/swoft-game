<template>
  <div>
    <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>

  <v-layout row wrap>
    <v-flex xs12>
      <v-alert :type="alertType" v-model="alertStat" dismissible outline>
        {{ alertMsg }}
      </v-alert>
    </v-flex>
    <v-flex d-flex xs12 md4>
      <v-card>
        <v-card-title class="title blue lighten-4"><v-icon>cast</v-icon> &nbsp;Operation</v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-layout row>
            <v-flex xs12>
              <v-text-field
                name="wsUrl"
                :label="'eg ' + locWsUrl"
                single-line
                required
                v-model="wsUrl"
                :hint="$t('App.wsEg')"
                persistent-hint
              ></v-text-field>
            </v-flex>
          </v-layout>
          <v-layout row>
            <v-flex xs12>
              <v-spacer></v-spacer>
              <v-btn
                outline
                @click="connect"
                color="info"
                :disabled="isConnected"
              >
                {{ $t('App.connect') }}
              </v-btn>
              <v-btn :disabled="!isConnected" @click="disconnect" color="warning" outline>
                 {{ $t('App.disConnect') }}
              </v-btn>
            </v-flex>
          </v-layout>

          <v-text-field
            name="message"
            :label="$t('App.message')"
            textarea
            v-model="message"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green" @click="send" large dark>
            <v-icon>send</v-icon> &nbsp; {{ $t('App.send') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-flex>

    <v-flex d-flex xs12 md8>
      <v-card color="grey lighten-5">
        <v-card-title class="title grey lighten-3">
          <v-icon>sms</v-icon> &nbsp;{{ $t('App.messages') }}
          <v-list-tile-content class="align-end">
            <v-btn @click="clearMessages" type="error" icon><v-icon>delete</v-icon></v-btn>
          </v-list-tile-content>
        </v-card-title>

        <v-divider></v-divider>
        <v-card-text class="msg-box">
            <v-layout row wrap v-for="(item, idx) in messages" :key="idx">
              <v-flex xs12>
                <v-avatar size="25px" class="teal" v-if="item.type === 1">
                  <span class="white--text headline">C</span>
                </v-avatar>
                <v-avatar size="25px" class="blue" v-else>
                  <span class="white--text headline">S</span>
                </v-avatar>
                <span class="blue--text"> {{item.date}}</span>
                <div>
                <pre class="px-2 py-2 my-1 ml-4 msg-detail">{{item.msg}}</pre>
                </div>
              </v-flex>
            </v-layout>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          
        </v-card-actions>
      </v-card>
    </v-flex>
  </v-layout>
  </div>
</template>

<script>
  import {VAlert, VAvatar, VBtnToggle} from 'vuetify'
  import * as VCard from 'vuetify/es5/components/VCard'
  import Util from '../../libs/util'

  export default {
    name: 'web-socket',
    components: {VAlert, VAvatar, VBtnToggle, ...VCard},
    data() {
      return {
        ws: null,
        wsUrl: '',
        loading: false,
        locWsUrl: '',
        alertStat: false,
        alertMsg: '',
        alertType: 'info',
        logHeartbeat: false,
        message: '',
        messages: [{
          type: 1,
          msg: 'send example',
          date: '2018-03-23 12:45:34'
        }, {
          type: 2,
          msg: 'receive example',
          date: '2018-03-23 12:45:44'
        }],
        urlHistories: [],
        defaultUrls: [
          'wss://echo.websocket.org/'
        ],
        // req/res data for handshake request
        reqData: [],
        resData: []
      }
    },
    created() {
      let proto = window.location.protocol
      let wsProto = proto.indexOf('s:') > 1 ? 'wss://' : 'ws://'
      let locWsUrl = wsProto + window.location.host

      this.wsUrl = 'wss://echo.websocket.org/'
      this.locWsUrl = locWsUrl
      this.defaultUrls.push(locWsUrl)
    },
    mounted() {
    },
    computed: {
      wsUrlIsEmpty() {
        return this.wsUrl === ''
      },
      isConnected() {
        return this.ws !== null
      }
    },
    methods: {
      connect() {
        if (this.ws) {
          this.alert(this.$t('App.wsConnected'))
          return
        }

        let app = this
        let timer

        app.alert()

        this.ws = new WebSocket(this.wsUrl)
        this.ws.onerror = function error(e) {
          console.log('connect failed!')
          app.alert(app.$t('App.wsFailed') + app.wsUrl)
        }

        this.ws.onopen = function open(ev) {
          console.log('connected', ev)
          app.alert(app.$t('App.wsSuccessfully'))

          // send Heartbeat
          timer = setTimeout(function () {
            app.sendMessage('@heartbeat', false)
          }, 20000)
        }

        this.ws.onmessage = function incoming(me) {
          console.log('received', me)
          app.saveMessage(me.data, 2, 1)
        }

        this.ws.onclose = function close() {
          console.log('disconnected')

          clearTimeout(timer)
          app.ws = null
          app.alert(app.$t('App.wsdisConnect'))
        }
      },
      disconnect() {
        if (this.ws) {
          this.ws.close()
        }
      },
      send() {
        this.sendMessage(Util.trim(this.message))
        this.message = ''
      },
      sendMessage(msg, log = true) {
        this.alert()
        let That = this
        if (!msg) {
          this.alert(That.$t('App.wsFailedMsg'), 'error')
          return
        }

        if (!this.ws) {
          this.alert(That.$t('App.wsconnectBefore'), 'error')
          return
        }

        this.ws.send(msg)

        if (log) {
          this.saveMessage(msg)
        }
      },
      saveMessage(msg, type = 1, isHeart = 0) {
        this.messages.push({
          type: type,
          isHeart: isHeart,
          msg: msg,
          date: Util.formatDate.format(new Date(), 'yyyy-MM-dd hh:mm:ss')
        })
      },
      clearMessages() {
        this.messages = []
      },
      alert (msg = '', type = 'info') {
        if (!msg) {
          this.alertMsg = ''
          this.alertStat = false
          return
        }

        this.alertMsg = Util.ucFirst(msg)
        this.alertStat = true
        this.alertType = type
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .msg-box
    min-height 400px
    max-height 550px
    overflow-y auto
  .msg-detail
    border 1px solid #cdcdcd
    border-radius 3px
    background-color #FFFDE7
</style>
