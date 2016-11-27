/** import external dependencies */
import 'jquery';
import 'picturefill';
import 'foundation-sites/dist/js/plugins/foundation.core';
import 'foundation-sites/dist/js/plugins/foundation.util.box';
import 'foundation-sites/dist/js/plugins/foundation.util.keyboard';
import 'foundation-sites/dist/js/plugins/foundation.util.mediaQuery';
import 'foundation-sites/dist/js/plugins/foundation.util.motion';
import 'foundation-sites/dist/js/plugins/foundation.util.nest';
import 'foundation-sites/dist/js/plugins/foundation.util.timerAndImageLoader';
import 'foundation-sites/dist/js/plugins/foundation.util.touch';
import 'foundation-sites/dist/js/plugins/foundation.util.triggers';
import 'foundation-sites/dist/js/plugins/foundation.abide';
import 'foundation-sites/dist/js/plugins/foundation.accordion';
import 'foundation-sites/dist/js/plugins/foundation.accordionMenu';
import 'foundation-sites/dist/js/plugins/foundation.drilldown';
import 'foundation-sites/dist/js/plugins/foundation.dropdown';
import 'foundation-sites/dist/js/plugins/foundation.dropdownMenu';
import 'foundation-sites/dist/js/plugins/foundation.equalizer';
import 'foundation-sites/dist/js/plugins/foundation.interchange';
import 'foundation-sites/dist/js/plugins/foundation.magellan';
import 'foundation-sites/dist/js/plugins/foundation.offcanvas';
import 'foundation-sites/dist/js/plugins/foundation.orbit';
import 'foundation-sites/dist/js/plugins/foundation.responsiveMenu';
import 'foundation-sites/dist/js/plugins/foundation.responsiveToggle';
import 'foundation-sites/dist/js/plugins/foundation.reveal';
import 'foundation-sites/dist/js/plugins/foundation.slider';
import 'foundation-sites/dist/js/plugins/foundation.sticky';
import 'foundation-sites/dist/js/plugins/foundation.tabs';
import 'foundation-sites/dist/js/plugins/foundation.toggler';
import 'foundation-sites/dist/js/plugins/foundation.tooltip';
import 'foundation-sites/dist/js/plugins/foundation.zf.responsiveAccordionTabs';
import 'motion-ui/dist/motion-ui';

/** import local dependencies */
import Router from './util/Router';
import common from './routes/common';
import home from './routes/home';
import aboutUs from './routes/about';

/** import components */
import gform from './components/gform';
// import headroom from './components/headroom';

/**
 * Populate Router instance with DOM routes
 * @type {Router} routes - An instance of our router
 */
const routes = new Router({
  /** All pages */
  common,
  /** Home page */
  home,
  /** About Us page, note the change from about-us to aboutUs. */
  aboutUs,
});

/**
 * Ensure correct images are set before plugins such as orbit begins measuring
 * dimensions.
 */
picturefill();
jQuery(document).foundation();

/** Custom components */
gform.init();
// headroom.init();

/** Load Events */
jQuery(document).ready(() => routes.loadEvents());
