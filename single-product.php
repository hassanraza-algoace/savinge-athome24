<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop');
global $product;
?>

<?php

/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */

?>

<div class="max-width">
    <div class="section-padding product-section">
        <?php
        do_action('woocommerce_before_main_content');
        ?>

        <?php while (have_posts()): ?>
                <?php the_post();
                ?>
                <?php wc_get_template_part('content', 'single-product'); ?>
        <?php endwhile;

        /**
         * woocommerce_after_main_content hook.
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
         */
        do_action('woocommerce_after_main_content');
        ?>
    </div>
</div>

<?php
/**
 * woocommerce_sidebar hook.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action('woocommerce_sidebar');
?>

<?php
// $taxonomy = 'yith_product_brand';
// $orderby = 'name';
// $show_count = 0;
// $pad_counts = 0;
// $hierarchical = 1;
// $title = '';
// $empty = 0;

// $args_brands = array(
//     'taxonomy' => $taxonomy,
//     'orderby' => $orderby,
//     'show_count' => $show_count,
//     'pad_counts' => $pad_counts,
//     'hierarchical' => $hierarchical,
//     'title_li' => $title,
//     'hide_empty' => $empty,
// );
// $all_brands = get_categories($args_brands);

?>

<!-- <section class="section-padding brands single-product-brands">
    <div class="row max-width">
        <div class="col col-12 d-flex justify-content-between align-items-bottom">
            <div>
                <h2 class="sub-heading"><?php _e('Prekių ženklai, kuriuos rasite pas mus'); ?></h2>
            </div>
            <div>
                <a href="<?php echo get_permalink(5); ?>" class="see-all-btn"><?php _e('Žiūrėti visus'); ?></a>
            </div>
        </div>
    </div>
    <div class="row max-width mt-5">
        <div class="col col-12">
            <div class="swiper brandSwiper">
                <div class="swiper-wrapper">
                    <?php if (have_rows('brands_section', 5)):
                        while (have_rows('brands_section', 5)):
                            the_row();
                            ?>
                            <div class="swiper-slide justify-content-center">
                                <a href="<?php echo get_sub_field('url'); ?>">
                                    <img src="<?php echo get_sub_field('image'); ?>" alt="" width="100%">
                                </a>
                            </div>
                        <?php endwhile;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
</section> -->

<section class="section-padding brands brands_home_page_section">
    <div class="row max-width">
        <div class="col col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="sub-heading"><?php _e('Prekių ženklai, kuriuos rasite pas mus'); ?></h2>
            </div>
            <div class="d-block mobile-nav mobile_none">
                <a href="#">Visi straipsniai <span><img
                            src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                            alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
            </div>
        </div>
    </div>
    <div class="row max-width mt-5">
        <div class="col col-12">
            <div class="swiper brandSwiper">
                <div class="swiper-wrapper">
                    <?php if (have_rows('brands_section', 5)):
                        while (have_rows('brands_section', 5)):
                            the_row();
                            ?>
                                    <div class="swiper-slide justify-content-center">
                                        <a href="<?php echo get_sub_field('url'); ?>">
                                            <img src="<?php echo get_sub_field('image'); ?>" alt="" width="100%">
                                        </a>
                                    </div>
                            <?php endwhile;
                    endif; ?>
                </div>
                <div class="hassanSwiperOneArrows">
                    <div class="swiper-button-prev">
                        <svg id="product-brand-swiper-button-prev" xmlns="https://www.w3.org/2000/svg" width="35"
                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                            aria-label="Previous slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                            <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg id="product-brand-swiper-button-next" xmlns="https://www.w3.org/2000/svg" width="35"
                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                            aria-label="Next slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                            <path d="M11 11L24 17.5L11 24" stroke="#054C73" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
                <div class="d-block mobile-nav desktop_none">
                    <a href="#">Visi straipsniai <span><img
                                src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */