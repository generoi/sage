/**
 * Sticky header when scrolling up.
 *
 * @see http://wicky.nillia.ms/headroom.js/
 * @see asset/styles/components/_headroom.scss
 * @see asset/styles/components/util/_motion-ui.scss
 */
import Headroom from 'headroom.js/dist/headroom';
import 'headroom.js/dist/jQuery.headroom';

const ESCAPE_KEYCODE = 27;

export default {
  init(options = {}) {
    this.$headroom = $('.headroom');
    this.$toggler = this.$headroom.find('.l-header__nav-toggle');
    this.$content = this.$headroom.next();

    // Expose it so jQuery.headroom can use it.
    window.Headroom = Headroom;

    this.$headroom.headroom(Object.assign({
      offset: 205,
      tolerance: 5,
      classes: {
        initial: 'is-animating',
        pinned: 'slide-in-down',
        unpinned: 'slide-out-up',
      },
      onPin: () => this.$headroom.trigger('headroom.pinned'),
      onUnpin: () => this.$headroom.trigger('headroom.unpinned'),
    }, options));

    this.$headroom.on('headroom.unpinned', this.closeMenu.bind(this));

    $(document).on('keyup', (e) => {
      if (e.keyCode === ESCAPE_KEYCODE) this.closeMenu();
    })

  },

  closeMenu() {
    if (this.$toggler.length && this.$toggler.is('.active')) {
      this.$toggler.trigger('click');
    }
  },
};
