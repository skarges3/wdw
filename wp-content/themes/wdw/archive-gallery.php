<?php

get_header();

get_template_part('page', 'header-gallery');
?>
    <div id="page-intro">
        <h3>Gallery</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container gallery-archive">
        <?php
        $featured = new WP_Query(array(
            'posts_per_page' => 4,
            'meta_key' => '_featured',
            'meta_value' => '1',
            'post_type' => 'gallery'
        ));
        while ($featured->have_posts()) {
            $featured->the_post();
            ?>
            <figure class="featured-gallery grid-25 tablet-grid-25 mobile-grid-100">
            <div class="featured-image"
                 style="background-image:url(<?php echo wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail')[0] ?>);"></div>
            <figcaption>
                <h2><?php the_title() ?></h2>
                <a class="view-gallery-link" data-href="<?php the_permalink() ?>">View Photos</a>
            </figcaption>
            </figure><?php
        }
        wp_reset_postdata();
        ?>
        <?php while (have_posts()) {
            the_post(); ?>
            <hr>
            <article class="gallery-post grid-80 tablet-grid-100 mobile-grid-100 grid-parent push-10">
                <header class="grid-45 tablet-grid-50 mobile-grid-100">
                    <div class="gallery-meta">
                        <div class="gallery-date"><?php echo get_the_date('n.j.y') ?></div>
                        <?php
                        $categories = get_the_terms(get_the_ID(), 'gallery-category');
                        if (!empty($categories)) {
                            ?>
                            <div class="gallery-categories">
                                <?php
                                foreach ($categories as $term) {
                                    ?><a href="<?php echo get_term_link($term) ?>"><?php echo $term->name ?></a><?php
                                }
                                ?>
                            </div>
                            <?php
                        } ?>
                        <?php do_action('social_shares') ?>
                    </div>
                    <div class="gallery-image">
                        <?php the_post_thumbnail('thumbnail') ?>
                    </div>
                </header>
                <section class="gallery-info grid-55 tablet-grid-50 mobile-grid-100">
                    <?php
                    if (!empty($categories)) {
                        ?>
                        <div class="gallery-categories">
                            <?php
                            foreach ($categories as $term) {
                                ?><a href="<?php echo get_term_link($term) ?>"><?php echo $term->name ?></a><?php
                            }
                            ?>
                        </div>
                        <?php
                    } ?>
                    <h2><?php the_title() ?></h2>

                    <section id="primary">
                        <?php the_content() ?>
                    </section>
                    <footer>
                        <a class="view-gallery-link button button-outline-purple" data-href="<?php the_permalink() ?>">View
                            Photos</a>
                    </footer>
                </section>
            </article>
        <?php } ?>

        <?php the_posts_pagination(array(
            'prev_text' => __('<', 'wdw'),
            'next_text' => __('>', 'wdw'),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
        )); ?>
        <hr>
        <?php dynamic_sidebar('gallery-1') ?>
    </div>
<?php
get_footer();