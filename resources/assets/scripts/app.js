/**
 * External Dependencies
 */
import $ from 'jquery';

/**
 * Internal Dependencies
 */
import { init as initFoundation } from './common/foundation';
import { init as initFontawesome } from './common/fontawesome';
import { accordion } from './components/accordion';


initFoundation();
initFontawesome();

accordion('.schema-faq', {titleSelector: '.schema-faq-question'});

$(document).ready(() => {
  // console.log('Hello world');
});
