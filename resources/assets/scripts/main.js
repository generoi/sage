// import external dependencies
import 'console-polyfill';
import 'babel-polyfill';
import $ from 'jquery';
import picturefill from 'picturefill';
import 'jquery.scrollto';
import 'jquery.localscroll';
import 'fastdom/fastdom';
import 'motion-ui/dist/motion-ui';
import 'lazysizes';

// Import components
import gform from './components/gform';
import slick from './components/slick';
import headroom from './components/headroom';
import analytics from './components/analytics';
import magnificpopup from './components/magnificpopup';

// Ensure correct images are set before plugins such as orbit begins measuring
// dimensions.
picturefill();
$(document).foundation();

// Custom components
magnificpopup('#content');
headroom('.headroom');
gform();
slick('.slick', { arrows: false, dots: true });
$.localScroll({ duration: 200 });
analytics.scrolldepth({
  elements: [
    '#header',
    '#content',
    '#page__listing',
    '#page__comments',
    '#page__related',
    '#footer',
  ],
  percentage: false,
  pixelDepth: false,
});
