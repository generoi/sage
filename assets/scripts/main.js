/** import external dependencies */
import 'jquery';
import 'picturefill';
import 'fastdom/fastdom';

/** import components */
import gform from './components/gform';
import headroom from './components/headroom';

/**
 * Ensure correct images are set before plugins such as orbit begins measuring
 * dimensions.
 */
picturefill();
jQuery(document).foundation();

/** Custom components */
slick.init('.slick');
gform.init();
// headroom.init();
