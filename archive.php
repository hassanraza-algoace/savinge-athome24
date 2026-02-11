<?php
/*
Template Name: Archives
 */
get_header();?>

<section class="section-padding archive">
    <div class="row max-width mb-5">
        <h1><?php echo single_cat_title(); ?></h1>
    </div>
    <div class="row max-width">
        <?php if (have_posts()):
    $count_posts = 1;
    while (have_posts()): the_post();?>
		        <div class="col col-12 col-sm-6 col-md-4">
		            <a class="text-center" href="<?php echo get_permalink(); ?>">
		                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" width="100%">
		                <p class="my-3 title"><strong><?php echo get_the_title(); ?></strong></p>
		                <p class="excerpt"><?php echo get_the_excerpt(); ?></p>
		            </a>
		        </div>
		        <?php
        $count_posts++;
    endwhile;?>
	        <?php else: ?>
        <?php endif;?>
        <?php the_posts_pagination(array('mid_size' => 1));?>

    </div>
</section>


<?php get_footer();?>