<?php

/**
 * Cart totals
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;
?>
<div
    class="cart_totals cart-totals-layout-inner cart-totals-summary <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">
    <?php do_action('woocommerce_before_cart_totals'); ?>

    <div class="">
        <div class="cart-totals-row cart-subtotal">
            <span class="cart-totals-label"><?php esc_html_e('Suma', 'woocommerce'); ?></span>
            <span class="cart-totals-value"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>
            <div class="cart-totals-row cart-shipping">
                <span class="cart-totals-label"><?php esc_html_e('Pristatymas', 'woocommerce'); ?></span>
                <!-- <span class="cart-totals-value"><?php wc_cart_totals_shipping_html(); ?></span> -->
                <span class="cart-totals-value">nuo 0 €</span>
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

        <!-- --- TAX / PVM --- -->
        <div class="cart-totals-row tax">
            <span class="cart-totals-label">PVM</span>
            <span class="cart-totals-value">
                <?php
                // WooCommerce ka function jo total tax dikhaata hai
                wc_cart_totals_taxes_total_html();
                ?>
            </span>
        </div>

        <div class="cart-totals-row order-total">
            <span class="cart-totals-label">Bendrai</span>
            <span class="cart-totals-value"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
    </div>

    <div class="wc-proceed-to-checkout">
        <?php do_action('woocommerce_proceed_to_checkout'); ?>
    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>
</div>