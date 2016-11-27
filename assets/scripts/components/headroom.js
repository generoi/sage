/**
 * Sticky header when scrolling up.
 *
 * @see http://wicky.nillia.ms/headroom.js/
 * @see asset/styles/components/_headroom.scss
 * @see asset/styles/components/util/_motion-ui.scss
 */
import Headroom from 'headroom.js/dist/headroom';
import 'headroom.js/dist/jQuery.headroom';

export default {
  init(options = {}) {
    this.$headroom = $('.headroom');
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
    }, options));

    $(window).on('changed.zf.mediaquery', this.offsetContent.bind(this));
    window.setTimeout(this.offsetContent.bind(this), 100);
  },

  offsetContent() {
    const dimensions = Foundation.Box.GetDimensions(this.$headroom);
    this.$content.css('margin-top', dimensions.height);
  },
};
