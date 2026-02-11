<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.6.0
 */

if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="orderdropdown">
    <form class="woocommerce-ordering d-flex align-items-center gap-3 position-relative" method="get">
        <span class="content_order">Rikiuoti pagal:</span>

        <select name="orderby" class="orderby">

            <?php
            $custom_labels = array(
                'menu_order' => 'Standartinis rikiavimas',
                'popularity' => 'Populiariausi viršuje',
                'rating' => 'Geriausiai įvertinti viršuje',
                'date' => 'Naujausi pirmiau',
                'price' => 'Pagal kainą: pigiausi viršuje',
                'price-desc' => 'Pagal kainą: brangiausi viršuje',
            );

            foreach ($catalog_orderby_options as $id => $name):

                $label = isset($custom_labels[$id]) ? $custom_labels[$id] : $name;
                ?>

                <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>>
                    <?php echo esc_html($label); ?>
                </option>

            <?php endforeach; ?>

        </select>

        <input type="hidden" name="paged" value="1" />
        <?php wc_query_string_form_fields(null, array('orderby', 'submit', 'paged', 'product-page')); ?>

    </form>
</div>