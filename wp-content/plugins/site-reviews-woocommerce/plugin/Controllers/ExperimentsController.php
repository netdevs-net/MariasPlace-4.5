<?php

namespace GeminiLabs\SiteReviews\Addon\Woocommerce\Controllers;

use GeminiLabs\SiteReviews\Addon\Woocommerce\Application;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

class ExperimentsController
{
    /**
     * @var Application
     */
    protected $addon;

    public function __construct()
    {
        $this->addon = glsr(Application::class);
    }

    /**
     * @param mixed $value
     * @param int $objectId
     * @param string $metaKey
     * @param bool $single
     * @return mixed|null
     * @filter get_comment_metadata
     */
    public function filterProductCommentMeta($value, $objectId, $metaKey, $single)
    {
        if (Review::isReview($objectId)) {
            $review = glsr_get_review($objectId);
            $value = $review[$metaKey];
            return $single ? $value : [$value];
        }
        return $value;
    }

    /**
     * @param array|int|null $data
     * @param \WP_Comment_Query $query
     * @return array|int|null
     * @filter comments_pre_query
     */
    public function filterProductCommentsQuery($data, $query)
    {
        if ('active' !== $this->addon->option('experiment.wp_comments')) {
            return $data;
        }
        $vars = glsr()->args($query->query_vars);
        $isProductQuery = 'review' === $vars->type || ('product' === $vars->post_type || 'product' === get_post_type($vars->post_id));
        if (!$isProductQuery) {
            return $data;
        }
        $args = $this->getReviewArgs($vars);
        $reviews = glsr_get_reviews($args);
        return true === $vars->count
            ? $this->getReviewsCount($reviews)
            : $this->getReviews($reviews);
    }

    /**
     * @return array
     */
    protected function getReviewArgs(Arguments $args)
    {
        $statuses = [
            'all' => 'all',
            'approve' => 'approved',
            'hold' => 'unapproved',
        ];
        $params = [
            'offset' => $args->offset,
            'page' => $args->paged,
            'per_page' => $args->get('number', 10),
            'status' => glsr_get($statuses, $args->status, 'approved'),
        ];
        if (!empty($args->post_id)) {
            $params['assigned_posts'] = $args->post_id;
        } else {
            $params['assigned_posts'] = 'product';
        }
        return $params;
    }

    /**
     * @return array
     */
    protected function getReviews(Reviews $reviews)
    {
        $data = [];
        foreach ($reviews as $review) {
            $data[] = new \WP_Comment((object) [
                'comment_agent' => '',
                'comment_approved' => (string) $review->is_approved,
                'comment_author' => $review->name,
                'comment_author_email' => $review->email,
                'comment_author_IP' => $review->ip_address,
                'comment_author_url' => '',
                'comment_content' => $review->content,
                'comment_date' => $review->date,
                'comment_date_gmt' => $review->date_gmt,
                'comment_ID' => $review->ID,
                'comment_karma' => 0,
                'comment_parent' => 0,
                'comment_post_ID' => glsr_get($review->assigned_posts, 0),
                'comment_type' => 'review',
                'user_id' => $review->user_id,
            ]);
        }
        return $data;
    }

    /**
     * @return int
     */
    protected function getReviewsCount(Reviews $reviews)
    {
        return $reviews->total;
    }
}
