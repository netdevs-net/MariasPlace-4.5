<?php

namespace GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers;

use GeminiLabs\SiteReviews\Addon\Woocommerce\Application;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Elementor\Widgets\ProductRating;
use GeminiLabs\SiteReviews\Addon\Woocommerce\ReviewVerifiedTag;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Template;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Widgets\WidgetRatingFilter;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Widgets\WidgetRecentReviews;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Widgets\WidgetTopRatedProducts;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\BlackHole;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class Controller extends AddonController
{
    const VERIFIED_META_KEY = '_verified';

    protected $addon;

    /**
     * @param string $css
     * @return string
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles($css)
    {
        $css .= '.glsr-review-verified{align-items:center;color:#0f834d;display:flex;margin-left:.5em;}'; // verified badge style
        $css .= '.glsr-pagination .nav-links{display:flex;flex-wrap:wrap;}'; // fix pagination links
        $css .= 'ul.glsr li a{display:flex;justify-content:space-between;}'; // fix rating filter widget
        if ('woocommerce' === glsr_get_option('addons.'.Application::SLUG.'.style')) {
            $css .= '.glsr-bar-background-percent{background-color:#96588A!important;}';
        }
        if ('black' === glsr_get_option('addons.'.Application::SLUG.'.style')) {
            $css .= '.glsr-bar-background-percent{background-color:#212121!important;}';
        }
        return $css;
    }

    /**
     * @param string $value
     * @return string
     * @filter option_woocommerce_enable_review_rating
     */
    public function filterOptionEnableReviewRating($value)
    {
        return 'yes';
    }

    /**
     * @param string $value
     * @return string
     * @filter option_woocommerce_review_rating_required
     */
    public function filterOptionReviewRatingRequired($value)
    {
        return 'yes';
    }

    /**
     * @param string $status
     * @param string $postType
     * @param string $commentType
     * @return string
     * @filter get_default_comment_status
     */
    public function filterProductCommentStatus($status, $postType, $commentType)
    {
        if ('product' === $postType && 'comment' === $commentType) {
            return 'open';
        }
        return $status;
    }

    /**
     * @param array $settings
     * @param string $section
     * @return array
     * @filter woocommerce_get_settings_products
     */
    public function filterProductSettings($settings, $section)
    {
        if (empty($section)) {
            $disabled = ['woocommerce_enable_review_rating', 'woocommerce_review_rating_required'];
            foreach ($settings as &$setting) {
                if (in_array(Arr::get($setting, 'id'), $disabled)) {
                    $setting = Arr::set($setting, 'custom_attributes.disabled', true);
                    $setting['desc'] = sprintf('%s <span class="required">(%s)</span>', $setting['desc'], _x('managed by Site Reviews', 'admin-text', 'site-reviews-woocommerce'));
                }
            }
        }
        return $settings;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/review/defaults
     */
    public function filterReviewDefaultsArray(array $defaults)
    {
        $defaults['verified'] = '';
        return $defaults;
    }

    /**
     * @return \WC_Product|BlackHole
     * @filter site-reviews/review/call/product
     */
    public function filterReviewProductMethod(Review $review) {
        if ($product = wc_get_product(Arr::get($review->assigned_posts, 0))) {
            return $product;
        }
        return glsr(BlackHole::class, ['alias' => 'Triggered by $review->product()']);
    }

    /**
     * @param string $template
     * @return string
     * @filter site-reviews/build/template/review
     */
    public function filterReviewTemplate($template)
    {
        if (false === strpos($template, '{{ verified }}')) {
            $template = str_replace('{{ author }}', '{{ author }} {{ verified }}', $template);
        }
        return $template;
    }

    /**
     * @return string
     * @filter site-reviews/review/tag/verified
     */
    public function filterReviewVerifiedTag()
    {
        return ReviewVerifiedTag::class;
    }

    /**
     * @param array $config
     * @return array
     * @filter site-reviews/config/inline-styles
     */
    public function filterStarImages($config)
    {
        if ($style = glsr_get_option('addons.'.Application::SLUG.'.style')) {
            $config[':star-empty'] = $this->addon->url('assets/stars/'.$style.'/star-empty.svg');
            $config[':star-full'] = $this->addon->url('assets/stars/'.$style.'/star-full.svg');
            $config[':star-half'] = $this->addon->url('assets/stars/'.$style.'/star-half.svg');
        }
        return $config;
    }

    /**
     * @return bool
     * @filter site-reviews/review/call/isVerified
     */
    public function isReviewVerified(Review $review) {
        $verified = get_post_meta($review->ID, static::VERIFIED_META_KEY, true);
        return '' === $verified
            ? $this->verifyReview($review)
            : (bool) $verified;
    }

    /**
     * @return void
     * @action elementor/widgets/register
     */
    public function registerElementorWidgets()
    {
        $widgets = \Elementor\Plugin::instance()->widgets_manager;
        $widgets->unregister('woocommerce-product-rating');
        if (class_exists('ElementorPro\Modules\Woocommerce\Widgets\Product_Rating')) {
            $widgets->register(new ProductRating());
        }
    }

    /**
     * @return void
     * @action widgets_init
     */
    public function registerWidgets()
    {
        unregister_widget('WC_Widget_Recent_Reviews');
        unregister_widget('WC_Widget_Rating_Filter');
        register_widget(WidgetRecentReviews::class);
        register_widget(WidgetRatingFilter::class);
    }

    /**
     * @param array $args
     * @return array
     * @action woocommerce_register_post_type_product
     */
    public function removeWoocommerceReviews($args)
    {
        if (array_key_exists('supports', $args)) {
            $args['supports'] = array_diff($args['supports'], ['comments']);
        }
        return $args;
    }

    /**
     * @return void
     * @action woocommerce_product_options_advanced
     */
    public function renderProductOptions()
    {
        global $product_object;
        glsr(Template::class)->render('product-options', [
            'product' => $product_object,
        ]);
    }

    /**
     * @return void|bool
     * @action site-reviews/review/created
     */
    public function verifyReview(Review $review)
    {
        $review = glsr_get_review($review->ID); // load a fresh instance of the review
        $verified = false;
        foreach ($review->assigned_posts as $postId) {
            if ('product' === get_post_type($postId)) {
                $verified = wc_customer_bought_product($review->email, $review->author_id, $postId);
                break;
            }
        }
        add_post_meta($review->ID, static::VERIFIED_META_KEY, (int) $verified, true);
        return $verified;
    }

    /**
     * @return void
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}
