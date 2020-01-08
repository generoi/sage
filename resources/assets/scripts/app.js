/**
 * External Dependencies
 */
import $ from 'jquery';

/**
 * Internal Dependencies
 */
import { init as foundation } from './common/foundation';
import { init as fontawesome } from './common/fontawesome';
import { init as accordion } from './components/accordion';
import { init as vue } from './vue';

fontawesome();

vue('#app').finally(() => {
  foundation();
  accordion('.schema-faq', {itemSelector: '.schema-faq-section', titleSelector: '.schema-faq-question'});
});
