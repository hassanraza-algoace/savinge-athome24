<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="section-padding">
    <div class="max-width">

        <?php
        get_template_part('partials/woo-breadcrumb');

        do_action('woocommerce_before_checkout_form', $checkout);

        // If checkout registration is disabled and not logged in, the user cannot checkout.
        if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
            echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to
        checkout.', 'woocommerce')));
            return;
        }

        ?>

        <div class="woocommerce-form-login-toggle">
            <p> <?php echo esc_html__('Turite paskyrą?', 'woocommerce') ?>
                <a class="text-decoration-underline" href="<?php echo get_permalink(wc_get_page_id('myaccount')); ?>">
                    <strong><?php echo esc_html__(' Prisijunkite ', 'woocommerce'); ?></strong>
                </a>
            </p>
        </div>
        <form name="checkout" method="post" class="checkout woocommerce-checkout"
            action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

            <?php if ($checkout->get_checkout_fields()): ?>

                <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                <div class="row justify-content-between"">
                    <div class=" col col-12 col-sm-7 left-col">
                    <?php do_action('woocommerce_checkout_billing'); ?>
                    <!-- <?php do_action('woocommerce_checkout_shipping'); ?> -->
                </div>
                <div class="col col-12 col-sm-4 right-col">
                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>

                <?php endif; ?>

                <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

                <h3 class="text-uppercase my-4 billing_headings" id="order_review_heading ">
                    <?php esc_html_e('užsakymas', 'woocommerce'); ?>
                </h3>

                <?php do_action('woocommerce_checkout_before_order_review'); ?>

                <div id="order_review" class="woocommerce-checkout-review-order">
                    <?php do_action('woocommerce_checkout_order_review'); ?>
                </div>

                <?php do_action('woocommerce_checkout_after_order_review'); ?>

            </div>
    </div>



    </form>
    <form class="checkout_coupon woocommerce-form-coupon" method="post">

        <p><?php esc_html_e('Turite nuolaidos kodą ar dovanų kuponą?', 'woocommerce'); ?></p>

        <p class="form-row form-row-first">
            <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>
            <input type="text" name="coupon_code" class="input-text"
                placeholder="<?php esc_attr_e('Nuolaidos kodas', 'woocommerce'); ?>" id="coupon_code" value="" />
        </p>
        <p class="form-row form-row-last">
            <button type="submit"
                class="btn-primary button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                name="apply_coupon"
                value="<?php esc_attr_e('Nuolaidos coupon', 'woocommerce'); ?>"><?php esc_html_e('Taikyti', 'woocommerce'); ?></button>
        </p>
        <div class="clear"></div>
    </form>
    <?php do_action('woocommerce_after_checkout_form', $checkout); ?>
    </div>
</section>