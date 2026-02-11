<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archives
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */
defined('ABSPATH') || exit;
get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
?>
<style>
    /* Sticky Sidebar */
    .search-sidebar.sticky-top {
        top: 100px;
        padding-right: 30px;
        height: fit-content;
        z-index: 1;
    }

    /* Right Column Styling */
    .col-lg-9 {
        margin-left: auto;
        padding-left: 30px;
    }

    /* Mobile Optimization */
    @media (max-width: 991px) {
        .col-lg-3 {
            display: none !important;
        }

        .col-lg-9 {
            padding-left: 15px;
        }
    }
</style>
<div>
    <div class="row section-padding max-width">
        <div class="col col-12 p-0 p-lg-3 d-block d-lg-none">
            <div class="row mobile-catalog-header d-block">
                <div class="col">
                    <a href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"
                        class="text-white p-3 text-uppercase d-flex gap-3 justify-content-between my-3">
                        <?php echo esc_html('Prekių katalogas'); ?>
                    </a>
                </div>
            </div>
            <div class="search-sidebar">
                <?php do_action('woocommerce_sidebar'); ?>
            </div>
            <div class="d-flex" style="margin-left: 30px;">
                <div class="row mobile-breadcrumb d-block d-lg-none">
                    <?php woocommerce_breadcrumb(); ?>
                </div>
            </div>
        </div>
        <!-- Desktop Layout -->
        <div class="row w-100">
            <!-- Left Sidebar -->
            <div class="col col-12">
                <?php
                do_action('woocommerce_before_main_content');
                if (is_tax()) {
                    $padding_bottom = 'pb-lg-5';
                } else {
                    $padding_bottom = '';
                }
                ?>

                <h2 class="text-start d-block d-sm-none woocommerce-products-header__title page-title my-3">
                    <?php if ($_GET['naujienos'] == 'true/') {
                        echo esc_html('Naujienos');
                    } elseif ($_GET['akcijos'] == 'true') {
                        echo esc_html('Akcijos');
                    } else {
                        woocommerce_page_title();
                    }

                    ?>
                </h2>

                <div
                    class="mb-3 pb-3 mb-lg-5 <?php echo $padding_bottom; ?> align-items-center justify-content-between archive-header d-none d-lg-flex">



                    <header class="woocommerce-products-header">
                        <?php if (apply_filters('woocommerce_show_page_title', true)): ?>
                            <h1 class="woocommerce-products-header__title page-title">
                                <?php if ($_GET['naujienos'] == 'true/') {
                                    echo esc_html('Naujienos');
                                } elseif ($_GET['akcijos'] == 'true') {
                                    echo esc_html('Akcijos');
                                } else {
                                    woocommerce_page_title();
                                }
                                $queried_object = get_queried_object();

                                ?>
                            </h1>
                        <?php endif; ?>

                        <?php
                        /**
                         * Hook: woocommerce_archive_description.
                         *
                         * @hooked woocommerce_taxonomy_archive_description - 10
                         * @hooked woocommerce_product_archive_description - 10
                         */
                        do_action('woocommerce_archive_description');
                        $term_current = get_queried_object();

                        if (get_field('brand_link', $term_current) != null) {
                            echo '<a class="brand-url" href="' . get_field('brand_link', $term_current) . '" target="_blank"> Nuoroda į gamintojo puslapį</a>';
                        }
                        ?>

                        <?php
                        if (woocommerce_product_loop()) {
                            /**
                             * Hook: woocommerce_before_shop_loop.
                             *
                             * @hooked woocommerce_output_all_notices - 10
                             * @hooked woocommerce_result_count - 20
                             * @hooked woocommerce_catalog_ordering - 30
                             */
                            do_action('woocommerce_before_shop_loop');

                            $taxonomy = 'yith_product_brand';
                            $orderby = 'name';
                            $show_count = 0;
                            $pad_counts = 0;
                            $hierarchical = 1;
                            $title = '';
                            $empty = 0;

                            $args_brands = array(
                                'taxonomy' => $taxonomy,
                                'orderby' => $orderby,
                                'show_count' => $show_count,
                                'pad_counts' => $pad_counts,
                                'hierarchical' => $hierarchical,
                                'title_li' => $title,
                                'hide_empty' => $empty,
                            );
                            $all_brands = get_categories($args_brands);
                            ?>

                    </div>
                    <?php
                    // Current category
                    $current_cat = get_queried_object();

                    // Get subcategories of this product category
                    $subcategories = array();
                    if ($current_cat && isset($current_cat->taxonomy) && $current_cat->taxonomy === 'product_cat' && isset($current_cat->term_id)) {
                        $subcategories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'parent' => $current_cat->term_id,
                            'hide_empty' => false,
                        ));
                        if (is_wp_error($subcategories)) {
                            $subcategories = array();
                        }
                    }

                    // Placeholder image
                    $placeholder = 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Placeholder_view_vector.svg/1362px-Placeholder_view_vector.svg.png';
                    ?>
                    <div class="subcategory-wrapper">
                        <?php if (!empty($subcategories) && !is_wp_error($subcategories)): ?>
                            <div class="subcategory-grid" style="display:flex; flex-wrap:wrap; gap:20px;">
                                <?php foreach ($subcategories as $subcategory): ?>
                                    <?php
                                    // WooCommerce category thumbnail
                                    $thumbnail_id = get_term_meta($subcategory->term_id, 'thumbnail_id', true);
                                    $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : $placeholder;
                                    ?>
                                    <div class="subcategory-item" style="max-width:116px; text-align:center;">
                                        <a href="<?php echo get_term_link($subcategory); ?>">
                                            <img src="<?php echo esc_url($image_url); ?>"
                                                alt="<?php echo esc_attr($subcategory->name); ?>" style="width:100%; height:auto;">
                                            <h3 style="margin-top:10px;" class="product_category_page_sub_category">
                                                <?php echo esc_html($subcategory->name); ?>
                                            </h3>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No subcategories available for this category.</p>
                        <?php endif; ?>
                    </div>
                    <div class="populer_product_section">
                        <div class="populer_product_top_row">
                            <div>
                                <h2>Populiariausios kategorijos prekės</h2>
                            </div>
                            <div class="navigation-btn">
                                <div>
                                    <svg xmlns="https://www.w3.org/2000/svg" class="swiper-button-prev" width="100"
                                        height="100" viewBox="0 0 44 45" fill="none">
                                        <rect width="44" height="45" rx="4" fill="#054C73" />
                                        <path d="M28 29L16 22.5L28 16" stroke="white" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div>
                                    <svg xmlns="https://www.w3.org/2000/svg" class="swiper-button-next" width="100"
                                        height="100" viewBox="0 0 45 45" fill="none">
                                        <rect width="45" height="45" rx="4" fill="#054C73" />
                                        <path d="M16 16L29 22.5L16 29" stroke="white" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="populer_product_bottom_row">
                            <?php
                            // Ensure WooCommerce product object
                        
                            // Get current category
                            $current_cat = get_queried_object();
                            $category_id = 0;
                            if ($current_cat && isset($current_cat->taxonomy) && $current_cat->taxonomy === 'product_cat' && isset($current_cat->term_id)) {
                                $category_id = $current_cat->term_id;
                            }

                            // Only fetch products if we have a valid category
                            if ($category_id > 0) {
                                // Fetch products from this category
                                $args = array(
                                    'post_type' => 'product',
                                    'posts_per_page' => 12,
                                    'meta_key' => 'total_sales',
                                    'orderby' => 'meta_value_num',
                                    'order' => 'DESC',
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'product_cat',
                                            'field' => 'term_id',
                                            'terms' => $category_id,
                                        ),
                                    ),
                                );

                                $products = new WP_Query($args);
                            } else {
                                // Not a category page, create empty query
                                $products = new WP_Query(array('post_type' => 'product', 'posts_per_page' => 0));
                            }
                            $placeholder = 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Placeholder_view_vector.svg/1362px-Placeholder_view_vector.svg.png';

                            ?>

                            <?php if ($products->have_posts()): ?>
                                <div class="popular-products-slider swiper">
                                    <div class="swiper-wrapper">
                                        <?php while ($products->have_posts()):
                                            $products->the_post();
                                            global $product;
                                            $image_url = get_the_post_thumbnail_url($product->get_id(), 'medium') ?: $placeholder;
                                            $wishlist_count = intval(get_post_meta($product->get_id(), '_yith_wcwl_count', true));
                                            ?>
                                            <?php
                                            $product = wc_get_product(get_the_ID());
                                            if (!$product)
                                                continue;

                                            $regular_price = (float) $product->get_regular_price();
                                            $sale_price = (float) $product->get_sale_price();

                                            $discount_percent = 0;
                                            if ($sale_price && $regular_price > 0) {
                                                $discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
                                            }
                                            ?>
                                            <div class="swiper-slide">
                                                <div class="product-card"
                                                    style="text-align:center; padding:10px; border:1px solid #eee; border-radius:8px;">
                                                    <div class="product" data-product-id="<?php echo $product->get_id(); ?>">
                                                        <div class="hassan_swiper_top_bar">
                                                            <p class="btn">
                                                                -<?php echo $discount_percent; ?>%
                                                            </p>
                                                            <div>
                                                                <?php
                                                                echo do_shortcode('[yith_wcwl_add_to_wishlist]');
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="<?php the_permalink(); ?>">
                                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title(); ?>"
                                                            style="width:100%; height:auto; border-radius:5px;">
                                                        <h3 style="margin:10px 0;"><?php the_title(); ?></h3>
                                                        <p style="text-align:left" class="price">
                                                            <?php echo $product->get_price_html(); ?>
                                                        </p>
                                                        <!-- Add-to-cart Button -->
                                                        <button class="hassanSwiperOne_btn">Į krepšelį</button>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <!-- Add Pagination -->
                                    <div class="swiper-pagination"></div>
                                </div>
                                <?php wp_reset_postdata();
                            endif; ?>
                        </div>
                    </div>
                    <!-- Swiper JS & CSS (if not already loaded in theme) -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
                    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            new Swiper('.popular-products-slider', {
                                slidesPerView: 4,
                                spaceBetween: 20,
                                loop: true,
                                navigation: {
                                    nextEl: '.swiper-button-next',
                                    prevEl: '.swiper-button-prev',
                                },
                                pagination: {
                                    el: '.swiper-pagination',
                                    clickable: true,
                                },
                                breakpoints: {
                                    0: {
                                        slidesPerView: 1
                                    },
                                    576: {
                                        slidesPerView: 2
                                    },
                                    768: {
                                        slidesPerView: 3
                                    },
                                    992: {
                                        slidesPerView: 4
                                    },
                                },
                            });
                        });
                    </script>
                    <!-- Archive Filter Bar (Figma / Athome.lt layout) -->
                    <div class="archive-filter-bar d-none d-lg-block" id="filterAccordion">
                        <div class="archive-filter-bar__row">
                            <div class="archive-filter-bar__left">
                                <?php
                                // Get current category
                                $current_cat = get_queried_object();
                                $current_cat_id = 0;
                                if ($current_cat && isset($current_cat->taxonomy) && $current_cat->taxonomy === 'product_cat' && isset($current_cat->term_id)) {
                                    $current_cat_id = $current_cat->term_id;
                                }

                                // Get all product categories
                                $all_categories = get_terms(array(
                                    'taxonomy' => 'product_cat',
                                    'hide_empty' => false,
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                ));

                                // Build filter URL parameters
                                $filters = [];
                                $filter_params = [
                                    'akcijos',
                                    'naujienos',
                                    'prekes-zenklas-filter',
                                    'medziagiskumas-filter',
                                    'matmenys-filter',
                                    'svoris-filter'
                                ];

                                foreach ($filter_params as $param) {
                                    if (!empty($_GET[$param])) {
                                        $filters[] = esc_attr($param) . '=' . esc_attr($_GET[$param]);
                                    }
                                }
                                $filter_query = $filters ? '&' . implode('&', $filters) : '';

                                // Function to build hierarchical category options
                                function build_category_options($parent_id = 0, $current_cat_id = 0, $filter_query = '', $depth = 0)
                                {
                                    $categories = get_terms(array(
                                        'taxonomy' => 'product_cat',
                                        'parent' => $parent_id,
                                        'hide_empty' => false,
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                    ));

                                    if (is_wp_error($categories) || empty($categories)) {
                                        return '';
                                    }

                                    $output = '';
                                    $prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $depth);

                                    foreach ($categories as $category) {
                                        $term_link = get_term_link($category);
                                        if (is_wp_error($term_link)) {
                                            continue;
                                        }

                                        $selected = ($category->term_id == $current_cat_id) ? 'selected' : '';
                                        $category_name = esc_html($category->name);

                                        // Build URL with filter parameters
                                        if ($filter_query) {
                                            $separator = (strpos($term_link, '?') !== false) ? '&' : '?';
                                            $category_url = esc_url($term_link . $separator . ltrim($filter_query, '&'));
                                        } else {
                                            $category_url = esc_url($term_link);
                                        }

                                        $output .= '<option value="' . $category_url . '" ' . $selected . ' data-category-id="' . $category->term_id . '">' . $prefix . $category_name . '</option>';

                                        // Recursively get child categories
                                        $output .= build_category_options($category->term_id, $current_cat_id, $filter_query, $depth + 1);
                                    }

                                    return $output;
                                }
                                ?>
                                <?php
                                $shop_url = get_permalink(woocommerce_get_page_id('shop'));
                                if ($filter_query) {
                                    $shop_url .= (strpos($shop_url, '?') !== false) ? '&' . ltrim($filter_query, '&') : '?' . ltrim($filter_query, '&');
                                }
                                ?>
                                <select
                                    class="archive-filter-bar__btn archive-filter-bar__btn--categories archive-filter-bar__categories-dropdown"
                                    id="categoryDropdown">
                                    <option value="<?php echo esc_url($shop_url); ?>">
                                        <?php echo esc_html('kategorijos'); ?>
                                    </option>
                                    <?php echo build_category_options(0, $current_cat_id, $filter_query); ?>
                                </select>
                                <!-- <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        var categoryDropdown = document.getElementById('categoryDropdown');
                                        if (categoryDropdown) {
                                            categoryDropdown.addEventListener('change', function () {
                                                if (this.value) {
                                                    window.location.href = this.value;
                                                }
                                            });
                                        }
                                    });
                                </script> -->
                                <div class="accordion-item archive-filter-bar__accordion-item">
                                    <h2 class="accordion-header" id="filterHeading">
                                        <button class="accordion-button archive-filter-bar__toggle collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false"
                                            aria-controls="filterCollapse">
                                            <span>
                                                <svg xmlns="https://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M6.45898 10.2086C7.85464 10.2089 9.01929 11.1898 9.30566 12.4996H17.5C17.8449 12.4998 18.1248 12.7797 18.125 13.1246C18.125 13.4697 17.845 13.7495 17.5 13.7496H9.30566C9.01962 15.0598 7.85496 16.0413 6.45898 16.0416C5.0627 16.0416 3.89654 15.06 3.61035 13.7496H2.5C2.15482 13.7496 1.875 13.4698 1.875 13.1246C1.87517 12.7796 2.15493 12.4996 2.5 12.4996H3.61035C3.89688 11.1895 5.06301 10.2086 6.45898 10.2086ZM6.45898 11.4586C5.53851 11.4586 4.79199 12.2051 4.79199 13.1256C4.79235 14.0458 5.53873 14.7916 6.45898 14.7916C7.37894 14.7913 8.12465 14.0456 8.125 13.1256C8.125 12.2054 7.37916 11.459 6.45898 11.4586ZM11.459 3.54163C12.8549 3.54193 14.0195 4.52346 14.3057 5.83362H17.5C17.845 5.8338 18.125 6.11355 18.125 6.45862C18.1248 6.80353 17.8449 7.08344 17.5 7.08362H14.3057C14.0194 8.39348 12.8547 9.37433 11.459 9.37463C10.0629 9.37463 8.89677 8.39372 8.61035 7.08362H2.5C2.15493 7.08362 1.87518 6.80365 1.875 6.45862C1.875 6.11344 2.15482 5.83362 2.5 5.83362H8.61035C8.89665 4.52322 10.0628 3.54163 11.459 3.54163ZM11.459 4.79163C10.5385 4.79163 9.79199 5.53814 9.79199 6.45862C9.79217 7.37894 10.5386 8.12463 11.459 8.12463C12.379 8.12428 13.1248 7.37872 13.125 6.45862C13.125 5.53836 12.3792 4.79198 11.459 4.79163Z"
                                                        fill="white" />
                                                </svg>
                                            </span>
                                            <?php echo esc_html('Filtruoti pagal'); ?>
                                        </button>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div id="filterCollapse" class="accordion-collapse collapse archive-filter-bar__collapse"
                            aria-labelledby="filterHeading" data-bs-parent="#filterAccordion">
                            <div class="archive-filter-bar__panel">
                                <p class="archive-filter-bar__label"><?php echo esc_html('Pasirinkite filtrus:'); ?></p>
                                <div class="archive-filter-bar__widgets">
                                    <?php get_template_part('partials/archive-filter-attributes'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="archive-products-container">
                        <?php
                        woocommerce_product_loop_start();
                        if (wc_get_loop_prop('total')) {
                            while (have_posts()) {
                                the_post();
                                do_action('woocommerce_shop_loop');

                                wc_get_template_part('content', 'product');
                            }
                        }
                        woocommerce_product_loop_end();
                        ?>
                    </div>
                    <?php
                    if ($term_current->taxonomy == 'product_cat' && $term_current->description) {
                        echo '<div class="d-block d-sm-none">';
                        echo '<h3 class="text-center">' . esc_html('Aprašymas:') . '</h3>';
                        echo '<p class="text-center my-3">' . esc_html($term_current->description) . '</p>';
                        echo '</div>';
                    }
                    do_action('woocommerce_after_shop_loop');
                        } else {
                            echo esc_html('Produktų nerasta');
                        }
                        do_action('woocommerce_after_main_content'); ?>
                <div class="mobile-tytle" style="font-weight:600; font-size:18px!important;">
                    <?php woocommerce_page_title(); ?>
                </div>
                <p class="mobyle-desc"> <?php do_action('woocommerce_archive_description'); ?> </p>
                <div class="gap-mobyle" style="height:20px;">

                </div>

                <?php
                /**
                 * Hook: woocommerce_sidebar.
                 *
                 * @hooked woocommerce_get_sidebar - 10
                 */
                ?>
            </div>
        </div>
    </div>
    <section class="section-padding newsletter-section"
        style="background: url('<?php echo wp_get_attachment_image_src(50, array(854, 854))[0]; ?>')">
        <div class="row py-5 max-width">
            <div class="col col-12 text-center">
                <p class="sub_heading">
                    <?php echo esc_html('Prenumeruokite naujienlaiškį ir gaukite'); ?>
                </p>
                <h2 class="my-3 ">
                    <?php echo esc_html('12 % nuolaidos kodą krepšeliui'); ?>
                </h2>
                <!-- <p class="mb-5"><?php echo esc_html('12 % nuolaidos kodą krepšeliui'); ?></p> -->
                <?php echo do_shortcode('[contact-form-7 id="40" title="Newsletter"]'); ?>
            </div>
        </div>
    </section>
</div>
<?php
get_footer('shop');
