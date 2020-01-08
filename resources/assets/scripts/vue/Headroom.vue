<template>
  <header :style="{height: `${height}px`}">
    <div ref="headroom">
      <slot></slot>
    </div>
  </header>
</template>

<style lang="scss">
  .headroom {
    will-change: transform;
    transition: transform 200ms ease-out;
    position: fixed;
    width: 100%;
    background: inherit;
    z-index: 100;

    &--pinned {
      transform: translateY(0%);
    }

    &--unpinned {
      transform: translateY(-100%);
    }
  }
</style>

<script>
import Headroom from 'headroom.js'

export default {
  props: {
    offset: {
      type: Number,
      default: 100,
    },
    tolerance: {
      type: [Object, Number],
      default: 0
    },
    initial: {
      type: String,
      default: 'headroom'
    },
    pinned: {
      type: String,
      default: 'headroom--pinned'
    },
    unpinned: {
      type: String,
      default: 'headroom--unpinned'
    },
    top: {
      type: String,
      default: 'headroom--top'
    },
    notTop: {
      type: String,
      default: 'headroom--not-top'
    },
    bottom: {
      type: String,
      default: 'headroom--bottom'
    },
    notBottom: {
      type: String,
      default: 'headroom--not-bottom'
    }
  },
  data() {
    return {
      height: 0,
    };
  },
  mounted() {
    this.$nextTick(this.initHeadroom.bind(this));
    this.resizeObserver = this.initResizeObserver();
  },
  methods: {
    initResizeObserver() {
      const observer = new ResizeObserver((entry) => {
        entry = entry[0];
        this.height = Math.floor(entry.contentRect.height);
      });
      observer.observe(this.$refs.headroom);
      return observer;
    },
    initHeadroom() {
      const el = this.$refs.headroom;


      const headroom = new Headroom(el, {
        offset: this.offset,
        tolerance: this.tolerance,
        classes: {
          initial: this.initial,
          pinned: this.pinned,
          unpinned: this.unpinned,
          top: this.top,
          notTop: this.notTop,
          bottom: this.bottom,
          notBottom: this.notBottom
        },
        onPin: () => this.$emit('onPin', el),
        onUnpin: () => this.$emit('onUnpin', el),
        onTop: () => this.$emit('onTop', el),
        onNotTop: () => this.$emit('onNotTop', el),
        onBottom: () => this.$emit('onBottom', el),
        onNotBottom: () => this.$emit('onNotBottom', el),
      });

      headroom.init();
    }
  }
}
</script>
