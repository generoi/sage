/**
 * External Dependencies
 */
// import $ from 'jquery';
import { applyPolyfills, defineCustomElements } from 'genero-design-system/loader';

/**
 * Internal Dependencies
 */
import { init as foundation } from './common/foundation';
import { init as fontawesome } from './common/fontawesome';
import { init as accordion } from './components/accordion';
import { init as vue } from './vue';

applyPolyfills().then(() => {
  defineCustomElements();
});

fontawesome();

vue('#app').finally(() => {
  foundation();
  accordion('.schema-faq', {itemSelector: '.schema-faq-section', titleSelector: '.schema-faq-question'});
});
