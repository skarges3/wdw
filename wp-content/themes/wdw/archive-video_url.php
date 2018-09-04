<?php
add_thickbox();
get_header();

get_template_part('page', 'header-video');
?>
    <div id="page-intro">
        <h3>Videos</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div id="primary" class="videos grid-container">
        <?php
        $count = 0;
        while (have_posts()) {
            the_post();
            $video_id = get_the_ID();
            $video = get_post_meta($video_id, '_video', true);
            if ($count % 4 == 0) {
                echo '<div class="grid-container hide-on-mobile"></div>';
            }
            if ($count % 2 == 0) {
                echo '<div class="grid-container hide-on-desktop hide-on-tablet"></div>';
            }
            ?>
            <div class="grid-25 tablet-grid-25 mobile-grid-50">
                <a href="<?php echo $video['url'] ?>" class="play-video recent-video"><?php
                    $tn_id = get_post_thumbnail_id($video_id);
                    if (empty($tn_id)) {
                        if (empty($video['thumbnail'])) {
                            ?><img src="http://placehold.it/269x177"><?php
                        } else {
                            ?><img src="<?php echo $video['thumbnail'] ?>"/><?php
                        }
                    } else {
                        echo wp_get_attachment_image($tn_id, 'full');
                    }
                    ?></a>

                <div class="format-default">
                    <div class="post-title">
                        <?php echo the_title(); ?>
                    </div>
                </div>
            </div>
            <?php
            $count++;
        }
        if ($count > 0){
            echo '<div class="grid-container"></div>';
        }
        ?>
        <?php the_posts_pagination(array(
            'prev_text' => __('<', 'wdw'),
            'next_text' => __('>', 'wdw'),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
        )); ?>
    </div>
<?php
get_footer();