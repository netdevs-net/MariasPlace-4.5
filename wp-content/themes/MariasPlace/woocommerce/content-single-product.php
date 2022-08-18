<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */
?>

<?php
$top_membership_option = get_field('top_membership_option', get_the_ID());
if (!empty($top_membership_option)) :
    echo do_shortcode($top_membership_option);
endif;
?>

<!-- <section class="member_top_sec">
        <div class="membership_top">
                <div class="container">
                        <div class="row">
                                <div class="col-12">
                                        <h2 class="section-main-title text-center py-md-5 py-3">Membership Options</h2>
                                </div>
                        </div>
                </div>
        </div>
        <div class="membership_bottom">
                <div class="container">
                        <div class="row justify-content-center">
                                <div class="col-lg-3 col-sm-4 col-12">
                                        <div class="membership_box">
                                                <div class="image_box text-center d-flex justify-content-center align-items-center">
                                                        <a href="#single_digtal_sec" class="d-block"><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/member1.png" alt=""></a>
                                                </div>
                                                <p class="font-36 text-center mmt-md-4 my-sm-3 my-2 mb-sm-0">Digital</p>
                                        </div>
                                </div>
                                <div class="col-lg-3 col-sm-4 col-12">
                                        <div class="membership_box">
                                                <div class="image_box text-center d-flex justify-content-center align-items-center">
                                                        <a href="#single_booklet_sec" class="d-block"><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/member2.png" alt=""></a>
                                                </div>
                                                <p class="font-36 text-center mt-md-4 my-sm-3 my-2 mb-sm-0">Booklet</p>
                                        </div>
                                </div>
                                <div class="col-lg-3 col-sm-4 col-12">
                                        <div class="membership_box">
                                                <div class="image_box text-center d-flex justify-content-center align-items-center">
                                                        <a href="#" class="d-block"><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/member1.png" alt=""></a>
                                                </div>
                                                <p class="font-36 text-center mt-md-4 my-sm-3 my-2 mb-sm-0">Supplies</p>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</section> -->
<?php
$product = wc_get_product(get_the_ID());
$args = array(
    'post_id' => get_the_ID(),
    'status' => 'approve',
    'post_status' => 'publish',
    'post_type' => 'product'
);

$comments = get_comments($args);
?>
<section class="single_digtal_sec bg-white" id="single_digtal_sec">
    <div class="container">
        <div class="row">
            <div class="col-md-12"><h1 class="section-main-title font-weight-normal"><?php echo $product->get_title(); ?></h1></div>
            <div class="col-12 mb-md-4 mb-3">
                <div class="digital_top_img">
                    <img src="<?php echo wp_get_attachment_url($product->get_image_id()); ?>" alt="product">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-9 col-12">
                <p class="digi_pro_desc"><?php echo $product->get_short_description(); ?> </p>
            </div>
            <div class="col-lg-3 col-12">
                <div class="digital_dec_right">
                    <div class="digi_rating">
                        <?php if ($average = $product->get_average_rating()) : ?>
                            <?php echo '<div class="star-rating" title="' . sprintf(__('Rated %s out of 5', 'woocommerce'), $average) . '"><span style="width:' . ( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">' . $average . '</strong> ' . __('out of 5', 'woocommerce') . '</span></div>'; ?>
                        <?php endif; ?>
                        <!-- <ul class="list-unstyled d-flex justify-content-between">
                                <li><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/star.png" alt="star"></li>
                                <li><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/star.png" alt="star"></li>
                                <li><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/star.png" alt="star"></li>
                                <li><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/star.png" alt="star"></li>
                                <li><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/star.png" alt="star"></li>
                        </ul> -->
                        <?php if (count($comments) != 0) : ?>
                            <span class="d-block"><?php echo count($comments); ?> Customer Reviews</span>
                        <?php endif; ?>
                    </div>
                    <p class="my-sm-3 my-2"><?php echo WC_Subscriptions_Product::get_price($product); ?><small> / <?php echo WC_Subscriptions_Product::get_period($product); ?> </small></p>
                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="cart_btn">Add to cart</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$sample_booklet_title = get_field('sample_booklet_title', get_the_ID());
$sample_booklet_image = get_field('sample_booklet_image', get_the_ID());
$sample_booklet_description = get_field('sample_booklet_description', get_the_ID());
$sample_booklet_radio = get_field('sample_booklet_radio', get_the_ID());
$sample_booklet_pdf_shortcode = get_field('sample_booklet_pdf_shortcode', get_the_ID());
if (!empty($sample_booklet_image) || !empty($sample_booklet_title) || !empty($sample_booklet_description)) :
    ?>
    <section class="single_booklet_sec" id="single_booklet_sec">
        <div class="container">
            <div class="row">
                <?php if (!empty($sample_booklet_title) || !empty($sample_booklet_description)) : ?> 
                    <div class="col-lg-6 text-center text-lg-right pr-lg-5 col-12">
                        <div class="booklet_dec">
                            <p class="section-main-title mb-3"><?php echo $sample_booklet_title; ?></p>
                            <?php echo $sample_booklet_description; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($sample_booklet_image) || !empty($sample_booklet_pdf_shortcode)) : ?>
                    <div class="col-lg-6 col-12">
                        <div class="mt-3 mt-lg-0">
                            <?php if ($sample_booklet_radio == "PDF"): ?>
                                <?php echo do_shortcode($sample_booklet_pdf_shortcode, $ignore_html = false); ?>
                            <?php else : ?>
                                <img src="<?php echo $sample_booklet_image['url']; ?>" alt="booklet">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<section class="desc_review_sec">
    <div class="container">
        <div class="row">
            <div class="col-12 bg-white">
                <div class="single_prod_tab">
                    <?php do_action('woocommerce_after_single_product_summary'); ?>
                </div>

                <!-- <ul class="nav nav-tabs" id="single_prod_tab" role="tablist">
                        <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">description</a>
                        </li>
                        <li class="nav-item" role="presentation">
                                <a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab" aria-controls="review" aria-selected="false">review</a>
                        </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                <p class="mb-3">Maria’s Place Activity booklets include:</p>
                                <ul class="list-unstyled desc_list">
                                        <li class="mb-2 mb-sm-4"><strong>Highlighted story’s of a special Maria’s Place Community Member</strong></li>
                                        <li class="mb-2 mb-sm-4"><strong>Productivity Section</strong>Including a monthly calendar and progress tracking page for goals and to do’s.</li>
                                        <li class="mb-2 mb-sm-4"><strong>Journaling Section</strong>This section includes 4 unique journal prompts that focus on the mind, body, spirit and action. There is also room to journal in the booklet.</li>
                                        <li class="mb-2 mb-sm-4"><strong>5 of our tried and true brain teasers</strong>Demonstrated effective in keeping our aging population’s minds engaged and sharp.</li>
                                        <li class="mb-2 mb-sm-4"><strong>Monthly Recipe</strong>To inspire you and your loved one. Recipes are a great way to incorporate sensory stimulation through taste, smell, touch and sight. These are provided in the booklets to encourage the practice of focusing while enjoying a delicious treat. </li>
                                        <li><strong>Weekly Zoom Social Hour with Maria’s Place </strong>To guide our participants through the different sections of the booklet. We have fun conversations, spend time getting to know each other and set goals for each month. Our passion is to provide motivation, support and accountability.</li>
                                </ul>
                        </div>
                        <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">...</div>
                </div> -->
            </div>
        </div>
    </div>
</section>
<?php
$booklet_section_title = get_field('booklet_section_title', get_the_ID());
$booklet_section_description = get_field('booklet_section_description', get_the_ID());
$booklet_section_button = get_field('booklet_section_button', get_the_ID());
if (!empty($booklet_section_title) || !empty($booklet_section_description) || !empty($booklet_section_button)) :
    ?>
    <section class="monthly_booklet_sec">
        <div class="container">
            <div class="row">
                <div class="col-12 monthly_booklet_inner text-center">
                    <?php echo ($booklet_section_title) ? '<p class="font-36 mb-3">' . $booklet_section_title . '</p>' : ''; ?>
                    <?php echo ($booklet_section_description) ? '<p class="mb-4">' . $booklet_section_description . '</p>' : ''; ?>

                    <?php echo ($booklet_section_button) ? '<a href="' . $booklet_section_button['url'] . '" class="booklet_cart_btn">' . $booklet_section_button['title'] . '</a>' : ''; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php
$bottom_membership_option = get_field('bottom_membership_option', get_the_ID());
if (!empty($bottom_membership_option)) :
    echo do_shortcode($bottom_membership_option);
endif;
?>

<!-- <section class="member_top_sec explore_membership">
        <div class="membership_top">
                <div class="container">
                        <div class="row">
                                <div class="col-12">
                                        <h2 class="section-main-title text-center py-md-5 py-3">Explore our Membership Options</h2>
                                </div>
                        </div>
                </div>
        </div>
        <div class="membership_bottom">
                <div class="container">
                        <div class="row justify-content-center">
                                <div class="col-lg-3 col-sm-4 col-12">
                                        <div class="membership_box">
                                                <div class="image_box text-center d-flex justify-content-center align-items-center">
                                                        <a href="#single_digtal_sec" class="d-block"><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/member1.png" alt=""></a>
                                                </div>
                                                <p class="font-36 text-center mt-md-4 my-sm-3 my-2 mb-sm-0">Digital</p>
                                        </div>
                                </div>
                                <div class="col-lg-3 col-sm-4 col-12">
                                        <div class="membership_box">
                                                <div class="image_box text-center d-flex justify-content-center align-items-center">
                                                        <a href="#single_booklet_sec" class="d-block"><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/member2.png" alt=""></a>
                                                </div>
                                                <p class="font-36 text-center mt-md-4 my-sm-3 my-2 mb-sm-0">Booklet</p>
                                        </div>
                                </div>
                                <div class="col-lg-3 col-sm-4 col-12">
                                        <div class="membership_box">
                                                <div class="image_box text-center d-flex justify-content-center align-items-center">
                                                        <a href="" class="d-block"><img src="<?php echo get_template_directory_uri(); ?>/inc/assets/images/member1.png" alt=""></a>
                                                </div>
                                                <p class="font-36 text-center mt-md-4 my-sm-3 my-2 mb-sm-0">Supplies</p>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</section> -->