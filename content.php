<?php
/* Template name: Content */
get_header();
?>
<style>
.is-open>.c-accordion__title::after {
    content: url('<?php echo wp_get_attachment_image_src(115)[0]; ?>');
}

.c-accordion__title:after {
    content: url('<?php echo wp_get_attachment_image_src(115)[0]; ?>');
}
</style>

<section class="content-banner"
    style="background:url('<?php echo wp_get_attachment_image_src(109, array(854, 854))[0]; ?>')">
    <div class="row h-100 max-width">
        <div class="col col-12 d-flex align-items-center">
            <h1><span class="title-divider position-relative">| </span> <?php echo get_the_title(); ?></h1>
        </div>
    </div>
</section>


<section class="section-padding content-section">
    <div class="row max-width">
        <div class="col col-12">
            <?php the_content();?>
        </div>
    </div>
</section>


<?php

get_footer();