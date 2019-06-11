import {
  unregisterBlockStyle,
} from '@wordpress/blocks';

// For whatever reasy domReady is not relying when using Gutenberg plugin
window._wpLoadBlockEditor.then(() => {
  unregisterBlockStyle('core/button', 'squared');
});
