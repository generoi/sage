<template>
  <div class="accordion">
    <a :href="href" @click.prevent="toggle" :id="`${localId}-title`" :aria-controls="`${localId}-content`" :aria-expanded="active" :role="role">
      <slot name="title"></slot>
    </a>
    <div v-show="active" :id="`${localId}-content`" :aria-labelledby="`${localId}-title`" :aria-hidden="!active" :role="role === 'tab' ? 'tabpanel' : null">
      <slot></slot>
    </div>
  </div>
</template>

<style>
</style>

<script>
import { MixinId } from 'vue-a11y-utils';

export default {
  mixins: [MixinId],
  props: {
    deepLink: {
      type: Boolean,
      default: false,
    },
    id: [String, Number],
    role: String,
  },
  data() {
    return {
      active: false,
    };
  },
  computed: {
    href() {
      return this.deepLink ? `#${this.localId}-content` : '#';
    },
    isTablist() {
      return this.$parent.$el;
    }
  },
  methods: {
    toggle() {
      this.active ? this.close() : this.open();
    },

    close() {
      this.active = false;
      this.$parent.$emit('closed', this);
    },

    open() {
      this.active = true;
      this.$parent.$emit('opened', this);

      if (this.href !== '#') {
        this.updateHash();
      }
    },

    updateHash() {
      window.history.replaceState({}, '', this.href);
    }
  },
  mounted() {
    if (window.location.hash === this.href && this.href !== '#') {
      this.active = true;
    }

    this.$parent.$on('close', (accordionList) => {
      if (!Array.isArray(accordionList)) {
        accordionList = [accordionList];
      }

      if (accordionList.indexOf(this) !== -1) {
        this.close();
      }
    });

    this.$parent.$on('open', (accordion) => {
      if (accordion === this) {
        this.open();
      }
    });
  }
}
</script>
