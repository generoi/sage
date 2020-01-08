import Vue from 'vue';

export function init(element, options) {
  return new Promise((resolve, reject) => {
    const app = new Vue({
      el: element,
      components: {
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
      }
    });
  });
}
