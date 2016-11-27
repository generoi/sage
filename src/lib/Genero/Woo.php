<?php

namespace Genero\Sage\Woo;

/**
 * Check if a product exists in the current user's cart.
 */
function is_product_in_cart($product_id)
{
    foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
        $_product = $values['data'];
        if ($product_id == $_product->id) {
            return true;
        }
    }
    return false;
}

/**
 * Check if a product was purchased by the user.
 */
function is_product_purchased_by_user($pid, $uid = null)
{
    $current_user = isset($uid) ? get_user_by('id', $uid) : wp_get_current_user();
    return wc_customer_bought_product($current_user->user_email, $current_user->ID, $pid);
}
