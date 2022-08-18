<?php

namespace GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers;

use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\StoreApi;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\AdminApi\ProductReviews;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\BlocksApi\ProductReviewSchema;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\BlocksApi\ProductReviewsRoute;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\RestApi\ProductReviewsController;
use GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers\RestApi\ReportReviewsTotalsController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class RestApiController
{
    /**
     * @param array $endpoints
     * @filter rest_endpoints
     */
    public function filterRestEndpoints($endpoints)
    {
        foreach ($endpoints as $route => &$endpoint) {
            if ('/wc-analytics/products/reviews' === $route) {
                $this->modifyAnalyticsReviewsEndpoint($endpoint);
            }
            if ('/wc-analytics/products/reviews/(?P<id>[\d]+)' === $route) {
                $this->modifyAnalyticsReviewEndpoint($endpoint);
            }
            if ('/wc-analytics/products/reviews/batch' === $route) {
                $this->modifyAnalyticsBatchEndpoint($endpoint);
            }
            if ('/wc/store/v1/products/reviews' === $route) {
                $this->modifyStoreEndpoint($endpoint);
            }
        }
        return $endpoints;
    }

    /**
     * @param array $namespaces
     * @filter woocommerce_rest_api_get_rest_namespaces
     */
    public function filterRestNamespaces($namespaces)
    {
        $namespaces['wc/v3']['product-reviews'] = ProductReviewsController::class;
        $namespaces['wc/v3']['reports-reviews-totals'] = ReportReviewsTotalsController::class;
        return $namespaces;
    }

    /**
     * @param bool $hasPermission
     * @param string $context
     * @param int $objectId
     * @param string $permissionType
     * @return bool
     * @filter woocommerce_rest_check_permissions
     */
    public function filterRestPermissions($hasPermission, $context, $objectId, $permissionType)
    {
        if ('product_review' === $permissionType) {
            $contexts = [
                'read' => 'edit_posts',
                'create' => 'create_posts',
                'edit' => 'edit_posts',
                'delete' => 'delete_posts',
                'batch' => '', // disabled
            ];
            if (isset($contexts[$context])) {
                return glsr()->can($contexts[$context]);
            }
        }
        return $hasPermission;
    }

    /**
     * @param array $join
     * @param string $handle
     * @param \GeminiLabs\SiteReviews\Database\Query $query
     * @return array
     * @filter site-reviews/query/sql/join
     */
    public function filterSqlJoin($join, $handle, $query)
    {
        if (Str::endsWith('rating', $query->args['orderby'])) {
            $join['woo_orderby_rating'] = "INNER JOIN {$query->db->posts} AS p ON r.review_id = p.ID";
        }
        return $join;
    }

    /**
     * @param array $orderBy
     * @param string $handle
     * @param \GeminiLabs\SiteReviews\Database\Query $query
     * @return array
     * @filter site-reviews/query/sql/order-by
     */
    public function filterSqlOrderBy($orderBy, $handle, $query)
    {
        if (Str::endsWith('rating', $query->args['orderby'])) {
            return [
                "r.rating {$query->args['order']}",
                "p.post_date_gmt {$query->args['order']}",
            ];
        }
        if (Str::endsWith('date_gmt', $query->args['orderby'])) {
            return [
                "p.post_date_gmt {$query->args['order']}", // ignore pinned reviews
            ];
        }
        return $orderBy;
    }

    /**
     * @param array $endpoint
     * @return void
     */
    protected function modifyAnalyticsBatchEndpoint(&$endpoint)
    {
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key) {
                $endpoint[$key] = [glsr(ProductReviews::class), 'get_public_batch_schema'];
                continue;
            }
            if ('POST, PUT, PATCH' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'batch_items'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'batch_items_permissions_check'];
            }
        }
    }

    /**
     * @param array $endpoint
     * @return void
     */
    protected function modifyAnalyticsReviewEndpoint(&$endpoint)
    {
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key) {
                $endpoint[$key] = [glsr(ProductReviews::class), 'get_public_item_schema'];
                continue;
            }
            if ('DELETE' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'delete_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'delete_item_permissions_check'];
            }
            if ('GET' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'get_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'get_item_permissions_check'];
            }
            if ('POST, PUT, PATCH' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'update_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'update_item_permissions_check'];
            }
        }
    }

    /**
     * @param array $endpoint
     * @return void
     */
    protected function modifyAnalyticsReviewsEndpoint(&$endpoint)
    {
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key) {
                $endpoint[$key] = [glsr(ProductReviews::class), 'get_public_item_schema'];
                continue;
            }
            if ('GET' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'get_items'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'get_items_permissions_check'];
            }
            if ('POST' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'create_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'create_item_permissions_check'];
            }
        }
    }

    /**
     * @param array $endpoint
     * @return void
     */
    protected function modifyStoreEndpoint(&$endpoint)
    {
        $controller = StoreApi::container()->get(SchemaController::class);
        $extend = StoreApi::container()->get(ExtendSchema::class);
        $schema = new ProductReviewSchema($extend, $controller);
        $routes = new ProductReviewsRoute($controller, $schema);
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key) {
                $endpoint[$key] = [$schema, 'get_public_item_schema'];
                continue;
            }
            if (isset($value['callback'])) {
                $endpoint[$key]['callback'] = [$routes, 'get_response'];
            }
        }
    }
}
