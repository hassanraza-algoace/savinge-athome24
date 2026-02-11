<?php

function the_breadcrumb(): void
{

    $sep = ' > ';

    if (!is_front_page()) {

        echo '<div class="breadcrumbs">';
        echo '<a href="';
        echo get_option('home');
        echo '">';
        bloginfo('name');
        echo '</a>' . $sep;

        if (is_category() || is_single()) {
            the_category('title_li=');
        } elseif (is_archive() || is_single()) {
            if (is_day()) {
                printf(__('%s', 'savinge'), get_the_date());
            } elseif (is_month()) {
                printf(__('%s', 'savinge'), get_the_date(_x('F Y', 'monthly archives date format', 'savinge')));
            } elseif (is_year()) {
                printf(__('%s', 'savinge'), get_the_date(_x('Y', 'yearly archives date format', 'savinge')));
            } else {
                _e('Blog Archives', 'savinge');
            }
        }

        if (is_single()) {
            echo $sep;
            the_title();
        }

        if (is_page()) {
            echo the_title();
        }

        if (is_home()) {
            global $post;
            $page_for_posts_id = get_option('page_for_posts');
            if ($page_for_posts_id) {
                $post = get_post($page_for_posts_id);
                setup_postdata($post);
                the_title();
                rewind_posts();
            }
        }

        echo '</div>';
    }
}
