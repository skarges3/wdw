<?php

get_header();

get_template_part('page', 'header-events');
?>
    <div id="page-intro">
        <h3>Events</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <div class="initiative-archive">
            <?php
            $now = time();
            $remaining = 2;
            while (have_posts()) {
                the_post();
                if ($remaining == 0) {
                    $dte_value = get_post_meta(get_the_ID(), '_date', true);
                    $dte = strtotime($dte_value);
                    if ($dte < $now){
                        break;
                    }
                }
                get_template_part('content', 'initiative-intro');
                $remaining--;
            } ?>
        </div>

        <?php the_posts_pagination(array(
            'prev_text' => __('<', 'wdw'),
            'next_text' => __('>', 'wdw'),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
        )); ?>
    </div>
<?php
get_footer();