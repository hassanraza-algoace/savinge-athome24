<?php
/**
 * Template Name: New products
 **/
get_header();

$date = date('Y-m-d', strtotime('today - 30 days'));

$year = date("Y", strtotime($date));

$month = date("m", strtotime($date));

$day = date("d", strtotime($date));
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'date_query' => array(
        'after' => array(
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ),
    ),
);
$loop = new WP_Query($args);
?>
<section class="section-padding">
    <div class="row max-width">
        <div class="col col-12 d-flex justify-content-between">
            <h2 class="sub-heading"><?php _e('Naujienos')?></h2>
        </div>
    </div>
</section>

<section class="woocommerce section-padding">
    <div class="max-width">
        <ul class="products columns-4">
            <?php
while ($loop->have_posts()): $loop->the_post();
    global $product;
    echo '<li>';
    wc_get_template_part('content', 'product');
    echo '</li>';
endwhile;
?>
        </ul>
    </div>
</section>
<?php
wp_reset_query();

?>






<?php
get_footer();