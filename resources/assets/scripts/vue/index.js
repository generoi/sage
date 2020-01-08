import Vue from 'vue';
import VueCompositionApi, { onMounted, onErrorCaptured, reactive } from "@vue/composition-api";

import Headroom from './Headroom.vue';

Vue.use(VueCompositionApi);

export function init(element, options) {
  return new Promise((resolve, reject) => {
    const app = new Vue({
      el: element,
      components: {
        Headroom,
      },
      setup() {
        const state = reactive(Object.assign({
          isLoading: true,
          isEditor: false,
        }, options));

        onMounted(() => {
          state.isLoading = false;
          resolve(app);
        });

        onErrorCaptured((err) => reject(err));

        return state;
      },
    });
  });
}
