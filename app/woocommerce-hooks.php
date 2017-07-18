<?php

/**
 * @file
 * Contains WooCommerce hooks.
 *
 * Grab the latest hooks from the woomcommerce sourcecode, comment everything
 * out, and uncomment and change `add_filter` to `remove_filter` on the ones
 * you need to remove.
 *
 * @see https://github.com/woocommerce/woocommerce/blob/master/includes/wc-template-hooks.php
 */

namespace App;

/**
 * Disable all default styles.
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');
