<?php

namespace App;

/**
 * Index all woocommerce variations.
 */
add_filter('facetwp_enable_product_variations', '__return_true');

/**
 * Index out of stock products.
 */
add_filter('facetwp_index_all_products', '__return_true');

/**
 * Modify the sort options available.
 */
add_filter('facetwp_sort_options', function ($options, $params) {
    unset($options['title_desc']);
    return $options;
}, 10, 2);

/**
 * Identify the main WP_Query when manually specified.
 */
add_filter('facetwp_is_main_query', function ($is_main_query, $query) {
    if (isset($query->query_vars['facetwp'])) {
        $is_main_query = true;
    }
    return $is_main_query;
}, 10, 2 );

/**
 * Allow for custom post types to be indexed using FacetWP.
 */
// add_filter('facetwp_indexer_query_args', function($args) {
//   $args['post_type'] = ['custom-post-type'];
//   return $args;
// });

/**
 * Modify how fields are indexed.
 */
// add_filter('facetwp_index_row', function($params, $class) {
//   switch ($params['facet_name']) {
//     case 'volume':
//       $params['facet_display_value'] = $params['facet_value'] . ' m<sup>3</sup>';
//       break;
//   }
//   return $params;
// }, 10, 2);

/**
 * Foundation themed pager output.
 */
add_filter('facetwp_pager_html', function ($output, $params) {
    $output = '';
    $page = (int) $params['page'];
    $total_pages = (int) $params['total_pages'];
    if ($total_pages > 1) {
        $output .= '<ul class="pager pagination" role="navigation">';
        $output .= '<li class="pager__item pager__item--previous pagination-previous ' . ($page == 1 ? 'disabled' : '') . '">';
        if ($page > 1) {
            $output .= '<a class="pager__link facetwp-page" data-page="' . ($page - 1) . '">' . __('Previous', 'theme') . '</a>';
        } else {
            $output .= __('Previous', 'theme');
        }
        $output .= '</li>';
        if ( 3 < $page ) {
            $output .= '<li class="pager__item">';
            $output .= '<a class="pager__link facetwp-page" data-page="1">1</a>';
            $output .= '</li>';
            $output .= '<li class="pager__item ellipsis"></li>';
        }
        for ($i = 2; $i > 0; $i--) {
            if (($page - $i) > 0) {
                $output .= '<li class="pager__item">';
                $output .= '<a class="pager__link facetwp-page" data-page="' . ($page - $i) . '">' . ($page - $i) . '</a>';
                $output .= '</li>';
            }
        }
        // Current page
        $output .= '<li class="pager__item pager__item--current">';
        $output .= '<span class="current">' . $page . '</span>';
        for ($i = 1; $i <= 2; $i++) {
            if ($total_pages >= ($page + $i)) {
                $output .= '<li class="pager__item">';
                $output .= '<a class="pager__link facetwp-page" data-page="' . ($page + $i) . '">' . ($page + $i) . '</a>';
                $output .= '</li>';
            }
        }
        if ( $total_pages > ( $page + 2 ) ) {
            $output .= '<li class="pager__item ellipsis"></li>';
            $output .= '<li class="pager__item">';
            $output .= '<a class="pager__link facetwp-page" data-page="' . $total_pages . '">' . $total_pages . '</a>';
            $output .= '</li>';
        }
        $output .= '<li class="pager__item pager__item--next pagination-next ' . ($page == $total_pages ? 'disabled' : '') . '">';
        if ($page < $total_pages) {
            $output .= '<a class="pager__link facetwp-page" data-page="' . ($page + 1) . '">' . __('Next', 'theme') . '</a>';
        } else {
            $output .= __('Next', 'theme');
        }
        $output .= '</li>';
        $output .= '</ul>';
    }
    return $output;
}, 10, 2);
