import {
  unregisterBlockType,
  unregisterBlockStyle,
} from '@wordpress/blocks';

// For whatever reasy domReady is not relying when using Gutenberg plugin
window._wpLoadBlockEditor.then(() => {
  unregisterBlockStyle('core/button', 'squared');

  // You can gather a list of all blocks by visiting a page with the block
  // editor loaded and running this in the developer console:
  // wp.blocks.getBlockTypes().map((b) => b.name).join('",\n"');
  const removeBlocks = [
    'jetpack/business-hours',
    'jetpack/contact-info',
    'jetpack/address',
    'jetpack/email',
    'jetpack/phone',
    'jetpack/gif',
    // 'jetpack/map',
    'jetpack/repeat-visitor',
    // 'jetpack/slideshow',
    'ptam/custom-posts',
    'yoast/how-to-block',
    'yoast/faq-block',
  ];

  removeBlocks.forEach((block) => unregisterBlockType(block));
});
