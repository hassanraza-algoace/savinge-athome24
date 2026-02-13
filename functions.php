<?php
add_filter('template_include', 'force_search_template', 99);

function force_search_template($template)
{

    if (is_search()) {

        $new_template = locate_template(array('search.php'));

        if (!empty($new_template)) {
            return $new_template;
        }
    }

    return $template;
}

add_action('init', 'stop_heartbeat', 1);
function stop_heartbeat()
{
    wp_deregister_script('heartbeat');
}
if (isset($_GET['testing_info'])) {
    echo phpinfo();
    exit();
}

require_once WP_CONTENT_DIR . '/themes/savinge/includes/product-sliders.php';
require_once WP_CONTENT_DIR . '/themes/savinge/includes/woocommerce.php';
require_once WP_CONTENT_DIR . '/themes/savinge/includes/breadcrumbs.php';

add_theme_support('custom-logo');

function custom_styles()
{
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css');
    wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.css', array(), null);
}
add_action('wp_enqueue_scripts', 'custom_styles');

function custom_scripts()
{

    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', '', '', true);
    wp_enqueue_script('jquery', '    https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', '', '', true);
    wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', '', '', true);
    wp_enqueue_script('swiper-main', get_template_directory_uri() . '/assets/js/swiper-main.js', '', '', true);
    wp_enqueue_script('bootstrap-min-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js', '', '', false);
}

add_action('wp_enqueue_scripts', 'custom_scripts');

add_filter('woocommerce_order_button_text', 'misha_custom_button_text');

function misha_custom_button_text($button_text)
{
    return 'Apmokėti';
}

/**
 * Display Shipping Methods before Add to Cart on Single Product Page
 */
add_action('woocommerce_after_add_to_cart_form', 'display_product_shipping_methods', 10);

function display_product_shipping_methods()
{
    if (!is_product() || !class_exists('WooCommerce')) {
        return;
    }

    global $product;

    if (!$product || !$product->needs_shipping()) {
        return;
    }

    // Get customer location from session or default
    $customer_country = WC()->customer->get_shipping_country() ? WC()->customer->get_shipping_country() : WC()->countries->get_base_country();
    $customer_state = WC()->customer->get_shipping_state() ? WC()->customer->get_shipping_state() : '';
    $customer_postcode = WC()->customer->get_shipping_postcode() ? WC()->customer->get_shipping_postcode() : '';
    $customer_city = WC()->customer->get_shipping_city() ? WC()->customer->get_shipping_city() : '';

    // Store original cart state
    $original_cart_contents = WC()->cart ? WC()->cart->get_cart_contents() : array();
    $cart_was_empty = empty($original_cart_contents);

    // Temporarily add product to cart for shipping calculation
    if ($cart_was_empty && WC()->cart) {
        WC()->cart->add_to_cart($product->get_id(), 1);
    }

    $available_methods = array();

    // Calculate shipping packages
    if (WC()->cart && WC()->cart->needs_shipping()) {
        $packages = WC()->cart->get_shipping_packages();

        if (!empty($packages)) {
            // Update package destination
            foreach ($packages as $index => $package) {
                $packages[$index]['destination']['country'] = $customer_country;
                $packages[$index]['destination']['state'] = $customer_state;
                $packages[$index]['destination']['postcode'] = $customer_postcode;
                $packages[$index]['destination']['city'] = $customer_city;
            }

            // Calculate shipping rates
            $calculated_packages = WC()->shipping()->calculate_shipping($packages);

            if (!empty($calculated_packages[0]['rates'])) {
                $available_methods = $calculated_packages[0]['rates'];
            }
        }
    }

    // Restore original cart state
    if ($cart_was_empty && WC()->cart) {
        WC()->cart->empty_cart();
    }

    // Get product price for small cart fee calculation
    $product_price = $product->get_price();
    $small_cart_fee_threshold = 15;
    $small_cart_fee = 1.95;
    $show_small_cart_fee = $product_price > 0 && $product_price < $small_cart_fee_threshold;

    // Calculate estimated delivery days (average of all methods or default)
    $estimated_days_min = 5;
    $estimated_days_max = 7;

    /**
     * 4 fixed cards (unique icon + label):
     * - Pickup
     * - Omniva paštomatai
     * - Venipak paštomatai
     * - Venipak kurjeris
     *
     * "Nemokamas pristatymas" (free shipping) hide rahe.
     */
    $display_methods = array();
    $selected = array();
    if (!empty($available_methods)) {
        foreach ($available_methods as $method) {
            $method_id_lower = strtolower($method->get_id());
            $method_label_lower = strtolower($method->get_label());

            // Hide free shipping method/card
            if (strpos($method_id_lower, 'free') !== false || strpos($method_label_lower, 'nemokamas') !== false) {
                continue;
            }

            $key = '';
            if (strpos($method_id_lower, 'local') !== false || strpos($method_id_lower, 'pickup') !== false) {
                $key = 'pickup';
            } elseif (strpos($method_id_lower, 'omniva') !== false) {
                $key = 'omniva_locker';
            } elseif (strpos($method_id_lower, 'venipak') !== false) {
                if (strpos($method_id_lower, 'kurjer') !== false || strpos($method_id_lower, 'courier') !== false || strpos($method_label_lower, 'kurjer') !== false) {
                    $key = 'venipak_courier';
                } else {
                    $key = 'venipak_locker';
                }
            } else {
                continue;
            }

            $total_cost = (float) $method->get_cost() + (float) $method->get_shipping_tax();
            if (!isset($selected[$key]) || $total_cost < $selected[$key]['total_cost']) {
                $selected[$key] = array(
                    'method' => $method,
                    'total_cost' => $total_cost,
                );
            }
        }
    }

    // fixed order (max 4)
    foreach (array('pickup', 'omniva_locker', 'venipak_locker', 'venipak_courier') as $k) {
        if (isset($selected[$k]['method'])) {
            $display_methods[] = $selected[$k]['method'];
        }
    }

    if (!empty($display_methods)) {
        $delivery_days = array();
        foreach ($display_methods as $method) {
            $method_id_lower = strtolower($method->get_id());
            if (strpos($method_id_lower, 'express') !== false || strpos($method_id_lower, 'fast') !== false) {
                $delivery_days[] = 1;
            } elseif (strpos($method_id_lower, 'standard') !== false) {
                $delivery_days[] = 3;
            } elseif (strpos($method_id_lower, 'economy') !== false) {
                $delivery_days[] = 5;
            } else {
                $delivery_days[] = 5;
            }
        }
        if (!empty($delivery_days)) {
            $estimated_days_min = min($delivery_days);
            $estimated_days_max = max($delivery_days);
        }
    }
    if (!function_exists('wc_get_template')) {
        return;
    }
    // Display shipping methods if available
    if (!empty($display_methods)):
        ?>
        <div class="static_display_delivery">
            <div class="items">
                <div>
                    <svg xmlns="https://www.w3.org/2000/svg" width="28" height="21" viewBox="0 0 28 21" fill="none">
                        <path
                            d="M27.5927 8.21L23.6927 4.36C23.4522 4.1317 23.1342 4.00305 22.8027 4H17.0027V1.38C17.0248 1.0399 16.9125 0.704712 16.6899 0.446605C16.4673 0.188498 16.1523 0.0281189 15.8126 0H1.21265C0.869413 0.0231156 0.549331 0.181338 0.322531 0.440003C0.0957298 0.698669 -0.019297 1.03669 0.00265064 1.38V15.62C-0.019297 15.9633 0.0957298 16.3013 0.322531 16.56C0.549331 16.8187 0.869413 16.9769 1.21265 17H2.00265C1.98741 17.1663 1.98741 17.3337 2.00265 17.5C2.00265 18.4283 2.3714 19.3185 3.02778 19.9749C3.68415 20.6313 4.57439 21 5.50265 21C6.43091 21 7.32115 20.6313 7.97752 19.9749C8.6339 19.3185 9.00265 18.4283 9.00265 17.5C9.01789 17.3337 9.01789 17.1663 9.00265 17H19.0027C18.9874 17.1663 18.9874 17.3337 19.0027 17.5C19.0027 18.4283 19.3714 19.3185 20.0278 19.9749C20.6842 20.6313 21.5744 21 22.5027 21C23.4309 21 24.3211 20.6313 24.9775 19.9749C25.6339 19.3185 26.0027 18.4283 26.0027 17.5C26.0179 17.3337 26.0179 17.1663 26.0027 17H26.7427C27.0919 16.9768 27.4185 16.819 27.6536 16.5596C27.8886 16.3003 28.0138 15.9598 28.0027 15.61V9.21C28.004 9.02446 27.9684 8.84051 27.898 8.66884C27.8277 8.49717 27.7238 8.3412 27.5927 8.21ZM5.50265 19C5.10483 19 4.72329 18.842 4.44199 18.5607C4.16069 18.2794 4.00265 17.8978 4.00265 17.5C4.00168 17.3292 4.03219 17.1597 4.09265 17C4.20935 16.6535 4.457 16.3663 4.78265 16.2C4.88721 16.1414 4.99787 16.0945 5.11265 16.06C5.23963 16.0238 5.37065 16.0036 5.50265 16C5.90048 16 6.28201 16.158 6.56331 16.4393C6.84462 16.7206 7.00265 17.1022 7.00265 17.5C7.00265 17.8978 6.84462 18.2794 6.56331 18.5607C6.28201 18.842 5.90048 19 5.50265 19ZM7.94265 15C7.29178 14.36 6.41549 14.0013 5.50265 14.0013C4.58981 14.0013 3.71352 14.36 3.06265 15H2.00265V2H15.0027V15H7.94265ZM24.0027 17.5C24.0027 17.7967 23.9147 18.0867 23.7499 18.3334C23.585 18.58 23.3508 18.7723 23.0767 18.8858C22.8026 18.9993 22.501 19.0291 22.21 18.9712C21.919 18.9133 21.6518 18.7704 21.442 18.5607C21.2322 18.3509 21.0894 18.0836 21.0315 17.7926C20.9736 17.5017 21.0033 17.2001 21.1168 16.926C21.2304 16.6519 21.4226 16.4176 21.6693 16.2528C21.916 16.088 22.206 16 22.5027 16C22.635 15.9996 22.7666 16.0198 22.8927 16.06C23.1236 16.1227 23.3356 16.2413 23.51 16.4052C23.6844 16.5691 23.8158 16.7734 23.8927 17C23.96 17.1584 23.9973 17.328 24.0027 17.5ZM26.0027 15H24.9427C24.2922 14.363 23.4181 14.0062 22.5077 14.0062C21.5972 14.0062 20.7231 14.363 20.0727 15H17.0027V6H22.5727L26.0027 9.53V15Z"
                            fill="#3BA57F"></path>
                    </svg>
                </div>
                <div>
                    <p>Nemokamas pristatymas nuo 35 EUR</p>
                </div>
            </div>
            <div class="items">
                <div>
                    <svg xmlns="https://www.w3.org/2000/svg" width="19" height="22" viewBox="0 0 19 22" fill="none">
                        <path
                            d="M4.83587 15.5145C5.09553 15.5145 5.30602 15.304 5.30602 15.0444C5.30602 14.7847 5.09553 14.5742 4.83587 14.5742C4.57622 14.5742 4.36572 14.7847 4.36572 15.0444C4.36572 15.304 4.57622 15.5145 4.83587 15.5145Z"
                            fill="#3BA57F" />
                        <path
                            d="M5.8432 14.5071C6.10285 14.5071 6.31335 14.2966 6.31335 14.0369C6.31335 13.7773 6.10285 13.5668 5.8432 13.5668C5.58354 13.5668 5.37305 13.7773 5.37305 14.0369C5.37305 14.2966 5.58354 14.5071 5.8432 14.5071Z"
                            fill="#3BA57F" />
                        <path
                            d="M6.91742 13.4327C7.17707 13.4327 7.38756 13.2222 7.38756 12.9625C7.38756 12.7029 7.17707 12.4924 6.91742 12.4924C6.65776 12.4924 6.44727 12.7029 6.44727 12.9625C6.44727 13.2222 6.65776 13.4327 6.91742 13.4327Z"
                            fill="#3BA57F" />
                        <path
                            d="M7.92474 12.4252C8.1844 12.4252 8.39489 12.2147 8.39489 11.9551C8.39489 11.6954 8.1844 11.4849 7.92474 11.4849C7.66508 11.4849 7.45459 11.6954 7.45459 11.9551C7.45459 12.2147 7.66508 12.4252 7.92474 12.4252Z"
                            fill="#3BA57F" />
                        <path
                            d="M8.99945 11.3505C9.2591 11.3505 9.4696 11.14 9.4696 10.8804C9.4696 10.6207 9.2591 10.4102 8.99945 10.4102C8.73979 10.4102 8.5293 10.6207 8.5293 10.8804C8.5293 11.14 8.73979 11.3505 8.99945 11.3505Z"
                            fill="#3BA57F" />
                        <path
                            d="M10.0068 10.3431C10.2664 10.3431 10.4769 10.1326 10.4769 9.87292C10.4769 9.61326 10.2664 9.40277 10.0068 9.40277C9.74711 9.40277 9.53662 9.61326 9.53662 9.87292C9.53662 10.1326 9.74711 10.3431 10.0068 10.3431Z"
                            fill="#3BA57F" />
                        <path
                            d="M11.081 9.26836C11.3406 9.26836 11.5511 9.05787 11.5511 8.79821C11.5511 8.53856 11.3406 8.32806 11.081 8.32806C10.8213 8.32806 10.6108 8.53856 10.6108 8.79821C10.6108 9.05787 10.8213 9.26836 11.081 9.26836Z"
                            fill="#3BA57F" />
                        <path
                            d="M12.0888 8.26092C12.3485 8.26092 12.559 8.05042 12.559 7.79077C12.559 7.53111 12.3485 7.32062 12.0888 7.32062C11.8291 7.32062 11.6187 7.53111 11.6187 7.79077C11.6187 8.05042 11.8291 8.26092 12.0888 8.26092Z"
                            fill="#3BA57F" />
                        <path
                            d="M13.0961 7.18621C13.3558 7.18621 13.5663 6.97572 13.5663 6.71606C13.5663 6.4564 13.3558 6.24591 13.0961 6.24591C12.8365 6.24591 12.626 6.4564 12.626 6.71606C12.626 6.97572 12.8365 7.18621 13.0961 7.18621Z"
                            fill="#3BA57F" />
                        <path
                            d="M14.1703 6.17876C14.43 6.17876 14.6405 5.96827 14.6405 5.70861C14.6405 5.44896 14.43 5.23846 14.1703 5.23846C13.9107 5.23846 13.7002 5.44896 13.7002 5.70861C13.7002 5.96827 13.9107 6.17876 14.1703 6.17876Z"
                            fill="#3BA57F" />
                        <path
                            d="M15.1777 5.10406C15.4373 5.10406 15.6478 4.89356 15.6478 4.63391C15.6478 4.37425 15.4373 4.16376 15.1777 4.16376C14.918 4.16376 14.7075 4.37425 14.7075 4.63391C14.7075 4.89356 14.918 5.10406 15.1777 5.10406Z"
                            fill="#3BA57F" />
                        <path
                            d="M16.2534 4.09661C16.513 4.09661 16.7235 3.88612 16.7235 3.62646C16.7235 3.3668 16.513 3.15631 16.2534 3.15631C15.9937 3.15631 15.7832 3.3668 15.7832 3.62646C15.7832 3.88612 15.9937 4.09661 16.2534 4.09661Z"
                            fill="#3BA57F" />
                        <path
                            d="M17.2612 3.08916C17.5208 3.08916 17.7313 2.87867 17.7313 2.61901C17.7313 2.35936 17.5208 2.14886 17.2612 2.14886C17.0015 2.14886 16.791 2.35936 16.791 2.61901C16.791 2.87867 17.0015 3.08916 17.2612 3.08916Z"
                            fill="#3BA57F" />
                        <path
                            d="M13.5678 15.5145C13.8275 15.5145 14.038 15.304 14.038 15.0444C14.038 14.7847 13.8275 14.5742 13.5678 14.5742C13.3081 14.5742 13.0977 14.7847 13.0977 15.0444C13.0977 15.304 13.3081 15.5145 13.5678 15.5145Z"
                            fill="#3BA57F" />
                        <path
                            d="M12.5605 14.5071C12.8201 14.5071 13.0306 14.2966 13.0306 14.0369C13.0306 13.7773 12.8201 13.5668 12.5605 13.5668C12.3008 13.5668 12.0903 13.7773 12.0903 14.0369C12.0903 14.2966 12.3008 14.5071 12.5605 14.5071Z"
                            fill="#3BA57F" />
                        <path
                            d="M11.4848 13.4327C11.7445 13.4327 11.9549 13.2222 11.9549 12.9625C11.9549 12.7029 11.7445 12.4924 11.4848 12.4924C11.2251 12.4924 11.0146 12.7029 11.0146 12.9625C11.0146 13.2222 11.2251 13.4327 11.4848 13.4327Z"
                            fill="#3BA57F" />
                        <path
                            d="M10.4775 12.4252C10.7371 12.4252 10.9476 12.2147 10.9476 11.9551C10.9476 11.6954 10.7371 11.4849 10.4775 11.4849C10.2178 11.4849 10.0073 11.6954 10.0073 11.9551C10.0073 12.2147 10.2178 12.4252 10.4775 12.4252Z"
                            fill="#3BA57F" />
                        <path
                            d="M9.40325 11.3505C9.66291 11.3505 9.8734 11.14 9.8734 10.8804C9.8734 10.6207 9.66291 10.4102 9.40325 10.4102C9.1436 10.4102 8.93311 10.6207 8.93311 10.8804C8.93311 11.14 9.1436 11.3505 9.40325 11.3505Z"
                            fill="#3BA57F" />
                        <path
                            d="M8.39544 10.3431C8.6551 10.3431 8.86559 10.1326 8.86559 9.87292C8.86559 9.61326 8.6551 9.40277 8.39544 9.40277C8.13579 9.40277 7.92529 9.61326 7.92529 9.87292C7.92529 10.1326 8.13579 10.3431 8.39544 10.3431Z"
                            fill="#3BA57F" />
                        <path
                            d="M7.38812 9.26836C7.64777 9.26836 7.85827 9.05787 7.85827 8.79821C7.85827 8.53856 7.64777 8.32806 7.38812 8.32806C7.12846 8.32806 6.91797 8.53856 6.91797 8.79821C6.91797 9.05787 7.12846 9.26836 7.38812 9.26836Z"
                            fill="#3BA57F" />
                        <path
                            d="M6.3139 8.26092C6.57356 8.26092 6.78405 8.05042 6.78405 7.79077C6.78405 7.53111 6.57356 7.32062 6.3139 7.32062C6.05424 7.32062 5.84375 7.53111 5.84375 7.79077C5.84375 8.05042 6.05424 8.26092 6.3139 8.26092Z"
                            fill="#3BA57F" />
                        <path
                            d="M5.30609 7.18621C5.56574 7.18621 5.77624 6.97572 5.77624 6.71606C5.77624 6.4564 5.56574 6.24591 5.30609 6.24591C5.04643 6.24591 4.83594 6.4564 4.83594 6.71606C4.83594 6.97572 5.04643 7.18621 5.30609 7.18621Z"
                            fill="#3BA57F" />
                        <path
                            d="M4.23187 6.17876C4.49152 6.17876 4.70202 5.96827 4.70202 5.70861C4.70202 5.44896 4.49152 5.23846 4.23187 5.23846C3.97221 5.23846 3.76172 5.44896 3.76172 5.70861C3.76172 5.96827 3.97221 6.17876 4.23187 6.17876Z"
                            fill="#3BA57F" />
                        <path
                            d="M3.22454 5.10406C3.4842 5.10406 3.69469 4.89356 3.69469 4.63391C3.69469 4.37425 3.4842 4.16376 3.22454 4.16376C2.96489 4.16376 2.75439 4.37425 2.75439 4.63391C2.75439 4.89356 2.96489 5.10406 3.22454 5.10406Z"
                            fill="#3BA57F" />
                        <path
                            d="M2.14984 4.09661C2.40949 4.09661 2.61999 3.88612 2.61999 3.62646C2.61999 3.3668 2.40949 3.15631 2.14984 3.15631C1.89018 3.15631 1.67969 3.3668 1.67969 3.62646C1.67969 3.88612 1.89018 4.09661 2.14984 4.09661Z"
                            fill="#3BA57F" />
                        <path
                            d="M1.14251 3.08916C1.40217 3.08916 1.61266 2.87867 1.61266 2.61901C1.61266 2.35936 1.40217 2.14886 1.14251 2.14886C0.882857 2.14886 0.672363 2.35936 0.672363 2.61901C0.672363 2.87867 0.882857 3.08916 1.14251 3.08916Z"
                            fill="#3BA57F" />
                        <path
                            d="M9.13433 21.1567C8.32836 20.6194 6.58209 19.4104 4.83582 17.1269C3.42537 15.2463 2.21642 12.9627 1.41045 10.4776C0.537314 7.85821 0.0671642 4.90298 0 1.74627C0.873134 1.74627 2.08209 1.81343 3.22388 1.81343C4.43284 1.81343 5.30597 1.81343 5.84328 1.74627C7.45522 1.54478 8.59701 0.537313 9.26866 0C9.9403 0.537313 11.0821 1.54478 12.694 1.74627C13.1642 1.81343 13.9702 1.8806 15.0448 1.8806C16.1866 1.8806 17.4627 1.81343 18.2687 1.81343C18.2015 4.90298 17.6642 7.85821 16.791 10.4776C15.9851 12.9627 14.8433 15.1791 13.4328 17.0597C11.7537 19.4104 10.0075 20.6866 9.13433 21.1567Z"
                            fill="#3BA57F" />
                        <path
                            d="M8.59779 13.2309L5.03809 9.67124L6.65003 8.12646L8.59779 10.0742L13.2993 5.2384L14.8441 6.78318L8.59779 13.2309Z"
                            fill="white" />
                    </svg>
                </div>
                <div>
                    <p>Prekių grąžinimo garantija per 14 dienų</p>
                </div>
            </div>
        </div>
        <div class="product-delivery-methods-container">
            <button type="button" class="product-delivery-methods-header" id="product-delivery-methods-toggle"
                aria-expanded="true" aria-controls="product-delivery-methods-content">
                <span class="header-left">
                    <span class="delivery-icon"><svg xmlns="https://www.w3.org/2000/svg" width="28" height="21"
                            viewBox="0 0 28 21" fill="none">
                            <path
                                d="M27.5927 8.21L23.6927 4.36C23.4522 4.1317 23.1342 4.00305 22.8027 4H17.0027V1.38C17.0248 1.0399 16.9125 0.704712 16.6899 0.446605C16.4673 0.188498 16.1523 0.0281189 15.8126 0H1.21265C0.869413 0.0231156 0.549331 0.181338 0.322531 0.440003C0.0957298 0.698669 -0.019297 1.03669 0.00265064 1.38V15.62C-0.019297 15.9633 0.0957298 16.3013 0.322531 16.56C0.549331 16.8187 0.869413 16.9769 1.21265 17H2.00265C1.98741 17.1663 1.98741 17.3337 2.00265 17.5C2.00265 18.4283 2.3714 19.3185 3.02778 19.9749C3.68415 20.6313 4.57439 21 5.50265 21C6.43091 21 7.32115 20.6313 7.97752 19.9749C8.6339 19.3185 9.00265 18.4283 9.00265 17.5C9.01789 17.3337 9.01789 17.1663 9.00265 17H19.0027C18.9874 17.1663 18.9874 17.3337 19.0027 17.5C19.0027 18.4283 19.3714 19.3185 20.0278 19.9749C20.6842 20.6313 21.5744 21 22.5027 21C23.4309 21 24.3211 20.6313 24.9775 19.9749C25.6339 19.3185 26.0027 18.4283 26.0027 17.5C26.0179 17.3337 26.0179 17.1663 26.0027 17H26.7427C27.0919 16.9768 27.4185 16.819 27.6536 16.5596C27.8886 16.3003 28.0138 15.9598 28.0027 15.61V9.21C28.004 9.02446 27.9684 8.84051 27.898 8.66884C27.8277 8.49717 27.7238 8.3412 27.5927 8.21ZM5.50265 19C5.10483 19 4.72329 18.842 4.44199 18.5607C4.16069 18.2794 4.00265 17.8978 4.00265 17.5C4.00168 17.3292 4.03219 17.1597 4.09265 17C4.20935 16.6535 4.457 16.3663 4.78265 16.2C4.88721 16.1414 4.99787 16.0945 5.11265 16.06C5.23963 16.0238 5.37065 16.0036 5.50265 16C5.90048 16 6.28201 16.158 6.56331 16.4393C6.84462 16.7206 7.00265 17.1022 7.00265 17.5C7.00265 17.8978 6.84462 18.2794 6.56331 18.5607C6.28201 18.842 5.90048 19 5.50265 19ZM7.94265 15C7.29178 14.36 6.41549 14.0013 5.50265 14.0013C4.58981 14.0013 3.71352 14.36 3.06265 15H2.00265V2H15.0027V15H7.94265ZM24.0027 17.5C24.0027 17.7967 23.9147 18.0867 23.7499 18.3334C23.585 18.58 23.3508 18.7723 23.0767 18.8858C22.8026 18.9993 22.501 19.0291 22.21 18.9712C21.919 18.9133 21.6518 18.7704 21.442 18.5607C21.2322 18.3509 21.0894 18.0836 21.0315 17.7926C20.9736 17.5017 21.0033 17.2001 21.1168 16.926C21.2304 16.6519 21.4226 16.4176 21.6693 16.2528C21.916 16.088 22.206 16 22.5027 16C22.635 15.9996 22.7666 16.0198 22.8927 16.06C23.1236 16.1227 23.3356 16.2413 23.51 16.4052C23.6844 16.5691 23.8158 16.7734 23.8927 17C23.96 17.1584 23.9973 17.328 24.0027 17.5ZM26.0027 15H24.9427C24.2922 14.363 23.4181 14.0062 22.5077 14.0062C21.5972 14.0062 20.7231 14.363 20.0727 15H17.0027V6H22.5727L26.0027 9.53V15Z"
                                fill="#3BA57F" />
                        </svg></span>
                    <span class="delivery-title"><?php esc_html_e('Galimi prekės pristatymo būdai', 'woocommerce'); ?></span>
                </span>
                <span class="chevron-icon"><svg xmlns="https://www.w3.org/2000/svg" width="17" height="9" viewBox="0 0 17 9"
                        fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M16.0683 8.56864C16.0103 8.62685 15.9413 8.67302 15.8654 8.70453C15.7894 8.73604 15.708 8.75226 15.6258 8.75226C15.5436 8.75226 15.4622 8.73604 15.3863 8.70453C15.3104 8.67302 15.2414 8.62685 15.1833 8.56864L8.12584 1.50989L1.06834 8.56864C1.01023 8.62675 0.94124 8.67285 0.865315 8.7043C0.789392 8.73574 0.708016 8.75193 0.625836 8.75193C0.543656 8.75193 0.462281 8.73574 0.386356 8.7043C0.310432 8.67285 0.241446 8.62675 0.183336 8.56864C0.125227 8.51053 0.0791302 8.44155 0.0476818 8.36562C0.0162334 8.2897 4.76837e-05 8.20832 4.76837e-05 8.12614C4.76837e-05 8.04396 0.0162334 7.96259 0.0476818 7.88666C0.0791302 7.81074 0.125227 7.74175 0.183336 7.68364L7.68334 0.183642C7.74139 0.125438 7.81036 0.0792599 7.88629 0.0477514C7.96223 0.0162439 8.04363 2.47955e-05 8.12584 2.47955e-05C8.20805 2.47955e-05 8.28945 0.0162439 8.36538 0.0477514C8.44131 0.0792599 8.51028 0.125438 8.56834 0.183642L16.0683 7.68364C16.1265 7.7417 16.1727 7.81067 16.2042 7.8866C16.2357 7.96253 16.252 8.04393 16.252 8.12614C16.252 8.20835 16.2357 8.28975 16.2042 8.36568C16.1727 8.44161 16.1265 8.51058 16.0683 8.56864Z"
                            fill="black" />
                    </svg></span>
            </button>

            <div class="product-delivery-methods-content" id="product-delivery-methods-content" aria-hidden="false">
                <span><?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>
                        <?php wc_cart_totals_shipping_html(); ?>
                    <?php endif; ?></span>


                <div class="delivery-info-section">
                    <div class="estimated-delivery">
                        <span
                            class="delivery-text"><?php esc_html_e('Numatomas prekės pristatymo laikas:', 'woocommerce'); ?></span>
                        <span
                            class="delivery-days"><?php echo esc_html($estimated_days_min . '-' . $estimated_days_max . ' d.d.'); ?></span>
                    </div>
                    <?php if ($show_small_cart_fee): ?>
                        <div class="small-cart-fee-note">
                            <?php printf(esc_html__('Užsakymams iki %s € taikomas mažo krepšelio mokestis %s eur', 'woocommerce'), $small_cart_fee_threshold, number_format($small_cart_fee, 2, ',', '')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
        /**
         * Product Reviews Accordion with Swiper slider
         */
        global $product;
        $product_id = $product ? $product->get_id() : get_the_ID();

        // Get only approved WooCommerce reviews for current product
        $reviews = get_comments(array(
            'post_id' => $product_id,
            'status' => 'approve',
            'type' => 'review',
            'number' => 50,
        ));

        $reviews_count = is_array($reviews) ? count($reviews) : 0;
        ?>

        <div class="product-delivery-methods-container single_product_reviews">
            <button type="button" class="product-delivery-methods-header" id="single-product-reviews-toggle"
                aria-expanded="false" aria-controls="single-product-reviews-content">
                <span class="header-left">
                    <span class="delivery-icon spr-star-icon" aria-hidden="true">
                        <svg xmlns="https://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.62L12 2L9.19 8.62L2 9.24L7.46 13.97L5.82 21L12 17.27Z"
                                stroke="#000" stroke-width="1.5" stroke-linejoin="round" fill="none" />
                        </svg>
                    </span>
                    <span class="delivery-title">
                        <?php esc_html_e('Atsiliepimai', 'savinge'); ?>
                        (<?php echo (int) $reviews_count; ?>)
                    </span>
                </span>
                <span class="chevron-icon spr-chevron" aria-hidden="false">
                    <svg xmlns="https://www.w3.org/2000/svg" width="17" height="9" viewBox="0 0 17 9" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M16.0683 8.56864C16.0103 8.62685 15.9413 8.67302 15.8654 8.70453C15.7894 8.73604 15.708 8.75226 15.6258 8.75226C15.5436 8.75226 15.4622 8.73604 15.3863 8.70453C15.3104 8.67302 15.2414 8.62685 15.1833 8.56864L8.12584 1.50989L1.06834 8.56864C1.01023 8.62675 0.94124 8.67285 0.865315 8.7043C0.789392 8.73574 0.708016 8.75193 0.625836 8.75193C0.543656 8.75193 0.462281 8.73574 0.386356 8.7043C0.310432 8.67285 0.241446 8.62675 0.183336 8.56864C0.125227 8.51053 0.0791302 8.44155 0.0476818 8.36562C0.0162334 8.2897 4.76837e-05 8.20832 4.76837e-05 8.12614C4.76837e-05 8.04396 0.0162334 7.96259 0.0476818 7.88666C0.0791302 7.81074 0.125227 7.74175 0.183336 7.68364L7.68334 0.183642C7.74139 0.125438 7.81036 0.0792599 7.88629 0.0477514C7.96223 0.0162439 8.04363 2.47955e-05 8.12584 2.47955e-05C8.20805 2.47955e-05 8.28945 0.0162439 8.36538 0.0477514C8.44131 0.0792599 8.51028 0.125438 8.56834 0.183642L16.0683 7.68364C16.1265 7.7417 16.1727 7.81067 16.2042 7.8866C16.2357 7.96253 16.252 8.04393 16.252 8.12614C16.252 8.20835 16.2357 8.28975 16.2042 8.36568C16.1727 8.44161 16.1265 8.51058 16.0683 8.56864Z"
                            fill="black"></path>
                    </svg>
                </span>
            </button>

            <div class="product-delivery-methods-content single-product-reviews-content product-delivery-methods__content--collapsed"
                id="single-product-reviews-content" aria-hidden="true">
                <?php if ($reviews_count > 0): ?>
                    <div class="swiper productReviewsSwiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($reviews as $review):
                                $rating = get_comment_meta($review->comment_ID, 'rating', true);
                                ?>
                                <div class="swiper-slide">
                                    <div class="single-review-card">
                                        <?php if ($rating): ?>
                                            <div class="single-review-rating" aria-label="<?php echo esc_attr($rating); ?>">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span
                                                        class="single-review-star <?php echo $i <= (int) $rating ? 'is-filled' : ''; ?>">★</span>
                                                <?php endfor; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="single-review-content">
                                            <?php echo wpautop(esc_html($review->comment_content)); ?>
                                        </div>

                                        <div class="single-review-meta">
                                            <span class="single-review-author">
                                                <?php echo esc_html($review->comment_author); ?>
                                            </span>
                                            <span class="single-review-date">
                                                <?php echo esc_html(get_comment_date('', $review)); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                <?php else: ?>
                    <div class="single-review-empty">
                        <?php esc_html_e('No reviews yet', 'savinge'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    endif;
}

function register_custom_widget_area()
{
    register_sidebar(
        array(
            'id' => 'widget-area',
            'name' => esc_html__('Widget', 'savinge'),
            'description' => esc_html__('Custom widget', 'savinge'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title-holder"><h3 class="widget-title">',
            'after_title' => '</h3></div>',
        )
    );
}
add_action('widgets_init', 'register_custom_widget_area');

function addMenu()
{
    register_nav_menu('main-menu', __('Main Menu'));
    register_nav_menu('bottom-menu', __('Bottom Menu'));
    register_nav_menu('footer-company', __('Įmonės informacija'));
    register_nav_menu('footer-links', __('Pagrindinės nuorodos'));
    register_nav_menu('footer-client', __('Kliento aptarnavimas'));
    register_nav_menu('footer_bottom_menu', __('Footer Bottom Menu'));
}
add_action('init', 'addMenu');

function create_advice_post_type()
{

    $args = array(
        'labels' => array(
            'name' => 'Naudingi Patarimai',
            'singular_name' => 'Patarimas',
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-clipboard',
        'publicly_queryable' => true,
        'show_in_rest' => true,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'custom-fields',
            'author'
        ),

    );

    register_post_type('patarimai', $args);
    register_taxonomy("patarimu-kategorija", array("patarimai"), array("hierarchical" => true, "label" => "Patarimų kategorijos", "singular_label" => "Patarimų kategorija", 'show_admin_column' => true, 'has_archive' => true, 'show_in_rest' => true, "rewrite" => array('slug' => 'patarimu-kategorija', 'with_front' => false)));
}

add_action('init', 'create_advice_post_type');

function getPosts($post_type = 'post')
{

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => 3,
    );

    $query = new WP_Query($args);
    wp_reset_postdata();
    return $query;
}

function getProductCategories($limit = 12, $parent = null): array
{
    $args = array(
        'taxonomy' => "product_cat",
        'number' => $limit = 12,
        'hide_empty' => 0,
        'parent' => $parent,
    );
    $product_categories = get_terms($args);

    if (!is_array($product_categories)) {
        $product_categories = [];
    }
    return $product_categories;
}

function getProductMainCategories($limit = 4, $parent = null): array
{
    $catsArray = array(16179, 16214, 16211, 16212, 16213);
    $product_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'number' => 9,
        'include' => $catsArray,
        'hide_empty' => false,
        'orderby' => 'include',
    ));

    if (!empty($product_categories)) {
        return $product_categories;
    }
    return array();
}

function hierarchical_term_tree($category = 0)
{
    $r = '';

    $args = array(
        'parent' => $category,
    );

    $next = get_terms('product_cat', $args);

    if ($next) {
        $r .= '<ul>';

        foreach ($next as $cat) {
            $r .= '<li><a href="' . get_term_link($cat->slug, $cat->taxonomy) . '" title="' . sprintf(__("View all products in %s"), $cat->name) . '" ' . '>' . $cat->name . ' (' . $cat->count . ')' . '</a>';
            $r .= $cat->term_id !== 0 ? hierarchical_term_tree($cat->term_id) : null;
        }
        $r .= '</li>';

        $r .= '</ul>';
    }

    return $r;
}

function custom_excerpt_length($length)
{
    return 20;
}
add_filter('excerpt_length', 'custom_excerpt_length', 999);

add_action('woocommerce_before_shop_loop_item_title', 'display_new_loop_woocommerce');
// function display_new_loop_woocommerce()
// {
//     global $product;

//     $datetime_created = $product->get_date_created(); //
//     $timestamp_created = $datetime_created->getTimestamp();

//     $datetime_now = new WC_DateTime();
//     $timestamp_now = $datetime_now->getTimestamp();

//     $time_delta = $timestamp_now - $timestamp_created;
//     $sixty_days = 120 * 24 * 60 * 60;
//     $add_class = '';
//     if ($product->is_on_sale()) {
//         $add_class = "badge-position";
//     }

//     if ($time_delta < $sixty_days) {
//         echo '<span class="new-product p-2 position-absolute ' . $add_class . ' d-flex justify-content-center align-items-center">' . __('Nauja', 'woocommerce') . '</span>';
//     }

// }
function display_new_loop_woocommerce()
{
    global $product;

    if (!$product)
        return; // safety check

    $datetime_created = $product->get_date_created();

    if (!$datetime_created) {
        return; // agar date created null ho to kuch bhi print mat karo
    }

    $timestamp_created = $datetime_created->getTimestamp();

    $datetime_now = new WC_DateTime();
    $timestamp_now = $datetime_now->getTimestamp();

    $time_delta = $timestamp_now - $timestamp_created;

    $sixty_days = 60 * 24 * 60 * 60; // 60 din ka seconds
    $add_class = '';

    if ($product->is_on_sale()) {
        $add_class = "badge-position";
    }

    if ($time_delta < $sixty_days) {
        echo '<span class="new-product p-2 position-absolute ' . esc_attr($add_class) . ' d-flex justify-content-center align-items-center">'
            . __('Nauja', 'woocommerce') . '</span>';
    }
}


add_action('woocommerce_product_query', 'so_20990199_product_query');

function so_20990199_product_query($q)
{
    if (isset($_GET['naujienos'])) {
        if ($_GET['naujienos'] == 'true') {
            $q->set('date_query', array(
                'after' => date('Y-m-d', strtotime('-120 days'))
            ));
        }
    }

    if (isset($_GET['akcijos'])) {
        if ($_GET['akcijos'] == 'true') {
            $product_ids_on_sale = wc_get_product_ids_on_sale();
            $q->set('post__in', $product_ids_on_sale);
        }
    }
}

/**
 * @snippet       Add First & Last Name to My Account Register Form - WooCommerce
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WC 3.9
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

///////////////////////////////
// 1. ADD FIELDS

add_action('woocommerce_register_form_start', 'bbloomer_add_name_woo_account_registration');

function bbloomer_add_name_woo_account_registration()
{
    ?>
    <p class="form-row form-row-first">
        <label for="reg_billing_first_name"><?php _e('First name', 'woocommerce'); ?> <span
                class="required">*</span></label>
        <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if (!empty($_POST['billing_first_name'])) {
            esc_attr_e($_POST['billing_first_name']);
        }
        ?>" />
    </p>

    <p class="form-row form-row-last">
        <label for="reg_billing_last_name"><?php _e('Last name', 'woocommerce'); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if (!empty($_POST['billing_last_name'])) {
            esc_attr_e($_POST['billing_last_name']);
        }
        ?>" />
    </p>

    <div class="clear"></div>

    <?php
}

///////////////////////////////
// 2. VALIDATE FIELDS

add_filter('woocommerce_registration_errors', 'bbloomer_validate_name_fields', 10, 3);

function bbloomer_validate_name_fields($errors, $username, $email)
{
    if (isset($_POST['billing_first_name']) && empty($_POST['billing_first_name'])) {
        $errors->add('billing_first_name_error', __('<strong>Error</strong>: First name is required!', 'woocommerce'));
    }
    if (isset($_POST['billing_last_name']) && empty($_POST['billing_last_name'])) {
        $errors->add('billing_last_name_error', __('<strong>Error</strong>: Last name is required!.', 'woocommerce'));
    }
    return $errors;
}

///////////////////////////////
// 3. SAVE FIELDS

add_action('woocommerce_created_customer', 'bbloomer_save_name_fields');

function bbloomer_save_name_fields($customer_id)
{
    if (isset($_POST['billing_first_name'])) {
        update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
        update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']));
    }
    if (isset($_POST['billing_last_name'])) {
        update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
        update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']));
    }
}

/*
 * Add the code below to your theme's functions.php file
 * to add a confirm password field on the register form under My Accounts.
 */
function woocommerce_registration_errors_validation($reg_errors, $sanitized_user_login, $user_email)
{
    global $woocommerce;
    extract($_POST);
    if (strcmp($password, $password2) !== 0) {
        return new WP_Error('registration-error', __('Passwords do not match.', 'woocommerce'));
    }
    return $reg_errors;
}
add_filter('woocommerce_registration_errors', 'woocommerce_registration_errors_validation', 10, 3);


function woocommerce_register_form_password_repeat()
{
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_password2"><?php _e('Pakartokite slaptažodį', 'woocommerce'); ?> <span
                class="required">*</span></label>
        <input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if (!empty($_POST['password2'])) {
            echo esc_attr($_POST['password2']);
        }
        ?>" />
    </p>
    <?php
}



function after_xml_import($import_id, $import)
{
    $filename = "update_post_meta_log_" . $import_id . ".txt";
    $data = file($filename);
    foreach ($data as $line) {
        $var = explode(':', $line, 2);
        if ($var[0] == $import_id) {
            $meta_value = get_post_meta($var[1], 'product_group', true);
            my_update_post_meta($var[1], 'product_group', $meta_value);
        }
    }
    rename($filename, "update_post_meta_log_" . $import_id . "_" . date('Y-m-d H:i:s') . ".txt");
    //$myfile = fopen($filename, "a");
    //fwrite($myfile, "\n".date('Y-m-d H:i:s').' after_xml_import'. $import_id);
    //fclose($myfile);
}
add_action('pmxi_after_xml_import', 'after_xml_import', 10, 2);

/*function my_saved_post( $post_id, $xml_node, $is_update ) {
$myfile = fopen("update_post_meta_log.txt", "a");
fwrite($myfile, "\n".date('Y-m-d H:i:s').' my_saved_post'. $post_id.' '. $xml_node.' '.$is_update);
fclose($myfile);
}
add_action( 'pmxi_saved_post', 'my_saved_post', 10, 3 );

function my_after_post_import($import_id) {
$myfile = fopen("update_post_meta_log.txt", "a");
fwrite($myfile, "\n".date('Y-m-d H:i:s').' my_after_post_import'. $import_id);
fclose($myfile);
}
add_action('pmxi_after_post_import', 'my_after_post_import', 10, 1);*/


add_action('woocommerce_register_form', 'woocommerce_register_form_password_repeat');

function my_update_post_meta($post_id, $meta_key, $meta_value)
{

    // $import_id = wp_all_import_get_import_id();
    if (function_exists('wp_all_import_get_import_id')) {

        $import_id = wp_all_import_get_import_id();

        // baki aapka import/update logic yahan
    }

    $theme_path = get_template_directory();
    $file = fopen($theme_path . '/Nauji pogrupiai.csv', "r");
    $cat_arry = array();
    while (!feof($file)) {
        $data = fgetcsv($file);
        if (isset($data[3]) && trim($data[3]) != '') {
            $cat_arry[$data[3]] = $data;
        }
    }
    fclose($file);

    //$myfile = fopen("update_post_meta_log.txt", "a");
    if ($meta_key == 'product_group' && $meta_value) {
        if (isset($cat_arry[$meta_value])) {
            $categories = array();
            $level1 = trim($cat_arry[$meta_value][0]);
            $level2 = trim($cat_arry[$meta_value][1]);
            $level3 = trim($cat_arry[$meta_value][2]);
            $level4 = trim($cat_arry[$meta_value][4]);

            $category1 = get_term_by('name', $level1, 'product_cat');
            $category2 = get_term_by('name', $level2, 'product_cat');
            $category3 = get_term_by('name', $level3, 'product_cat');
            $category4 = get_term_by('name', $level4, 'product_cat');

            if ($category1) {
                $categories[] = $category1->term_id;
            }
            if ($category2) {
                $categories[] = $category2->term_id;
            }
            if ($category3) {
                $categories[] = $category3->term_id;
            }
            if ($category4) {
                $categories[] = $category4->term_id;
            }


            print_r($categories);

            if (count($categories)) {
                $product = wc_get_product($post_id);
                //wp_set_object_terms( $post_id, $categories, 'product_cat');
                $product->set_category_ids($categories); // Update product categories
                $product->save();
                echo '<br>' . $post_id . ' 1';
                add_post_meta($post_id, 'importcat', 1, true);
                //$terms = get_the_terms ( $post_id, 'product_cat' );		
                //fwrite($myfile, "\n".date('Y-m-d H:i:s').' importid='.$import_id .' postid='. $post_id.' '.$meta_key.' '.$meta_value);
                //fwrite($myfile, "\n".$import_id .'='. $post_id);
            } else {
                echo '<br>' . $post_id . ' 2';
                add_post_meta($post_id, 'importcat', 2, true);
            }
        } else {
            echo '<br>' . $post_id . ' 3';
            add_post_meta($post_id, 'importcat', 2, true);
        }
    }
    //fclose($myfile);

}

function pmxi_update_post_meta($post_id, $meta_key, $meta_value)
{
    if (function_exists('wp_all_import_get_import_id')) {
        $import_id = wp_all_import_get_import_id();
    }
    $filename = "update_post_meta_log_" . $import_id . ".txt";
    if (!file_exists($filename)) {
        $myfile = fopen($filename, 'w');
    } else {
        $myfile = fopen($filename, 'a');
    }
    //$myfile = fopen("update_post_meta_log.txt", "a");
    if ($meta_key == 'product_group' && $meta_value) {
        fwrite($myfile, "\n" . $import_id . ':' . $post_id);
    }
    fclose($myfile);
}
add_action('pmxi_update_post_meta', 'pmxi_update_post_meta', 10, 3);

function my_ajax_function()
{

    //$newcat=wp_insert_term( 'test', 'product_cat', $args = array() );
    //echo '<pre>';
    //print_r($newcat['term_id']);
    //exit;

    $theme_path = get_template_directory();
    $file = fopen($theme_path . '/Nauji pogrupiai.csv', "r");
    $cat_arry = array();
    while (!feof($file)) {
        $data = fgetcsv($file);
        $cat_arry[$data[3]] = $data;
    }
    fclose($file);
    //echo '<pre>';
    //print_r($cat_arry); exit;

    foreach ($cat_arry as $k => $val) {
        if ($k != '' && $k != 'Grupė') {
            $level1 = trim($val[0]);
            $level2 = trim($val[1]);
            $level3 = trim($val[2]);
            $level4 = trim($val[4]);
            if ($level3 == $level4) {
                $level4 = '';
            }

            $exit1 = 'not exist';
            $category1 = get_term_by('name', $level1, 'product_cat');
            if ($category1) {
                $exit1 = $category1->term_id;
            } else {
                $newcat1 = wp_insert_term($level1, 'product_cat', $args = array());
                if (isset($newcat1['term_id'])) {
                    $exit1 = $newcat1['term_id'];
                } else {
                    $exit1 = $newcat1->term_id;
                }
            }

            $exit2 = 'not exist';
            $category2 = get_term_by('name', $level2, 'product_cat');
            //print_r($category2->parent);
            if ($category2) {
                $exit2 = $category2->term_id;
                if ($exit1 && $exit1 != 'not exist' && $category2->parent != $exit1) {
                    echo '<br>update1=' . $category2->parent . '==' . $exit1;
                    $result = wp_update_term($exit2, 'product_cat', array('parent' => $exit1));
                }
            } else {
                $newcat2 = wp_insert_term($level2, 'product_cat', array('parent' => $exit1));
                if (isset($newcat2['term_id'])) {
                    $exit2 = $newcat2['term_id'];
                } else {
                    $exit2 = $newcat2->term_id;
                }
            }



            $exit3 = 'not exist';
            $category3 = get_term_by('name', $level3, 'product_cat');
            if ($category3) {
                $exit3 = $category3->term_id;
                if ($exit2 && $exit2 != 'not exist' && $category3->parent != $exit2) {
                    echo '<br>update2=' . $exit3 . '==' . $category3->parent . '==' . $exit2;
                    $result = wp_update_term($exit3, 'product_cat', array('parent' => $exit2));
                }
            } else {
                $newcat3 = wp_insert_term($level3, 'product_cat', array('parent' => $exit2));
                if (isset($newcat3['term_id'])) {
                    $exit3 = $newcat3['term_id'];
                } else {
                    $exit3 = $newcat3->term_id;
                }
            }

            if ($level4 != '') {
                $exit4 = 'not exist';
                $category4 = get_term_by('name', $level4, 'product_cat');
                if ($category4) {
                    $exit4 = $category4->term_id;
                    if ($exit3 && $exit3 != 'not exist' && $category3->parent != $exit3) {
                        echo '<br>update3=' . $exit4 . '==' . $category3->parent . '==' . $exit3;
                        $result = wp_update_term($exit4, 'product_cat', array('parent' => $exit3));
                    }
                } else {

                    $newcat4 = wp_insert_term($level4, 'product_cat', array('parent' => $exit3));
                    if (isset($newcat4['term_id'])) {
                        $exit4 = $newcat4['term_id'];
                    } else {
                        $exit4 = $newcat4->term_id;
                    }
                }
            }

            echo '<br>' . $k;
            echo '<br>1=' . $level1 . ' id=' . $exit1;
            echo '<br>2=' . $level2 . ' id=' . $exit2 . ' p=' . $category2->parent;
            echo '<br>3=' . $level3 . ' id=' . $exit3 . ' p=' . $category3->parent;
            if ($level4 != '') {
                echo '<br>4=' . $level4 . ' id=' . $exit4 . ' p=' . $category4->parent;
            }
            echo '<br><br>';
        }
    }

    wp_die();
}
add_action('wp_ajax_add_new_categories', 'my_ajax_function');
add_action('wp_ajax_nopriv_add_new_categories', 'my_ajax_function');


add_action("wp_ajax_import_products_cat_update", "import_products_cat_update");
add_action("wp_ajax_nopriv_import_products_cat_update", "import_products_cat_update");

function import_products_cat_update()
{
    global $wpdb;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(0);

    //echo get_post_meta(71936,'importcat',true); exit;


    $args = array(
        //'post_type'     => 'product_variation',
        'post_type' => 'product',
        //'post_type' => array('product', 'product_variation'),
        'post_status' => array('private', 'publish'),
        'numberposts' => 20,
        //'numberposts' => -1,
        'orderby' => 'menu_order',
        'order' => 'asc',
        'meta_query' => array(
            array('key' => 'importcat', 'compare' => 'NOT EXISTS'),
            array(
                'key' => 'product_group',
                'value' => '',
                'compare' => '!='
            )
        ),
    );
    if (isset($_GET['count'])) {
        /*echo $q="DELETE FROM $wpdb->postmeta WHERE `meta_key`='importcat'";
$wpdb->query( $q );*/

        /*$posts = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE (meta_key = 'importcat')");
echo '<pre>';
print_r($posts); */



        //my_update_post_meta( 63301, 'product_group', 'P4863');
        $args['numberposts'] = $_GET['count'];
        $query = new WP_Query($args);
        echo $count = $query->found_posts;
        exit;
    }


    $variations = get_posts($args);
    echo '<pre>';
    print_r($variations);

    foreach ($variations as $k => $product) {
        echo '<br>';
        echo $meta_value = get_post_meta($product->ID, 'product_group', true);
        my_update_post_meta($product->ID, 'product_group', $meta_value);
    }




    exit;
}

add_action("wp_ajax_import_image_testing", "import_image_testing");
add_action("wp_ajax_nopriv_import_image_testing", "import_image_testing");

function import_image_testing()
{



    /*$images=getproductImages($_GET['sku']);
$theme_path = get_template_directory();
$content = $_GET['sku'].' '.$images."\n\n";
$fp = fopen($theme_path . "/Images.txt","wb");
fwrite($fp,$content);
fclose($fp);
print_r($images); exit;*/
    echo 'import_image_testing';
}

function getproductImages($product_sku)
{
    $image = '';
    $images = array();
    for ($i = 0; $i < 11; $i++) {
        if ($i == 0) {
            $image = 'https://www.savinge.lt/images/shop/products/' . $product_sku . '.jpg';
        } else {
            $image = 'https://www.savinge.lt/images/shop/products/' . $product_sku . '_' . $i . '.jpg';
        }
        $headers = get_headers($image);
        if ($headers && strpos($headers[0], '200')) {
            $existimage = MediaFileAlreadyExists(basename($image));
            //echo '<br>'.basename($image);
            //print_r($existimage);
            if ($existimage) {
                $images[] = $existimage;
            } else {
                $result = download_image_and_save_to_media($image);
                if (!is_wp_error($result)) {
                    $images[] = $result;
                } else {
                    echo 'Error: ' . $result->get_error_message();
                }
            }
        }
    }
    return $images;
}

function download_image_and_save_to_media($image_url)
{
    // Require the necessary files
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Download the image from the URL
    $tmp = download_url($image_url);

    // Set up the image name
    $file_array = array(
        'name' => basename($image_url),
        'tmp_name' => $tmp,
    );

    // Check for download errors
    if (is_wp_error($tmp)) {
        @unlink($file_array['tmp_name']);
        return $tmp;
    }

    // Upload the image to the media library
    $media_id = media_handle_sideload($file_array, 0);

    // Check for media handle sideload errors
    if (is_wp_error($media_id)) {
        @unlink($file_array['tmp_name']);
        return $media_id;
    }

    // Set the image as the featured image (optional)
    set_post_thumbnail(0, $media_id);

    return $media_id;
}

function is_image_in_media_library($image_name)
{
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_title = %s", $image_name));
    if (!empty($attachment)) {
        return $attachment[0];
    }
    return false;
}

function MediaFileAlreadyExists($filename)
{
    global $wpdb;
    $query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%/$filename'";
    if ($wpdb->get_var($query)) {
        return $wpdb->get_var($query);
    }
    return false;
}

function my_saved_post($post_id, $xml_node, $is_update)
{


    $product = wc_get_product($post_id);
    if ($product) {
        $sku = $product->get_sku();
        $imageids = getproductImages($sku);

        if (isset($imageids[0])) {
            $firstimage = $imageids[0];
            $product->set_image_id($firstimage);
            unset($imageids[0]);
        }
        $product->save();
        if (count($imageids) > 0) {
            $gallery = get_post_meta($post_id, '_product_image_gallery', true);
            if (!empty(trim($gallery))) {
                $exitgalleryItems = explode(",", trim($gallery));
                if (count($exitgalleryItems)) {
                    $galleryItems = array_merge($exitgalleryItems, $imageids);
                } else {
                    $galleryItems = $imageids;
                }
                $galleryItems = array_unique($galleryItems);
            } else {
                $galleryItems = $imageids;
            }
            update_post_meta($post_id, '_product_image_gallery', implode(',', $galleryItems));
        }

        /*$import_id = wp_all_import_get_import_id(); 
$theme_path = get_template_directory();
$content = $sku.' '.join(',', $galleryItems).' '.$type. ' '.print_r($gallery, true)."\n\n";
$fp = fopen($theme_path . "/Images.txt","wb");
fwrite($fp,$content);
fclose($fp);*/
    }
}
add_action('pmxi_saved_post', 'my_saved_post', 10, 3);

// Add a minimum cart order administration fee
add_action('woocommerce_cart_calculate_fees', 'add_min_order_fee', 10, 1);
function add_min_order_fee($cart)
{
    if (is_admin() && !defined('DOING_AJAX'))
        return;

    // Set your minimum order amount and fee
    $minimum_order_amount = 10; // Adjust this value according to your needs
    $administration_fee = 1.95; // Adjust this value according to your needs

    if ($cart->subtotal < $minimum_order_amount) {
        $cart->add_fee(__('Minimalaus krepšelio administravimo mokestis (skaičiuojamas, jei užsakymo suma neviršija 10 eur.)', 'woocommerce'), $administration_fee);
    }
}


add_action('wp_ajax_delete_noneed_categories', 'delete_noneed_categories');
add_action('wp_ajax_nopriv_delete_noneed_categories', 'delete_noneed_categories');
function delete_noneed_categories()
{
    echo 'Those categories not exist in Nauji pogrupiai.csv So we are going to delete';


    $theme_path = get_template_directory();
    $file = fopen($theme_path . '/Nauji pogrupiai.csv', "r");
    $cat_arry = array();
    $allcategories = array();

    while (!feof($file)) {
        $data = fgetcsv($file);
        $cat_arry[$data[3]] = $data;

        if (trim($data[0]))
            $allcategories[] = $data[0];

        if (trim($data[1]))
            $allcategories[] = $data[1];

        if (trim($data[2]))
            $allcategories[] = $data[2];

        if (trim($data[4]))
            $allcategories[] = $data[4];
    }
    fclose($file);
    echo '<pre>';
    $allcategories = array_unique($allcategories);

    $allcategories = array_values($allcategories);

    //print_r($allcategories); exit;



    /*$categories = get_terms([
'taxonomy' => 'product_cat'
]);*/

    $taxonomy = 'product_cat';
    $orderby = 'name';
    $show_count = 0;      // 1 for yes, 0 for no
    $pad_counts = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title = '';
    $empty = 0;

    $args = array(
        'taxonomy' => $taxonomy,
        'orderby' => $orderby,
        'show_count' => $show_count,
        'pad_counts' => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li' => $title,
        'hide_empty' => $empty
    );
    $categories = get_categories($args);

    $i = 1;
    foreach ($categories as $category) {

        if (!in_array($category->name, $allcategories)) {
            $products = wc_get_products(['category_id' => $category->term_id]);
            foreach ($products as $k => $p) {
                echo $p->get_id() . ',';
                wp_remove_object_terms($p->get_id(), $category->term_id, 'product_cat');
                delete_post_meta($p->get_id(), 'importcat', null);
            }
            wp_delete_term($category->term_id, 'product_cat');
            echo ' <div class="col-md-4"><a href="' . get_category_link($category->term_id) . '">' . $i . ' ' . $category->name . ' ' . $category->term_id . '</a></div>';
            $i++;
        }
    }

    exit;
}


function delete_duplicate_categories()
{


    $taxonomy = 'product_cat';
    $orderby = 'name';
    $show_count = 0;      // 1 for yes, 0 for no
    $pad_counts = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title = '';
    $empty = 0;

    $args = array(
        'taxonomy' => $taxonomy,
        'orderby' => $orderby,
        'show_count' => $show_count,
        'pad_counts' => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li' => $title,
        'hide_empty' => $empty
    );
    $categories = get_categories($args);

    $i = 1;
    $catarray = array();
    foreach ($categories as $category) {
        $catarray[$category->name][] = $category->term_id;
    }

    echo '<pre>';
    foreach ($catarray as $k => $v) {
        if (count($v) > 1) {
            echo '<br>' . $k;
            echo '<br>';
            print_r($v);
        }
    }
}

add_action('wp_ajax_delete_duplicate_categories', 'delete_duplicate_categories');
add_action('wp_ajax_nopriv_delete_duplicate_categories', 'delete_duplicate_categories');



/* Disable the old import system*/
remove_action('pmxi_after_xml_import', 'after_xml_import', 10);
remove_action('pmxi_update_post_meta', 'pmxi_update_post_meta', 10);
remove_action('pmxi_saved_post', 'my_saved_post', 10);
remove_action('wp_ajax_add_new_categories', 'my_ajax_function');
remove_action('wp_ajax_nopriv_add_new_categories', 'my_ajax_function');
remove_action("wp_ajax_import_products_cat_update", "import_products_cat_update");
remove_action("wp_ajax_nopriv_import_products_cat_update", "import_products_cat_update");


// Edit By Missem



// Remove default payment box from order review (run late so WC has already added it)
add_action('wp', function () {
    remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
}, 21);

// Add payment methods box after billing form on the left (once only)
add_action('woocommerce_after_checkout_billing_form', function () {
    echo '<h3 class="billing_headings">3. Apmokėjimas</h3> <div class="custom-payment-methods"> ';

    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

    if (!empty($available_gateways)) {
        // Call payment template ONCE with all gateways (template loops inside)
        wc_get_template('checkout/payment.php', array(
            'checkout' => WC()->checkout(),
            'available_gateways' => $available_gateways,
            'order_button_text' => apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce')),
        ));
    } else {
        echo '<p>' . esc_html__('Sorry, no payment methods are available for your location.', 'woocommerce') . '</p>';
    }

    echo '</div>';
}, 20);






// priority 5 ensures it’s at the top of the right column

function at24_mobile_category_menu()
{
    $args = array(
        'taxonomy' => 'product_cat',
        'orderby' => 'name',
        'hide_empty' => false,
        'parent' => 0
    );

    $parent_cats = get_terms($args);

    echo '<ul class="at24-cat-menu">';

    foreach ($parent_cats as $parent) {

        // Sub categories
        $childs = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => $parent->term_id
        ));

        echo '<li>';
        echo '<a href="' . get_term_link($parent) . '">'
            . $parent->name .
            '</a>';
        if (!empty($childs)) {
            echo '<span class="at24-toggle"><svg xmlns="https://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14" fill="none">
<g clip-path="url(#clip0_488_2271)">
<path d="M1 13L7 7L1 1" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_488_2271">
<rect width="8" height="14" fill="white"/>
</clipPath>
</defs>
</svg></span>';
        }



        // ===== CHILDREN =====
        if (!empty($childs)) {

            echo '<ul class="at24-sub-menu">';

            foreach ($childs as $child) {

                echo '<li>
                                    <a href="' . get_term_link($child) . '">'
                    . $child->name .
                    '</a>
                                </li>';
            }

            echo '</ul>';
        }

        echo '</li>';
    }

    echo '</ul>';
}
add_action('pre_get_posts', 'force_search_to_products');

function force_search_to_products($query)
{

    if (!is_admin() && $query->is_main_query() && $query->is_search()) {

        $query->set('post_type', 'product');
    }
}
// 1️⃣ Function to generate custom cart totals
function custom_cart_totals_fragment()
{
    if (!WC()->cart)
        return '';

    ob_start();
    ?>

    <div id="custom-cart-totals-container">
        <h3 class="billing_headings">Atsiskaitymas</h3>
        <div class="cart-totals-row cart-subtotal">
            <span class="cart-totals-label"><?php esc_html_e('Suma', 'woocommerce'); ?></span>
            <span class="cart-totals-value"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>
        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>

            <div class="cart-totals-row cart-shipping">
                <span class="cart-totals-label">
                    <?php esc_html_e('Pristatymas', 'woocommerce'); ?>
                </span>

                <span class="cart-totals-value">
                    <?php
                    $packages = WC()->shipping()->get_packages();
                    $rates = !empty($packages[0]['rates']) ? $packages[0]['rates'] : array();
                    $chosen_methods = WC()->session->get('chosen_shipping_methods');
                    $chosen = !empty($chosen_methods[0]) ? $chosen_methods[0] : '';

                    if (!empty($rates)) {
                        if ($chosen && isset($rates[$chosen])) {
                            echo wc_price($rates[$chosen]->cost);
                        } else {
                            // AJAX / refresh par chosen empty ho sakta hai — pehla available rate use karo
                            $first_rate = reset($rates);
                            echo wc_price($first_rate->cost);
                        }
                    } else {
                        echo esc_html__('—', 'woocommerce');
                    }
                    ?>
                </span>
            </div>

        <?php endif; ?>

        <?php
        // --- ADDITIONAL FEES ---
        $fees = WC()->cart->get_fees();
        if (!empty($fees)) {
            foreach ($fees as $fee): ?>
                <div class="cart-totals-row fee fee-<?php echo esc_attr(sanitize_title($fee->name)); ?>">
                    <span class="cart-totals-label"><?php echo esc_html($fee->name); ?></span>
                    <span class="cart-totals-value"><?php wc_cart_totals_fee_html($fee); ?></span>
                </div>
            <?php endforeach;
        } else { ?>
            <div class="cart-totals-row fee fee-none">
                <span class="cart-totals-label">Mažo krepšelio mokestis (užsakymams iki 15 €)</span>
                <span class="cart-totals-value">0.00€</span>
            </div>
        <?php } ?>

        <!-- SUBTOTAL -->
        <div class="cart-totals-row tax">
            <span class="cart-totals-label">PVM</span>
            <span class="cart-totals-value">
                <?php
                // WooCommerce ka function jo total tax dikhaata hai
                wc_cart_totals_taxes_total_html();
                ?>
            </span>
        </div>

        <!-- TOTAL -->
        <div class="cart-totals-row order-total">
            <span class="cart-totals-label">Bendrai</span>
            <span class="cart-totals-value"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 2️⃣ Initial display on checkout page
add_action('woocommerce_checkout_after_order_review', function () {
    echo custom_cart_totals_fragment();
});

// 3️⃣ AJAX callback to refresh cart totals dynamically
add_action('wp_ajax_get_custom_cart_totals', 'ajax_get_custom_cart_totals');
add_action('wp_ajax_nopriv_get_custom_cart_totals', 'ajax_get_custom_cart_totals');
function ajax_get_custom_cart_totals()
{
    if (!WC()->cart) {
        WC()->cart = new WC_Cart();
    }
    // AJAX request mein shipping/totals calculate karo taake packages + rates milen (— na aaye)
    WC()->cart->calculate_shipping();
    WC()->cart->calculate_totals();
    echo custom_cart_totals_fragment();
    wp_die();
}

// 4️⃣ Enqueue JS to update totals after AJAX updates
add_action('wp_footer', function () {
    if (is_checkout()):
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                function updateCustomCartTotals() {
                    var $container = $('#custom-cart-totals-container');
                    if ($container.length) {
                        $.ajax({
                            url: wc_checkout_params.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'get_custom_cart_totals'
                            },
                            success: function (response) {
                                $container.replaceWith(response);
                            }
                        });
                    }
                }
                // Trigger after WooCommerce checkout updates
                $(document.body).on('updated_checkout updated_cart_totals', updateCustomCartTotals);
            });
        </script>
        <?php
    endif;
});
// Place order button ko alag se display karna
add_action('woocommerce_checkout_after_order_review', 'custom_place_order_button');
function custom_place_order_button()
{
    $order_button_text = apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce'));
    ?>
    <div id="my-custom-place-order" style="margin-bottom: 15px; text-align:center;">
        <?php if (!is_user_logged_in()): ?>
            <p class="create-account-notice">
                <label>
                    <input type="checkbox" name="createaccount" id="createaccount" value="1">
                    <?php esc_html_e('Sukurti paskyrą, jog lengvai apsipirkti ateityje', 'woocommerce'); ?>
                </label>
            </p>
        <?php endif; ?>

        <?php wc_get_template('checkout/terms.php'); ?>

        <?php do_action('woocommerce_review_order_before_submit'); ?>

        <?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button alt' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html__('Apmokėti užsakymą', 'woocommerce') . '</button>'); ?>

        <?php do_action('woocommerce_review_order_after_submit'); ?>

        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>

        <p style="margin-top:15px;">
            Pateikdami užsakymą athome24.lt parduotuvėje sutinkate su
            <u>privatumo politika</u>, <u>sąlygomis ir nuostatomis</u> bei
            atšaukimo politika.
        </p>
    </div>
    <?php
}

// 1️⃣ Display checkbox for specific SKU
add_action('woocommerce_after_checkout_form', 'custom_sku_checkbox_product', 10);
function custom_sku_checkbox_product()
{
    $sku = '4841421';
    $product_id = wc_get_product_id_by_sku($sku);

    if (!$product_id)
        return;

    $product = wc_get_product($product_id);

    $title = $product->get_name();
    $image = $product->get_image(array(80, 80)); // 80x80 thumbnail
    $price = $product->get_regular_price() ?: '0.00';
    $sale_price = $product->get_sale_price() ?: $price;

    // Check if product already in cart
    $in_cart = false;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $in_cart = true;
            break;
        }
    }

    ?>
    <div id="custom-sku-product" style="margin-bottom: 20px;">
        <h2 class="checkout_offer">SPECIALUS PASIŪLYMAS TAU!</h2>
        <?php echo wp_kses_post($image); ?>
        <h6 class="checkout_product_title">
            <?php echo esc_html($title); ?>
        </h6>
        <?php echo wc_price($sale_price); ?>
        <label class="checkout_lable">
            <input type="checkbox" class="custom-add-to-cart-checkbox"
                data-product_id="<?php echo esc_attr($product_id); ?>" <?php checked($in_cart); ?>>
            <span style="font-size: 0.9em; color: #555;">Pridėti prie užsakymo</span>
        </label>
    </div>

    <script type="text/javascript">
        jQuery(function ($) {
            $('.custom-add-to-cart-checkbox').on('change', function () {
                var product_id = $(this).data('product_id');
                var checked = $(this).is(':checked');

                $.ajax({
                    url: wc_checkout_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'custom_sku_add_remove_cart',
                        product_id: product_id,
                        add: checked ? 1 : 0
                    },
                    success: function (response) {
                        // Update checkout fragments dynamically
                        $(document.body).trigger('update_checkout');
                    }
                });
            });
        });
    </script>
    <?php
}

// 2️⃣ AJAX handler to add/remove product
add_action('wp_ajax_custom_sku_add_remove_cart', 'custom_sku_add_remove_cart');
add_action('wp_ajax_nopriv_custom_sku_add_remove_cart', 'custom_sku_add_remove_cart');

function custom_sku_add_remove_cart()
{
    if (!isset($_POST['product_id']))
        wp_send_json_error();

    $product_id = intval($_POST['product_id']);
    $add = intval($_POST['add']);

    if (!WC()->cart)
        WC()->cart = new WC_Cart();

    if ($add) {
        // Add to cart if not already
        $found = false;
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                $found = true;
                break;
            }
        }
        if (!$found)
            WC()->cart->add_to_cart($product_id);
    } else {
        // Remove from cart
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }
    }

    WC()->cart->calculate_totals();
    wp_send_json_success();
}
add_filter('template_include', 'force_shop_page_template', 99);

function force_shop_page_template($template)
{

    if (is_shop()) {
        $new_template = locate_template(array('page-shop.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }

    return $template;
}
add_filter('gettext', 'translate_myaccount_address_lithuanian', 20, 3);
function translate_myaccount_address_lithuanian($translated_text, $text, $domain)
{

    if ($domain == 'woocommerce') {

        $translations = array(

            // Address Tab Headings
            'Addresses' => 'Adresai',
            'Billing address' => 'Atsiskaitymo adresas',
            'Shipping address' => 'Pristatymo adresas',

            // Buttons
            'Edit' => 'Redaguoti',
            'Add' => 'Pridėti',
            'Save address' => 'Išsaugoti adresą',

            // Empty address messages
            'You have not set up this type of address yet.' =>
                'Jūs dar nesate nustatę šio adreso.',

            // Fields
            'First name' => 'Vardas',
            'Last name' => 'Pavardė',
            'Company name' => 'Įmonės pavadinimas',
            'Country / Region' => 'Šalis / Regionas',
            'Street address' => 'Gatvės adresas',
            'Town / City' => 'Miestas',
            'State / County' => 'Apskritis',
            'Postcode / ZIP' => 'Pašto kodas',
            'Phone' => 'Telefonas',
            'Email address' => 'El. paštas',

            // Notices
            'Address changed successfully.' =>
                'Adresas sėkmingai pakeistas.',

            'The following addresses will be used on the checkout page by default.' =>
                'Šie adresai bus naudojami atsiskaitymo puslapyje pagal nutylėjimą.'
        );

        if (isset($translations[$text])) {
            $translated_text = $translations[$text];
        }
    }

    return $translated_text;
}
add_filter('the_title', 'limit_product_title_length_except_single', 10, 2);
function limit_product_title_length_except_single($title, $post_id)
{

    // Sirf frontend pe apply ho
    if (is_admin()) {
        return $title;
    }

    // Check karo ke post type product ho
    if (get_post_type($post_id) === 'product') {

        // Single product page pe apply na ho
        if (!is_product()) {

            // 10 letters limit
            if (mb_strlen($title) > 40) {
                $title = mb_substr($title, 0, 40) . '...';
            }
        }
    }

    return $title;
}
add_action('wp_enqueue_scripts', 'savinge_force_wpgis_vertical_mobile', 120);
function savinge_force_wpgis_vertical_mobile()
{
    if (is_admin() || !function_exists('is_product') || !is_product()) {
        return;
    }

    if (!wp_script_is('wpgis-front-js', 'enqueued') && !wp_script_is('wpgis-front-js', 'registered')) {
        return;
    }

    $inline_js = <<<'JS'
(function ($) {
    "use strict";

    if (!$.fn || !$.fn.slick || $.fn.slick.__savingeVerticalPatched) {
        return;
    }

    var originalSlick = $.fn.slick;

    $.fn.slick = function () {
        var args = Array.prototype.slice.call(arguments);

        if (this && this.hasClass && this.hasClass("wpgis-slider-nav") && args.length && typeof args[0] === "object" && args[0] !== null) {
            var options = $.extend(true, {}, args[0]);
            var isVerticalLayout = typeof window.object_name !== "undefined" &&
                window.object_name.wpgis_slider_layout &&
                window.object_name.wpgis_slider_layout !== "horizontal";

            if (isVerticalLayout) {
                options.vertical = true;

                if (Array.isArray(options.responsive)) {
                    options.responsive = options.responsive.map(function (item) {
                        if (!item || typeof item !== "object") {
                            return item;
                        }

                        var updatedItem = $.extend(true, {}, item);
                        if (updatedItem.settings && typeof updatedItem.settings === "object") {
                            updatedItem.settings.vertical = true;
                        }

                        return updatedItem;
                    });
                }
            }

            args[0] = options;
        }

        return originalSlick.apply(this, args);
    };

    $.fn.slick.__savingeVerticalPatched = true;
})(jQuery);
JS;

    $fallback_js = <<<'JS'
(function ($) {
    "use strict";

    function reforceVerticalAfterInit() {
        var isVerticalLayout = typeof window.object_name !== "undefined" &&
            window.object_name.wpgis_slider_layout &&
            window.object_name.wpgis_slider_layout !== "horizontal";

        if (!isVerticalLayout || !window.matchMedia("(max-width: 767px)").matches) {
            return;
        }

        var $nav = $(".wpgis-slider-nav.slick-initialized");
        if (!$nav.length) {
            return;
        }

        $nav.slick("slickSetOption", "vertical", true, false);
        $nav.slick("slickSetOption", "slidesToShow", 4, true);
    }

    $(window).on("load resize orientationchange", reforceVerticalAfterInit);
    $(document).on("woocommerce_variation_has_changed", reforceVerticalAfterInit);
    $(reforceVerticalAfterInit);
})(jQuery);
JS;

    wp_add_inline_script('wpgis-front-js', $inline_js, 'before');
    wp_add_inline_script('wpgis-front-js', $fallback_js, 'after');
}
add_filter('woocommerce_shop_loop_item_title', 'custom_trim_loop_title', 10);
add_filter('woocommerce_shop_loop_item_title', 'custom_trim_loop_title', 10);

function custom_trim_loop_title($title)
{
    // Limit to 30 words
    $trimmed = wp_trim_words($title, 30, '...');
    return $trimmed;
}