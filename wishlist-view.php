<?php
/**
 * Wishlist page template - Standard Layout
 *
 * @author YITH
 * @package YITH\Wishlist\Templates\Wishlist\View
 * @version 3.0.0
 */
/**
 * Template variables:
 *
 * @var $wishlist                      \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items                array Array of items to show for current page
 * @var $wishlist_token                string Current wishlist token
 * @var $wishlist_id                   int Current wishlist id
 * @var $users_wishlists               array Array of current user wishlists
 * @var $pagination                    string yes/no
 * @var $per_page                      int Items per page
 * @var $current_page                  int Current page
 * @var $page_links                    array Array of page links
 * @var $is_user_owner                 bool Whether current user is wishlist owner
 * @var $show_price                    bool Whether to show price column
 * @var $show_dateadded                bool Whether to show item date of addition
 * @var $show_stock_status             bool Whether to show product stock status
 * @var $show_add_to_cart              bool Whether to show Add to Cart button
 * @var $show_remove_product           bool Whether to show Remove button
 * @var $show_price_variations         bool Whether to show price variation over time
 * @var $show_variation                bool Whether to show variation attributes when possible
 * @var $show_cb                       bool Whether to show checkbox column
 * @var $show_quantity                 bool Whether to show input quantity or not
 * @var $show_ask_estimate_button      bool Whether to show Ask an Estimate form
 * @var $show_last_column              bool Whether to show last column (calculated basing on previous flags)
 * @var $move_to_another_wishlist      bool Whether to show Move to another wishlist select
 * @var $move_to_another_wishlist_type string Whether to show a select or a popup for wishlist change
 * @var $additional_info               bool Whether to show Additional info textarea in Ask an estimate form
 * @var $price_excl_tax                bool Whether to show price excluding taxes
 * @var $enable_drag_n_drop            bool Whether to enable drag n drop feature
 * @var $repeat_remove_button          bool Whether to repeat remove button in last column
 * @var $available_multi_wishlist      bool Whether multi wishlist is enabled and available
 * @var $no_interactions               bool
 */

if (!defined('YITH_WCWL')) {
    exit;
} // Exit if accessed directly
?>
<!-- WISHLIST TABLE -->

<section class="wishlist">
    <div class="row">
        <?php
if ($wishlist && $wishlist->has_items()):
    foreach ($wishlist_items as $item):
        global $product;

        $product = $item->get_product();

        if ($product && $product->exists()):

        ?>
		        <div class="col col-6 col-sm-3">
		            <li
		                class="product type-product post-<?php echo $product->get_id(); ?> status-publish last instock product_cat-uncategorized has-post-thumbnail shipping-taxable purchasable product-type-simple">
		                <?php if ($show_remove_product): ?>
		                <?php
        getSale($product);
        display_new_loop_woocommerce();
        ?>
		                <div>
		                    <a href="<?php echo esc_url($item->get_remove_url()); ?>" class="remove remove_from_wishlist"
		                        title="<?php echo esc_html(apply_filters('yith_wcwl_remove_product_wishlist_message_title', __('Remove this product', 'yith-woocommerce-wishlist'))); ?>">&times;</a>
		                </div>
		                <?php endif;?>
	                <a href="<?php echo $product->get_permalink(); ?>"
	                    class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><?php echo $product->get_image(); ?>
	                </a>
	                <h2 class="woocommerce-loop-product__title"><?php echo $product->get_title(); ?></h2>
	                <?php
    if ($product->is_on_sale()) {
        ?>
	                <span class="price"><del aria-hidden="true"><span
	                            class="woocommerce-Price-amount amount"><bdi><?php echo $product->get_regular_price(); ?><span
	                                    class="woocommerce-Price-currencySymbol">€</span></bdi></span></del> <ins><span
	                            class="woocommerce-Price-amount amount"><bdi><?php echo $product->get_sale_price(); ?><span
	                                    class="woocommerce-Price-currencySymbol">€</span></bdi></span></ins></span>

	                <?php
    } else {
        ?>
	                <span class="price"><span
	                        class="woocommerce-Price-amount amount"><bdi><?php echo $product->get_regular_price(); ?><span
	                                class="woocommerce-Price-currencySymbol">€</span></bdi></span></span>

	                <?php }?>
	                <a href="?add-to-cart=<?php echo $product->get_id(); ?>" data-quantity="1"
	                    class="button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart"
	                    data-product_id="<?php echo $product->get_id(); ?>" data-product_sku=""
	                    aria-label="Add “Ąsotis melsvas 11x1 x22,5 cm FanniK 310123” to your cart" rel="nofollow">Į
	                    krepšelį</a>
	            </li>
	        </div>
	        <?php
endif;
endforeach;
endif;
?>
    </div>
</section>