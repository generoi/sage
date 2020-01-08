<template>
  <header :style="{height: `${height}px`}">
    <div ref="headroom" v-bind="$attrs">
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
import { ref, onMounted } from "@vue/composition-api";

function useHeadroom(props, { emit }) {
  const headroom = ref(null);

  onMounted(() => {
    const el = headroom.value;
    const headroomObject = new Headroom(el, {
      offset: props.offset,
      tolerance: props.tolerance,
      classes: {
        initial: props.initial,
        pinned: props.pinned,
        unpinned: props.unpinned,
        top: props.top,
        notTop: props.notTop,
        bottom: props.bottom,
        notBottom: props.notBottom
      },
      onPin: () => emit('onPin', el),
      onUnpin: () => emit('onUnpin', el),
      onTop: () => emit('onTop', el),
      onNotTop: () => emit('onNotTop', el),
      onBottom: () => emit('onBottom', el),
      onNotBottom: () => emit('onNotBottom', el),
    });
    headroomObject.init();
  });

  return {
    headroom,
  };
}

function useResizeObserver(element) {
  const height = ref(0);
  const observer = new ResizeObserver((entry) => {
    entry = entry[0];
    height.value = Math.floor(entry.contentRect.height);
  });

  onMounted(() => {
    observer.observe(element.value);
  })

  return {
    height,
  }
}


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
  setup(props, context) {
    const { headroom } = useHeadroom(props, context);
    const { height } = useResizeObserver(headroom);

    return {
      headroom,
      height
    };
  },
}

</script>
