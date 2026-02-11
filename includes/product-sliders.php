<?php

function getProductCats(): array
{
    global $product;
    $terms = get_the_terms($product->get_id(), 'product_cat');

    if ($terms != null) {
        foreach ($terms as $term) {
            $product_cat_id = $term->term_id;
            break;
        }
    }
    $categories = get_the_terms($product->get_id(), 'product_cat');

    $top_level_ids = [];
    if ($categories != null) {

        foreach ($categories as $category) {
            if (!$category->parent) {
                $top_level_ids[] = $category->term_id;
                $top_level_slugs[] = $category->slug;
            }
        }
    }
    $second_levelids = [];
    if ($categories != null) {
        foreach ($categories as $category) {
            if (in_array($category->parent, $top_level_ids)) {
                $second_levelids[] = $category->term_id;
                $second_level_slugs[] = $category->slug;
            }
        }
    }
    if (!empty($second_levelids)) {
        $arg_ids = $second_levelids;
    } else {
        $arg_ids = $top_level_ids;
    }

    return $arg_ids;

}

function getSliderProducts($type, $product_id = null, $product_cat_id = null): void
{
    ?>
<div class="woocommerce quick-sale">
    <ul class="products columns-1">
        <?php
if ($type == 'popular') {
        $args = array(
            'posts_per_page' => 8,
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        );
    } elseif ($type == 'new') {
        $args = array(
            'posts_per_page' => 8,
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        );
    } elseif ($type == 'sale') {
        $args = array(
            'posts_per_page' => 8,
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'order' => 'DESC',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'numeric',
                ),
                array(
                    'key' => '_min_variation_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'numeric',
                ),
            ),
        );

    } elseif ($type == 'related') {

        if ($product_cat_id == null) {
            // $product_cat_id = '';
        }
        $args = array(
            'posts_per_page' => 8,
            'post_type' => 'product',
            'post__not_in' => array($product_id),
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'order' => 'ASC',
            'relation' => 'AND',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => $product_cat_id,
                ),
            ),
        );

    }

    $loop = new WP_Query($args);
    ?>
        <div #swiperRef="" class="swiper productSwiper <?php
if ($type == 'popular') {
        echo 'productPopularSwiper';
    } elseif ($type == 'new') {
        echo 'productNewSwiper';
    } elseif ($type == 'sale') {
        echo 'productSaleSwiper';
    } elseif ($type == 'related') {
        echo 'productRelatedSwiper';
    }
    ?>">
            <div class="swiper-wrapper">
                <?php
if ($loop->have_posts()) {
        while ($loop->have_posts()): $loop->the_post();?>
	                <div class="swiper-slide d-flex justify-content-center">
	                    <?php wc_get_template_part('content', 'product');?>
	                </div>
	                <?php endwhile;
    } else {
        // echo __('Produktų šioje kategorijoje nėra.');
    }
    wp_reset_postdata();
    ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </ul>
</div>
<?php
}