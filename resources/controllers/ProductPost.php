<?php

/**
 * @file
 * Contains App\Controller\ProductPost class used for WooCommerce products.
 */

namespace App\Controller;

use App;
use Timber;
use TimberHelper;
use WC_Product_Variable;

class ProductPost extends Post
{

    public $product;
    public $variations;
    public $attributes;
    public $animal;

    public function __construct($pid = null)
    {
        parent::__construct($pid);
        $this->product = WC()->product_factory->get_product($this->ID);
    }

    /**
     * Simplified review form using Foundation classes.
     *
     * @see https://github.com/woocommerce/woocommerce/blob/master/templates/single-product-reviews.php
     */
    public function get_review_form()
    {
        if (get_option('woocommerce_review_rating_verification_required') !== 'no' && !wc_customer_bought_product('', get_current_user_id(), $this->product->get_id())) {
            return '<p class="woocommerce-verification-required">' . __('Only logged in customers who have purchased this product may leave a review.', 'woocommerce') . '</p>';
        }
        $commenter = wp_get_current_commenter();
        $comment_form = array(
            'title_reply'          => __('Add a review', 'woocommerce'),
            'title_reply_to'       => __('Leave a Reply to %s', 'woocommerce'),
            'title_reply_before'   => '<h5 id="reply-title" class="comment-reply-title">',
            'title_reply_after'    => '</h5>',
            'comment_notes_before'  => '',
            'comment_notes_after'  => '',
            'fields'               => array(
                'author' => ''
                    . '<div class="grid-x grid-margin-x">'
                    . '<div class="cell medium-auto comment-form-author">'
                    . '<label for="author">' . esc_html__('Name', 'woocommerce') . ' <span class="form-required">*</span></label>'
                    . '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" aria-required="true" required />'
                    . '</div>',
                'email' => ''
                    . '<div class="cell medium-auto comment-form-email">'
                    . '<label for="email">' . esc_html__('Email', 'woocommerce') . ' <span class="form-required">*</span></label>'
                    . '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-required="true" required />'
                    . '</div>'
                    . '</div>',
            ),
            'label_submit'  => __('Submit', 'woocommerce'),
            'class_submit'  => 'button button--primary',
            'logged_in_as'  => '',
            'comment_field' => '',
        );
        if ($account_page_url = wc_get_page_permalink('myaccount')) {
            $comment_form['must_log_in'] = '<div class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a review.', 'woocommerce'), esc_url($account_page_url)) . '</div>';
        }
        if (get_option('woocommerce_enable_review_rating') === 'yes') {
            $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__('Your rating', 'woocommerce') . '</label><select name="rating" id="rating" aria-required="true" required>
                <option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
                <option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
                <option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
                <option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
                <option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
                <option value="1">' . esc_html__('Very poor', 'woocommerce') . '</option>
            </select></div>';
        }
        $comment_form['comment_field'] .= '<div class="comment-form-comment"><label for="comment">' . esc_html__('Your review', 'woocommerce') . ' <span class="form-required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></div>';

        return comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
    }

    public function get_upsell_products()
    {
        if (isset($this->upsell_products)) {
            return $this->upsell_products;
        }

        $cid = $this->generate_cid('upsell');
        $product = $this->product;

        $this->upsell_products = TimberHelper::transient($cid, function () use ($product) {
            return (new Timber\PostQuery($product->get_upsell_ids()))->get_posts();
        }, App\config('timber.cache') ? $this->cache_duration : false);

        return $this->upsell_products;
    }

    public function get_related_products($posts_per_page = 4, $orderby = 'rand', $order = 'desc')
    {
        $cid = $this->generate_cid('related', func_get_args());

        if (!isset($this->related_products)) {
            $this->related_products = [];
        }
        if (isset($this->related_products[$cid])) {
            return $this->related_products[$cid];
        }

        $product = $this->product;
        $this->related_products[$cid] = TimberHelper::transient($cid, function () use ($product, $posts_per_page, $orderby, $order) {
            // Get visble related products then sort them at random.
            $related_products = array_filter(array_map('wc_get_product', wc_get_related_products($product->get_id(), $posts_per_page, $product->get_upsell_ids())), 'wc_products_array_filter_visible');
            $related_products = wc_products_array_orderby($related_products, $orderby, $order);
            foreach ($related_products as $idx => $related) {
                $related_products[$idx] = Timber\PostGetter::get_post($related->get_id());
            }
            return $related_products;
        }, App\config('timber.cache') ? $this->cache_duration : false);

        return $this->related_products[$cid];
    }

    public function set_loop_product()
    {
        global $product;
        if (is_woocommerce()) {
            $product = $this->product;
        }
    }

    public function get_product_images()
    {
        return TimberHelper::ob_function('woocommerce_show_product_images');
    }

    public function get_add_to_cart()
    {
        return TimberHelper::ob_function('woocommerce_template_single_add_to_cart');
    }

    public function get_attributes($name = null)
    {
        if (!isset($this->attributes)) {
            $this->attributes = [];
            $attributes = $this->product->get_attributes();
            foreach ($attributes as $idx => $attribute) {
                if ($attribute->is_taxonomy()) {
                    $this->attributes[$idx] = $this->get_terms($attribute->get_name());
                } else {
                    $this->attributes[$idx] = $attribute->get_options();
                }
            }
        }
        if (isset($name)) {
            return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
        }
        return $this->attributes;
    }

    protected function generate_cid($prefix, $args = [])
    {
        return $prefix . '_' . $this->product_get_id() . '_' . substr(md5(json_encode($args)), 0, 6);
    }
}
