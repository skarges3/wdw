<?php

get_header();

get_template_part('page', 'header-events');
?>
    <div id="page-intro">
        <h3>Event Details</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <div class="initiative-details">
            <?php while (have_posts()) {
                the_post();
                $dte_value = get_post_meta(get_the_ID(), '_date', true);
                ?>
                <div class="initiative">
                    <div class="initiative-image grid-60 tablet-grid-60 mobile-grid-100">
                        <?php the_post_thumbnail('large') ?>
                    </div>
                    <div class="initiative-info grid-40 tablet-grid-40 mobile-grid-100">
                        <h1><?php the_title() ?></h1>

                        <div class="price"><?php echo get_post_meta(get_the_ID(), '_price', true) ?></div>
                        <div class="initiative-info"><?php the_content() ?></div>


                        <dl>
                            <dt>City</dt>
                            <dd><?php echo get_post_meta(get_the_ID(), '_city', true) ?></dd>
                            <dt>Date</dt>
                            <dd><?php echo empty($dte_value) ? 'Date TBD' : date('F j, Y', strtotime($dte_value)) ?></dd>
                            <dt>Location</dt>
                            <dd><?php echo get_post_meta(get_the_ID(), '_location', true) ?></dd>
                        </dl>
                        <a href="<?php echo get_post_meta(get_the_ID(), '_register', true) ?>"
                           class="button">Register</a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php the_posts_pagination(array(
            'prev_text' => __('<', 'wdw'),
            'next_text' => __('>', 'wdw'),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
        )); ?>
    </div>
<?php
get_footer();