<?php

get_header();

get_template_part('page', 'header-blog');
?>
    <div id="page-intro">
        <h3><?php
            if (is_search()){
                echo "Search";
            }
            else{
                echo "Blog";
            }
            ?></h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container post-archive">
        <div class="grid-75 tablet-grid-75 mobile-grid-100">
            <?php while (have_posts()) {
                the_post(); ?>
                <article>
                    <div class="article-image">
                        <?php the_post_thumbnail('thumbnail') ?>
                    </div>
                    <?php $thumbnail_id = get_post_thumbnail_id()?>
                    <section class="article-intro<?php echo empty($thumbnail_id) ? ' full-width' : ''?>">
                        <nav class="category-links">
                            <?php the_category('&nbsp;&nbsp;') ?>
                        </nav>
                        <h1><a href="<?php the_permalink() ?>"><?php the_title() ?><?php echo get_post_meta(get_the_ID(), 'sub_title', true) ?></a></h1>

                        <section id="primary">
                            <?php the_excerpt() ?>
                        </section>
                        <div class="article-meta">
                            <span class="fa fa-user"><?php the_author_link() ?></span>
                            <span class="fa fa-calendar-o"><?php the_date('j F') ?></span>
                            <?php if (comments_open()) {
                                $c = get_comment_count()['approved'];
                                ?>
                                <span class="fa fa-eye"><?php echo $c ?> comment<?php echo $c == 1 ? '' : 's' ?></span>
                            <?php } ?>
                        </div>
                        <?php
                        global $post;
                        if ($post->post_type == 'person'){ ?>
                            <a href="<?php echo get_post_type_archive_link('person') ?>#<?php echo strtolower(str_replace(' ', '-', get_the_title()))?>" class="button">Read More</a>
                        <?php } else { ?><a href="<?php the_permalink() ?>" class="button">Read More</a><?php }?>

                    </section>
                </article>
            <?php } ?>

            <?php the_posts_pagination( array(
                'prev_text'          => __( '<', 'wdw' ),
                'next_text'          => __( '>', 'wdw' ),
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'wdw' ) . ' </span>',
            ) );?>
            <?php if (is_search()){
                get_search_form();
            }?>
        </div>
        <div id="sidebar" class="grid-25 tablet-grid-25 mobile-grid-100">
            <?php dynamic_sidebar('sidebar-1') ?>
        </div>
    </div>
<?php
get_footer();