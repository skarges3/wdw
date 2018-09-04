<?php

get_header();
get_template_part('page', 'header');
?>
    <div id="page-intro">
        <h3>Page Not Found</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <article class="grid-100 tablet-grid-100 mobile-grid-100">
            <section id="primary">
                <?php
                $not_found = get_page_by_path('page-not-found');
                if (!empty($not_found)) {
                    echo apply_filters('the_content', $not_found->post_content);
                }
                ?>
                <hr/>
                <div class="aligncenter">
                    <?php get_search_form() ?>
                </div>
            </section>
            </nav>
        </article>
    </div>
<?php
get_footer();