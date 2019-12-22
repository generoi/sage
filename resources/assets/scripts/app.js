/**
 * External Dependencies
 */
import $ from 'jquery';

/**
 * Internal Dependencies
 */
import { init as initFoundation } from './common/foundation';
import { init as initFontawesome } from './common/fontawesome';


initFoundation();
initFontawesome();

$(document).ready(() => {
  // console.log('Hello world');
});
