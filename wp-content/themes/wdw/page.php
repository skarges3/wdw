<?php

get_header();

the_post();
do_action('single_page_header', get_the_ID());
?>
    <div id="page-intro">
        <h3><?php the_title() ?></h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <article class="grid-100 tablet-grid-100 mobile-grid-100">
            <section id="primary">
                <?php the_content() ?>
            </section>
            </nav>
        </article>
    </div>
<?php
get_footer();