<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝.
 *
 * Plugin Name:       Site Reviews: Woocommerce Reviews
 * Plugin URI:        https://niftyplugins.com/plugins/woocommerce
 * Description:       Integrate Site Reviews with your products
 * Version:           2.3.0
 * Author:            Paul Ryley
 * Author URI:        https://niftyplugins.com
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Tested up to:      6.0
 * Text Domain:       site-reviews-woocommerce
 * Domain Path:       languages
 * WC requires at least: 6.4
 * WC tested up to: 6.4
 */
defined('WPINC') || die;

if (!class_exists('GL_Plugin_Check_v7')) {
    require_once __DIR__.'/activate.php';
}
if (!(new GL_Plugin_Check_v7(__FILE__))->canProceed()) {
    return;
}
require_once __DIR__.'/autoload.php';
add_action('site-reviews/addon/register', function ($app) {
    // Overriding the Woocommerce REST API is currently very fragile, 
    // we need to limit Woocommerce compatibilty to v6.4-7.0 as a safety precaution.
    $gatekeeper = new GeminiLabs\SiteReviews\Addon\Woocommerce\Gatekeeper(__FILE__, [
        'site-reviews/site-reviews.php' => 'Site Reviews|5.24|https://wordpress.org/plugins/site-reviews|6.0',
        'woocommerce/woocommerce.php' => 'Woocommerce|6.4|https://wordpress.org/plugins/woocommerce|7.0',
    ]);
    if ($gatekeeper->allows()) {
        $app->singleton(GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\Controller::class);
        $app->singleton(GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\ExperimentsController::class);
        $app->singleton(GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\ProductController::class);
        $app->register(GeminiLabs\SiteReviews\Addon\Woocommerce\Application::class);
    }
});
add_action('site-reviews/addon/update', function ($app) {
    $app->update(GeminiLabs\SiteReviews\Addon\Woocommerce\Application::class, __FILE__);
});
