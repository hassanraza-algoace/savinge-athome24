<?php

/**
 * Search results template - WooCommerce products search.
 * Layout matches archive-product.php but without subcategory section.
 */

defined('ABSPATH') || exit;
get_header('shop');

$search_query = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : get_search_query();
$paged = max(1, get_query_var('paged'));
$orderby = isset($_GET['orderby']) ? sanitize_text_field(wp_unslash($_GET['orderby'])) : '';
$search_action = home_url('/');
$current_search = $search_query;

// Build product query
$product_query_args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    's' => $search_query,
    'paged' => $paged,
    'posts_per_page' => 24,
);

if ($orderby) {
    switch ($orderby) {
        case 'popularity':
            $product_query_args['meta_key'] = 'total_sales';
            $product_query_args['orderby'] = 'meta_value_num';
            $product_query_args['order'] = 'DESC';
            break;
        case 'rating':
            $product_query_args['meta_key'] = '_wc_average_rating';
            $product_query_args['orderby'] = 'meta_value_num';
            $product_query_args['order'] = 'DESC';
            break;
        case 'date':
            $product_query_args['orderby'] = 'date';
            $product_query_args['order'] = 'DESC';
            break;
        case 'price':
            $product_query_args['meta_key'] = '_price';
            $product_query_args['orderby'] = 'meta_value_num';
            $product_query_args['order'] = 'ASC';
            break;
        case 'price-desc':
            $product_query_args['meta_key'] = '_price';
            $product_query_args['orderby'] = 'meta_value_num';
            $product_query_args['order'] = 'DESC';
            break;
        default:
            $product_query_args['orderby'] = 'menu_order title';
            $product_query_args['order'] = 'ASC';
    }
}

$product_query = new WP_Query($product_query_args);
?>
<style>
    /* Search Page Specific Styles */
    .search-page-wrapper {
        width: 100%;
        max-width: 100%;
    }

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

    /* Search Page Header */
    .search-page-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
    }

    .search-page-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1f1f1f;
        margin-bottom: 20px;
    }

    /* Archive Header Improvements */
    .archive-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e0e0e0;
    }

    /* Search Filter Section */
    .archive-filter-search {
        margin-bottom: 24px;
    }

    /* Products Container */
    #archive-products-container {
        margin-top: 30px;
    }

    /* Mobile Title */
    .mobile-tytle {
        margin-top: 20px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    /* Mobile Optimization */
    @media (max-width: 991px) {
        .col-lg-3 {
            display: none !important;
        }

        .col-lg-9 {
            padding-left: 15px;
        }

        .archive-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-page-header h1 {
            font-size: 22px;
        }
    }

    /* No Results Message */
    .woocommerce-info {
        padding: 30px;
        background: #f5f5f5;
        border-radius: 6px;
        border-left: 4px solid #3ba57f;
        margin: 30px 0;
    }

    .woocommerce-info p {
        margin: 0;
        font-size: 16px;
        color: #1f1f1f;
    }
</style>

<div class="search-page-wrapper">
    <div class="max-width row section-padding">

        <!-- Mobile Layout -->
        <div class="col col-12 p-0 p-lg-3 d-block">
            <div class="row mobile-catalog-header d-block d-lg-none">
                <div class="col">
                    <a href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"
                        class="text-white p-3 text-uppercase d-flex gap-3 justify-content-between my-3">
                        <?php echo esc_html('Prekių katalogas'); ?>
                    </a>
                </div>
            </div>
            <div class="search-sidebar d-lg-none">
                <?php do_action('woocommerce_sidebar'); ?>
            </div>
            <div class="d-flex" style="margin-left: 30px;">
                <div class="row mobile-breadcrumb d-block d-lg-none">
                    <?php woocommerce_breadcrumb(); ?>
                </div>
            </div>
            <div class="row mobile-filter d-flex d-lg-none">
                <div class="col col-6 d-flex align-items-center justify-content-center">
                    <!-- Modal -->
                    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-between">
                                    <h2>Filtrai</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        <svg xmlns="https://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 14 14" fill="none">
                                            <path
                                                d="M12.2197 0.219727C12.5126 -0.0731667 12.9873 -0.0731667 13.2802 0.219727C13.5731 0.51262 13.5731 0.98738 13.2802 1.28027L7.81049 6.75L13.2802 12.2197C13.5731 12.5126 13.5731 12.9874 13.2802 13.2803C12.9873 13.5732 12.5126 13.5732 12.2197 13.2803L6.74994 7.81055L1.28021 13.2803C0.987319 13.5732 0.512559 13.5732 0.219666 13.2803C-0.0732277 12.9874 -0.0732277 12.5126 0.219666 12.2197L5.68939 6.75L0.219666 1.28027C-0.0732277 0.98738 -0.0732277 0.51262 0.219666 0.219727C0.512559 -0.0731667 0.987319 -0.0731667 1.28021 0.219727L6.74994 5.68945L12.2197 0.219727Z"
                                                fill="white" />
                                        </svg>
                                    </button>
                                </div>
                                <?php set_query_var('archive_filter_form_id', 'modal'); ?>
                                <?php get_template_part('partials/archive-filter-attributes'); ?>
                                <?php get_template_part('partials/category-list'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="categories-container-mobile text-uppercase ">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#categoryModal">
                            <?php echo esc_html('Filtruoti'); ?>
                        </button>
                    </div>
                </div>
                <div class="col col-6">
                    <form
                        class="d-block d-lg-none woocommerce-ordering d-flex align-items-center gap-3 position-relative"
                        method="get">
                        <input type="hidden" name="s" value="<?php echo esc_attr($search_query); ?>">
                        <input type="hidden" name="post_type" value="product">
                        <select name="orderby" class="orderby w-100" aria-label="Parduotuvės užsakymas">
                            <option value="menu_order" <?php echo $orderby == 'menu_order' ? 'selected="selected"' : '' ?>>
                                RIKIUOTI</option>
                            <option value="popularity" <?php echo $orderby == 'popularity' ? 'selected="selected"' : '' ?>>
                                Rikiuoti pagal populiarumą</option>
                            <option value="rating" <?php echo $orderby == 'rating' ? 'selected="selected"' : '' ?>>
                                Rikiuoti pagal vertinimą</option>
                            <option value="date" <?php echo $orderby == 'date' ? 'selected="selected"' : '' ?>>
                                Rikiuoti nuo naujausių</option>
                            <option value="price" <?php echo $orderby == 'price' ? 'selected="selected"' : '' ?>>
                                Rikiuoti pagal kainą (min → maks)</option>
                            <option value="price-desc" <?php echo $orderby == 'price-desc' ? 'selected="selected"' : '' ?>>
                                Rikiuoti pagal kainą (maks → min)</option>
                        </select>
                        <input type="hidden" name="paged" value="1">
                    </form>
                </div>
            </div>
        </div>
        <!-- Desktop Layout -->
        <div class="row w-100">
            <div class="col col-12">
                <?php
                do_action('woocommerce_before_main_content');
                ?>

                <h2 class="text-start d-block d-sm-none woocommerce-products-header__title page-title my-3">
                    <?php printf(esc_html__('Paieškos rezultatai: %s', 'savinge'), esc_html($search_query)); ?>
                </h2>

                <div class="search-page-header d-none d-lg-block">
                    <?php if (apply_filters('woocommerce_show_page_title', true)): ?>
                        <h1 class="woocommerce-products-header__title page-title">
                            <?php printf(esc_html__('Paieškos rezultatai: %s', 'savinge'), esc_html($search_query)); ?>
                        </h1>
                    <?php endif; ?>
                </div>

                <div
                    class="mb-3 pb-3 mb-lg-5 align-items-center justify-content-between archive-header d-none d-lg-flex">
                    <div class="woocommerce-products-header">
                        <?php if ($product_query->have_posts()): ?>
                            <div class="woocommerce-result-count">
                                <?php
                                $total = $product_query->found_posts;
                                printf(
                                    esc_html(_n('Rasta %d prekė', 'Rasta %d prekės', $total, 'savinge')),
                                    $total
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    if ($product_query->have_posts()):
                        /**
                         * Hook: woocommerce_before_shop_loop.
                         */
                        do_action('woocommerce_before_shop_loop');
                        ?>

                    </div>
                    <div class="archive-filter-search">
                        <form method="get" class="archive-filter-search__form"
                            action="<?php echo esc_url($search_action); ?>">
                            <input type="hidden" name="post_type" value="product">
                            <input type="search" name="s" class="archive-filter-search__input Search_page_form_input"
                                placeholder="<?php echo esc_attr__('Ieškoti prekių...', 'woocommerce'); ?>"
                                value="<?php echo esc_attr($current_search); ?>"
                                aria-label="<?php echo esc_attr__('Ieškoti', 'woocommerce'); ?>">
                            <button type="submit" class="archive-filter-search__btn"
                                aria-label="<?php echo esc_attr__('Paieška', 'woocommerce'); ?>">
                                <svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <!-- Archive Filter Bar -->
                    <div class="archive-filter-bar d-none d-lg-block" id="filterAccordion">
                        <div class="archive-filter-bar__row">
                            <div class="archive-filter-bar__left">
                                <?php
                                // Get current category (if any)
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
                                function build_category_options_search($parent_id = 0, $current_cat_id = 0, $filter_query = '', $depth = 0)
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
                                        $output .= build_category_options_search($category->term_id, $current_cat_id, $filter_query, $depth + 1);
                                    }

                                    return $output;
                                }

                                $shop_url = get_permalink(woocommerce_get_page_id('shop'));
                                if ($filter_query) {
                                    $shop_url .= (strpos($shop_url, '?') !== false) ? '&' . ltrim($filter_query, '&') : '?' . ltrim($filter_query, '&');
                                }
                                ?>
                                <select
                                    class="archive-filter-bar__btn archive-filter-bar__btn--categories archive-filter-bar__categories-dropdown"
                                    id="categoryDropdownSearch">
                                    <option value="<?php echo esc_url($shop_url); ?>">
                                        <?php echo esc_html('kategorijos'); ?>
                                    </option>
                                    <?php echo build_category_options_search(0, $current_cat_id, $filter_query); ?>
                                </select>
                                <!-- <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        var categoryDropdown = document.getElementById('categoryDropdownSearch');
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
                                            <?php echo esc_html('Filtruoti pagal'); ?>
                                            <span><svg xmlns="https://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M6.45898 10.2086C7.85464 10.2089 9.01929 11.1898 9.30566 12.4996H17.5C17.8449 12.4998 18.1248 12.7797 18.125 13.1246C18.125 13.4697 17.845 13.7495 17.5 13.7496H9.30566C9.01962 15.0598 7.85496 16.0413 6.45898 16.0416C5.0627 16.0416 3.89654 15.06 3.61035 13.7496H2.5C2.15482 13.7496 1.875 13.4698 1.875 13.1246C1.87517 12.7796 2.15493 12.4996 2.5 12.4996H3.61035C3.89688 11.1895 5.06301 10.2086 6.45898 10.2086ZM6.45898 11.4586C5.53851 11.4586 4.79199 12.2051 4.79199 13.1256C4.79235 14.0458 5.53873 14.7916 6.45898 14.7916C7.37894 14.7913 8.12465 14.0456 8.125 13.1256C8.125 12.2054 7.37916 11.459 6.45898 11.4586ZM11.459 3.54163C12.8549 3.54193 14.0195 4.52346 14.3057 5.83362H17.5C17.845 5.8338 18.125 6.11355 18.125 6.45862C18.1248 6.80353 17.8449 7.08344 17.5 7.08362H14.3057C14.0194 8.39348 12.8547 9.37433 11.459 9.37463C10.0629 9.37463 8.89677 8.39372 8.61035 7.08362H2.5C2.15493 7.08362 1.87518 6.80365 1.875 6.45862C1.875 6.11344 2.15482 5.83362 2.5 5.83362H8.61035C8.89665 4.52322 10.0628 3.54163 11.459 3.54163ZM11.459 4.79163C10.5385 4.79163 9.79199 5.53814 9.79199 6.45862C9.79217 7.37894 10.5386 8.12463 11.459 8.12463C12.379 8.12428 13.1248 7.37872 13.125 6.45862C13.125 5.53836 12.3792 4.79198 11.459 4.79163Z"
                                                        fill="white" />
                                                </svg></span>
                                        </button>
                                    </h2>
                                </div>
                            </div>
                            <!-- <div>
                                <form class="woocommerce-ordering d-flex align-items-center gap-3 position-relative"
                                    method="get">
                                    <span class="content_order">Rikiuoti pagal:</span>

                                    <select name="orderby" class="orderby">

                                        <?php
                                        $custom_labels = array(
                                            'menu_order' => 'Numatytasis rūšiavimas',
                                            'popularity' => 'Populiariausi viršuje',
                                            'rating' => 'Geriausiai įvertinti viršuje',
                                            'date' => 'Naujausi pirmiau',
                                            'price' => 'Pagal kainą: pigiausi viršuje',
                                            'price-desc' => 'Pagal kainą: brangiausi viršuje',
                                        );

                                        foreach ($catalog_orderby_options as $id => $name):

                                            $label = isset($custom_labels[$id]) ? $custom_labels[$id] : $name;
                                            ?>

                                            <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>>
                                                <?php echo esc_html($label); ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                    <input type="hidden" name="paged" value="1" />
                                    <?php wc_query_string_form_fields(null, array('orderby', 'submit', 'paged', 'product-page')); ?>

                                </form>
                            </div> -->
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
                        while ($product_query->have_posts()):
                            $product_query->the_post();
                            do_action('woocommerce_shop_loop');
                            wc_get_template_part('content', 'product');
                        endwhile;
                        woocommerce_product_loop_end();
                        ?>
                    </div>

                    <?php
                    // Pagination
                    if ($product_query->max_num_pages > 1):
                        $base = add_query_arg('paged', '%#%');
                        if (!empty($search_query)) {
                            $base = add_query_arg('s', $search_query, $base);
                            $base = add_query_arg('post_type', 'product', $base);
                        }
                        if ($orderby) {
                            $base = add_query_arg('orderby', $orderby, $base);
                        }
                        ?>
                        <nav class="woocommerce-pagination mt-4">
                            <?php
                            echo paginate_links(array(
                                'base' => $base,
                                'format' => '',
                                'current' => $paged,
                                'total' => $product_query->max_num_pages,
                                'prev_text' => '&larr;',
                                'next_text' => '&rarr;',
                                'type' => 'list',
                            ));
                            ?>
                        </nav>
                        <?php
                    endif;

                    wp_reset_postdata();
                    do_action('woocommerce_after_shop_loop');

                    else:
                        ?>
                    <div class="woocommerce-info">
                        <p>
                            <?php printf(esc_html__('Produktų nerasta pagal užklausą "%s".', 'savinge'), esc_html($search_query)); ?>
                        </p>
                        <p style="margin-top: 15px;">
                            <a href="<?php echo esc_url(get_permalink(woocommerce_get_page_id('shop'))); ?>" class="button">
                                <?php echo esc_html__('Grįžti į katalogą', 'savinge'); ?>
                            </a>
                        </p>
                    </div>
                    <?php
                    endif;

                    do_action('woocommerce_after_main_content');
                    ?>
                <div class="mobile-tytle" style="font-weight:600; font-size:18px!important;">
                    <?php printf(esc_html__('Paieškos rezultatai: %s', 'savinge'), esc_html($search_query)); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer('shop');
