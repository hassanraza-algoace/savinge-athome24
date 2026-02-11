<?php
/* Breadcrumb for Cart and Checkout pages */
if (is_checkout()): ?>
    <div class="breadcrumbs my-4">
        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Pradžia', 'woocommerce'); ?></a> &gt;
        <a href="<?php echo wc_get_cart_url(); ?>"><?php esc_html_e('Krepšelis', 'woocommerce'); ?></a> &gt;
        <span><?php esc_html_e('Apmokėjimas', 'woocommerce'); ?></span>
    </div>
<?php elseif (is_cart()): ?>
    <div class="breadcrumbs my-4">
        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Pradžia', 'woocommerce'); ?></a> &gt;
        <span><?php esc_html_e('Krepšelis', 'woocommerce'); ?></span>
    </div>
<?php endif; ?>