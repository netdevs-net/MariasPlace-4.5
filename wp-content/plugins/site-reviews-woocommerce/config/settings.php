<?php

$addon = glsr('Addon\Woocommerce\Application');

return [
    'settings.addons.'.$addon->slug.'.enabled' => [
        'default' => 'no',
        'label' => _x('Enable Integration?', 'admin-text', 'site-reviews-woocommerce'),
        'tooltip' => sprintf(_x('This will completely replace the default Woocommerce review system with Site Reviews. If you have existing Woocommerce comment reviews, you may need to first export them to a CSV file, and then import them using the %s tool.', 'admin-text', 'site-reviews-woocommerce'),
            sprintf('<a data-expand="tools-import-reviews" href="%s">%s</a>', glsr_admin_url('tools', 'general'), _x('Import Reviews', 'admin-text', 'site-reviews-woocommerce'))
        ),
        'type' => 'yes_no',
    ],
    'settings.addons.'.$addon->slug.'.style' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
        ],
        'label' => _x('Rating Style', 'admin-text', 'site-reviews-woocommerce'),
        'options' => [
            '' => _x('Site Reviews (yellow)', 'admin-text', 'site-reviews-woocommerce'),
            'black' => _x('Woocommerce (black)', 'admin-text', 'site-reviews-woocommerce'),
            'woocommerce' => _x('Woocommerce (purple)', 'admin-text', 'site-reviews-woocommerce'),
        ],
        'tooltip' => _x('This changes the colour of the stars and the summary bars', 'admin-text', 'site-reviews-woocommerce'),
        'type' => 'select',
    ],
    'settings.addons.'.$addon->slug.'.summary' => [
        'class' => 'large-text',
        'default' => '[site_reviews_summary assigned_posts="post_id" hide="rating"]',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
        ],
        'label' => _x('Summary Shortcode', 'admin-text', 'site-reviews-woocommerce'),
        'tooltip' => _x('Enter the summary shortcode used on the product page (the schema option is unnecessary)', 'admin-text', 'site-reviews-woocommerce'),
        'type' => 'text',
    ],
    'settings.addons.'.$addon->slug.'.reviews' => [
        'class' => 'large-text',
        'default' => '[site_reviews assigned_posts="post_id" hide="assigned_links,title" pagination="ajax"]',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
        ],
        'label' => _x('Reviews Shortcode', 'admin-text', 'site-reviews-woocommerce'),
        'tooltip' => _x('Enter the reviews shortcode used on the product page (the schema option is unnecessary)', 'admin-text', 'site-reviews-woocommerce'),
        'type' => 'text',
    ],
    'settings.addons.'.$addon->slug.'.form' => [
        'class' => 'large-text',
        'default' => '[site_reviews_form assigned_posts="post_id" hide="title"]',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
        ],
        'label' => _x('Form Shortcode', 'admin-text', 'site-reviews-woocommerce'),
        'tooltip' => _x('Enter the form shortcode used on the product page', 'admin-text', 'site-reviews-woocommerce'),
        'type' => 'text',
    ],
    'settings.addons.'.$addon->slug.'.sorting' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
        ],
        'description' => sprintf('<span class="dashicons dashicons-arrow-right"></span> %s<br><span class="dashicons dashicons-arrow-right"></span> %s',
            sprintf('<a href="https://www.xkcd.com/937/" target="_blank">%s</a>', _x('The problem with average star ratings', 'admin-text', 'site-reviews-woocommerce')),
            sprintf('<a href="https://fulmicoton.com/posts/bayesian_rating/" target="_blank">%s</a>', _x('Of bayesian average and star ratings', 'admin-text', 'site-reviews-woocommerce'))
        ),
        'label' => _x('Product Sorting', 'admin-text', 'site-reviews-woocommerce'),
        'options' => [
            '' => _x('Average Rating', 'admin-text', 'site-reviews-woocommerce'),
            'bayesian' => _x('Bayesian Average Rating', 'admin-text', 'site-reviews-woocommerce'),
        ],
        'tooltip' => _x('This is the method used to sort products by rating on the shop page.', 'admin-text', 'site-reviews-woocommerce'),
        'type' => 'select',
    ],
    'settings.addons.'.$addon->slug.'.display_empty' => [
        'default' => 'no',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
        ],
        'label' => _x('Display empty ratings?', 'admin-text', 'site-reviews-woocommerce'),
        'tooltip' => _x('This will display the rating stars even if the product has no reviews.', 'admin-text', 'site-reviews-woocommerce'),
        'type' => 'yes_no',
    ],
    'settings.addons.'.$addon->slug.'.experiments' => [
        'default' => 'no',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
        ],
        'label' => _x('Enable Experiments?', 'admin-text', 'site-reviews-woocommerce'),
        'description' => _x('Access new and experimental features before they are officially released. As these features are still in development, they are likely to change, evolve or even be removed altogether.', 'admin-text', 'site-reviews-woocommerce'),
        'tooltip' => _x('To use an experiment on your site, click on the dropdown next to it and switch to Active. You can always deactivate them at any time.', 'admin-text', 'site-reviews-woocommerce'),
        'type' => 'yes_no',
    ],
    'settings.addons.'.$addon->slug.'.experiment.wp_comments' => [
        'default' => 'inactive',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.enabled' => ['yes'],
            'settings.addons.'.$addon->slug.'.experiments' => ['yes'],
        ],
        'options' => [
            'active' => '🟢 &nbsp;'._x('Active', 'admin-text', 'site-reviews-woocommerce'),
            'inactive' => '⚪️ &nbsp;'._x('Inactive', 'admin-text', 'site-reviews-woocommerce'),
        ],
        'label' => _x('Filter Comment Queries', 'admin-text', 'site-reviews-woocommerce'),
        'tooltip' => _x('This will filter the output of the wp_comments() function when it\'s used to query Woocommerce product reviews.', 'admin-text', 'site-reviews-woocommerce'),
        'description' => sprintf('%s<br><span class="glsr-experimental">%s</span>',
            _x('This may fix issues with other plugins which query Woocommerce product reviews. Keep in mind that activating this experiment may also cause conflicts with incompatible plugins.', 'admin-text', 'site-reviews-woocommerce'),
            _x('Status: Beta', 'admin-text', 'site-reviews-woocommerce')
        ),
        'type' => 'select',
    ],
];
