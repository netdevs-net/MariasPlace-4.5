<?php
/**
 * @version 3.0
 */

namespace GeminiLabs\SiteReviews\Addon\Woocommerce;

class Notice
{
    /**
     * @var array
     */
    protected $notices;

    public function __construct()
    {
        $this->notices = [];
    }

    /**
     * @param string $type
     * @param string|array|\WP_Error $message
     * @return void
     */
    public function add($type, $message)
    {
        if (is_wp_error($message)) {
            $message = $message->get_error_message();
        }
        if (is_array($message)) {
            $message = array_reduce($message, function ($carry, $line) {
                return $carry.wpautop($line);
            });
        }
        $this->notices[] = [
            'message' => $message,
            'type' => $type,
        ];
    }

    /**
     * @param string|array|\WP_Error $message
     * @return void
     */
    public function addError($message)
    {
        $this->add('error', $message);
    }

    /**
     * @param string|array|\WP_Error $message
     * @return void
     */
    public function addSuccess($message)
    {
        $this->add('success', $message);
    }

    /**
     * @param string|array|\WP_Error $message
     * @return void
     */
    public function addWarning($message)
    {
        $this->add('warning', $message);
    }

    /**
     * @return string
     */
    public function build()
    {
        if ($notices = $this->get()) {
            return array_reduce($notices, function ($carry, $args) {
                return $carry.sprintf('<div class="notice notice-%s is-dismissible">%s</div>',
                    $args['type'],
                    $args['message']
                );
            });
        }
        return '';
    }

    /**
     * @return array
     */
    public function get()
    {
        $notices = array_map('unserialize', array_unique(array_map('serialize', $this->notices)));
        usort($notices, function ($a, $b) {
            $order = ['error', 'warning', 'info', 'success'];
            return array_search($a['type'], $order) - array_search($b['type'], $order);
        });
        return $notices;
    }

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }
}
