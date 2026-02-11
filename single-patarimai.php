<?php
get_header();
?>
<section class="section-padding advice-section">
    <div class="row max-width">
        <div class="col col-12">
            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" width="100%">
            <h1 class="my-4"><?php echo get_the_title(); ?></h1>
            <div class="mt-5">
                <?php the_content();?>
            </div>
        </div>
    </div>
</section>


<?php get_footer();