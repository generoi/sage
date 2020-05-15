import Vue from 'vue';

import Headroom from './Headroom.vue';

// Ignore all components prefixed with 'gds'
Vue.config.ignoredElements = [/gds-\w*/];

export function init(element, options) {
  return new Promise((resolve, reject) => {
    const app = new Vue({
      el: element,
      components: {
        Headroom,
      },
      data() {
        return Object.assign({
          loading: true,
          isEditor: false,
        }, options);
      },
      mounted() {
        this.$nextTick(() => {
          this.loading = false
          resolve(app);
        });
      },
      errorCaptured(err) {
        reject(err);
      },
    });
  });
}
