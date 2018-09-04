<?php

get_header();

get_template_part('page', 'header-blog');
?>
    <div id="page-intro">
        <h3>Blog Post</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
<?php the_post() ?>

    <div class="grid-container">
        <section class="featured-image grid-100 tablet-grid-100 mobile-grid-100">
            <?php the_post_thumbnail('full') ?>
            <?php previous_post_link("<span class='prev'>%link</span>", '<') ?>
            <?php next_post_link("<span class='next'>%link</span>", '>') ?>
        </section>
        <article class="grid-75 tablet-grid-75 mobile-grid-100">
            <header class="article-intro">
                <nav class="category-links">
                    <?php the_category('&nbsp;&nbsp;') ?>
                </nav>
                <h1><?php the_title() ?><?php echo get_post_meta(get_the_ID(), 'sub_title', true) ?></h1>

                <div class="article-meta">
                    <span class="fa fa-user"><?php the_author_link() ?></span>
                    <span class="fa fa-calendar-o"><?php the_date('j F') ?></span>
                    <?php if (comments_open()) {
                        $c = get_comment_count()['approved'];
                        ?>
                        <span class="fa fa-eye"><?php echo $c ?> comment<?php echo $c == 1 ? '' : 's' ?></span>
                    <?php } ?>
                    <span class="share">
                        Share With <?php do_action('social_shares'); ?>
                    </span>
                </div>
            </header>
            <section id="primary">
                <?php the_content() ?>
            </section>
            <?php
            $exclude = get_the_author_meta('wpseo_excludeauthorsitemap');
            if (!$exclude) {
                ?>
                <footer>
                    <div class="grid-container">
                        <?php echo get_avatar(get_the_author_meta('ID')) ?>
                        <div class="author-info">
                            <h3>About Author</h3>

                            <div class="author-name">
                                <?php echo get_the_author_meta('display_name') ?>
                            </div>
                            <div class="author-bio"><?php
                                echo apply_filters('the_content', get_the_author_meta('description'));
                                ?></div>
                            <a class="author-link"
                               href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">MORE
                                POSTS BY: <?php echo get_the_author_meta('display_name') ?></a>
                        </div>
                    </div>
                </footer>
            <?php } ?>
            <nav class="post-nav">
                <?php
                $prev_link = get_previous_post_link("%link", "&lt; Previous");
                $next_link = get_next_post_link("%link", "Next &gt;");

                echo $prev_link;
                if (!empty($prev_link) && !empty($next_link)) {
                    echo '|';
                }
                echo $next_link;

                ?>
            </nav>
        </article>
        <div id="sidebar" class="grid-25 tablet-grid-25 mobile-grid-100">
            <?php dynamic_sidebar('sidebar-1') ?>
        </div>
    </div>
<?php
get_footer();