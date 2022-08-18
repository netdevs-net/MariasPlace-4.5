=== Site Reviews: Woocommerce ===
Contributors: pryley, geminilabs
Donate link: https://www.paypal.me/pryley
Tags: Site Reviews, Woocommerce
Tested up to: 6.0
Requires at least: 5.8
Requires PHP: 7.0
Stable tag: 2.3.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrate Site Reviews with Woocommerce.

== Description ==

= Minimum plugin requirements =

If your server and website does not meet these minimum requirements, the plugin will automatically deactivate and a notice will appear explaining why.

- WordPress 5.8
- PHP 7.0

== Installation ==

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 2.3.0 (2022-05-30) =

- Added setting to display or hide empty ratings on products

= 2.2.0 (2022-04-16) =

- Fixed performance issues with websites with hundreds of products
- Requires Site Reviews 5.24

= 2.1.1 (2022-04-14) =

- Fixed `get_comment_metadata` usage when "Filter Comment Queries" setting is enabled

= 2.1.0 (2022-04-14) =

- Added experimental "Filter Comment Queries" setting

= 2.0.0 (2022-04-13) =

- Fixed batch API requests
- Fixed compatibility with Woocommerce v6.4.0 Store API
- Requires PHP v7.0 or higher
- Requires Site Review v5.23.0 or higher
- Requires Woocommerce v6.4.0 or higher
- Requires WordPress v5.8.0 or higher

= 1.6.0 (2022-01-26) =

- Added support for the Elementor Pro "Product Rating" widget
- Updated the WordPress required version to 5.8

= 1.5.0 (2021-12-15) =

- Added compatibilty for Woocommerce v6.*.*

= 1.4.4 (2021-11-15) =

- Fixed add-on updater
- Fixed deactivation notice on WordPress settings pages

= 1.4.2 (2021-11-10) =

- Fixed admin urls
- Fixed bug where reviews are disabled by default for new products
- Requires Site Reviews >= 5.17.0

= 1.3.3 (2021-04-05) =

- Fixed rating values in the Woocommerce Products widget

= 1.3.2 (2021-03-20) =

- Added setting tooltips

= 1.3.1 (2021-03-18) =

- Fixed translated Woocommerce template strings, they should now inherit the Woocommerce translation strings

= 1.3.0 (2021-02-10) =

- Added support for Woocommerce 5.0
- Fixed the integration with the Woocommerce product review schema
- Fixed the "Filter Products by Rating" widget
- Fixed the `/wc-analytics/products/reviews` REST API routes

= 1.2.2 (2021-01-29) =

- Fixed an internal bug

= 1.2.1 (2021-01-28) =

- Fixed a SQL error

= 1.2.0 (2021-01-25) =

__Note__: The review links in the "Recent Product Reviews" widget do not yet work; this will be addressed with Site Reviews 5.6!

- Added a black star option
- Added a setting to sort reviews on the shop page by the bayesian average rating
- Added integration with the Woocommerce Analytics
- Added integration with the Woocommerce Blocks and Widgets
- Added integration with the Woocommerce REST API
- Fixed an issue preventing the "Enable reviews" product option from syncing in multilingual products
- Fixed an issue which disabled Woocommerce auto-updates

= 1.1.2 (2021-01-12) =

- Updated the "WC tested up to" in the readme to Woocommerce 4.9.0

= 1.1.1 (2021-01-08) =

- Fixed override of `wc_get_template('single-product-reviews.php');`

= 1.1.0 (2021-01-08) =

- Added the `site-reviews-woocommerce/render/loop/rating` action hook which can be used to display the product rating elsewhere in the shop product loop.
- Added the `site-reviews-woocommerce/render/product/reviews` action hook which can be used to display the single-product reviews section elsewhere on the page.
- Fixed the single product reviews template

= 1.0.1 (2021-01-07) =

- Fixed review pagination link styling in some Woocommerce themes
- Fixed the loop rating on the shop page of some Woocommerce themes

= 1.0.0 (2020-12-22) =

- First release
