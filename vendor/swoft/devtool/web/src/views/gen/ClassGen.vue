<template>
  <div>
    <v-subheader><h1>{{ $t(this.$route.name) }}</h1></v-subheader>
    <v-layout row wrap>
      <v-flex d-flex xs12 md4>
        <v-card>
          <v-card-title primary-title class="blue lighten-5">
            <div>
              <div class="headline">Class {{ $t('App.setting') }}</div>
              <span class="grey--text">{{ $t('App.generateSetting') }}</span>
            </div>
          </v-card-title>
          <v-container>
            <v-form v-model="valid" ref="form" lazy-validation>
              <v-select
                :label="'Class ' + $t('App.type')"
                v-model="select"
                :items="items"
                required
              ></v-select>
              <v-text-field
                :label="'Class ' + $t('App.name')"
                v-model="name"
                :rules="nameRules"
                :counter="10"
                :hint="$t('App.ext')+'('+ $t('App.eg') +'. demo)'"
                persistent-hint
                required
              ></v-text-field>
              <v-text-field
                :label="$t('App.directory')"
                v-model="dir"
                :rules="dirRules"
                :hint="$t('App.directory')+'('+ $t('App.default') +': @app/Controllers)'"
                persistent-hint
                required
              ></v-text-field>
              <v-text-field
                :label="'Class ' + $t('App.suffix')"
                v-model="suffix"
                :rules="[v => /^[a-zA-Z]+$/.test(v) || $t('App.onlySuffix')]"
                :hint="$t('App.suffixClass') + 'Controller'"
                persistent-hint
              ></v-text-field>
              <v-text-field
                :label="$t('App.templateDirectory')"
                v-model="tplDir"
                :rules="[v => /^@+[a-zA-Z]+\w|\/+$/.test(v) || $t('App.onlySuffix')]"
                :hint="$t('App.templateDir') +'('+ $t('App.default') +': @devtool/res/template)'"
                persistent-hint
              ></v-text-field>
              <v-text-field
                :label="$t('App.templateFilename')"
                v-model="tplFile"
                :rules="[v => /^[a-zA-Z]+[a-zA-Z]|.+$/.test(v) || $t('App.onlySuffix')]"
                :hint="$t('App.templateName')+'(' + $t('App.default')+': controller.stub)'"
                persistent-hint
              ></v-text-field>
              <v-checkbox
                :label="$t('App.existsFile')"
                v-model="override"
              ></v-checkbox>

              <v-btn @click="submit" :disabled="!valid" color="success">
                {{ $t('App.submit') }}
              </v-btn>
              <v-btn @click="clear">{{ $t('App.clear') }}</v-btn>
            </v-form>
          </v-container>
        </v-card>

      </v-flex>
      <v-flex d-flex xs12 md8>
        <v-card>
          <v-card-title primary-title class="blue-grey lighten-5">
            <div>
              <div class="headline">Class {{ $t('App.preview') }}</div>
              <span class="grey--text">1,000 miles of wonder</span>
            </div>
          </v-card-title>
          <v-card-actions>
            <v-btn flat>{{ $t('App.share') }}</v-btn>
            <v-btn flat color="purple">{{ $t('App.explore') }}</v-btn>
            <v-spacer></v-spacer>
            <v-btn flat @click.native="show = !show">
               {{ $t('Index.view') }}<v-icon>{{ show ? 'keyboard_arrow_down' : 'keyboard_arrow_up' }}</v-icon>
            </v-btn>
          </v-card-actions>
          <v-slide-y-transition>
            <v-card-text v-show="show">
              {{ $t('App.escape') }}.
            </v-card-text>
          </v-slide-y-transition>
        </v-card>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
  import axios from 'axios'
  import * as VCard from 'vuetify/es5/components/VCard'
  import {VForm, VCheckbox, VSelect} from 'vuetify'

  export default {
    name: 'class-gen',
    components: {VForm, VCheckbox, VSelect, ...VCard},
    data: () => ({
      show: false,
      valid: true,
      name: 'demo',
      nameRules: [
        v => !!v || 'Name is required',
        v => (v && v.length <= 10) || 'Name must be less than 10 characters'
      ],
      dir: '@app/Controllers',
      dirRules: [
        v => !!v || 'directory is required',
        v => /^@+\w+\w|\//.test(v) || 'directory must be valid'
      ],
      suffix: 'Controller',
      tplDir: '@devtool/res/template',
      tplFile: 'controller.stub',
      select: 'controller',
      items: [
        'command',
        'controller',
        'listener',
        'middleware',
        'process',
        'task',
        'ws controller'
      ],
      defaultTplFiles: [
        'command.stub',
        'controller-rest.stub',
        'controller.stub',
        'listener.stub',
        'middleware.stub',
        'process.stub',
        'task.stub',
        'ws-controller.stub'
      ],
      override: false,
      config: {}
    }),

    methods: {
      submit () {
        if (this.$refs.form.validate()) {
          // Native form submission is not yet supported
          axios.post('/api/submit', {
            name: this.name,
            email: this.email,
            select: this.select,
            checkbox: this.checkbox
          })
        }
      },
      clear () {
        this.$refs.form.reset()
      }
    }
  }
</script>

<style scoped>

</style>
