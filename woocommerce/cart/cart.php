<?php

/**
 * Cart Page
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.4.0
 */

defined('ABSPATH') || exit;

$free_shipping_threshold = 35;
$cart_subtotal = (float) WC()->cart->get_subtotal();
$amount_left = max(0, $free_shipping_threshold - $cart_subtotal);
$progress_percent = $free_shipping_threshold > 0 ? min(100, ($cart_subtotal / $free_shipping_threshold) * 100) : 100;

do_action('woocommerce_before_cart');
?>
<section class="section-padding cart-page-layout">
    <div class="max-width">
        <?php get_template_part('partials/woo-breadcrumb'); ?>

        <div class="cart-page-header">
            <h1 class="cart-page-title"><?php esc_html_e('Krepšelis', 'woocommerce'); ?></h1>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="cart-page-close"
                aria-label="<?php esc_attr_e('Close', 'woocommerce'); ?>"><svg xmlns="https://www.w3.org/2000/svg"
                    width="21" height="21" viewBox="0 0 21 21" fill="none">
                    <path
                        d="M20.6923 19.196C20.8895 19.3957 21 19.6652 21 19.9463C21 20.2272 20.8895 20.4969 20.6923 20.6966C20.4918 20.8912 20.2237 21 19.9447 21C19.6657 21 19.3976 20.8912 19.197 20.6966L10.5 11.9567L1.80302 20.6966C1.60243 20.8912 1.33433 21 1.05531 21C0.776297 21 0.508194 20.8912 0.307617 20.6966C0.110552 20.4969 0 20.2272 0 19.9463C0 19.6652 0.110552 19.3957 0.307617 19.196L9.01771 10.4693L0.307617 1.74255C0.140271 1.53796 0.0547517 1.27822 0.0676944 1.01386C0.0806371 0.749504 0.191105 0.499424 0.37762 0.312271C0.564135 0.125118 0.813362 0.0142721 1.07682 0.00128511C1.34028 -0.0117019 1.59913 0.0741107 1.80302 0.242029L10.5 8.98192L19.197 0.242029C19.4008 0.0741107 19.6597 -0.0117019 19.9232 0.00128511C20.1866 0.0142721 20.4358 0.125118 20.6224 0.312271C20.8089 0.499424 20.9194 0.749504 20.9323 1.01386C20.9453 1.27822 20.8597 1.53796 20.6923 1.74255L11.9822 10.4693L20.6923 19.196Z"
                        fill="#767676" />
                </svg></a>
        </div>

        <div class="cart-free-shipping-bar">
            <div class="cart_free_shiping_imageandtext">
                <?php $truck_icon_id = 73; ?>
                <?php if ($truck_icon_id && wp_get_attachment_image_src($truck_icon_id)): ?>
                    <img src="<?php echo esc_url(wp_get_attachment_image_src($truck_icon_id, 'thumbnail')[0]); ?>" alt=""
                        class="cart-free-shipping-icon" width="30" height="30">
                <?php endif; ?>
                <div class="cart-free-shipping-text">
                    <?php if ($amount_left > 0): ?>
                        <?php echo wp_kses_post(sprintf(__('Jums trūksta <strong class="amount-left">%s</strong> iki nemokamo pristatymo', 'woocommerce'), wc_price($amount_left))); ?>
                    <?php else: ?>
                        <strong><?php esc_html_e('Sveikiname! Jums prikauso nemokamas pristatymas 🎉', 'woocommerce'); ?></strong>
                    <?php endif; ?>
                </div>
            </div>
            <span class="cart-free-shipping-threshold">
                <?php echo wc_price($free_shipping_threshold); ?>
            </span>
            <div class="cart-free-shipping-progress-wrap">
                <div class="cart-free-shipping-progress" role="progressbar"
                    style="--progress: <?php echo esc_attr($progress_percent); ?>%;"
                    aria-valuenow="<?php echo esc_attr($progress_percent); ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>

        <form class="woocommerce-cart-form cart-form-layout" action="<?php echo esc_url(wc_get_cart_url()); ?>"
            method="post">
            <?php do_action('woocommerce_before_cart_table'); ?>

            <div class="cart-items-list">
                <?php do_action('woocommerce_before_cart_contents'); ?>
                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        $has_sale = $_product->is_on_sale();
                        ?>
                        <div class="cart-item-row woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>"
                            data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                            <div class="cart-item-thumbnail">
                                <?php if ($has_sale): ?>
                                    <span
                                        class="cart-item-badge cart-item-badge-sale"><?php echo esc_html(getSalePercent($_product)); ?></span>
                                <?php endif; ?>
                                <?php
                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key);
                                if ($product_permalink) {
                                    echo '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>';
                                } else {
                                    echo $thumbnail;
                                }
                                ?>
                            </div>
                            <div class="cart-item-info">
                                <div class="cart-item-name">
                                    <?php
                                    if ($product_permalink) {
                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                    } else {
                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                                    }
                                    ?>
                                </div>
                                <?php if ($_product->get_short_description()): ?>
                                    <div class="cart-item-desc">
                                        <?php echo wp_kses_post(wp_trim_words($_product->get_short_description(), 15)); ?>
                                    </div>
                                <?php endif; ?>
                                <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                                <?php do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key); ?>
                            </div>
                            <div class="cart-item-quantity" data-title="<?php esc_attr_e('Kiekis', 'woocommerce'); ?>">
                                <?php
                                if ($_product->is_sold_individually()) {
                                    echo '1';
                                    echo '<input type="hidden" name="cart[' . esc_attr($cart_item_key) . '][qty]" value="1" />';
                                } else {
                                    $min = 0;
                                    $max = $_product->get_max_purchase_quantity();
                                    $qty = $cart_item['quantity'];
                                    ?>
                                    <div class="quantity-wrap">
                                        <button type="button" class="cart-qty-minus"
                                            aria-label="<?php esc_attr_e('Mažinti', 'woocommerce'); ?>">−</button>
                                        <input type="number" class="input-text qty text"
                                            name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]"
                                            value="<?php echo esc_attr($qty); ?>" min="<?php echo esc_attr($min); ?>"
                                            max="<?php echo esc_attr($max); ?>" step="1" inputmode="numeric" />
                                        <button type="button" class="cart-qty-plus"
                                            aria-label="<?php esc_attr_e('Didinti', 'woocommerce'); ?>">+</button>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="cart-item-price" data-title="<?php esc_attr_e('Kaina', 'woocommerce'); ?>">
                                <?php if ($has_sale): ?>
                                    <span
                                        class="cart-item-price-current"><?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?></span>
                                    <span class="cart-item-price-old"><?php echo wc_price($_product->get_regular_price()); ?></span>
                                <?php else: ?>
                                    <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                                <?php endif; ?>
                            </div>
                            <div class="cart-item-remove">
                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg xmlns="https://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
<path d="M14.1911 13.12C14.3263 13.2565 14.4021 13.4407 14.4021 13.6328C14.4021 13.8248 14.3263 14.0092 14.1911 14.1456C14.0536 14.2786 13.8697 14.353 13.6784 14.353C13.487 14.353 13.3032 14.2786 13.1656 14.1456L7.20108 8.17213L1.23654 14.1456C1.09897 14.2786 0.915103 14.353 0.72375 14.353C0.532396 14.353 0.348527 14.2786 0.210968 14.1456C0.0758181 14.0092 0 13.8248 0 13.6328C0 13.4407 0.0758181 13.2565 0.210968 13.12L6.18448 7.15553L0.210968 1.19099C0.0962 1.05116 0.0375495 0.873635 0.0464258 0.692951C0.0553021 0.512268 0.131062 0.341345 0.258977 0.21343C0.386892 0.0855154 0.557816 0.00975464 0.738499 0.000878344C0.919182 -0.00799796 1.09671 0.050653 1.23654 0.165421L7.20108 6.13893L13.1656 0.165421C13.3054 0.050653 13.4829 -0.00799796 13.6636 0.000878344C13.8443 0.00975464 14.0152 0.0855154 14.1431 0.21343C14.271 0.341345 14.3468 0.512268 14.3557 0.692951C14.3646 0.873635 14.3059 1.05116 14.1911 1.19099L8.21758 7.15553L14.1911 13.12Z" fill="#EF7F7A"/>
</svg></a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_html__('Pašalinti', 'woocommerce'),
                                        esc_attr($product_id),
                                        esc_attr($_product->get_sku())
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php do_action('woocommerce_cart_contents'); ?>
            </div>

            <div class="cart-actions-hidden">
                <button type="submit" class="button" name="update_cart"
                    value="<?php esc_attr_e('Atnaujinti krepšelį', 'woocommerce'); ?>"><?php esc_html_e('Atnaujinti krepšelį', 'woocommerce'); ?></button>
                <?php do_action('woocommerce_cart_actions'); ?>
                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            </div>

            <?php do_action('woocommerce_after_cart_contents'); ?>
            <?php do_action('woocommerce_after_cart_table'); ?>
        </form>

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
                                <svg xmlns="https://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35"
                                    fill="none" tabindex="0" role="button" aria-label="Previous slide">
                                    <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                    <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-next">
                                <svg xmlns="https://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35"
                                    fill="none" tabindex="0" role="button" aria-label="Next slide">
                                    <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                    <path d="M11 11L24 17.5L11 24" stroke="#054C73" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
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

        <div class="cart-discount-banner">
            <h3 class="cart-discount-banner-title">
                <?php esc_html_e('Pirkite daugiau ir sutaupykite!', 'woocommerce'); ?>
            </h3>
            <p class="cart-discount-banner-subtitle">
                <?php esc_html_e('Gaukite iki 15% nuolaidą krepšeliui', 'woocommerce'); ?>
            </p>
            <p class="cart-discount-banner-desc">
                <?php esc_html_e('Nuolaida pritaikoma automatiškai. Nesumuojama su kitomis nuolaidomis, tačiau kiekvienam produktui parenkama didesnė nuolaida.', 'woocommerce'); ?>
            </p>
            <div>
                <div class="cart-discount-tiers">
                    <div class="cart-discount-tier"><span class="tier-amount">0 €</span></div>
                    <div class="cart-discount-tier"><span class="tier-amount">50 €</span></div>
                    <div class="cart-discount-tier"><span class="tier-amount">120 €</span></div>
                    <div class="cart-discount-tier"><span class="tier-amount">200 €</span></div>
                </div>
                <div class="cart-discount-progress-wrap">
                    <div class="cart-discount-progress"
                        style="--cart-value: <?php echo esc_attr(min(100, ($cart_subtotal / 200) * 100)); ?>%;"></div>
                </div>
                <div class="cart-discount-tiers">
                    <div class="cart-discount-tier"><span
                            class="tier-label"><?php esc_html_e('Nemokamas pristatymas', 'woocommerce'); ?></span></div>
                    <div class="cart-discount-tier"><span class="tier-label">5%
                            <?php esc_html_e('nuolaida', 'woocommerce'); ?></span></div>
                    <div class="cart-discount-tier"><span class="tier-label">10%
                            <?php esc_html_e('nuolaida', 'woocommerce'); ?></span></div>
                    <div class="cart-discount-tier"><span class="tier-label">15%
                            <?php esc_html_e('nuolaida', 'woocommerce'); ?></span></div>
                </div>
            </div>
        </div>

        <?php if (wc_coupons_enabled()): ?>
            <div class="cart-coupon-prompt">
                <button type="button" class="cart-coupon-toggle"
                    aria-expanded="false"><?php esc_html_e('Turite nuolaidos kuponą?', 'woocommerce'); ?></button>
                <div class="cart-coupon-form-wrap" hidden>
                    <form class="woocommerce-coupon-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value=""
                            placeholder="<?php esc_attr_e('Nuolaidos kuponas', 'woocommerce'); ?>" />
                        <button type="submit" class="button" name="apply_coupon"
                            value="<?php esc_attr_e('Taikyti', 'woocommerce'); ?>"><?php esc_attr_e('Taikyti', 'woocommerce'); ?></button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php do_action('woocommerce_before_cart_collaterals'); ?>
        <div class="cart-collaterals cart-totals-layout">
            <?php do_action('woocommerce_cart_collaterals'); ?>
        </div>
        <?php do_action('woocommerce_after_cart_collaterals'); ?>
    </div>
</section>
<?php do_action('woocommerce_after_cart'); ?>