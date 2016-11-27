<?php

namespace Genero\Sage\WpAllImport;

/**
 * Get a comma separated list of URLs to <img>-elements found in a HTML string.
 */
function get_image_urls($html)
{
    preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $html, $matches);
    if (!empty($matches[1])) {
        return str_replace("'", "", implode("', '", $matches[1]));
    }
    return '';
}

/**
 * Get the first image URL found in a HTML string.
 */
function wpallimport_get_first_image_url($html)
{
    return current(explode(',', wpallimport_get_image_urls($html)));
}

/**
 * Strip away common styling attributes and element from imported content.
 */
function strip_content_styling($html)
{
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXPath($dom);
    // Strip away all style attributes.
    foreach ($xpath->query('//*[@style]') as $node) {
        $node->removeAttribute('style');
    }
    // Remove all <span>-tags.
    foreach ($xpath->query('//span') as $node) {
        remove_wrapper_tag($node);
    }
    // Remove all <a>-tags linking to remote images.
    foreach ($xpath->query('//a[@class="fancybox"]') as $node) {
        remove_wrapper_tag($node);
    }
    $html = $dom->saveHTML($dom->documentElement);
    return $html;
}

/**
 * Helper function to remove an HTML-tag but keep it's content.
 * @private
 */
function remove_wrapper_tag($node)
{
    while($node->hasChildNodes()) {
        $child = $node->removeChild($node->firstChild);
        $node->parentNode->insertBefore($child, $node);
    }
    $node->parentNode->removeChild($node);
}
