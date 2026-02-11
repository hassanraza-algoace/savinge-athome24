<div class="sidebar">

    <?php if (isset($_GET['s'])) {?>
    <div class="mb-3">
        <p class="mt-3"><strong>Rasti produktai kategorijose:</strong></p>
        <?php $args = array(
    's' => $_GET['s'],
    'post_type' => array('post', 'page'),
    'post_status' => 'publish',
    'category_name' => 'music',
    'posts_per_page' => -1,
);

    $categories = array();
    $count = array();
    $custom_search = new WP_Query($args);
    $count['test'] = 0;

    if ($custom_search->have_posts()) {
        while ($custom_search->have_posts()): $custom_search->the_post();?>
	        <!-- <h2 class="title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2> -->
	        <?php
    $terms = get_the_terms($post->ID, 'product_cat');
            // print_r($terms);

            foreach ($terms as $key => $term) {
                if (!in_array($term->term_id, $categories)) {
                    $categories[] = $term->term_id;
                    $count[$term->slug] = 1;
                } else {
                    $count[$term->slug] = $count[$term->slug] + 1;
                }
            }

            // print_r(get_the_terms(get_the_id()), 'product_cat');?>
	        <?php endwhile;

        ?>
    </div>
    <?php

    }
    echo '<ul>';
    foreach ($categories as $category) {
        $term = get_term($category, 'product_cat');
        $cat_info = get_the_category_by_ID($category);
        echo '<li class="mb-3"><a href="' . get_category_link($category) . '/' . '">' . $cat_info . ' (' . $count[$term->slug] . ')</a></li>';

    }
    echo '</ul>';
    echo '<hr>';
}
dynamic_sidebar('widget-area');?>
    <div class="d-none d-lg-block">
        <?php get_template_part('partials/category-list');?>
    </div>
</div>