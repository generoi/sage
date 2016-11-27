import 'jquery.scrollto';
import 'jquery.localscroll';
import 'scroll-depth';

export default {
  init() {
    // Track scroll depth to Google Analytics.
    $.scrollDepth();
    // Global smooth anchor scrolling.
    $.localScroll({ duration: 200 });
  },

  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
