import '@wordpress/edit-post';
import domReady from '@wordpress/dom-ready';
import {
  unregisterBlockStyle,
  registerBlockStyle,
} from '@wordpress/blocks';

import { init as foundation } from './common/foundation';
import { init as fontawesome } from './common/fontawesome';
import { init as accordion } from './components/accordion';
import { init as vue } from './vue';

const addComponents = (el) => vue(el, {isEditor: true}).finally(() => foundation());

domReady(() => {
  unregisterBlockStyle('core/button', 'outline');

  registerBlockStyle('core/button', {
    name: 'outline',
    label: 'Outline',
  });

  fontawesome();
  accordion('.schema-faq', {itemSelector: '.schema-faq-section', titleSelector: '.schema-faq-question'});

  window.acf.addAction('remount', ($el) => {
    // @todo acf bug render_block_preview is not called on re-renders
    if ($el && $el[0] && $el[0].classList && $el[0].classList.contains('acf-block-preview')) {
      addComponents($el[0]);
    }
  });

  window.acf.addAction('render_block_preview', ($el) => {
    addComponents($el[0]);
  });
});
