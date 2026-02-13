<!doctype html>
<html lang="<?php echo get_locale() ?>">
<?php
if (!isset($_GET['naujienos'])) {
    $_GET['naujienos'] = '';
}

if (!isset($_GET['akcijos'])) {
    $_GET['akcijos'] = '';
}
?>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|'); ?></title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <style>
        .product .yith-wcwl-icon:before {
            content: url('<?php echo wp_get_attachment_image_src(65, array(854, 854))[0]; ?>');
        }

        .product .yith-wcwl-add-to-wishlist.exists .yith-wcwl-icon:before {
            content: url('<?php echo wp_get_attachment_image_src(170, array(854, 854))[0]; ?>');
        }
    </style>
</head>

<body <?php body_class(); ?>>
    <?php
    // Get WooCommerce cart count
    $cart_count = 0;
    if (function_exists('WC') && WC()->cart) {
        $cart_count = WC()->cart->get_cart_contents_count();
    }

    // Get wishlist count
    $wishlist_count = 0;
    if (function_exists('yith_wcwl_count_products')) {
        $wishlist_count = (int) yith_wcwl_count_products();
    }

    // Get user info
    $current_user = wp_get_current_user();
    $is_user_logged_in = is_user_logged_in();
    $user_display_name = $is_user_logged_in ? $current_user->display_name : '';
    $my_account_url = '';
    if (function_exists('wc_get_page_permalink')) {
        $my_account_url = wc_get_page_permalink('myaccount');
    } elseif (function_exists('get_permalink')) {
        $account_page_id = get_option('woocommerce_myaccount_page_id');
        if ($account_page_id) {
            $my_account_url = get_permalink($account_page_id);
        }
    }

    // Get product categories for dropdown with subcategories
    $product_categories = array();
    $product_categories_with_children = array();
    if (function_exists('get_terms')) {
        $product_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => 0,
            'orderby' => 'name',
            'order' => 'ASC'
        ));

        // Get categories with their children for dropdown
        if (!is_wp_error($product_categories)) {
            foreach ($product_categories as $cat) {
                $children = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'parent' => $cat->term_id,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ));
                $product_categories_with_children[$cat->term_id] = array(
                    'category' => $cat,
                    'children' => !is_wp_error($children) ? $children : array()
                );
            }
        }
    }

    // Helper function to get subcategories
    if (!function_exists('savinge_get_category_children')) {
        function savinge_get_category_children($parent_id)
        {
            $children = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'parent' => $parent_id,
                'orderby' => 'name',
                'order' => 'ASC'
            ));
            return !is_wp_error($children) ? $children : array();
        }
    }

    // Get menu items
    $menu_items = array();
    $menu_locations = get_nav_menu_locations();
    if (isset($menu_locations['main-menu'])) {
        $menu_items = wp_get_nav_menu_items($menu_locations['main-menu']);
    }

    // Get shop URL
    $shop_url = '';
    if (function_exists('wc_get_page_permalink')) {
        $shop_url = wc_get_page_permalink('shop');
    }

    // Get special category URLs
    $sale_url = $shop_url ? add_query_arg('on_sale', '1', $shop_url) : '#';
    $popular_url = $shop_url ? add_query_arg('orderby', 'popularity', $shop_url) : '#';
    $new_url = $shop_url ? add_query_arg('orderby', 'date', $shop_url) : '#';
    ?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <!-- Black Friday Banner -->
            <div class="black-friday-banner">
                <span class="fire-emoji">🔥</span> BLACK FRIDAY pasiūlymas: pamėgtus produktus įsigykite iki 50 % pigiau
            </div>

            <!-- Top Info Bar -->
            <div class="top-info">
                <div class="top-info-container">
                    <div class="top-info-left">
                        <?php
                        // Get pages for top info links
                        $about_page = get_page_by_path('apie-mus');
                        $contact_page = get_page_by_path('kontaktai');
                        $faq_page = get_page_by_path('duk');
                        $advice_page = get_page_by_path('naudingi-patarimai');

                        if ($about_page) {
                            echo '<a href="' . esc_url(get_permalink($about_page->ID)) . '">Apie mus</a>';
                        } else {
                            echo '<a href="' . esc_url(home_url('/apie-mus')) . '">Apie mus</a>';
                        }

                        if ($contact_page) {
                            echo '<a href="' . esc_url(get_permalink($contact_page->ID)) . '">Kontaktai</a>';
                        } else {
                            echo '<a href="' . esc_url(home_url('/kontaktai')) . '">Kontaktai</a>';
                        }

                        if ($faq_page) {
                            echo '<a href="' . esc_url(get_permalink($faq_page->ID)) . '">D.U.K</a>';
                        } else {
                            echo '<a href="' . esc_url(home_url('/duk')) . '">D.U.K</a>';
                        }

                        if ($advice_page) {
                            echo '<a href="' . esc_url(get_permalink($advice_page->ID)) . '">Naudingi patarimai</a>';
                        } else {
                            echo '<a href="' . esc_url(get_post_type_archive_link('patarimai')) . '">Naudingi patarimai</a>';
                        }
                        ?>
                    </div>
                    <div class="top-info-right">
                        <span><a href="tel:+37060832314">+370 608 32 314</a></span>
                        <span><a href="mailto:info@athome24.lt">info@athome24.lt</a></span>
                    </div>
                </div>
            </div>

            <!-- Main Header -->
            <header class="main-header">
                <div class="header-container">
                    <!-- Logo -->
                    <div class="logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php bloginfo('name'); ?>"
                            class="logo-link">
                            <?php
                            // Get logo from WordPress Customizer
                            $logo_id = get_theme_mod('custom_logo');

                            // Try ACF header logo field if available
                            $header_logo = null;
                            if (function_exists('get_field')) {
                                $header_logo = get_field('header_logo', 'option');
                                if (empty($header_logo)) {
                                    $header_logo = get_field('header_logo', 5);
                                }
                            }

                            // Use ACF logo if available, otherwise use customizer logo
                            if (!empty($header_logo)) {
                                if (is_array($header_logo) && isset($header_logo['ID'])) {
                                    $logo_url = wp_get_attachment_image_src($header_logo['ID'], 'full')[0];
                                    $logo_alt = !empty($header_logo['alt']) ? $header_logo['alt'] : get_bloginfo('name');
                                } elseif (is_numeric($header_logo)) {
                                    $logo_url = wp_get_attachment_image_src($header_logo, 'full')[0];
                                    $logo_alt = get_bloginfo('name');
                                } else {
                                    $logo_url = $header_logo;
                                    $logo_alt = get_bloginfo('name');
                                }
                            } elseif ($logo_id) {
                                $logo_url = wp_get_attachment_image_src($logo_id, 'full')[0];
                                $logo_alt = get_bloginfo('name');
                            }

                            if (!empty($logo_url)):
                                ?>
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($logo_alt); ?>"
                                    class="site-logo"
                                    style="height: auto; max-height: 50px; width: auto; max-width: 200px;">
                            <?php else: ?>
                                <div class="logo-icon"></div>
                            <?php endif; ?>
                        </a>
                    </div>

                    <div class="actions">
                        <div class="actions-item">
                            <!-- Search -->
                            <div class="search-container">
                                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                                    class="search-form">
                                    <input type="search" name="s" class="search-box" placeholder="Ieškoti..."
                                        value="<?php echo get_search_query(); ?>">
                                    <button type="submit" class="search-btn"
                                        aria-label="<?php esc_attr_e('Search', 'savinge'); ?>">
                                        <svg xmlns="https://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 18 18" fill="none">
                                            <path
                                                d="M12.4974 10.9954H11.7076L11.4277 10.7255C12.4075 9.586 12.9973 8.10662 12.9973 6.49729C12.9973 2.90879 10.0879 0 6.49867 0C2.90941 0 0 2.90879 0 6.49729C0 10.0858 2.90941 12.9946 6.49867 12.9946C8.10834 12.9946 9.58804 12.4048 10.7278 11.4252L10.9978 11.7051V12.4948L15.9967 17.4827L17.4864 15.9933L12.4974 10.9954ZM6.49867 10.9954C4.00918 10.9954 1.99959 8.98625 1.99959 6.49729C1.99959 4.00833 4.00918 1.99917 6.49867 1.99917C8.98816 1.99917 10.9978 4.00833 10.9978 6.49729C10.9978 8.98625 8.98816 10.9954 6.49867 10.9954Z"
                                                fill="white" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Header Actions -->
                            <div class="header-actions-item">
                                <div class="header-actions">
                                    <div class="hamburger" onclick="toggleMobileDropdown()"
                                        aria-label="<?php esc_attr_e('Toggle Menu', 'savinge'); ?>">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <!-- User Account Icon -->
                                    <div class="header-icon user-account-icon"
                                        title="<?php echo $is_user_logged_in ? esc_attr($user_display_name) : esc_attr__('My Account', 'savinge'); ?>">
                                        <a href="<?php echo esc_url($my_account_url ? $my_account_url : home_url('/my-account')); ?>"
                                            aria-label="<?php esc_attr_e('My Account', 'savinge'); ?>">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="18" height="25"
                                                viewBox="0 0 18 25" fill="none">
                                                <path
                                                    d="M9 11C6.105 11 3.75 8.645 3.75 5.75C3.75 2.855 6.105 0.5 9 0.5C11.895 0.5 14.25 2.855 14.25 5.75C14.25 8.645 11.895 11 9 11ZM9 2C6.93225 2 5.25 3.68225 5.25 5.75C5.25 7.81775 6.93225 9.5 9 9.5C11.0677 9.5 12.75 7.81775 12.75 5.75C12.75 3.68225 11.0677 2 9 2ZM14.9565 24.5H3.0435C2.23656 24.4992 1.46289 24.1783 0.892298 23.6077C0.321703 23.0371 0.000794235 22.2634 0 21.4565C0 16.4945 4.03725 12.4565 9 12.4565C13.9628 12.4565 18 16.4945 18 21.4565C17.9992 22.2634 17.6783 23.0371 17.1077 23.6077C16.5371 24.1783 15.7634 24.4992 14.9565 24.5ZM9 13.9565C4.8645 13.9565 1.5 17.321 1.5 21.4565C1.5004 21.8657 1.66314 22.2581 1.95252 22.5475C2.2419 22.8369 2.63426 22.9996 3.0435 23H14.9565C15.3657 22.9996 15.7581 22.8369 16.0475 22.5475C16.3369 22.2581 16.4996 21.8657 16.5 21.4565C16.5 17.321 13.1355 13.9565 9 13.9565Z"
                                                    fill="#1F1F1F" />
                                            </svg>
                                            <?php if ($is_user_logged_in && $user_display_name): ?>
                                                <span
                                                    class="user-name-tooltip"><?php echo esc_html($user_display_name); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </div>

                                    <!-- Wishlist Icon -->
                                    <div class="header-icon">
                                        <a href="<?php echo esc_url(function_exists('yith_wcwl_get_wishlist_url') ? yith_wcwl_get_wishlist_url() : home_url('/megstamiausi')); ?>"
                                            aria-label="<?php esc_attr_e('Wishlist', 'savinge'); ?>">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="27" height="24"
                                                viewBox="0 0 27 24" fill="none">
                                                <path
                                                    d="M4.94629 0.72876C6.88726 -0.174452 8.7073 0.0963381 10.1855 0.743408C11.5778 1.35291 12.6727 2.29943 13.2998 2.92407C13.9281 2.30126 15.0225 1.35587 16.4141 0.746338C17.8913 0.0993076 19.7098 -0.172354 21.6523 0.72876H21.6514C22.7729 1.23073 23.7674 1.98344 24.5596 2.92993C25.3515 3.8763 25.9206 4.99187 26.2246 6.19263C26.4912 7.23199 26.5215 8.31959 26.3125 9.37231C26.1034 10.4249 25.6604 11.416 25.0176 12.2708V12.2717C23.9995 13.6069 21.2837 16.2719 18.7881 18.636C17.5378 19.8203 16.3392 20.9326 15.4297 21.7698L13.8066 23.2532C13.6677 23.3796 13.4873 23.4504 13.2998 23.4504C13.1121 23.4504 12.931 23.3798 12.792 23.2532V23.2522C12.5988 23.0773 10.307 21.0008 7.81055 18.636C5.31503 16.272 2.60021 13.6069 1.58203 12.2717L1.58105 12.2708C0.938542 11.4159 0.496168 10.4248 0.287109 9.37231C0.0780781 8.31964 0.10767 7.232 0.374023 6.19263C0.678007 4.99175 1.24797 3.87637 2.04004 2.92993C2.83186 1.98382 3.82528 1.23073 4.94629 0.72876ZM21.0244 2.11938C19.1816 1.26498 17.4181 1.8421 16.0918 2.65942C14.7802 3.46769 13.9224 4.49515 13.8916 4.53247L13.8926 4.53345C13.8212 4.62112 13.7308 4.69202 13.6289 4.74048C13.527 4.78893 13.4155 4.81311 13.3027 4.81274C13.1903 4.81297 13.0792 4.7888 12.9775 4.74048C12.8756 4.69202 12.7853 4.62114 12.7139 4.53345V4.53247C11.8679 3.51067 10.7192 2.60954 9.46387 2.1145C8.20956 1.61987 6.85606 1.53316 5.58496 2.12524L5.58203 2.12622C4.66964 2.53309 3.86045 3.1444 3.21484 3.91333C2.56925 4.68234 2.10361 5.58957 1.85449 6.56665V6.56763C1.64445 7.38275 1.61944 8.23545 1.78125 9.06177C1.943 9.88764 2.28726 10.6659 2.78809 11.3381L2.96289 11.5588C3.91076 12.7223 5.97634 14.7717 8.06152 16.7639C10.2013 18.8083 12.3505 20.7811 13.3027 21.6506C14.2547 20.7814 16.4048 18.8087 18.5449 16.7639C20.7693 14.6387 22.9716 12.4487 23.8184 11.3381C24.3205 10.6654 24.665 9.88583 24.8271 9.05884C24.9893 8.23148 24.9645 7.37787 24.7539 6.56177V6.56079C24.5045 5.58381 24.0391 4.67646 23.3936 3.90747C22.748 3.13857 21.9386 2.52744 21.0264 2.12036L21.0244 2.11938Z"
                                                    fill="#1F1F1F" stroke="#1F1F1F" stroke-width="0.3" />
                                            </svg>
                                            <?php if ($wishlist_count > 0): ?>
                                                <span class="cart-badge"><?php echo esc_html($wishlist_count); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </div>

                                    <!-- Cart Icon -->
                                    <div class="header-icon">
                                        <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart')); ?>"
                                            aria-label="<?php esc_attr_e('Cart', 'savinge'); ?>">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="19" height="24"
                                                viewBox="0 0 19 24" fill="none">
                                                <path
                                                    d="M18.9789 19.8395L17.4974 7.27612C17.4067 6.665 17.0931 6.10855 16.6164 5.71307C16.1397 5.31759 15.5334 5.1108 14.9133 5.13219H13.7505C13.7505 4.96925 13.7505 4.80632 13.6988 4.64338C13.5093 3.32272 13.3457 2.18215 12.3293 1.21309C11.9661 0.830001 11.5282 0.524798 11.0424 0.316199C10.5566 0.107599 10.0331 0 9.504 0C8.97492 0 8.45142 0.107599 7.9656 0.316199C7.47978 0.524798 7.04188 0.830001 6.67875 1.21309C5.66234 2.19072 5.49869 3.33129 5.30919 4.64338C5.30919 4.80632 5.30919 4.96925 5.25751 5.13219H4.06883C3.44872 5.1108 2.84242 5.31759 2.36575 5.71307C1.88908 6.10855 1.57545 6.665 1.48476 7.27612L0.0290646 19.8395C-0.0355526 20.3397 0.00786468 20.8479 0.156411 21.33C0.304958 21.8121 0.555209 22.2571 0.890422 22.6352C1.29517 23.0778 1.79076 23.4287 2.34361 23.6639C2.89645 23.8992 3.49363 24.0134 4.09467 23.9988H14.9133C15.5028 24.0036 16.0867 23.8846 16.627 23.6497C17.1672 23.4148 17.6516 23.0691 18.0487 22.6352C18.3961 22.2629 18.659 21.8206 18.8195 21.3382C18.98 20.8558 19.0344 20.3447 18.9789 19.8395ZM7.01468 4.8835C7.20417 3.57999 7.30754 2.98826 7.87603 2.43942C8.30264 1.99346 8.88603 1.72917 9.504 1.70191C10.122 1.72917 10.7054 1.99346 11.132 2.43942C11.7005 2.98826 11.8038 3.57999 11.9933 4.8835V5.13219H6.99745C7.00606 5.04643 7.00606 4.96925 7.01468 4.8835ZM16.7566 21.4946C16.5232 21.7488 16.2381 21.9507 15.9203 22.0867C15.6025 22.2227 15.2592 22.2899 14.9133 22.2836H4.09467C3.74879 22.2899 3.40553 22.2227 3.08771 22.0867C2.76989 21.9507 2.48479 21.7488 2.25137 21.4946C2.06696 21.3022 1.92677 21.0722 1.8405 20.8204C1.75424 20.5687 1.72397 20.3013 1.75178 20.0368L3.21609 7.47336C3.25914 7.28284 3.3702 7.11436 3.52862 6.99922C3.68705 6.88408 3.88207 6.83011 4.07745 6.84733H5.19721C5.19721 7.17321 5.24028 7.49909 5.28335 7.82497C5.31533 8.05241 5.43675 8.25788 5.6209 8.39619C5.80505 8.5345 6.03685 8.59432 6.26529 8.56248C6.49374 8.53063 6.70013 8.40975 6.83905 8.2264C6.97797 8.04306 7.03805 7.81229 7.00606 7.58485C7.00606 7.33615 7.00606 7.09603 6.95438 6.84733H12.0881C12.0881 7.09603 12.0881 7.33615 12.0364 7.58485C12.0011 7.80501 12.0533 8.03022 12.1819 8.21272C12.3105 8.39523 12.5055 8.52068 12.7255 8.56248H12.8461C13.0551 8.56452 13.2577 8.49084 13.4162 8.35514C13.5747 8.21945 13.6782 8.03102 13.7074 7.82497C13.7505 7.49909 13.7763 7.17321 13.7936 6.84733H14.9478C15.1432 6.83011 15.3382 6.88408 15.4966 6.99922C15.655 7.11436 15.7661 7.28284 15.8091 7.47336L17.2562 20.0368C17.284 20.3013 17.2538 20.5687 17.1675 20.8204C17.0812 21.0722 16.941 21.3022 16.7566 21.4946Z"
                                                    fill="#231F20" />
                                            </svg>
                                            <?php if ($cart_count > 0): ?>
                                                <span class="cart-badge"><?php echo esc_html($cart_count); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <div class="nav-container custom_max_width">
                    <button class="catalog-btn" onclick="toggleDropdown()"
                        aria-label="<?php esc_attr_e('Toggle Catalog', 'savinge'); ?>">
                        Prekių katalogas <span><svg xmlns="http://www.w3.org/2000/svg" width="14" height="8"
                                viewBox="0 0 14 8" fill="none">
                                <path d="M0.75 0.75L6.75 6.75L12.75 0.75" stroke="white" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="catalog-dropdown" id="catalogDropdown">
                        <div class="dropdown-content">
                            <!-- Search in dropdown -->
                            <div class="dropdown-search">
                                <div class="search-container">
                                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                                        class="search-form">
                                        <input type="search" name="s" class="search-box" placeholder="Ko ieškote?"
                                            value="<?php echo get_search_query(); ?>">
                                        <button type="submit" class="search-btn"
                                            aria-label="<?php esc_attr_e('Search', 'savinge'); ?>">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 18 18" fill="none">
                                                <path
                                                    d="M12.4974 10.9954H11.7076L11.4277 10.7255C12.4075 9.586 12.9973 8.10662 12.9973 6.49729C12.9973 2.90879 10.0879 0 6.49867 0C2.90941 0 0 2.90879 0 6.49729C0 10.0858 2.90941 12.9946 6.49867 12.9946C8.10834 12.9946 9.58804 12.4048 10.7278 11.4252L10.9978 11.7051V12.4948L15.9967 17.4827L17.4864 15.9933L12.4974 10.9954ZM6.49867 10.9954C4.00918 10.9954 1.99959 8.98625 1.99959 6.49729C1.99959 4.00833 4.00918 1.99917 6.49867 1.99917C8.98816 1.99917 10.9978 4.00833 10.9978 6.49729C10.9978 8.98625 8.98816 10.9954 6.49867 10.9954Z"
                                                    fill="white" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Categories -->
                            <ul class="dropdown-categories">
                                <?php if ($sale_url && $shop_url): ?>
                                    <li>
                                        <a href="<?php echo esc_url($sale_url); ?>" class="special-item">
                                            <div>
                                                <svg xmlns="https://www.w3.org/2000/svg" width="20" height="13"
                                                    viewBox="0 0 20 13" fill="none">
                                                    <path
                                                        d="M18.5625 7.5625C18.7448 7.5625 18.9197 7.49007 19.0486 7.36114C19.1776 7.23221 19.25 7.05734 19.25 6.875V0.996875C19.25 0.732487 19.145 0.478928 18.958 0.291978C18.7711 0.105027 18.5175 0 18.2531 0H0.996875C0.732487 0 0.478928 0.105027 0.291978 0.291978C0.105028 0.478928 0 0.732487 0 0.996875V3.14875C0.00410327 3.39479 0.0990613 3.63062 0.266599 3.81085C0.434137 3.99108 0.662415 4.10297 0.9075 4.125C1.45451 4.125 1.97911 4.3423 2.36591 4.72909C2.7527 5.11589 2.97 5.64049 2.97 6.1875C2.97 6.73451 2.7527 7.25911 2.36591 7.64591C1.97911 8.0327 1.45451 8.25 0.9075 8.25C0.662415 8.27203 0.434137 8.38392 0.266599 8.56415C0.0990613 8.74438 0.00410327 8.98021 0 9.22625V11.3781C0 11.6425 0.105028 11.8961 0.291978 12.083C0.478928 12.27 0.732487 12.375 0.996875 12.375H18.2531C18.5175 12.375 18.7711 12.27 18.958 12.083C19.145 11.8961 19.25 11.6425 19.25 11.3781V9.625C19.25 9.44266 19.1776 9.26779 19.0486 9.13886C18.9197 9.00993 18.7448 8.9375 18.5625 8.9375C18.3802 8.9375 18.2053 9.00993 18.0764 9.13886C17.9474 9.26779 17.875 9.44266 17.875 9.625V11H8.25V10.6562C8.25 10.4739 8.17757 10.299 8.04864 10.1701C7.9197 10.0412 7.74484 9.96875 7.5625 9.96875C7.38016 9.96875 7.2053 10.0412 7.07636 10.1701C6.94743 10.299 6.875 10.4739 6.875 10.6562V11H1.375V9.55625C2.15207 9.39846 2.8507 8.97687 3.35251 8.36293C3.85432 7.74898 4.12845 6.98043 4.12845 6.1875C4.12845 5.39457 3.85432 4.62602 3.35251 4.01207C2.8507 3.39813 2.15207 2.97654 1.375 2.81875V1.375H6.875V1.71875C6.875 1.90109 6.94743 2.07595 7.07636 2.20489C7.2053 2.33382 7.38016 2.40625 7.5625 2.40625C7.74484 2.40625 7.9197 2.33382 8.04864 2.20489C8.17757 2.07595 8.25 1.90109 8.25 1.71875V1.375H17.875V6.875C17.875 7.05734 17.9474 7.23221 18.0764 7.36114C18.2053 7.49007 18.3802 7.5625 18.5625 7.5625Z"
                                                        fill="#FF240C" />
                                                    <path
                                                        d="M9.721 9.1875L13.545 3.5875H14.697L10.873 9.1875H9.721ZM10.233 6.6195C9.961 6.6195 9.71567 6.55817 9.497 6.4355C9.27833 6.31283 9.10767 6.13683 8.985 5.9075C8.86233 5.67283 8.801 5.39283 8.801 5.0675C8.801 4.74217 8.86233 4.46483 8.985 4.2355C9.10767 4.00617 9.27833 3.83017 9.497 3.7075C9.71567 3.5795 9.961 3.5155 10.233 3.5155C10.5103 3.5155 10.7557 3.5795 10.969 3.7075C11.1877 3.83017 11.3583 4.00617 11.481 4.2355C11.609 4.46483 11.673 4.74217 11.673 5.0675C11.673 5.3875 11.609 5.66483 11.481 5.8995C11.3583 6.13417 11.1877 6.31283 10.969 6.4355C10.7557 6.55817 10.5103 6.6195 10.233 6.6195ZM10.241 5.8595C10.3743 5.8595 10.4837 5.79817 10.569 5.6755C10.6597 5.55283 10.705 5.35017 10.705 5.0675C10.705 4.78483 10.6597 4.58217 10.569 4.4595C10.4837 4.33683 10.3743 4.2755 10.241 4.2755C10.1077 4.2755 9.993 4.33683 9.897 4.4595C9.80633 4.58217 9.761 4.78483 9.761 5.0675C9.761 5.35017 9.80633 5.55283 9.897 5.6755C9.993 5.79817 10.1077 5.8595 10.241 5.8595ZM14.185 9.2595C13.9077 9.2595 13.6597 9.19817 13.441 9.0755C13.2277 8.95283 13.0597 8.77683 12.937 8.5475C12.8143 8.31283 12.753 8.03283 12.753 7.7075C12.753 7.38217 12.8143 7.10483 12.937 6.8755C13.0597 6.64617 13.2277 6.47017 13.441 6.3475C13.6597 6.2195 13.9077 6.1555 14.185 6.1555C14.4623 6.1555 14.7077 6.2195 14.921 6.3475C15.1397 6.47017 15.3103 6.64617 15.433 6.8755C15.561 7.10483 15.625 7.38217 15.625 7.7075C15.625 8.03283 15.561 8.31283 15.433 8.5475C15.3103 8.77683 15.1397 8.95283 14.921 9.0755C14.7077 9.19817 14.4623 9.2595 14.185 9.2595ZM14.185 8.4995C14.3183 8.4995 14.4303 8.43817 14.521 8.3155C14.6117 8.19283 14.657 7.99017 14.657 7.7075C14.657 7.42483 14.6117 7.22217 14.521 7.0995C14.4303 6.97683 14.3183 6.9155 14.185 6.9155C14.057 6.9155 13.945 6.97683 13.849 7.0995C13.7583 7.22217 13.713 7.42483 13.713 7.7075C13.713 7.99017 13.7583 8.19283 13.849 8.3155C13.945 8.43817 14.057 8.4995 14.185 8.4995Z"
                                                        fill="#FF240C" />
                                                </svg>
                                                Akcijos
                                            </div>
                                            <span class="arrow">›</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if ($popular_url && $shop_url): ?>
                                    <li>
                                        <a href="<?php echo esc_url($popular_url); ?>" class="popular-item">
                                            <span>
                                                🔥 Populiariausi</span> <span class="arrow">›</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if ($new_url && $shop_url): ?>
                                    <li>
                                        <a href="<?php echo esc_url($new_url); ?>" class="new-item">
                                            <span>🆕 Naujienos</span> <span class="arrow">›</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                // Display product categories dynamically with subcategories
                                if (!is_wp_error($product_categories) && !empty($product_categories)):
                                    foreach ($product_categories as $category):
                                        $category_link = get_term_link($category);
                                        if (is_wp_error($category_link))
                                            continue;

                                        // Get subcategories
                                        $subcategories = savinge_get_category_children($category->term_id);
                                        $has_subcategories = !empty($subcategories);
                                        ?>
                                        <li class="<?php echo $has_subcategories ? 'has-children' : ''; ?>">
                                            <a href="<?php echo esc_url($category_link); ?>"
                                                class="<?php echo $has_subcategories ? 'category-with-children' : ''; ?>">
                                                <?php echo esc_html($category->name); ?>
                                                <?php if ($has_subcategories): ?>
                                                    <span class="arrow">›</span>
                                                <?php else: ?>
                                                    <span class="arrow">›</span>
                                                <?php endif; ?>
                                            </a>
                                            <?php if ($has_subcategories): ?>
                                                <ul class="subcategories">
                                                    <?php foreach ($subcategories as $subcat):
                                                        $subcat_link = get_term_link($subcat);
                                                        if (is_wp_error($subcat_link))
                                                            continue;
                                                        ?>
                                                        <li>
                                                            <a href="<?php echo esc_url($subcat_link); ?>">
                                                                <?php echo esc_html($subcat->name); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </li>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </ul>

                            <!-- Footer -->
                            <div class="dropdown-footer">
                                +370 608 32 314 • info@athome24.lt
                            </div>
                        </div>
                    </div>

                    <!-- Marquee Navigation for all screens -->
                    <div class="nav-marquee">
                        <div class="swiper nav-links-swiper">
                            <div class="swiper-wrapper nav-links">
                                <?php if ($sale_url): ?>
                                    <div class="swiper-slide" style="display: flex; align-items: center;">
                                        <a href="<?php echo esc_url($sale_url); ?>"
                                            style="color: #FF240C; display: flex; align-items: center; gap: 5px;">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="20" height="13"
                                                viewBox="0 0 20 13" fill="none">
                                                <path
                                                    d="M18.5625 7.5625C18.7448 7.5625 18.9197 7.49007 19.0486 7.36114C19.1776 7.23221 19.25 7.05734 19.25 6.875V0.996875C19.25 0.732487 19.145 0.478928 18.958 0.291978C18.7711 0.105027 18.5175 0 18.2531 0H0.996875C0.732487 0 0.478928 0.105027 0.291978 0.291978C0.105028 0.478928 0 0.732487 0 0.996875V3.14875C0.00410327 3.39479 0.0990613 3.63062 0.266599 3.81085C0.434137 3.99108 0.662415 4.10297 0.9075 4.125C1.45451 4.125 1.97911 4.3423 2.36591 4.72909C2.7527 5.11589 2.97 5.64049 2.97 6.1875C2.97 6.73451 2.7527 7.25911 2.36591 7.64591C1.97911 8.0327 1.45451 8.25 0.9075 8.25C0.662415 8.27203 0.434137 8.38392 0.266599 8.56415C0.0990613 8.74438 0.00410327 8.98021 0 9.22625V11.3781C0 11.6425 0.105028 11.8961 0.291978 12.083C0.478928 12.27 0.732487 12.375 0.996875 12.375H18.2531C18.5175 12.375 18.7711 12.27 18.958 12.083C19.145 11.8961 19.25 11.6425 19.25 11.3781V9.625C19.25 9.44266 19.1776 9.26779 19.0486 9.13886C18.9197 9.00993 18.7448 8.9375 18.5625 8.9375C18.3802 8.9375 18.2053 9.00993 18.0764 9.13886C17.9474 9.26779 17.875 9.44266 17.875 9.625V11H8.25V10.6562C8.25 10.4739 8.17757 10.299 8.04864 10.1701C7.9197 10.0412 7.74484 9.96875 7.5625 9.96875C7.38016 9.96875 7.2053 10.0412 7.07636 10.1701C6.94743 10.299 6.875 10.4739 6.875 10.6562V11H1.375V9.55625C2.15207 9.39846 2.8507 8.97687 3.35251 8.36293C3.85432 7.74898 4.12845 6.98043 4.12845 6.1875C4.12845 5.39457 3.85432 4.62602 3.35251 4.01207C2.8507 3.39813 2.15207 2.97654 1.375 2.81875V1.375H6.875V1.71875C6.875 1.90109 6.94743 2.07595 7.07636 2.20489C7.2053 2.33382 7.38016 2.40625 7.5625 2.40625C7.74484 2.40625 7.9197 2.33382 8.04864 2.20489C8.17757 2.07595 8.25 1.90109 8.25 1.71875V1.375H17.875V6.875C17.875 7.05734 17.9474 7.23221 18.0764 7.36114C18.2053 7.49007 18.3802 7.5625 18.5625 7.5625Z"
                                                    fill="#FF240C" />
                                                <path
                                                    d="M9.721 9.1875L13.545 3.5875H14.697L10.873 9.1875H9.721ZM10.233 6.6195C9.961 6.6195 9.71567 6.55817 9.497 6.4355C9.27833 6.31283 9.10767 6.13683 8.985 5.9075C8.86233 5.67283 8.801 5.39283 8.801 5.0675C8.801 4.74217 8.86233 4.46483 8.985 4.2355C9.10767 4.00617 9.27833 3.83017 9.497 3.7075C9.71567 3.5795 9.961 3.5155 10.233 3.5155C10.5103 3.5155 10.7557 3.5795 10.969 3.7075C11.1877 3.83017 11.3583 4.00617 11.481 4.2355C11.609 4.46483 11.673 4.74217 11.673 5.0675C11.673 5.3875 11.609 5.66483 11.481 5.8995C11.3583 6.13417 11.1877 6.31283 10.969 6.4355C10.7557 6.55817 10.5103 6.6195 10.233 6.6195ZM10.241 5.8595C10.3743 5.8595 10.4837 5.79817 10.569 5.6755C10.6597 5.55283 10.705 5.35017 10.705 5.0675C10.705 4.78483 10.6597 4.58217 10.569 4.4595C10.4837 4.33683 10.3743 4.2755 10.241 4.2755C10.1077 4.2755 9.993 4.33683 9.897 4.4595C9.80633 4.58217 9.761 4.78483 9.761 5.0675C9.761 5.35017 9.80633 5.55283 9.897 5.6755C9.993 5.79817 10.1077 5.8595 10.241 5.8595ZM14.185 9.2595C13.9077 9.2595 13.6597 9.19817 13.441 9.0755C13.2277 8.95283 13.0597 8.77683 12.937 8.5475C12.8143 8.31283 12.753 8.03283 12.753 7.7075C12.753 7.38217 12.8143 7.10483 12.937 6.8755C13.0597 6.64617 13.2277 6.47017 13.441 6.3475C13.6597 6.2195 13.9077 6.1555 14.185 6.1555C14.4623 6.1555 14.7077 6.2195 14.921 6.3475C15.1397 6.47017 15.3103 6.64617 15.433 6.8755C15.561 7.10483 15.625 7.38217 15.625 7.7075C15.625 8.03283 15.561 8.31283 15.433 8.5475C15.3103 8.77683 15.1397 8.95283 14.921 9.0755C14.7077 9.19817 14.4623 9.2595 14.185 9.2595ZM14.185 8.4995C14.3183 8.4995 14.4303 8.43817 14.521 8.3155C14.6117 8.19283 14.657 7.99017 14.657 7.7075C14.657 7.42483 14.6117 7.22217 14.521 7.0995C14.4303 6.97683 14.3183 6.9155 14.185 6.9155C14.057 6.9155 13.945 6.97683 13.849 7.0995C13.7583 7.22217 13.713 7.42483 13.713 7.7075C13.713 7.99017 13.7583 8.19283 13.849 8.3155C13.945 8.43817 14.057 8.4995 14.185 8.4995Z"
                                                    fill="#FF240C" />
                                            </svg>
                                            Akcijos
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if ($popular_url): ?>
                                    <div class="swiper-slide" style="display: flex; align-items: center;">
                                        <a href="<?php echo esc_url($popular_url); ?>"
                                            style="color: #FF6C10; display: flex; align-items: center; gap: 5px;">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="20" height="13"
                                                viewBox="0 0 20 13" fill="none">
                                                <path
                                                    d="M18.5625 7.5625C18.7448 7.5625 18.9197 7.49007 19.0486 7.36114C19.1776 7.23221 19.25 7.05734 19.25 6.875V0.996875C19.25 0.732487 19.145 0.478928 18.958 0.291978C18.7711 0.105027 18.5175 0 18.2531 0H0.996875C0.732487 0 0.478928 0.105027 0.291978 0.291978C0.105028 0.478928 0 0.732487 0 0.996875V3.14875C0.00410327 3.39479 0.0990613 3.63062 0.266599 3.81085C0.434137 3.99108 0.662415 4.10297 0.9075 4.125C1.45451 4.125 1.97911 4.3423 2.36591 4.72909C2.7527 5.11589 2.97 5.64049 2.97 6.1875C2.97 6.73451 2.7527 7.25911 2.36591 7.64591C1.97911 8.0327 1.45451 8.25 0.9075 8.25C0.662415 8.27203 0.434137 8.38392 0.266599 8.56415C0.0990613 8.74438 0.00410327 8.98021 0 9.22625V11.3781C0 11.6425 0.105028 11.8961 0.291978 12.083C0.478928 12.27 0.732487 12.375 0.996875 12.375H18.2531C18.5175 12.375 18.7711 12.27 18.958 12.083C19.145 11.8961 19.25 11.6425 19.25 11.3781V9.625C19.25 9.44266 19.1776 9.26779 19.0486 9.13886C18.9197 9.00993 18.7448 8.9375 18.5625 8.9375C18.3802 8.9375 18.2053 9.00993 18.0764 9.13886C17.9474 9.26779 17.875 9.44266 17.875 9.625V11H8.25V10.6562C8.25 10.4739 8.17757 10.299 8.04864 10.1701C7.9197 10.0412 7.74484 9.96875 7.5625 9.96875C7.38016 9.96875 7.2053 10.0412 7.07636 10.1701C6.94743 10.299 6.875 10.4739 6.875 10.6562V11H1.375V9.55625C2.15207 9.39846 2.8507 8.97687 3.35251 8.36293C3.85432 7.74898 4.12845 6.98043 4.12845 6.1875C4.12845 5.39457 3.85432 4.62602 3.35251 4.01207C2.8507 3.39813 2.15207 2.97654 1.375 2.81875V1.375H6.875V1.71875C6.875 1.90109 6.94743 2.07595 7.07636 2.20489C7.2053 2.33382 7.38016 2.40625 7.5625 2.40625C7.74484 2.40625 7.9197 2.33382 8.04864 2.20489C8.17757 2.07595 8.25 1.90109 8.25 1.71875V1.375H17.875V6.875C17.875 7.05734 17.9474 7.23221 18.0764 7.36114C18.2053 7.49007 18.3802 7.5625 18.5625 7.5625Z"
                                                    fill="#FF6C10" />
                                                <path
                                                    d="M9.721 9.1875L13.545 3.5875H14.697L10.873 9.1875H9.721ZM10.233 6.6195C9.961 6.6195 9.71567 6.55817 9.497 6.4355C9.27833 6.31283 9.10767 6.13683 8.985 5.9075C8.86233 5.67283 8.801 5.39283 8.801 5.0675C8.801 4.74217 8.86233 4.46483 8.985 4.2355C9.10767 4.00617 9.27833 3.83017 9.497 3.7075C9.71567 3.5795 9.961 3.5155 10.233 3.5155C10.5103 3.5155 10.7557 3.5795 10.969 3.7075C11.1877 3.83017 11.3583 4.00617 11.481 4.2355C11.609 4.46483 11.673 4.74217 11.673 5.0675C11.673 5.3875 11.609 5.66483 11.481 5.8995C11.3583 6.13417 11.1877 6.31283 10.969 6.4355C10.7557 6.55817 10.5103 6.6195 10.233 6.6195ZM10.241 5.8595C10.3743 5.8595 10.4837 5.79817 10.569 5.6755C10.6597 5.55283 10.705 5.35017 10.705 5.0675C10.705 4.78483 10.6597 4.58217 10.569 4.4595C10.4837 4.33683 10.3743 4.2755 10.241 4.2755C10.1077 4.2755 9.993 4.33683 9.897 4.4595C9.80633 4.58217 9.761 4.78483 9.761 5.0675C9.761 5.35017 9.80633 5.55283 9.897 5.6755C9.993 5.79817 10.1077 5.8595 10.241 5.8595ZM14.185 9.2595C13.9077 9.2595 13.6597 9.19817 13.441 9.0755C13.2277 8.95283 13.0597 8.77683 12.937 8.5475C12.8143 8.31283 12.753 8.03283 12.753 7.7075C12.753 7.38217 12.8143 7.10483 12.937 6.8755C13.0597 6.64617 13.2277 6.47017 13.441 6.3475C13.6597 6.2195 13.9077 6.1555 14.185 6.1555C14.4623 6.1555 14.7077 6.2195 14.921 6.3475C15.1397 6.47017 15.3103 6.64617 15.433 6.8755C15.561 7.10483 15.625 7.38217 15.625 7.7075C15.625 8.03283 15.561 8.31283 15.433 8.5475C15.3103 8.77683 15.1397 8.95283 14.921 9.0755C14.7077 9.19817 14.4623 9.2595 14.185 9.2595ZM14.185 8.4995C14.3183 8.4995 14.4303 8.43817 14.521 8.3155C14.6117 8.19283 14.657 7.99017 14.657 7.7075C14.657 7.42483 14.6117 7.22217 14.521 7.0995C14.4303 6.97683 14.3183 6.9155 14.185 6.9155C14.057 6.9155 13.945 6.97683 13.849 7.0995C13.7583 7.22217 13.713 7.42483 13.713 7.7075C13.713 7.99017 13.7583 8.19283 13.849 8.3155C13.945 8.43817 14.057 8.4995 14.185 8.4995Z"
                                                    fill="#FF6C10" />
                                            </svg>
                                            Antras šansas
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if ($popular_url): ?>
                                    <div class="swiper-slide" style="display: flex; align-items: center;">
                                        <a href="<?php echo esc_url($popular_url); ?>"
                                            style="color: #DC9600; display: flex; align-items: center; gap: 5px;">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="22" height="22"
                                                viewBox="0 0 22 22" fill="none">
                                                <path
                                                    d="M13.7926 12.2897L13.75 12.375V9.78931C13.75 9.73431 13.75 9.625 13.75 9.625C13.7926 6.90671 12.1337 4.44984 12.1337 4.44984L11 2.75L9.86666 4.45019C8.81247 6.03126 8.24995 7.88903 8.25 9.78931V12.375L8.20737 12.2897C7.34254 10.56 5.93998 9.15746 4.21025 8.29263L4.125 8.25V13.75C4.125 17.5471 7.20294 20.625 11 20.625C14.7971 20.625 17.875 17.5471 17.875 13.75V7.80204L15.8372 9.625C14.9933 10.3759 14.2995 11.2802 13.7926 12.2897Z"
                                                    fill="#FAB400" />
                                                <path
                                                    d="M5.5 13.75V9.08359C5.0932 8.78398 4.66174 8.51937 4.21025 8.29263L4.125 8.25V13.75C4.125 17.5471 7.20294 20.625 11 20.625C11.232 20.625 11.4613 20.6126 11.6875 20.5903C8.21356 20.2452 5.5 17.3147 5.5 13.75Z"
                                                    fill="#DC9600" />
                                                <path
                                                    d="M10.9996 12.375L8.95396 17.6347C8.39468 19.0737 9.45584 20.625 10.9996 20.625C12.5434 20.625 13.6046 19.0737 13.0449 17.6347L10.9996 12.375Z"
                                                    fill="#FA6450" />
                                            </svg>
                                            Populiariausi
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if ($new_url): ?>
                                    <div class="swiper-slide" style="display: flex; align-items: center;">
                                        <a href="<?php echo esc_url($new_url); ?>"
                                            style="color: #3BA57F; display: flex; align-items: center; gap: 5px;">
                                            <svg xmlns="https://www.w3.org/2000/svg" width="22" height="22"
                                                viewBox="0 0 22 22" fill="none">
                                                <path
                                                    d="M16.0714 19.6769L18.4264 19.931C15.6651 22.991 10.3043 21.8869 8.47656 21.4046C9.51056 18.7213 13.7129 8.71887 13.2083 8.47859L16.5619 0C18.8791 2.61009 18.0809 6.24663 18.0809 6.24663C18.0809 6.24663 19.677 5.91937 20.2627 6.15106C21.1685 10.438 18.1621 12.6868 18.0424 12.7748C18.1174 12.7737 19.3349 12.727 20.3775 13.1395C19.8457 14.3382 17.5351 16.4725 17.5351 16.4725L19.7388 16.6124C18.6248 18.4277 16.0714 19.6769 16.0714 19.6769Z"
                                                    fill="#AAD15D" />
                                                <path
                                                    d="M16.5617 0L13.2084 8.47894C12.7065 8.23969 8.74721 18.2909 7.56506 20.9969C5.73218 19.9537 1.49443 17.1497 1.56834 13.2636L3.45999 14.6891C3.45999 14.6891 2.45177 12.0312 2.88077 9.94537L4.58406 11.3506C4.58406 11.3506 4.35546 8.21219 4.79065 6.97503C5.83049 7.38616 6.68952 8.25447 6.74349 8.30603C6.71668 8.15994 6.05874 4.46222 9.65471 1.95559C10.2405 2.18728 11.181 3.51759 11.181 3.51759C11.181 3.51759 13.086 0.318656 16.5617 0Z"
                                                    fill="#90B74B" />
                                            </svg>
                                            Naujienos
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php
                                // Display product categories in marquee navigation
                                if (!is_wp_error($product_categories) && !empty($product_categories)):
                                    foreach ($product_categories as $category):
                                        $category_link = get_term_link($category);
                                        if (is_wp_error($category_link))
                                            continue;
                                        ?>
                                        <div class="swiper-slide" style="display: flex; align-items: center; color: #1F1F1F!;">
                                            <a
                                                href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category->name); ?></a>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Mobile Menu Dropdown -->
            <div class="mobile-menu-dropdown" id="mobileMenuDropdown" style="display: none;">
                <div class="close_btn" onclick="closeMobMenu()">
                    <svg xmlns="https://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path
                            d="M12.2197 0.219666C12.5126 -0.0732277 12.9874 -0.0732277 13.2803 0.219666C13.5732 0.512559 13.5732 0.987319 13.2803 1.28021L7.81055 6.74994L13.2803 12.2197C13.5732 12.5126 13.5732 12.9873 13.2803 13.2802C12.9874 13.5731 12.5126 13.5731 12.2197 13.2802L6.75 7.81049L1.28027 13.2802C0.98738 13.5731 0.51262 13.5731 0.219727 13.2802C-0.0731667 12.9873 -0.0731667 12.5126 0.219727 12.2197L5.68945 6.74994L0.219727 1.28021C-0.0731667 0.987319 -0.0731667 0.512559 0.219727 0.219666C0.51262 -0.0732277 0.98738 -0.0732277 1.28027 0.219666L6.75 5.68939L12.2197 0.219666Z"
                            fill="black" fill-opacity="0.55" />
                    </svg>
                </div>
                <div>
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form">
                        <input type="search" name="s" class="search-box" placeholder="Ieškoti..."
                            value="<?php echo get_search_query(); ?>">
                        <button type="submit" class="search-btn" aria-label="<?php esc_attr_e('Search', 'savinge'); ?>">
                            <svg xmlns="https://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <path
                                    d="M12.4974 10.9954H11.7076L11.4277 10.7255C12.4075 9.586 12.9973 8.10662 12.9973 6.49729C12.9973 2.90879 10.0879 0 6.49867 0C2.90941 0 0 2.90879 0 6.49729C0 10.0858 2.90941 12.9946 6.49867 12.9946C8.10834 12.9946 9.58804 12.4048 10.7278 11.4252L10.9978 11.7051V12.4948L15.9967 17.4827L17.4864 15.9933L12.4974 10.9954ZM6.49867 10.9954C4.00918 10.9954 1.99959 8.98625 1.99959 6.49729C1.99959 4.00833 4.00918 1.99917 6.49867 1.99917C8.98816 1.99917 10.9978 4.00833 10.9978 6.49729C10.9978 8.98625 8.98816 10.9954 6.49867 10.9954Z"
                                    fill="white" />
                            </svg>
                        </button>
                    </form>
                </div>
                <?php
                if (has_nav_menu('main-menu')):
                    wp_nav_menu(array(
                        'theme_location' => 'main-menu',
                        'container' => false,
                        'menu_class' => 'mobile-menu-list',
                        'fallback_cb' => false,
                    ));
                else:
                    // Fallback: Show categories if no menu is set
                    if (!is_wp_error($product_categories) && !empty($product_categories)):
                        echo '<ul class="mobile-menu-list">';
                        foreach ($product_categories as $category):
                            $category_link = get_term_link($category);
                            if (is_wp_error($category_link))
                                continue;
                            echo '<li><a href="' . esc_url($category_link) . '">' . esc_html($category->name) . '</a></li>';
                        endforeach;
                        echo '</ul>';
                    endif;
                endif;
                ?>
                <div>
                    <?php at24_mobile_category_menu(); ?>
                </div>
            </div>

            <?php get_template_part('partials/popup'); ?>