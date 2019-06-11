<template>
  <div class="accordion-list">
    <slot></slot>
  </div>
</template>

<script>
import { MixinId } from 'vue-a11y-utils';

export default {
  computed: {
    accordions() {
      return this.$children;
    }
  },
  methods: {
    onOpen(accordion) {
      this.$emit('close', this.accordions.filter((child) => child !== accordion));
    }
  },
  mounted() {
    this.$on('opened', this.onOpen);
  }
}
</script>
