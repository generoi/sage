// import external dependencies
import 'console-polyfill';
import 'babel-polyfill';
import $ from 'jquery';
import picturefill from 'picturefill';
import 'fastdom/fastdom';
import 'motion-ui/dist/motion-ui';

// Import components
import gform from './components/gform';
import slick from './components/slick';
import headroom from './components/headroom';

// Ensure correct images are set before plugins such as orbit begins measuring
// dimensions.
picturefill();
$(document).foundation();

// Custom components
headroom('.headroom');
gform();
slick('.slick', { arrows: false, dots: true });
