<?php

namespace GeminiLabs\SiteReviews\Addon\Woocommerce;

use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\Controller;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\ExperimentsController;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\ProductController;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\RestApiController;
use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;

class Hooks extends AddonHooks
{
    public $experiments;
    public $product;
    public $restapi;

    /**
     * @return void
     */
    public function run()
    {
        parent::run();
        if ($this->reviewsEnabled()) {
            remove_action('comment_post', ['WC_Comments', 'add_comment_purchase_verification'], 10);
            remove_action('wp_update_comment_count', ['WC_Comments', 'clear_transients'], 10);
            remove_filter('comments_open', ['WC_Comments', 'comments_open'], 10);
            add_filter('site-reviews/enqueue/public/inline-styles', [$this->controller, 'filterInlineStyles'], 20);
            add_filter('option_woocommerce_enable_review_rating', [$this->controller, 'filterOptionEnableReviewRating']);
            add_filter('option_woocommerce_review_rating_required', [$this->controller, 'filterOptionReviewRatingRequired']);
            add_filter('get_default_comment_status', [$this->controller, 'filterProductCommentStatus'], 10, 3);
            add_filter('woocommerce_get_settings_products', [$this->controller, 'filterProductSettings'], 10, 2);
            add_filter('site-reviews/defaults/review/defaults', [$this->controller, 'filterReviewDefaultsArray']);
            add_filter('site-reviews/review/call/product', [$this->controller, 'filterReviewProductMethod']);
            add_filter('site-reviews/build/template/review', [$this->controller, 'filterReviewTemplate']);
            add_filter('site-reviews/review/tag/verified', [$this->controller, 'filterReviewVerifiedTag']);
            add_filter('site-reviews/config/inline-styles', [$this->controller, 'filterStarImages'], 20);
            add_filter('site-reviews/review/call/isVerified', [$this->controller, 'isReviewVerified']);
            add_action('elementor/widgets/register', [$this->controller, 'registerElementorWidgets'], 20);
            add_action('widgets_init', [$this->controller, 'registerWidgets'], 20);
            add_action('woocommerce_register_post_type_product', [$this->controller, 'removeWoocommerceReviews']);
            add_action('woocommerce_product_options_advanced', [$this->controller, 'renderProductOptions']);
            add_action('site-reviews/review/created', [$this->controller, 'verifyReview'], 20);
            add_filter('comments_template', [$this->product, 'filterCommentsTemplate'], 50);
            add_filter('woocommerce_product_get_rating_html', [$this->product, 'filterGetRatingHtml'], 10, 3);
            add_filter('woocommerce_get_star_rating_html', [$this->product, 'filterGetStarRatingHtml'], 10, 3);
            add_filter('woocommerce_product_get_average_rating', [$this->product, 'filterProductAverageRating'], 10, 2);
            add_filter('woocommerce_product_query_meta_query', [$this->product, 'filterProductMetaQuery'], 20);
            add_filter('woocommerce_get_catalog_ordering_args', [$this->product, 'filterProductPostClauses'], 20, 2);
            add_filter('woocommerce_product_get_rating_counts', [$this->product, 'filterProductRatingCounts'], 10, 2);
            add_filter('woocommerce_product_get_review_count', [$this->product, 'filterProductReviewCount'], 10, 2);
            add_filter('woocommerce_product_tabs', [$this->product, 'filterProductTabs']);
            add_filter('woocommerce_product_query_tax_query', [$this->product, 'filterProductTaxQuery'], 20);
            add_filter('woocommerce_structured_data_product', [$this->product, 'filterStructuredData'], 10, 2);
            add_filter('woocommerce_top_rated_products_widget_args', [$this->product, 'filterWidgetArgsTopRatedProducts']);
            add_filter('wc_get_template', [$this->product, 'filterWoocommerceTemplate'], 20, 2);
            add_action('site-reviews-woocommerce/render/loop/rating', [$this->product, 'renderLoopRating'], 5);
            add_action('site-reviews-woocommerce/render/product/reviews', [$this->product, 'renderReviews']);
            add_action('woocommerce_single_product_summary', [$this->product, 'renderTitleRating']);
            add_filter('rest_endpoints', [$this->restapi, 'filterRestEndpoints']);
            add_filter('woocommerce_rest_api_get_rest_namespaces', [$this->restapi, 'filterRestNamespaces']);
            add_filter('woocommerce_rest_check_permissions', [$this->restapi, 'filterRestPermissions'], 10, 4);
            add_filter('site-reviews/query/sql/join', [$this->restapi, 'filterSqlJoin'], 10, 3);
            add_filter('site-reviews/query/sql/order-by', [$this->restapi, 'filterSqlOrderBy'], 10, 3);
            if ($this->addon()->option('experiments', true, 'bool')) {
                add_filter('get_comment_metadata', [$this->experiments, 'filterProductCommentMeta'], 20, 4);
                add_filter('comments_pre_query', [$this->experiments, 'filterProductCommentsQuery'], 20, 2);
            }
        }
    }

    /**
     * @return mixed
     */
    protected function addon()
    {
        return glsr(Application::class);
    }

    /**
     * @return mixed
     */
    protected function controller()
    {
        $this->experiments = glsr(ExperimentsController::class);
        $this->product = glsr(ProductController::class);
        $this->restapi = glsr(RestApiController::class);
        return glsr(Controller::class);
    }

    /**
     * @return bool
     */
    protected function reviewsEnabled()
    {
        return 'yes' === get_option('woocommerce_enable_reviews', 'yes') && $this->addon()->option('enabled', false, 'bool');
    }
}
