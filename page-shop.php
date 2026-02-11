<?php
/*
Template Name: Shop Custom
*/

get_header('shop'); ?>
<div class="row shop_page max-width">
    <?php woocommerce_breadcrumb(); ?>
    <div>
        <?php woocommerce_content(); ?>
    </div>
</div>
<?php get_footer('shop'); ?>