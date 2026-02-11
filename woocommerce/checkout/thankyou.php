<?php

/**
 * Thankyou page
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 *
 * @var WC_Order|false $order
 */

defined('ABSPATH') || exit;

if (!$order) {
    echo '<div class="page thankyou-page-wrap"><div class="thankyou-header header"><h1>' . esc_html__('Ačiū! Jūsų užsakymą gavome!', 'woocommerce') . '</h1><p>' . esc_html__('Sąskaitą faktūrą, tolimesnę informaciją bei siuntos sekimo kodą gausite el. paštu.', 'woocommerce') . '</p></div></div>';
    return;
}

if ($order->has_status('failed')) {
    echo '<div class="page thankyou-page-wrap"><div class="thankyou-header header"><p class="woocommerce-notice woocommerce-notice--error">' . esc_html__('Unfortunately your order cannot be processed. Please attempt your purchase again.', 'woocommerce') . '</p><p><a href="' . esc_url($order->get_checkout_payment_url()) . '" class="btn btn-primary">' . esc_html__('Pay', 'woocommerce') . '</a></p></div></div>';
    return;
}

$totals = $order->get_order_item_totals();
$shipping_methods = $order->get_shipping_methods();
$first_shipping = !empty($shipping_methods) ? reset($shipping_methods) : null;

// Get coupon codes from order
$order_coupon_codes = $order->get_coupon_codes();
$order_discount_total = $order->get_total_discount();

// Optional: next order discount code (filter so you can pass from plugin/theme)
$thankyou_discount_code = apply_filters('savinge_thankyou_discount_code', '', $order);

// Show banner if: filter provides code OR order has discount applied
$has_order_discount = !empty($order_coupon_codes) && $order_discount_total > 0;
$thankyou_show_discount_banner = apply_filters('savinge_thankyou_show_discount_banner', (bool) ($thankyou_discount_code || $has_order_discount), $order);

// Use order coupon code if filter doesn't provide one
if (empty($thankyou_discount_code) && $has_order_discount) {
    $thankyou_discount_code = !empty($order_coupon_codes) ? $order_coupon_codes[0] : '';
}
?>

<div class="page thankyou-page-wrap">
    <!-- Header -->
    <div class="thankyou-header header">
        <h1><?php esc_html_e('Ačiū! Jūsų užsakymą gavome!', 'woocommerce'); ?></h1>
        <p><?php esc_html_e('Sąskaitą faktūrą, tolimesnę informaciją bei siuntos sekimo kodą gausite el. paštu.', 'woocommerce'); ?>
        </p>
    </div>

    <!-- Order summary -->
    <div class="card thankyou-card">
        <h2><?php esc_html_e('Užsakymo santrauka', 'woocommerce'); ?></h2>

        <div class="thankyou-card-content content">
            <!-- LEFT: Items list -->
            <ul class="items-list">
                <?php
                foreach ($order->get_items() as $item_id => $item) {
                    if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
                        continue;
                    }
                    $product = $item->get_product();
                    $sku = $product && $product->get_sku() ? $product->get_sku() : '—';
                    $name = $item->get_name();
                    $qty = (int) $item->get_quantity();
                    $name_display = $qty > 1 ? 'x' . $qty . ' ' . $name : $name;
                    ?>
                    <li class="item">
                        <div class="item-row">
                            <span class="item-name"><span
                                    style="color: black; line-height: 0px; font-weight: bold;">x</span>
                                <?php echo esc_html($name_display); ?></span>
                            <span
                                class="item-price"><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></span>
                        </div>
                        <span class="item-sku"><?php echo esc_html__('SKU:', 'woocommerce'); ?>
                            <?php echo esc_html($sku); ?></span>
                    </li>
                <?php } ?>
            </ul>

            <!-- RIGHT: Shipping -->
            <div class="shipping">
                <h3 class="ship-title"><?php esc_html_e('Pristatymas', 'woocommerce'); ?></h3>
                <div class="ship-box">
                    <?php if ($first_shipping): ?>
                        <p class="method"><?php echo esc_html($first_shipping->get_method_title()); ?></p>
                        <p class="sub"><?php esc_html_e('Kurjeris', 'woocommerce'); ?></p>
                        <p class="ship-price">
                            <?php echo wc_price($first_shipping->get_total(), array('currency' => $order->get_currency())); ?>
                        </p>
                    <?php else: ?>
                        <p class="method">—</p>
                        <p class="ship-price"><?php echo wc_price(0, array('currency' => $order->get_currency())); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="totals">
            <?php
            // Get order totals
            $subtotal = $order->get_subtotal();
            $shipping_total = $order->get_shipping_total();
            $tax_total = $order->get_total_tax();
            $discount_total = $order->get_total_discount();
            $coupon_codes = $order->get_coupon_codes();
            $grand_total = $order->get_total();
            $currency = $order->get_currency();

            // Suma (Subtotal)
            ?>
            <div>
                <span><?php esc_html_e('Suma', 'woocommerce'); ?></span>
                <span><?php echo wc_price($subtotal, array('currency' => $currency)); ?></span>
            </div>

            <?php
            // Pristatymas ke price (Shipping)
            ?>
            <div>
                <span><?php esc_html_e('Pristatymas', 'woocommerce'); ?></span>
                <span><?php echo wc_price($shipping_total, array('currency' => $currency)); ?></span>
            </div>

            <?php
            // PVM ke price (VAT/Tax)
            if ($tax_total > 0) {
                ?>
                <div>
                    <span><?php esc_html_e('PVM', 'woocommerce'); ?></span>
                    <span><?php echo wc_price($tax_total, array('currency' => $currency)); ?></span>
                </div>
                <?php
            }

            // Nuolaida agar laga je tu kitne euro - howe hen (Discount if applied)
            if ($discount_total > 0) {
                ?>
                <div class="discount">
                    <span><?php esc_html_e('Nuolaida', 'woocommerce'); ?></span>
                    <span><?php echo wc_price($discount_total, array('currency' => $currency)); ?></span>
                </div>
                <?php
            } else {
                ?>
                <div class="discount">
                    <span><?php esc_html_e('Nuolaida', 'woocommerce'); ?></span>
                    <span><?php echo wc_price(0, array('currency' => $currency)); ?></span>
                </div>
                <?php
            }
            // Nuolaidos kodas [____] is square bracket me code ae ga or samne me jitna euro - howa he woh show hoga
            if (!empty($coupon_codes) && $discount_total > 0) {
                foreach ($coupon_codes as $coupon_code) {
                    // Get individual coupon discount amount
                    $coupon_discount = 0;
                    foreach ($order->get_items('coupon') as $coupon_item) {
                        if ($coupon_item->get_code() === $coupon_code) {
                            $coupon_discount = abs($coupon_item->get_discount());
                            break;
                        }
                    }
                    ?>
                    <div class="discount-code-row">
                        <span><?php echo esc_html__('Nuolaidos kodas', 'woocommerce') . ' [' . esc_html($coupon_code) . ']'; ?></span>
                        <span><?php echo wc_price($coupon_discount, array('currency' => $currency)); ?></span>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="discount-code-row">
                    <span><?php echo esc_html__('Nuolaidos kodas', 'woocommerce') . ' [—]'; ?></span>
                    <span><?php echo wc_price(0, array('currency' => $currency)); ?></span>
                </div>
                <?php
            }

            // Bendrai last me same jitna bana he woh show hoga (Grand Total)
            ?>
            <div class="grand-total">
                <span><?php esc_html_e('Bendrai', 'woocommerce'); ?></span>
                <span><?php echo wc_price($grand_total, array('currency' => $currency)); ?></span>
            </div>
        </div>
    </div>

    <?php if ($thankyou_show_discount_banner && !empty($thankyou_discount_code)): ?>
        <!-- Discount banner -->
        <div class="discount-banner">
            <div class="discount-inner">
                <div class="discount-dash top"></div>
                <p class="discount-title">
                    <?php esc_html_e('Gavote 10% nuolaidos kodą sekančiam užsakymui!', 'woocommerce'); ?>
                </p>
                <div class="discount-code"><?php echo esc_html($thankyou_discount_code); ?></div>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop')) ?: home_url('/')); ?>"
                    class="discount-btn"><?php esc_html_e('APSIPIRKTI DABAR', 'woocommerce'); ?></a>
                <div class="discount-dash bottom"></div>
                <p class="discount-note">
                    <?php esc_html_e('Nuolaidos kodas galioja 30 d. nuo šio pranešimo gavimo datos.', 'woocommerce'); ?>
                    <?php esc_html_e('Nuolaida negalioja prekėms su akcija ir dovanų kuponams.', 'woocommerce'); ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
    <?php
    // Best-selling products (sab se ziyada sale hone wali)
    $related_ids = get_posts(array(
        'posts_per_page' => 12,
        'post_type' => 'product',
        'post_status' => 'publish',
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'fields' => 'ids',
    ));
    $related_ids = array_map('intval', (array) $related_ids);
    if (!empty($related_ids)):
        ?>
        <section class="related-products-section cart-related-products">
            <div>
                <h2 class="section-title">Jums taip pat gali patikti</h2>
                <div class="swiper-container related-products-swiper">
                    <div class="swiper-wrapper">
                        <?php
                        foreach ($related_ids as $related_id) {
                            $related_product = wc_get_product($related_id);
                            if (!$related_product || !$related_product->exists()) {
                                continue;
                            }
                            $product_title = $related_product->get_name();
                            $product_link = get_permalink($related_id);
                            $thumbnail_id = get_post_thumbnail_id($related_id);
                            $product_image = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'medium') : false;
                            $regular_price = $related_product->get_regular_price();
                            $sale_price = $related_product->get_sale_price();
                            $discount_percentage = 0;
                            if ($sale_price && $regular_price > 0) {
                                $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                            }
                            $image_url = $product_image ? $product_image[0] : wc_placeholder_img_src('medium');
                            ?>
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="top_bar_related_product">
                                        <?php if ($discount_percentage > 0): ?>
                                            <div class="discount-badge">-<?php echo (int) $discount_percentage; ?>%</div>
                                        <?php endif; ?>
                                        <div>
                                            <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . (int) $related_id . '"]'); ?>
                                        </div>
                                    </div>
                                    <div class="product-image">
                                        <a href="<?php echo esc_url($product_link); ?>">
                                            <img src="<?php echo esc_url($image_url); ?>"
                                                alt="<?php echo esc_attr($product_title); ?>">
                                        </a>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title">
                                            <a
                                                href="<?php echo esc_url($product_link); ?>"><?php echo esc_html($product_title); ?></a>
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
                                            class="add-to-cart-btn" data-product-id="<?php echo esc_attr($related_id); ?>">Į
                                            krepšelį</a>
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
                                tabindex="0" role="button" aria-label="Previous slide">
                                <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next">
                            <svg xmlns="https://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none"
                                tabindex="0" role="button" aria-label="Next slide">
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
    endif;
    $cross_sells = array_filter(array_map('wc_get_product', WC()->cart->get_cross_sells()));
    if ($cross_sells):
        $heading = apply_filters('woocommerce_product_cross_sells_products_heading', __('Jums taip pat gali patikti', 'woocommerce'));
        ?>
        <section class="cart-cross-sells">
            <h2 class="cart-cross-sells-title"><?php echo esc_html($heading); ?></h2>
            <div class="cart-cross-sells-swiper swiper cartCrossSellsSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($cross_sells as $cross_sell): ?>
                        <?php
                        $post_object = get_post($cross_sell->get_id());
                        setup_postdata($GLOBALS['post'] = &$post_object);
                        $cross_sale = $cross_sell->is_on_sale();
                        ?>
                        <div class="swiper-slide">
                            <div class="cart-cross-sell-card">
                                <a href="<?php echo esc_url(get_permalink($cross_sell->get_id())); ?>"
                                    class="cart-cross-sell-image">
                                    <?php if ($cross_sale): ?>
                                        <span
                                            class="cart-cross-sell-badge cart-cross-sell-badge-sale"><?php echo esc_html(getSalePercent($cross_sell)); ?></span>
                                    <?php endif; ?>
                                    <?php echo $cross_sell->get_image('woocommerce_thumbnail'); ?>
                                </a>
                                <div class="cart-cross-sell-info">
                                    <a href="<?php echo esc_url(get_permalink($cross_sell->get_id())); ?>"
                                        class="cart-cross-sell-name"><?php echo wp_kses_post($cross_sell->get_name()); ?></a>
                                    <div class="cart-cross-sell-price"><?php echo $cross_sell->get_price_html(); ?></div>
                                    <a href="<?php echo esc_url($cross_sell->add_to_cart_url()); ?>"
                                        class="button cart-cross-sell-add"><?php esc_html_e('Į krepšelį', 'woocommerce'); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
    <?php
    // Best-selling products (sab se ziyada sale hone wali)
    $related_ids = get_posts(array(
        'posts_per_page' => 12,
        'post_type' => 'product',
        'post_status' => 'publish',
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'fields' => 'ids',
    ));
    $related_ids = array_map('intval', (array) $related_ids);
    if (!empty($related_ids)):
        ?>
        <section class="related-products-section cart-related-products">
            <div>
                <h2 class="section-title">Neseniai žiūrėta</h2>
                <div class="swiper-container related-products-swiper">
                    <div class="swiper-wrapper">
                        <?php
                        foreach ($related_ids as $related_id) {
                            $related_product = wc_get_product($related_id);
                            if (!$related_product || !$related_product->exists()) {
                                continue;
                            }
                            $product_title = $related_product->get_name();
                            $product_link = get_permalink($related_id);
                            $thumbnail_id = get_post_thumbnail_id($related_id);
                            $product_image = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'medium') : false;
                            $regular_price = $related_product->get_regular_price();
                            $sale_price = $related_product->get_sale_price();
                            $discount_percentage = 0;
                            if ($sale_price && $regular_price > 0) {
                                $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                            }
                            $image_url = $product_image ? $product_image[0] : wc_placeholder_img_src('medium');
                            ?>
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="top_bar_related_product">
                                        <?php if ($discount_percentage > 0): ?>
                                            <div class="discount-badge">-<?php echo (int) $discount_percentage; ?>%</div>
                                        <?php endif; ?>
                                        <div>
                                            <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . (int) $related_id . '"]'); ?>
                                        </div>
                                    </div>
                                    <div class="product-image">
                                        <a href="<?php echo esc_url($product_link); ?>">
                                            <img src="<?php echo esc_url($image_url); ?>"
                                                alt="<?php echo esc_attr($product_title); ?>">
                                        </a>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title">
                                            <a
                                                href="<?php echo esc_url($product_link); ?>"><?php echo esc_html($product_title); ?></a>
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
                                            class="add-to-cart-btn" data-product-id="<?php echo esc_attr($related_id); ?>">Į
                                            krepšelį</a>
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
                                tabindex="0" role="button" aria-label="Previous slide">
                                <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next">
                            <svg xmlns="https://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none"
                                tabindex="0" role="button" aria-label="Next slide">
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
    endif;
    $cross_sells = array_filter(array_map('wc_get_product', WC()->cart->get_cross_sells()));
    if ($cross_sells):
        $heading = apply_filters('woocommerce_product_cross_sells_products_heading', __('Neseniai žiūrėta', 'woocommerce'));
        ?>
        <section class="cart-cross-sells">
            <h2 class="cart-cross-sells-title"><?php echo esc_html($heading); ?></h2>
            <div class="cart-cross-sells-swiper swiper cartCrossSellsSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($cross_sells as $cross_sell): ?>
                        <?php
                        $post_object = get_post($cross_sell->get_id());
                        setup_postdata($GLOBALS['post'] = &$post_object);
                        $cross_sale = $cross_sell->is_on_sale();
                        ?>
                        <div class="swiper-slide">
                            <div class="cart-cross-sell-card">
                                <a href="<?php echo esc_url(get_permalink($cross_sell->get_id())); ?>"
                                    class="cart-cross-sell-image">
                                    <?php if ($cross_sale): ?>
                                        <span
                                            class="cart-cross-sell-badge cart-cross-sell-badge-sale"><?php echo esc_html(getSalePercent($cross_sell)); ?></span>
                                    <?php endif; ?>
                                    <?php echo $cross_sell->get_image('woocommerce_thumbnail'); ?>
                                </a>
                                <div class="cart-cross-sell-info">
                                    <a href="<?php echo esc_url(get_permalink($cross_sell->get_id())); ?>"
                                        class="cart-cross-sell-name"><?php echo wp_kses_post($cross_sell->get_name()); ?></a>
                                    <div class="cart-cross-sell-price"><?php echo $cross_sell->get_price_html(); ?></div>
                                    <a href="<?php echo esc_url($cross_sell->add_to_cart_url()); ?>"
                                        class="button cart-cross-sell-add"><?php esc_html_e('Į krepšelį', 'woocommerce'); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
</div>