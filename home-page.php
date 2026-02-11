<?php
/**
 * Template Name: Home Page
 **/
get_header();
?>
<section class="main-section">
    <div class="row gap-0 gx-0">
        <div class="col col-12 col-sm-3 max-width-start d-none d-lg-block position-relative sidebar-categories-col">
            <div class="position-absolute scroll-icons d-flex">
                <img id='upClick' src="<?php echo wp_get_attachment_image_src(25529)[0]; ?>" alt="" width="30px">
                <img id='downClick' src="<?php echo wp_get_attachment_image_src(25529)[0]; ?>" alt="" width="30px">
            </div>
            <div class="sidebar-categories category-list pt-4">


                <?php

                $args = array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'parent' => 0,
                    'taxonomy' => "product_cat",
                );
                $args_level_3 = [];

                $parent_categories = get_categories($args);

                foreach ($parent_categories as $parent_cat) {
                    ?>

                        <?php
                        $args_level_2 = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'parent' => $parent_cat->term_id,
                            'taxonomy' => "product_cat",
                        );
                        $level_2_categories = get_categories($args_level_2);
                        foreach ($level_2_categories as $sub_category_level_2) {
                            ?>
                                <h2 class="py-4 text-uppercase d-flex justify-content-between align-items-center">
                                    <a
                                        href="<?php echo get_category_link($sub_category_level_2->term_id); ?>"><?php echo $sub_category_level_2->name; ?></a>
                                    <?php if ($level_2_categories) { ?>
                                            <img src="<?php echo wp_get_attachment_image_src(21)[0]; ?>" alt="" width="10px">
                                    <?php }
                                    ?>
                                    <input name="current_cat" type="hidden" value="<?php echo $sub_category_level_2->term_id; ?>">
                                </h2>

                                <?php

                        }
                }

                ?>
            </div>
        </div>
        <div class="col col-12 col-lg-9" style=" overflow: hidden; min-height: 600px;">


            <?php

            $taxonomy = 'product_cat';
            $orderby = 'name';
            $show_count = 0;
            $pad_counts = 0;
            $hierarchical = 1;
            $empty = 0;

            $args = array(
                'taxonomy' => $taxonomy,
                'orderby' => $orderby,
                'show_count' => $show_count,
                'pad_counts' => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li' => $title,
                'hide_empty' => $empty,
            );
            ?>
            <div class="row" style="position: relative; z-index: 1;">
                <div class="col col-12 d-flex justify-content-between align-items-center position-relative">

                    <?php
                    $all_categories = get_categories($args);
                    foreach ($all_categories as $cat) {
                        ?>
                            <div class="categories-content  position-absolute top-0 parent-<?php echo $cat->term_id; ?>"
                                style="background-color: white;">


                                <div class="row">
                                    <div class="col col-12">
                                        <?php

                                        $args2 = array(
                                            'taxonomy' => $taxonomy,
                                            'child_of' => 0,
                                            'parent' => $cat->term_id,
                                            'orderby' => $orderby,
                                            'show_count' => $show_count,
                                            'pad_counts' => $pad_counts,
                                            'hierarchical' => $hierarchical,
                                            'title_li' => $title,
                                            'hide_empty' => $empty,
                                        );

                                        $sub_cats = get_categories($args2);
                                        echo '<div class="second-level">';
                                        foreach ($sub_cats as $sub_category) {
                                            echo '<div class="second-level-item"><a href="' . get_term_link($sub_category->term_id) . '">' . $sub_category->name . '</a>';

                                            $args3 = array(
                                                'taxonomy' => $taxonomy,
                                                'child_of' => 0,
                                                'parent' => $sub_category->term_id,
                                                'orderby' => $orderby,
                                                'show_count' => $show_count,
                                                'pad_counts' => $pad_counts,
                                                'hierarchical' => $hierarchical,
                                                'title_li' => $title,
                                                'hide_empty' => $empty,
                                            );
                                            $sub_cats_2 = get_categories($args3);
                                            if ($sub_cats_2 != null) {
                                                echo '<div class="third-level">';
                                                foreach ($sub_cats_2 as $sub_category_2) {
                                                    echo '<div><a href="' . get_term_link($sub_category_2->term_id) . '">' . $sub_category_2->name . '</a></div>';
                                                }
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                    }

                    ?>
                </div>
            </div>


            <!-- <?php if (have_rows('slider_section', 5)): ?>
            <input id="slider-speed" name="slider_speed" type="hidden" value="<?php echo get_field('slider_speed'); ?>">
            <div class="swiper main-swiper">
                <div class="swiper-wrapper">
                    <?php while (have_rows('slider_section', 5)):
                        the_row(); ?>
                    <div class="swiper-slide" style="background:url('<?php echo get_sub_field('image'); ?>')">
                        <div class="row 0 gx-0 w-100 justify-content-end align-items-center">
                            <div class="col col-12 col-sm-8 d-flex justify-content-end">
                                <div class="box-item m-4 m-sm-0 p-5">
                                    <p class="subheading"><?php echo get_sub_field('subheading'); ?></p>
                                    <h1 class="my-3"><?php echo get_sub_field('heading'); ?></h1>
                                    <p><?php echo get_sub_field('turinys'); ?></p>
                                    <a class="mt-4 btn btn-primary rounded-pill"
                                        href="<?php echo get_sub_field('button_link'); ?>"><?php echo get_sub_field('button'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <?php endif; ?> -->


            <!-- Hassan's code for the bannar -->
            <?php
            /**
             * Template Name: ACF Product Layout
             * Description: Custom layout with ACF placeholders and WooCommerce product
             */

            // get_header();
            
            // Get ACF field for product SKU
            $product_sku = get_field('product_sku');
            $product = null;

            if ($product_sku) {
                $product_id = wc_get_product_id_by_sku($product_sku);
                if ($product_id) {
                    $product = wc_get_product($product_id);
                }
            }
            ?>

            <div class="acf-product-container" style="position: relative; z-index: 0;">
                <div class="acf-product-layout">

                    <!-- Left Section - ACF Placeholders -->
                    <div class="acf-left-section">
                        <div class="acf-placeholder large">
                            <?php
                            $image_1 = get_field('image_section_1');
                            if ($image_1):
                                $image_url = is_array($image_1) ? $image_1['url'] : $image_1;
                                $image_alt = is_array($image_1) ? $image_1['alt'] : 'Image 1';
                                ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
                            <?php else: ?>
                                    <span>ACF Image Section 1 - Large</span>
                            <?php endif; ?>
                        </div>
                        <div class="bottom-images-place">
                            <div class="acf-placeholder medium">
                                <?php
                                $image_2 = get_field('image_section_2');
                                if ($image_2):
                                    $image_url = is_array($image_2) ? $image_2['url'] : $image_2;
                                    $image_alt = is_array($image_2) ? $image_2['alt'] : 'Image 2';
                                    ?>
                                        <img src="<?php echo esc_url($image_url); ?>"
                                            alt="<?php echo esc_attr($image_alt); ?>" />
                                <?php else: ?>
                                        <span>ACF Image Section 2 - Medium</span>
                                <?php endif; ?>
                            </div>

                            <div class="acf-placeholder medium">
                                <?php
                                $image_3 = get_field('image_section_3');
                                if ($image_3):
                                    $image_url = is_array($image_3) ? $image_3['url'] : $image_3;
                                    $image_alt = is_array($image_3) ? $image_3['alt'] : 'Image 3';
                                    ?>
                                        <img src="<?php echo esc_url($image_url); ?>"
                                            alt="<?php echo esc_attr($image_alt); ?>" />
                                <?php else: ?>
                                        <span>ACF Image Section 3 - Small</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="bannar-bottom-bar mobile-bar">
                        <div class="container-bar">
                            <img src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector.svg"
                                alt="Nemokamas pristatymas nuo 35 EUR">
                            <p class="bar-content">Nemokamas pristatymas nuo 35 EUR</p>
                        </div>
                        <div class="container-bar">
                            <img src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/XMLID_770_.svg"
                                alt="Prekių grąžinimas per 14 dienų">
                            <p class="bar-content">Prekių grąžinimas per 14 dienų</p>
                        </div>
                        <div class="container-bar">
                            <img src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Frame.svg"
                                alt="Žemos kainos ir geros kokybės garantija">
                            <p class="bar-content">Žemos kainos ir geros kokybės garantija</p>
                        </div>
                    </div>
                    <!-- Right Section - WooCommerce Product -->
                    <div class="product-right-section">
                        <?php if ($product && $product->is_type('simple')): ?>

                                <?php if ($product->is_on_sale()): ?>
                                        <div class="product-badge">
                                            <?php
                                            $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                                            echo '-' . $percentage . '%';
                                            ?>
                                        </div>
                                <?php endif; ?>

                                <div class="add_to_wishlist product-wishlist">
                                    <?php
                                    echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product->get_id()) . '"]');
                                    ?>
                                </div>
                                <div class="product-image">
                                    <?php echo $product->get_image('medium'); ?>
                                    <p class="discount-text">Dienos pasiūlymas!</p>
                                    <p class="discount-text discount-number">-50%</p>
                                </div>

                                <div class="product-availability">
                                    Pasiūlymas baigs galioti po:
                                    <strong><?php echo get_field('offer_countdown') ? get_field('offer_countdown') : '23 VAL 99 MIN'; ?></strong>
                                </div>
                                <div class="product-content">
                                    <h2 class="product-title">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </h2>

                                    <div class="product-pricing">
                                        <span class="product-price">
                                            <?php echo $product->get_price_html(); ?>
                                        </span>
                                    </div>

                                    <form class="cart" action="<?php echo esc_url($product->add_to_cart_url()); ?>"
                                        method="post" enctype="multipart/form-data">
                                        <button type="submit" name="add-to-cart"
                                            value="<?php echo esc_attr($product->get_id()); ?>"
                                            class="product-add-cart button product_type_simple add_to_cart_button ajax_add_to_cart">
                                            Į Krepšelį
                                        </button>
                                    </form>
                                </div>
                        <?php else: ?>
                                <div class="product-not-found">
                                    <p>Product not found. Please enter a valid SKU in ACF field 'product_sku'.</p>
                                </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
<div class="bannar-bottom-bar desktop-bar">
    <div class="container-bar">
        <img src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector.svg"
            alt="Nemokamas pristatymas nuo 35 EUR">
        <p class="bar-content">Nemokamas pristatymas nuo 35 EUR</p>
    </div>
    <div class="container-bar">
        <img src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/XMLID_770_.svg"
            alt="Prekių grąžinimas per 14 dienų">
        <p class="bar-content">Prekių grąžinimas per 14 dienų</p>
    </div>
    <div class="container-bar">
        <img src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Frame.svg"
            alt="Žemos kainos ir geros kokybės garantija">
        <p class="bar-content">Žemos kainos ir geros kokybės garantija</p>
    </div>
</div>
<section class="scrolling-bar">
    <div class="scroll-bar-container scrolling-bar-col-one">
        <h2>IŠPARDAVIMAS</h2>
        <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto" width="500"
            height="500">
        <a href="#">Naršyti išpardavimą <span><img
                    src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                    alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
    </div>
    <div class="scroll-bar-container scrolling-bar-col-two">
        <h2>SODO PREKĖS</h2>
        <div class="main-scrolling-inner-container">
            <div class="scrolling-bar-col-two-inner">
                <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                    width="500" height="500">
                <h5>Sodo technika ir priedai</h5>
            </div>
            <div class="scroll-bar-container-inner">
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Vazonai ir...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Sodas ir t...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Kastuvai...</h5>
                </div>
            </div>
        </div>
        <a href="#">Naršyti viską <span><img
                    src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                    alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
    </div>
    <div class="scroll-bar-container scrolling-bar-col-three">
        <h2>SODO PREKĖS</h2>

        <div class="scrolling-bar-col-three-inner">
            <div class="scroll-bar-container-inner">
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Vazonai ir...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Sodas ir t...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Kastuvai...</h5>
                </div>
            </div>
            <div class="scroll-bar-container-inner">
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Vazonai ir...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Sodas ir t...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Kastuvai...</h5>
                </div>
            </div>
        </div>
        <a href="#">Naršyti sodo prekes <span><img
                    src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                    alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
    </div>
    <div class="scroll-bar-container scrolling-bar-col-four">
        <h2>SODO PREKĖS</h2>
        <div class="scrolling-bar-col-four-inner">
            <div class="scroll-bar-container-inner">
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Vazonai ir...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Sodas ir t...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Kastuvai...</h5>
                </div>
            </div>
            <div class="scroll-bar-container-inner">
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Vazonai ir...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Sodas ir t...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Kastuvai...</h5>
                </div>
            </div>
        </div>
        <a href="#">Naršyti produktus sodui ir daržui <span><img
                    src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                    alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
    </div>
    <div class="scroll-bar-container scrolling-bar-col-five">
        <h2>SODO PREKĖS</h2>
        <div class="scrolling-bar-col-five">
            <div class="scroll-bar-container-inner">
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Vazonai ir...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Sodas ir t...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Kastuvai...</h5>
                </div>
            </div>
            <div class="scroll-bar-container-inner">
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Vazonai ir...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Sodas ir t...</h5>
                </div>
                <div class="inner-scroll">
                    <img src="https://athome24.lt/wp-content/uploads/2026/01/4863342_1.jpg" alt="SavingePrekiu_foto"
                        width="500" height="500">
                    <h5>Kastuvai...</h5>
                </div>
            </div>
        </div>
        <a href="#">Naršyti produktus sodui ir daržui <span><img
                    src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                    alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
    </div>
</section>

<section class="section-padding home_page_populer_product popular-products position-relative">
    <!-- <div class="d-block  mobile-nav">
        <?php get_template_part('partials/swiper-navigation') ?>
    </div> -->
    <div class="row max-width">
        <div
            class="col col-12 d-flex justify-content-center justify-content-sm-between align-items-center hassanAdjutsIconWidth">
            <div>
                <h2 class="sub-heading text-center text-sm-start"><?php _e('Pasiruoškite artėjančiam sezonui'); ?></h2>
                <h3 class="mb-2 h3-heading text-center text-sm-start"><?php _e('Pasiūlymai Jums'); ?></h3>
            </div>
            <div class=" d-sm-block mobile_none">
                <a href="#" style="font-size:16px ">Naršyti visus produktus <span><img
                            src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                            alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
            </div>
        </div>
    </div>
    <div class="row max-width">
        <div class="col col-12">
            <div class="mt-5 my-lg-5">
                <?php getSliderProducts('popular'); ?>
                <?php get_template_part('partials/swiper-navigation') ?>
                <div class=" d-sm-block desktop_none">
                    <a href="#" style="font-size:16px ">Naršyti visus produktus <span><img
                                src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$group = get_field('month_proposals');
$image1 = get_field('image_1');
$image2 = get_field('image_2');
$image3 = get_field('image_3');
$size = 'full';
?>

<section class="section-padding proposals-section best_deals_section">
    <div class="row max-width align-items-center inner_section">
        <div class="col-12 col-sm-5 position-relative best_deals_inner">
            <img class="best_deals_image"
                src="<?php echo wp_get_attachment_image_src($group['image_1'], array(854, 854))[0]; ?>" alt=""
                width="100" height="100">
            <img class="best_deals_image center_image_best_deals"
                src="<?php echo wp_get_attachment_image_src($group['image_2'], array(854, 854))[0]; ?>" alt=""
                width="100" height="100">
            <img class="best_deals_image"
                src="<?php echo wp_get_attachment_image_src($group['image_3'], array(854, 854))[0]; ?>" alt=""
                width="100" height="100">
        </div>
        <div
            class="col-12 col-sm-7 d-flex flex-column gap-2 justify-content-center justify-content-sm-start best_deals_content">
            <h2 class="mt-5 mt-lg-0 sub-heading text-center text-sm-start"><?php echo $group["heading"]; ?></h2>
            <!-- <p class="my-4 sale-text text-center text-sm-start"><?php echo $group["subheading"]; ?></p> -->
            <p class="sale-content"><?php echo $group["content"]; ?></p>
            <a class="best_deals_section_button"
                href="<?php echo $group["button_link"]; ?>"><?php echo $group["button"]; ?></a>
        </div>
    </div>
</section>


<section class="section-padding home_page_populer_product popular-products section position-relative">

    <div class="row max-width">
        <div
            class="col col-12 d-flex justify-content-center justify-content-sm-between align-items-center hassanAdjutsIconWidth">
            <div>
                <h2 class="sub-heading"><?php _e('Išpardavimas'); ?></h2>
                <p>Iki -50% nuolaida mėgstamiausioms prekėms</p>
            </div>
            <div class="d-block mobile-nav mobile_none">
                <a href="#">Naršyti išpardavimą <span><img
                            src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                            alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
            </div>
        </div>
    </div>
    <div class="row max-width">
        <div class="col col-12">
            <div class="mt-5 my-lg-5">
                <?php getSliderProducts('popular'); ?>
                <?php get_template_part('partials/swiper-navigation') ?>
                <div class="d-block mobile-nav desktop_none">
                    <a href="#">Naršyti išpardavimą <span><img
                                src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                </div>
            </div>

        </div>
    </div>
</section>


<section class="section-padding category-products">
    <div class="row max-width">
        <div class="col-12">
            <div class="hassanSwiperOneContentContainer">
                <div>
                    <h2 class="mb-2 sub-heading text-center text-sm-start">
                        <?php echo esc_html('Prekės su pažeistomis pakuotėmis'); ?>
                    </h2>
                    <h3 class="h3-heading text-center text-sm-start">
                        <?php echo esc_html('Produktai, kuriuos renkasi kiti'); ?>
                    </h3>
                </div>
                <div class="hassanAdjutsIconWidth d-block mobile-nav mobile_none">
                    <a href="#">Naršyti išpardavimą <span><img
                                src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                </div>
            </div>
            <div class="mt-5 my-lg-5">
                <div class="row hassanSwiperOneRow">
                    <!-- <?php
                    foreach (getProductMainCategories() as $cat) {
                        if ($cat->slug == 'numatyta') {
                            continue;
                        }
                        $cat_image = get_term_meta($cat->term_id, 'thumbnail_id', true);
                        ?>
                        <div class="col col-6 col-lg-3 mb-4">
                            <a class="text-center" href="<?php echo get_category_link($cat->term_id); ?>">
                                <img src="<?php echo wp_get_attachment_image_src($cat_image, array(854, 854))[0]; ?>" alt=""
                                    width="100%">
                                <p class="mt-4"><?php echo $cat->name; ?></p>
                            </a>
                        </div>
                        <?php
                    }
                    ?> -->
                    <?php
                    $product_ids = array(312264, 312265, 312263, 312285, 312289);

                    $args = array(
                        'post_type' => 'product',
                        'post__in' => $product_ids,
                        'posts_per_page' => -1,
                        'orderby' => 'post__in',
                    );

                    $products = get_posts($args);
                    ?>

                    <?php if (!empty($products)): ?>
                            <!-- Swiper -->
                            <div class="swiper hassanSwiperOne ">
                                <div class="swiper-wrapper hassanSwiperOneContainer">
                                    <?php foreach ($products as $post):
                                        $product = wc_get_product($post->ID);
                                        if (!$product)
                                            continue;

                                        $image_url = $product->get_image_id()
                                            ? wp_get_attachment_image_src($product->get_image_id(), [854, 854])[0]
                                            : 'https://via.placeholder.com/854';
                                        ?>
                                            <div class="swiper-slide text-center">
                                                <div class="hassan_swiper_container">
                                                    <div class="product" data-product-id="<?php echo $product->get_id(); ?>">
                                                        <div class="hassan_swiper_top_bar">
                                                            <p class="btn">Nauja</p>
                                                            <div>
                                                                <?php
                                                                echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product->get_id()) . '"]');
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                                        <img src="<?php echo esc_url($image_url); ?>"
                                                            alt="<?php echo esc_attr($product->get_name()); ?>" width="100%">
                                                        <h2 class="mt-2"><?php echo esc_html($product->get_name()); ?></h2>
                                                        <p><?php echo $product->get_price_html(); ?></p>
                                                        <button class="hassanSwiperOne_btn">Į krepšelį</button>
                                                    </a>
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Add Arrows -->
                                <div class="hassanSwiperOneArrows">
                                    <div class="swiper-button-prev">
                                        <svg id="hassan_swiper_one_prev_arrow" xmlns="https://www.w3.org/2000/svg" width="35"
                                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                                            aria-label="Previous slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                            <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <div class="swiper-button-next">
                                        <svg id="hassan_swiper_one_next_arrow" xmlns="https://www.w3.org/2000/svg" width="35"
                                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                                            aria-label="Next slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                            <path d="M11 11L24 17.5L11 24" stroke="#054C73" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Add Pagination -->
                                <div class="swiper-pagination"></div>
                            </div>
                    <?php else: ?>
                            <p>No products found!</p>
                    <?php endif; ?>
                    <div class="hassanAdjutsIconWidth d-block mobile-nav desktop_none">
                        <a href="#">Naršyti išpardavimą <span><img
                                    src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                    alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-padding brands brands_home_page_section">
    <div class="row max-width">
        <div class="col col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="sub-heading"><?php _e('Prekių ženklai, kuriuos rasite pas mus'); ?></h2>
            </div>
            <div class="d-block mobile-nav mobile_none">
                <a href="#">Visi straipsniai <span><img
                            src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                            alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
            </div>
        </div>
    </div>
    <div class="row max-width mt-5">
        <div class="col col-12">
            <div class="swiper brandSwiper">
                <div class="swiper-wrapper">
                    <?php if (have_rows('brands_section', 5)):
                        while (have_rows('brands_section', 5)):
                            the_row();
                            ?>
                                    <div class="swiper-slide justify-content-center">
                                        <a href="<?php echo get_sub_field('url'); ?>">
                                            <img src="<?php echo get_sub_field('image'); ?>" alt="" width="100%">
                                        </a>
                                    </div>
                            <?php endwhile;
                    endif; ?>
                </div>
                <div class="hassanSwiperOneArrows">
                    <div class="swiper-button-prev">
                        <svg id="product-brand-swiper-button-prev" xmlns="https://www.w3.org/2000/svg" width="35"
                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                            aria-label="Previous slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                            <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg id="product-brand-swiper-button-next" xmlns="https://www.w3.org/2000/svg" width="35"
                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                            aria-label="Next slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                            <path d="M11 11L24 17.5L11 24" stroke="#054C73" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
                <div class="d-block mobile-nav desktop_none">
                    <a href="#">Visi straipsniai <span><img
                                src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-padding category-products">
    <div class="row max-width">
        <div class="col-12">
            <div class="hassanSwiperOneContentContainer">
                <div>
                    <h2 class="mb-2 sub-heading text-center text-sm-start">
                        <?php echo esc_html('Naujienos'); ?>
                    </h2>
                    <h3 class="h3-heading text-center text-sm-start">
                        <?php echo esc_html('Naujos prekės - įsigykite pirmieji'); ?>
                    </h3>
                </div>
                <div class="hassanAdjutsIconWidth d-block mobile-nav mobile_none">
                    <a href="#">Naršyti visus produktus <span><img
                                src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                </div>
            </div>
            <div class="mt-5 my-lg-5">
                <div class="row hassanSwiperOneRow">
                    <!-- <?php
                    foreach (getProductMainCategories() as $cat) {
                        if ($cat->slug == 'numatyta') {
                            continue;
                        }
                        $cat_image = get_term_meta($cat->term_id, 'thumbnail_id', true);
                        ?>
                        <div class="col col-6 col-lg-3 mb-4">
                            <a class="text-center" href="<?php echo get_category_link($cat->term_id); ?>">
                                <img src="<?php echo wp_get_attachment_image_src($cat_image, array(854, 854))[0]; ?>" alt=""
                                    width="100%">
                                <p class="mt-4"><?php echo $cat->name; ?></p>
                            </a>
                        </div>
                        <?php
                    }
                    ?> -->
                    <?php
                    $product_ids = array(312264, 312265, 312263, 312285, 312289);

                    $args = array(
                        'post_type' => 'product',
                        'post__in' => $product_ids,
                        'posts_per_page' => -1,
                        'orderby' => 'post__in',
                    );

                    $products = get_posts($args);
                    ?>

                    <?php if (!empty($products)): ?>
                            <!-- Swiper -->
                            <div class="swiper hassanSwiperOne ">
                                <div class="swiper-wrapper hassanSwiperOneContainer">
                                    <?php foreach ($products as $post):
                                        $product = wc_get_product($post->ID);
                                        if (!$product)
                                            continue;

                                        $image_url = $product->get_image_id()
                                            ? wp_get_attachment_image_src($product->get_image_id(), [854, 854])[0]
                                            : 'https://via.placeholder.com/854';
                                        ?>
                                            <div class="swiper-slide text-center">
                                                <div class="hassan_swiper_container">
                                                    <div class="product" data-product-id="<?php echo $product->get_id(); ?>">
                                                        <div class="hassan_swiper_top_bar">
                                                            <p class="btn">Nauja</p>
                                                            <div>
                                                                <?php
                                                                echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product->get_id()) . '"]');
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                                        <img src="<?php echo esc_url($image_url); ?>"
                                                            alt="<?php echo esc_attr($product->get_name()); ?>" width="100%">
                                                        <h2 class="mt-2"><?php echo esc_html($product->get_name()); ?></h2>
                                                        <p><?php echo $product->get_price_html(); ?></p>
                                                        <button class="hassanSwiperOne_btn">Į krepšelį</button>
                                                    </a>
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Add Arrows -->
                                <div class="hassanSwiperOneArrows">
                                    <div class="swiper-button-prev">
                                        <svg id="hassan_swiper_one_prev_arrow" xmlns="https://www.w3.org/2000/svg" width="35"
                                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                                            aria-label="Previous slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                            <path d="M23.5 24L11.5 17.5L23.5 11" stroke="#054C73" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <div class="swiper-button-next">
                                        <svg id="hassan_swiper_one_next_arrow" xmlns="https://www.w3.org/2000/svg" width="35"
                                            height="35" viewBox="0 0 35 35" fill="none" tabIndex="0" role="button"
                                            aria-label="Next slide" aria-controls="swiper-wrapper-5ebd69b1b9e3a757">
                                            <rect width="35" height="35" rx="4" fill="#D2D2D2"></rect>
                                            <path d="M11 11L24 17.5L11 24" stroke="#054C73" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Add Pagination -->
                                <div class="swiper-pagination"></div>
                            </div>
                    <?php else: ?>
                            <p>No products found!</p>
                    <?php endif; ?>
                    <div class="hassanAdjutsIconWidth d-block mobile-nav desktop_none">
                        <a href="#">Naršyti visus produktus <span><img
                                    src="https://bisque-fly-315242.hostingersite.com/wp-content/uploads/2026/01/Vector-1.svg"
                                    alt="right-arrow-icon" width="5" class="srcolling-right-arrow-icon"></span></a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding newsletter-section"
    style="background: url('<?php echo wp_get_attachment_image_src(50, array(854, 854))[0]; ?>')">
    <div class="row py-5 max-width">
        <div class="col col-12 text-center">
            <p class="sub_heading"><?php echo esc_html('Prenumeruokite naujienlaiškį ir gaukite'); ?></p>
            <h2 class="my-3 "><?php echo esc_html('12 % nuolaidos kodą krepšeliui'); ?></h2>
            <!-- <p class="mb-5"><?php echo esc_html('12 % nuolaidos kodą krepšeliui'); ?></p> -->
            <?php echo do_shortcode('[contact-form-7 id="40" title="Newsletter"]'); ?>
        </div>
    </div>
</section>


<section class="section-padding news-section">
    <div class="row mt-2 mt-lg-5 max-width">
        <div class="col-12">
            <div
                class="d-flex justify-content-center justify-content-sm-between align-items-center blog_section_content">
                <h2 class="mb-2 sub-heading"><?php echo esc_html('Naudingi patarimai'); ?>
                </h2>
                <a class="d-flex gap-3 d-sm-block text-center text-sm-start mobile_none"
                    href="<?php echo get_post_type_archive_link('patarimai'); ?>">
                    <?php esc_html_e('Visi straipsniai', 'savinge'); ?>
                    <img src="<?php echo wp_get_attachment_image_src(19)[0]; ?>" alt="" width="10px">
                </a>
            </div>

            <div class="my-lg-5">
                <div class="row blogs_section_row">
                    <?php
                    $query = getPosts('patarimai');
                    $count_posts = 1;
                    if ($query->have_posts()):
                        while ($query->have_posts()):
                            $query->the_post(); ?>
                                    <div class="inner_container">
                                        <a class="text-center" href="<?php echo get_permalink(); ?>">
                                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" width="100%">
                                            <h2 class="mb-3 mt-3 text-start title"><?php echo get_the_title(); ?></h2>
                                            <p class="excerpt mb-3 text-start"><?php echo get_the_excerpt(); ?></p>
                                            <button class="hassanBlogSection_btn">Skaityti</button>
                                        </a>
                                    </div>
                                    <?php
                                    $count_posts++;
                        endwhile;
                    endif;
                    ?>
                </div>
                <a class="d-flex gap-3 d-sm-block text-center text-sm-start desktop_none"
                    href="<?php echo get_post_type_archive_link('patarimai'); ?>">
                    <?php esc_html_e('Visi straipsniai', 'savinge'); ?>
                    <img src="<?php echo wp_get_attachment_image_src(19)[0]; ?>" alt="" width="10px">
                </a>
            </div>
        </div>
    </div>
</section>


<!-- <section class="section-padding brands">
    <div class="row max-width">
        <div class="col col-12 d-flex justify-content-center justify-content-sm-between align-items-boottom">
            <div>
                <h2 class="sub-heading"><?php echo esc_html('Prekių ženklai, kuriuos rasite pas mus'); ?></h2>
            </div>
        </div>
    </div>
    <?php

    // $taxonomy = 'yith_product_brand';
// $orderby = 'name';
// $show_count = 0;
// $pad_counts = 0;
// $hierarchical = 1;
// $title = '';
// $empty = 0;
    
    // $args_brands = array(
//     'taxonomy' => $taxonomy,
//     'orderby' => $orderby,
//     'show_count' => $show_count,
//     'pad_counts' => $pad_counts,
//     'hierarchical' => $hierarchical,
//     'title_li' => $title,
//     'hide_empty' => $empty,
// );
// $all_brands = get_categories($args_brands);
    
    if (have_rows('brands_section', 5)):
        $count_brands = 0;
        $style = '';
        ?>
        <div class="row max-width mt-5">
            <?php while (have_rows('brands_section', 5)):
                the_row();
                if ($count_brands > 11) {
                    $style = 'style="display: none !important;"';
                }
                // $thumb_id = get_woocommerce_term_meta($brand->term_id, 'thumbnail_id', true);
                // $term_img = wp_get_attachment_url($thumb_id);
                ?>
                <div class="col col-6 col-lg-2 mb-5 d-flex align-items-center justify-content-center blogBox moreBox"
                    style="<?php echo esc_attr($style); ?>">
                    <div class=" m-3 h-100">
                        <a href="<?php echo get_sub_field('url'); ?>">
                            <img src="<?php echo get_sub_field('image'); ?>" alt="" width="100%">
                        </a>
                    </div>
                </div>
                <?php
                $count_brands++;
            endwhile; ?>
        </div>
    <?php endif; ?>
    <div class="row max-width">
        <div class="col col-12 d-flex justify-content-center">
            <p id="loadMore" class="btn d-flex align-items-center justify-content-center flex-column" href="#">
                <?php _e('Žiūrėti daugiau'); ?>
                <svg class="mt-3" width="18" height="10" viewBox="0 0 18 10" fill="none"
                    xmlns="https://www.w3.org/2000/svg">
                    <path d="M1 1.05615L9 9.05615L17 1.05615" stroke="#333333" stroke-width="1.25"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </p>
        </div>
    </div>
</section> -->



<?php

get_footer();