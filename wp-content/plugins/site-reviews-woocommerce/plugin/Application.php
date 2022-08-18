<?php

namespace GeminiLabs\SiteReviews\Addon\Woocommerce;

use GeminiLabs\SiteReviews\Addons\Addon;
use GeminiLabs\SiteReviews\Helpers\Arr;

final class Application extends Addon
{
    const ID = 'site-reviews-woocommerce';
    const NAME = 'Woocommerce Reviews';
    const SLUG = 'woocommerce';
    const UPDATE_URL = 'https://niftyplugins.com';

    /**
     * @return array
     */
    public function productTerms()
    {
        $query = new \WP_Term_Query([
            'count' => false,
            'fields' => 'id=>name',
            'hide_empty' => false,
            'taxonomy' => 'product_cat',
        ]);
        $terms = $query->terms;
        array_walk($terms, function (&$termName, $termId) {
            $termName = sprintf('%s (%s)', $termName, $termId);
        });
        natcasesort($terms);
        return Arr::prepend($terms, _x('All Product Categories', 'admin-text', 'site-reviews-woocommerce'), '');
    }
}
