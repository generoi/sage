<?php

/**
 * @file
 * Contains image size definitions and other image related filters.
 */

namespace App;

use Timber;

/**
 * Define image sizes
 */
add_action('after_setup_theme', function () {
    global $content_width;

    // Set the maximum allowed width for any content (eg. oEmbeds, images).
    // This should be the width of the content area.
    if (!isset($content_width)) {
        $content_width = intval(Foundation\paragraph_width('large'));
    }

    // Modify Core sizes.
    if (get_option('large_size_w') != $content_width) {
        update_option('thumbnail_size_w', 150);
        update_option('thumbnail_size_h', 150);
        update_option('thumbnail_crop', 1);
        update_option('medium_size_w', 300);
        update_option('medium_size_h', 300);
        // Desktop Version
        update_option('large_size_w', $content_width);
        update_option('large_size_h', round($content_width/1.5));
    }

    // Hero image sizes.
    add_image_size('hero--desktop', 1400, 350, 1);
    add_image_size('hero--tablet', 768, 400, 1);
    add_image_size('hero--mobile', 640, 560, 1);

    // Tablet version
    add_image_size('tablet', 768, 400);
    // Mobile version
    add_image_size('mobile', 400, 400);
    // Teaser image.
    add_image_size('teaser', 300, 150, 1);
});


/**
 * Expose our custom image sizes to the Admin UI.
 */
add_filter('image_size_names_choose', function ($sizes) {
    return array_merge($sizes, [
        'mobile' => __('Mobile', '<example-project>'),
        'tablet' => __('Tablet', '<example-project>'),
    ]);
});

/**
 * Remove all srcset sizes larger than the content width.
 */
add_filter('max_srcset_image_width', function () {
    global $content_width;
    return $content_width;
});

/**
 * Sort srcset according to sizes so it's be more readable.
 * @note by default Wordpress excludes images that have a different aspect ratio.
 */
add_filter('wp_calculate_image_srcset', function ($sources, $size_array, $image_src, $image_meta) {
    ksort($sources, SORT_NUMERIC);
    return $sources;
}, 10, 4);

/**
 * Tell Wordpress about the paragraph width in different viewport sizes.
 */
add_filter('wp_calculate_image_sizes', function ($sizes, $size) {
    $width = $size[0];
    if ($width >= Foundation\breakpoint('medium')) {
        $sizes = [];
        $sizes[] = '(max-width: ' . Foundation\breakpoint('medium') . 'px) ' . Foundation\paragraph_width('small');
        $sizes[] = '(max-width: ' . Foundation\breakpoint('large') . 'px) ' . Foundation\paragraph_width('medium');
        $sizes[] = Foundation\paragraph_width('large');
        $sizes = implode(', ', $sizes);
    }
    return $sizes;
}, 10, 2);

/**
 * Make all content images lazyloaded (unless they omit the class attribute).
 */
add_filter('the_content', __NAMESPACE__ . '\\filter_lazyload_images', PHP_INT_MAX);
function filter_lazyload_images($content)
{
    $matches = $search = $replace = [];
    $placeholder_url = 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=';

    preg_match_all('/<img[\s\r\n]+.*?>/is', $content, $matches);

    foreach ($matches[0] as $img_tag) {
        // Skip data URLs.
        if (preg_match("/src=['\"]data:image/is", $img_tag)) {
            continue;
        }
        // Replace the src and add the data-src attribute
        $lazy_tag = preg_replace('/<img(.*?)src=/is', '<img$1src="' . esc_attr($placeholder_url) . '" data-src=', $img_tag);
        // Replace the srcset
        $lazy_tag = str_replace('srcset', 'data-srcset', $lazy_tag);
        // Add the lazy class to the img element
        if (preg_match('/class=["\']/i', $lazy_tag)) {
            $lazy_tag = preg_replace('/class=(["\'])(.*?)["\']/is', 'class=$1lazyload $2$1', $lazy_tag);
        } else {
            $lazy_tag = preg_replace('/<img/is', '<img class="lazyload"', $lazy_tag);
        }
        $lazy_tag .= sprintf('<noscript>%s</noscript>', $img_tag);
        $search[] = $img_tag;
        $replace[] = $lazy_tag;
    }

    $content = str_replace($search, $replace, $content);
    return $content;
}
