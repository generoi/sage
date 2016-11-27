// import external dependencies
import 'jquery';
import 'picturefill';
import 'fastdom/fastdom';

// Import components
import gform from './components/gform';
import slick from './components/slick';
import headroom from './components/headroom';

// Ensure correct images are set before plugins such as orbit begins measuring
// dimensions.
picturefill();
jQuery(document).foundation();

// Custom components
gform.init();
slick.init();
headroom.init();
