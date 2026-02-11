<?php

add_theme_support('woocommerce', array(
    'thumbnail_image_width' => 400,
    'gallery_thumbnail_image_width' => 400,
    'single_image_width' => 500,
));

add_theme_support('align-wide');

add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
    function loop_columns()
    {

        if (is_page('249')) {
            return 4;
        } else {
            return 3;
        }
    }
}

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter('loop_shop_per_page', 'new_loop_shop_per_page', 20);

function new_loop_shop_per_page($cols)
{
    // $cols contains the current number of products per page based on the value stored on Options –> Reading
    // Return the number of products you wanna show per page.
    $cols = 24;
    return $cols;
}

add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single');
function woocommerce_add_to_cart_button_text_single()
{
    return __('Į krepšelį', 'woocommerce');
}

add_filter('woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives');
function woocommerce_add_to_cart_button_text_archives()
{
    return __('Į krepšelį', 'woocommerce');
}

add_filter('woocommerce_sale_flash', 'ds_replace_sale_text');

function ds_replace_sale_text($text)
{
    global $product;
    $stock = $product->get_stock_status();
    $product_type = $product->get_type();
    $sale_price = 0;
    $regular_price = 0;
    if ($product_type == 'variable') {
        $product_variations = $product->get_available_variations();
        foreach ($product_variations as $kay => $value) {
            if ($value['display_price'] < $value['display_regular_price']) {
                $sale_price = $value['display_price'];
                $regular_price = $value['display_regular_price'];
            }
        }
        if ($regular_price > $sale_price && $stock != 'outofstock') {
            $product_sale = intval(((intval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100);
            return '<span class="onsale d-flex justify-content-center align-items-center"> <span class="sale-icon" aria-hidden="true" data-icon="&#xe0da"></span> ' . esc_html($product_sale) . '%</span>';
        } else {
            return '';
        }
    } else {
        $regular_price = get_post_meta(get_the_ID(), '_regular_price', true);
        $sale_price = get_post_meta(get_the_ID(), '_sale_price', true);
        $product_sale = intval(((floatval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100);
        return '<span class="onsale d-flex justify-content-center align-items-center"> <span class="sale-icon" aria-hidden="true" data-icon="&#xe0da"></span> ' . esc_html($product_sale) . '%</span>';
    }
}

if (!function_exists('getSalePercent')) {
    function getSalePercent($product)
    {
        if (!$product || !$product->is_on_sale())
            return '';
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        if ($regular <= 0)
            return '';
        $percent = round((($regular - $sale) / $regular) * 100);
        return '-' . $percent . '%';
    }
}

function getSale($product)
{
    $stock = $product->get_stock_status();
    $product_type = $product->get_type();
    $sale_price = 0;
    $regular_price = 0;
    if ($product_type == 'variable') {
        $product_variations = $product->get_available_variations();
        foreach ($product_variations as $kay => $value) {
            if ($value['display_price'] < $value['display_regular_price']) {
                $sale_price = $value['display_price'];
                $regular_price = $value['display_regular_price'];
            }
        }
        if ($regular_price > $sale_price && $stock != 'outofstock') {
            $product_sale = intval(((intval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100);
            echo '<span class="onsale d-flex justify-content-center align-items-center"> <span class="sale-icon" aria-hidden="true" data-icon="&#xe0da"></span> ' . esc_html($product_sale) . '%</span>';
        } else {
            echo '';
        }
    } else {
        $regular_price = get_post_meta($product->get_id(), '_regular_price', true);
        $sale_price = get_post_meta($product->get_id(), '_sale_price', true);
        if ($sale_price) {
            $product_sale = intval(((floatval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100);
            echo '<span class="onsale d-flex justify-content-center align-items-center"> <span class="sale-icon" aria-hidden="true" data-icon="&#xe0da"></span> ' . esc_html($product_sale) . '%</span>';
        }
    }
}

add_action('woocommerce_before_cart', function () {
    remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
}, 5);

add_filter('woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_delimiter');
function wcc_change_breadcrumb_delimiter($defaults)
{
    $defaults['delimiter'] = ' &gt; ';
    return $defaults;
}

add_filter('woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text');
function wcc_change_breadcrumb_home_text($defaults)
{
    $icon = wp_get_attachment_image_src(66, array(854, 854))[0];
    ?>
    <style>
        .woocommerce-breadcrumb a:nth-child(1) {
            display: hidden;
        }

        .woocommerce-breadcrumb a:nth-child(1)::before {
            content: url('<?php echo $icon; ?>') ' > ';
            visibility: visible;
        }
    </style>
    <?php

    $defaults['home'] = '';
    return $defaults;
}

add_action('woocommerce_single_product_summary', 'custom_action_after_single_product_title', 6);
function custom_action_after_single_product_title()
{
    global $product;

    echo '<p class="sku-line">SKU: ' . $product->get_sku() . '</p>';
}

add_action('woocommerce_before_add_to_cart_form', 'stockLocation');
function stockLocation()
{
    global $product;

    if (!$product) {
        return;
    }

    // Agar product out of stock hai (manage stock ON ya OFF)
    if (!$product->is_in_stock()) {
        $text = 'Neturime';
        $add_class = 'stock-out';
    }
    // Agar manage stock OFF hai lekin product in stock hai
    elseif (!$product->managing_stock()) {
        $text = 'Turime';
        $add_class = 'stock-normal';
    }
    // Agar manage stock ON hai
    else {
        $stock_qty = (int) $product->get_stock_quantity();

        if ($stock_qty >= 10) {
            $text = 'Turime';
            $add_class = 'stock-normal';
        } elseif ($stock_qty < 10 && $stock_qty >= 5) {
            $text = 'Turime mažiau nei 10 vnt.';
            $add_class = 'stock-low';
        } elseif ($stock_qty < 5 && $stock_qty > 2) {
            $text = 'Turime mažiau nei 5 vnt.';
            $add_class = 'stock-few';
        } elseif ($stock_qty == 2) {
            $text = 'Turime tik 2 vnt.';
            $add_class = 'stock-few';
        } elseif ($stock_qty == 1) {
            $text = 'Turime tik 1 vnt.';
            $add_class = 'stock-few';
        } else {
            $text = 'Neturime';
            $add_class = 'stock-out';
        }
    }
    ?>

    <div class="mt-2 stock-list">
        <div class="d-flex align-items-center gap-3">
            <p class="m-0 <?php echo esc_attr($add_class); ?>">
                <?php echo esc_html($text); ?>
            </p>
        </div>
    </div>

    <?php
}


/**
 * @snippet       Plus Minus Quantity Buttons @ WooCommerce Product Page & Cart
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 5
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

// -------------
// 1. Show plus minus buttons

add_action('woocommerce_after_add_to_cart_button', 'bbloomer_display_quantity_plus');

function bbloomer_display_quantity_plus()
{
    global $product;
    echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $product->get_id() . '"]');

    // echo '<button type="button" class="plus">+</button>';
}

// add_action('woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus');

function bbloomer_display_quantity_minus()
{
    echo '<button type="button" class="minus">-</button>';
}

// -------------
// 2. Trigger update quantity script

// add_action('wp_footer', 'bbloomer_add_cart_quantity_plus_minus');

function bbloomer_add_cart_quantity_plus_minus()
{

    if (!is_product() && !is_cart()) {
        return;
    }

    wc_enqueue_js("

      $(document).on( 'click', 'button.plus, button.minus', function() {

         var qty = $( this ).parent( '.quantity' ).find( '.qty' );
         var val = parseFloat(qty.val());
         var max = parseFloat(qty.attr( 'max' ));
         var min = parseFloat(qty.attr( 'min' ));
         var step = parseFloat(qty.attr( 'step' ));

         if ( $( this ).is( '.plus' ) ) {
            if ( max && ( max <= val ) ) {
               qty.val( max ).change();
            } else {
               qty.val( val + step ).change();
            }
         } else {
            if ( min && ( min >= val ) ) {
               qty.val( min ).change();
            } else if ( val > 1 ) {
               qty.val( val - step ).change();
            }
         }

      });

   ");
}

add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);

function woo_remove_product_tabs($tabs)
{

    unset($tabs['reviews']);
    // unset($tabs['additional_information']);

    return $tabs;
}

// remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

/**
 * Related products section - summary entry-summary ke baad (PHP hook se)
 */
add_action('woocommerce_after_single_product_summary', 'savinge_related_products_section', 100);

function savinge_related_products_section()
{
    global $product;
    if (!$product) {
        return;
    }
    $current_product_id = $product->get_id();
    // Best-selling products (sab se ziyada sale hone wali)
    $best_selling = get_posts(array(
        'posts_per_page' => 12,
        'post_type' => 'product',
        'post_status' => 'publish',
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'post__not_in' => array($current_product_id),
        'fields' => 'ids',
    ));
    $related_ids = array_map('intval', $best_selling);

    if (empty($related_ids)) {
        return;
    }
    ?>
    <!-- Empty Section (related-products-section se pehle) -->
    <section class="main_product_section"></section>

    <!-- Related Products Section (summary ke baad) -->
    <section class="related-products-section">
        <div>
            <h2 class="section-title">Jums taip pat gali patikti</h2>
            <div class="swiper-container related-products-swiper">
                <div class="swiper-wrapper">
                    <?php
                    foreach ($related_ids as $related_id) {
                        $related_product = wc_get_product($related_id);
                        if (!$related_product) {
                            continue;
                        }
                        $product_title = $related_product->get_name();
                        $product_link = get_permalink($related_id);
                        $product_image = wp_get_attachment_image_src(get_post_thumbnail_id($related_id), 'medium');
                        $regular_price = $related_product->get_regular_price();
                        $sale_price = $related_product->get_sale_price();
                        $discount_percentage = 0;
                        if ($sale_price && $regular_price > 0) {
                            $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                        }
                        ?>
                        <div class="swiper-slide">
                            <div class="product-card">
                                <div class="top_bar_related_product">
                                    <div class="discount-badge">
                                        -<?php echo $discount_percentage; ?>%
                                    </div>
                                    <div>
                                        <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
                                    </div>
                                </div>
                                <div class="product-image">
                                    <a href="<?php echo esc_url($product_link); ?>">
                                        <img src="<?php echo esc_url($product_image[0]); ?>"
                                            alt="<?php echo esc_attr($product_title); ?>">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title">
                                        <a href="<?php echo esc_url($product_link); ?>">
                                            <?php echo esc_html($product_title); ?>
                                        </a>
                                    </h3>
                                    <div class="related-product-price">
                                        <?php if ($sale_price): ?>
                                            <span class="regular-price"><?php echo wc_price($regular_price); ?></span>
                                            <span class="sale-price"><?php echo wc_price($sale_price); ?></span>
                                        <?php else: ?>
                                            <span class="regular-price-only"><?php echo wc_price($regular_price); ?></span>
                                            <span class="no-sale-price">
                                                <p>0.00€</p>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?php echo esc_url($related_product->add_to_cart_url()); ?>"
                                        class="add-to-cart-btn" data-product-id="<?php echo esc_attr($related_id); ?>">
                                        Į krepšelį
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="related_products_navigation">
                    <div class="swiper-button-prev">
                        <svg xmlns="https://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none"
                            tabIndex="0" role="button" aria-label="Previous slide"
                            aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                            <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next">
                        <svg xmlns="https://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none"
                            tabIndex="0" role="button" aria-label="Next slide"
                            aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                            <path d="M11 11L24 17.5L11 24" stroke="#054C73" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}

add_action('wp_footer', 'cart_update_qty_script', 1000);
function cart_update_qty_script()
{
    if (is_cart()):
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $(document).ajaxSuccess(function () {
                    $('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);
                });
                $('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);

                $('body').on('change', '.qty', function () {
                    var quantity_selected = $(".qty option:selected").val();
                    $('#product_quantity').val(quantity_selected);

                    jQuery("[name='update_cart']").removeAttr('disabled');
                    jQuery("[name='update_cart']").trigger("click");

                });

            });
        </script>
        <?php
    endif;
}

// Removed - Plugin will manage checkout fields
// add_filter('woocommerce_default_address_fields', 'custom_override_global_checkout_fields', 9999);
//
// function custom_override_global_checkout_fields($fields)
// {
//     $fields['first_name']['label'] = 'Vardas';
//     $fields['last_name']['label'] = 'Pavardė';
//     $fields['country']['label'] = 'Šalis';
//     $fields['address_1']['label'] = 'Adresas';
//     $fields['address_1']['placeholder'] = 'Gatvė, namo nr.';
//     $fields['address_2']['placeholder'] = 'Buto nr.';
//     $fields['city']['label'] = 'Miestas';
//     $fields['state']['label'] = 'Apskritis';
//     $fields['postcode']['label'] = 'Pašto kodas';
//     return $fields;
// }

// Removed - Plugin will manage checkout fields
// add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');
// function custom_override_checkout_fields($fields)
// {
//     $fields['billing']['billing_company']['label'] = 'Įmonės pavadinimas';
//     $fields['shipping']['shipping_company']['label'] = 'Įmonės pavadinimas';
//     $fields['billing']['billing_email']['label'] = 'El. paštas ';
//     $fields['billing']['billing_phone']['label'] = 'Telefono nr. ';
//     $fields['order']['order_comments']['placeholder'] = '';
//     $fields['order']['order_comments']['label'] = 'Papildoma informacija';
//
//     return $fields;
// }

// ULTIMATE FIX: Prevent parcel machine required field error from being shown
add_filter('woocommerce_checkout_required_field_notice', 'prevent_parcel_machine_required_notice', 99999, 2);
function prevent_parcel_machine_required_notice($error_notice, $field_label)
{
    // Check if this is a parcel machine related field
    $field_label_lower = strtolower($field_label);

    if (
        stripos($field_label_lower, 'parcel machine') !== false ||
        stripos($field_label_lower, 'parcelmachine') !== false ||
        stripos($field_label_lower, 'parcel') !== false ||
        stripos($field_label_lower, 'locker') !== false ||
        stripos($field_label_lower, 'terminal') !== false ||
        stripos($field_label_lower, 'paštomat') !== false
    ) {

        // Get shipping method
        $chosen_method = '';
        if (isset($_POST['shipping_method']) && is_array($_POST['shipping_method']) && !empty($_POST['shipping_method'][0])) {
            $chosen_method = $_POST['shipping_method'][0];
        } elseif (WC()->session) {
            $chosen_methods = WC()->session->get('chosen_shipping_methods');
            $chosen_method = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';
        }

        if (!empty($chosen_method)) {
            $method_lower = strtolower($chosen_method);
            $is_courier = (strpos($method_lower, 'courier') !== false || strpos($method_lower, 'kurjer') !== false);
            $is_pickup = (strpos($method_lower, 'pickup') !== false || strpos($method_lower, 'local') !== false);

            // Return empty string to prevent error from being shown
            if ($is_courier || $is_pickup) {
                return ''; // Empty string = no error shown
            }
        }
    }

    // For non-parcel-machine fields, return original notice
    return $error_notice;
}

// AGGRESSIVE: Make parcel machine field optional when courier/pickup shipping methods are selected
// Run at the earliest possible point
add_action('woocommerce_checkout_process', 'handle_parcel_machine_validation', 1);
add_action('woocommerce_before_checkout_process', 'handle_parcel_machine_validation', 1);
add_action('woocommerce_checkout_process', 'handle_parcel_machine_validation', 99999);

function handle_parcel_machine_validation()
{
    // Get shipping method - try ALL possible sources
    $chosen_method = '';

    // Method 1: POST data
    if (isset($_POST['shipping_method']) && is_array($_POST['shipping_method']) && !empty($_POST['shipping_method'][0])) {
        $chosen_method = $_POST['shipping_method'][0];
    }
    // Method 2: Session
    elseif (WC()->session) {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';
    }
    // Method 3: Check all POST keys
    if (empty($chosen_method)) {
        foreach ($_POST as $key => $value) {
            $key_lower = strtolower($key);
            if (
                (strpos($key_lower, 'shipping') !== false || strpos($key_lower, 'method') !== false) &&
                is_string($value) && !empty($value) &&
                strpos(strtolower($value), 'courier') !== false ||
                strpos(strtolower($value), 'pickup') !== false ||
                strpos(strtolower($value), 'local') !== false ||
                strpos(strtolower($value), 'kurjer') !== false
            ) {
                $chosen_method = $value;
                break;
            }
        }
    }

    if (!empty($chosen_method)) {
        $method_lower = strtolower($chosen_method);

        // Check if courier or pickup method is selected
        $is_courier = (strpos($method_lower, 'courier') !== false || strpos($method_lower, 'kurjer') !== false);
        $is_pickup = (strpos($method_lower, 'pickup') !== false || strpos($method_lower, 'local') !== false);

        if ($is_courier || $is_pickup) {
            // AGGRESSIVE: Clear ALL possible parcel machine fields from POST
            foreach ($_POST as $key => $value) {
                $key_lower = strtolower($key);
                if (
                    strpos($key_lower, 'parcel') !== false ||
                    strpos($key_lower, 'locker') !== false ||
                    strpos($key_lower, 'terminal') !== false ||
                    strpos($key_lower, 'paštomat') !== false ||
                    strpos($key_lower, 'omniva') !== false ||
                    strpos($key_lower, 'venipak') !== false
                ) {
                    $_POST[$key] = '';
                    unset($_POST[$key]);
                }
            }

            // Also clear from $_REQUEST
            foreach ($_REQUEST as $key => $value) {
                $key_lower = strtolower($key);
                if (
                    strpos($key_lower, 'parcel') !== false ||
                    strpos($key_lower, 'locker') !== false ||
                    strpos($key_lower, 'terminal') !== false ||
                    strpos($key_lower, 'paštomat') !== false
                ) {
                    $_REQUEST[$key] = '';
                    unset($_REQUEST[$key]);
                }
            }
        }
    }
}

// AGGRESSIVE: Make parcel machine field conditionally required - run at highest priority
add_filter('woocommerce_checkout_fields', 'make_parcel_machine_conditional', 99999);
add_filter('woocommerce_checkout_fields', 'make_parcel_machine_conditional', 1);

function make_parcel_machine_conditional($fields)
{
    // Get shipping method - try ALL possible sources
    $chosen_method = '';

    // Method 1: POST data
    if (isset($_POST['shipping_method']) && is_array($_POST['shipping_method']) && !empty($_POST['shipping_method'][0])) {
        $chosen_method = $_POST['shipping_method'][0];
    }
    // Method 2: Session
    elseif (WC()->session) {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';
    }
    // Method 3: Check all POST keys
    if (empty($chosen_method)) {
        foreach ($_POST as $key => $value) {
            if (strpos(strtolower($key), 'shipping') !== false && is_string($value) && !empty($value)) {
                $chosen_method = $value;
                break;
            }
        }
    }

    // Always check - if courier/pickup, make ALL parcel fields optional
    $is_courier_or_pickup = false;
    if (!empty($chosen_method)) {
        $method_lower = strtolower($chosen_method);
        $is_courier = (strpos($method_lower, 'courier') !== false || strpos($method_lower, 'kurjer') !== false);
        $is_pickup = (strpos($method_lower, 'pickup') !== false || strpos($method_lower, 'local') !== false);
        $is_courier_or_pickup = ($is_courier || $is_pickup);
    }

    // Make ALL fields optional that contain parcel/locker/terminal keywords
    foreach ($fields as $field_type => $field_group) {
        if (is_array($field_group)) {
            foreach ($field_group as $field_key => $field_data) {
                $field_key_lower = strtolower($field_key);
                if (
                    strpos($field_key_lower, 'parcel') !== false ||
                    strpos($field_key_lower, 'locker') !== false ||
                    strpos($field_key_lower, 'terminal') !== false ||
                    strpos($field_key_lower, 'paštomat') !== false ||
                    strpos($field_key_lower, 'omniva') !== false ||
                    strpos($field_key_lower, 'venipak') !== false
                ) {
                    // Always make optional if courier/pickup, or if we can't determine method
                    if ($is_courier_or_pickup || empty($chosen_method)) {
                        $fields[$field_type][$field_key]['required'] = false;
                        $fields[$field_type][$field_key]['validate'] = array();
                    }
                }
            }
        }
    }

    return $fields;
}

// AGGRESSIVE: Remove parcel machine validation errors - run at highest priority
add_action('woocommerce_after_checkout_validation', 'remove_parcel_machine_errors', 99999, 2);
add_action('woocommerce_checkout_process', 'remove_parcel_machine_errors_direct', 99999);

function remove_parcel_machine_errors_direct()
{
    // Direct error removal without waiting for validation hook
    if (function_exists('wc_get_notices')) {
        $notices = wc_get_notices('error');
        if (!empty($notices)) {
            foreach ($notices as $key => $notice) {
                $notice_text = is_array($notice) ? (isset($notice['notice']) ? $notice['notice'] : '') : (string) $notice;
                $notice_lower = strtolower($notice_text);

                if (
                    stripos($notice_lower, 'parcel machine') !== false ||
                    stripos($notice_lower, 'parcelmachine') !== false ||
                    stripos($notice_lower, 'parcel') !== false
                ) {
                    wc_remove_notice($notice_text, 'error');
                }
            }
        }
    }
}

function remove_parcel_machine_errors($data, $errors)
{
    if (!$errors || !is_a($errors, 'WP_Error')) {
        return;
    }

    // Get shipping method - try multiple sources
    $chosen_method = '';

    // First try POST data
    if (isset($_POST['shipping_method']) && is_array($_POST['shipping_method']) && !empty($_POST['shipping_method'][0])) {
        $chosen_method = $_POST['shipping_method'][0];
    }
    // Then try session
    elseif (WC()->session) {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';
    }
    // Also check data array
    elseif (isset($data['shipping_method']) && is_array($data['shipping_method']) && !empty($data['shipping_method'][0])) {
        $chosen_method = $data['shipping_method'][0];
    }

    // If still empty, check all POST keys for shipping method
    if (empty($chosen_method)) {
        foreach ($_POST as $key => $value) {
            if (strpos(strtolower($key), 'shipping') !== false && is_string($value) && !empty($value)) {
                $chosen_method = $value;
                break;
            }
        }
    }

    if (!empty($chosen_method)) {
        $method_lower = strtolower($chosen_method);

        // Check if courier or pickup method is selected
        $is_courier = (strpos($method_lower, 'courier') !== false || strpos($method_lower, 'kurjer') !== false);
        $is_pickup = (strpos($method_lower, 'pickup') !== false || strpos($method_lower, 'local') !== false);

        if ($is_courier || $is_pickup) {
            // Remove ALL parcel machine related errors - check all error codes multiple times
            $max_iterations = 10; // Prevent infinite loop
            $iteration = 0;

            while ($iteration < $max_iterations) {
                $error_codes = $errors->get_error_codes();
                $codes_to_remove = array();

                foreach ($error_codes as $code) {
                    $message = $errors->get_error_message($code);
                    $message_lower = strtolower($message);

                    // Check for any parcel machine related keywords
                    if (
                        stripos($message_lower, 'parcel machine') !== false ||
                        stripos($message_lower, 'parcelmachine') !== false ||
                        stripos($message_lower, 'paštomat') !== false ||
                        stripos($message_lower, 'parcel') !== false ||
                        stripos($message_lower, 'locker') !== false ||
                        stripos($message_lower, 'terminal') !== false ||
                        (stripos($message_lower, 'required') !== false && stripos($message_lower, 'parcel') !== false)
                    ) {
                        $codes_to_remove[] = $code;
                    }
                }

                // If no codes to remove, break
                if (empty($codes_to_remove)) {
                    break;
                }

                // Remove all matching errors
                foreach ($codes_to_remove as $code) {
                    $errors->remove($code);
                }

                $iteration++;
            }
        }
    }
}

// Additional hook to clear parcel machine fields before validation
add_filter('woocommerce_checkout_posted_data', 'clear_parcel_machine_for_courier_pickup', 1, 1);
function clear_parcel_machine_for_courier_pickup($data)
{
    // Get shipping method - try multiple sources
    $chosen_method = '';

    // First try POST data
    if (isset($_POST['shipping_method']) && is_array($_POST['shipping_method']) && !empty($_POST['shipping_method'][0])) {
        $chosen_method = $_POST['shipping_method'][0];
    }
    // Then try data array
    elseif (isset($data['shipping_method']) && is_array($data['shipping_method']) && !empty($data['shipping_method'][0])) {
        $chosen_method = $data['shipping_method'][0];
    }
    // Then try session
    elseif (WC()->session) {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';
    }

    if (!empty($chosen_method)) {
        $method_lower = strtolower($chosen_method);

        // Check if courier or pickup method is selected
        $is_courier = (strpos($method_lower, 'courier') !== false || strpos($method_lower, 'kurjer') !== false);
        $is_pickup = (strpos($method_lower, 'pickup') !== false || strpos($method_lower, 'local') !== false);

        if ($is_courier || $is_pickup) {
            // Clear ALL fields that contain parcel/locker/terminal keywords
            foreach ($data as $key => $value) {
                $key_lower = strtolower($key);
                if (
                    strpos($key_lower, 'parcel') !== false ||
                    strpos($key_lower, 'locker') !== false ||
                    strpos($key_lower, 'terminal') !== false ||
                    strpos($key_lower, 'paštomat') !== false
                ) {
                    $data[$key] = '';
                }
            }

            // Also clear from $_POST
            foreach ($_POST as $key => $value) {
                $key_lower = strtolower($key);
                if (
                    strpos($key_lower, 'parcel') !== false ||
                    strpos($key_lower, 'locker') !== false ||
                    strpos($key_lower, 'terminal') !== false ||
                    strpos($key_lower, 'paštomat') !== false
                ) {
                    $_POST[$key] = '';
                }
            }
        }
    }

    return $data;
}

// Final safety net - remove parcel machine errors right before order processing
add_action('woocommerce_checkout_order_processed', 'final_parcel_machine_check', 1, 3);
function final_parcel_machine_check($order_id, $posted_data, $order)
{
    // This runs after order is created, but we can still prevent errors
    // by ensuring parcel machine data is cleared for courier/pickup
    if (!$order_id) {
        return;
    }

    $chosen_method = '';
    if (isset($posted_data['shipping_method']) && is_array($posted_data['shipping_method']) && !empty($posted_data['shipping_method'][0])) {
        $chosen_method = $posted_data['shipping_method'][0];
    } elseif (WC()->session) {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';
    }

    if (!empty($chosen_method)) {
        $method_lower = strtolower($chosen_method);
        $is_courier = (strpos($method_lower, 'courier') !== false || strpos($method_lower, 'kurjer') !== false);
        $is_pickup = (strpos($method_lower, 'pickup') !== false || strpos($method_lower, 'local') !== false);

        if ($is_courier || $is_pickup) {
            // Clear parcel machine meta from order if somehow it got saved
            $parcel_keys = array('parcelmachine', 'parcel_machine', 'parcelmachine_omniva', 'parcelmachine_venipak');
            foreach ($parcel_keys as $key) {
                delete_post_meta($order_id, $key);
                delete_post_meta($order_id, '_' . $key);
            }
        }
    }
}

// ULTIMATE FIX: Remove parcel machine errors at multiple points
add_action('woocommerce_before_checkout_form', 'remove_parcel_errors_before_display', 1);
add_action('woocommerce_after_checkout_validation', 'remove_parcel_errors_before_display', 99999);
add_action('woocommerce_checkout_process', 'remove_parcel_errors_before_display', 99999);
add_action('wp_loaded', 'remove_parcel_errors_before_display', 99999);
add_action('template_redirect', 'remove_parcel_errors_before_display', 99999);

function remove_parcel_errors_before_display()
{
    if (!WC()->session && !isset($_POST['woocommerce_checkout_place_order'])) {
        return;
    }

    // Get shipping method
    $chosen_method = '';
    if (isset($_POST['shipping_method']) && is_array($_POST['shipping_method']) && !empty($_POST['shipping_method'][0])) {
        $chosen_method = $_POST['shipping_method'][0];
    } elseif (WC()->session) {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';
    }

    if (!empty($chosen_method)) {
        $method_lower = strtolower($chosen_method);
        $is_courier = (strpos($method_lower, 'courier') !== false || strpos($method_lower, 'kurjer') !== false);
        $is_pickup = (strpos($method_lower, 'pickup') !== false || strpos($method_lower, 'local') !== false);

        if ($is_courier || $is_pickup) {
            // Clear all notices first
            if (function_exists('wc_clear_notices')) {
                $all_notices = wc_get_notices();

                // Store non-parcel-machine notices
                $keep_notices = array();

                foreach ($all_notices as $notice_type => $notices) {
                    foreach ($notices as $notice) {
                        $notice_text = is_array($notice) ? (isset($notice['notice']) ? $notice['notice'] : '') : (string) $notice;
                        $notice_lower = strtolower($notice_text);

                        // Keep notices that are NOT parcel machine related
                        if (
                            stripos($notice_lower, 'parcel machine') === false &&
                            stripos($notice_lower, 'parcelmachine') === false &&
                            stripos($notice_lower, 'parcel') === false &&
                            stripos($notice_lower, 'locker') === false &&
                            stripos($notice_lower, 'terminal') === false &&
                            stripos($notice_lower, 'paštomat') === false
                        ) {
                            $keep_notices[$notice_type][] = $notice;
                        }
                    }
                }

                // Clear all and re-add only non-parcel-machine notices
                wc_clear_notices();
                foreach ($keep_notices as $notice_type => $notices) {
                    foreach ($notices as $notice) {
                        $notice_text = is_array($notice) ? (isset($notice['notice']) ? $notice['notice'] : '') : (string) $notice;
                        wc_add_notice($notice_text, $notice_type);
                    }
                }
            }
        }
    }
}

add_filter('woocommerce_get_breadcrumb', 'remove_shop_crumb', 20, 2);
function remove_shop_crumb($crumbs, $breadcrumb)
{
    foreach ($crumbs as $key => $crumb) {
        if ($crumb[0] === __('Shop', 'Woocommerce')) {
            unset($crumbs[$key]);
        }
    }

    return $crumbs;
}

add_filter('woocommerce_account_menu_items', 'rename_editaddress');
function rename_editaddress($menu_links)
{

    $menu_links['edit-address'] = 'Adresas';
    return $menu_links;
}

add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment($fragments)
{
    global $woocommerce;

    ob_start();

    ?>
    <a class="cart-customlocation position-relative" href="<?php echo wc_get_cart_url(); ?>"><img
            src="<?php echo wp_get_attachment_image_src(16)[0]; ?>" width="25px" alt="">
        <div class="position-absolute cart-count">
            <?php echo esc_html((WC()->cart->get_cart_contents_count()), WC()->cart->get_cart_contents_count()); ?>
        </div>
    </a>
    <?php
    $fragments['a.cart-customlocation'] = ob_get_clean();
    return $fragments;
}

// add_action('woocommerce_before_add_to_cart_form', 'mish_before_add_to_cart_btn');

// function mish_before_add_to_cart_btn()
// {
//     echo '<p class="my-4 product-description-custom-hassan">' . get_the_content() . '</p>';

// }

/**
 * Cart tier discount: 50€ = 5%, 120€ = 10%, 200€ = 15%
 * Same logic as cart discount banner; applied to cart & checkout totals.
 */
add_action('woocommerce_cart_calculate_fees', 'savinge_add_tier_discount_fee');
function savinge_add_tier_discount_fee($cart)
{
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    if ($cart->get_cart_contents_count() === 0) {
        return;
    }
    $subtotal = (float) $cart->get_subtotal();
    $percent = 0;
    if ($subtotal >= 200) {
        $percent = 15;
    } elseif ($subtotal >= 120) {
        $percent = 10;
    } elseif ($subtotal >= 50) {
        $percent = 5;
    }
    if ($percent <= 0) {
        return;
    }
    $discount_amount = -($subtotal * $percent / 100);
    $label = sprintf(
        /* translators: %d = discount percentage */
        __('Krepšelio nuolaida (%d%%)', 'woocommerce'),
        $percent
    );
    $cart->add_fee($label, $discount_amount, false);
}

/**
 * Custom archive filter: apply WooCommerce attribute filters from GET params.
 * Params: pa_<attribute_name>=term_slug (e.g. pa_spalva=juoda)
 */
add_action('woocommerce_product_query', 'savinge_apply_attribute_filters', 999);
function savinge_apply_attribute_filters($q)
{
    // Safety check: Ensure $q is valid
    if (!$q || !is_a($q, 'WP_Query')) {
        return;
    }

    // Apply on frontend product queries (shop/category/tag/attribute archives).
    if (is_admin()) {
        return;
    }

    // Only apply to WooCommerce product queries
    if (!$q->get('wc_query')) {
        return;
    }

    // Collect attribute filters from GET params
    $attribute_filters = array();
    foreach ($_GET as $key => $value) {
        // Only process pa_* taxonomies
        if (strpos($key, 'pa_') !== 0) {
            continue;
        }

        // Validate value
        if (!is_string($value) || trim($value) === '') {
            continue;
        }

        // Check taxonomy exists
        if (!taxonomy_exists($key)) {
            continue;
        }

        // Parse terms (support comma-separated)
        $terms = array_map('trim', explode(',', $value));
        $terms = array_filter($terms);
        if (empty($terms)) {
            continue;
        }

        // Sanitize terms
        $terms = array_map('sanitize_text_field', $terms);

        $attribute_filters[] = array(
            'taxonomy' => $key,
            'field' => 'slug',
            'terms' => $terms,
            'operator' => 'IN',
        );
    }

    // If no attribute filters, skip
    if (empty($attribute_filters)) {
        return;
    }

    // Get existing tax_query (might already have category/tag filters)
    $existing_tax_query = $q->get('tax_query');

    // Build new tax_query array
    $new_tax_query = array();

    // Preserve existing filters if they exist
    if (is_array($existing_tax_query) && !empty($existing_tax_query)) {
        // Extract relation
        $relation = isset($existing_tax_query['relation']) ? $existing_tax_query['relation'] : 'AND';

        // Add existing filters (skip relation key)
        foreach ($existing_tax_query as $key => $filter) {
            if ($key === 'relation') {
                continue;
            }
            if (is_array($filter) && isset($filter['taxonomy'])) {
                $new_tax_query[] = $filter;
            }
        }
    } else {
        $relation = 'AND';
    }

    // Add our attribute filters
    foreach ($attribute_filters as $filter) {
        $new_tax_query[] = $filter;
    }

    // Set relation and apply
    if (!empty($new_tax_query)) {
        $new_tax_query['relation'] = $relation;
        $q->set('tax_query', $new_tax_query);
    }

    // Apply price filter
    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;

    if ($min_price > 0 || $max_price > 0) {
        $meta_query = $q->get('meta_query');
        if (!is_array($meta_query)) {
            $meta_query = array();
        }

        $price_query = array('relation' => 'AND');

        if ($min_price > 0) {
            $price_query[] = array(
                'key' => '_price',
                'value' => $min_price,
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }

        if ($max_price > 0) {
            $price_query[] = array(
                'key' => '_price',
                'value' => $max_price,
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }

        $meta_query[] = $price_query;
        $q->set('meta_query', $meta_query);
    }

    // Alternate approach: Safety check to ensure tax_query is set correctly
    // This section only runs if tax_query wasn't set at line 662 (shouldn't happen normally)
    // Since we add filters to $new_tax_query at lines 655-657, it should never be empty at line 660
    // But this fallback ensures tax_query is set even in edge cases
    if (!empty($attribute_filters)) {
        $verify_tax_query = $q->get('tax_query');
        // Only set fallback if tax_query is truly empty/null (not set)
        if (empty($verify_tax_query) || !is_array($verify_tax_query)) {
            // Build tax_query from attribute_filters as fallback
            $tax_query_fallback = array('relation' => 'AND');
            foreach ($attribute_filters as $filter) {
                if (is_array($filter) && isset($filter['taxonomy'])) {
                    $tax_query_fallback[] = $filter;
                }
            }
            // Only set if we have valid filters (more than just relation)
            if (count($tax_query_fallback) > 1) {
                $q->set('tax_query', $tax_query_fallback);
            }
        }
    }
    // Note: If tax_query was already set above (line 662), this section won't override it
}
