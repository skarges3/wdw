<?php

get_header();
$page = get_page_by_path('people');
do_action('single_page_header', $page->ID);
?>
    <div id="page-intro">
        <h3>Our Team</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <div id="primary">
            <?php
            $content = apply_filters('the_content', $page->post_content);
            echo $content;
            edit_post_link('Edit Content', '', '', $page->ID);
            ?>
        </div>
    </div>
    <div class="person-archive ">
        <?php
        /**
         * @param $meta
         * @param $icon
         * @param $label
         */
        function print_link($meta, $icon, $label)
        {

            $url = get_post_meta(get_the_ID(), $meta, true);
            if (!empty($url)) {
                if ($meta == '_email_address') {
                    $url = 'mailto:' . $url;
                }
                ?><a href="<?php echo $url ?>" target="_blank" rel="nofollow"
                     class="fa fa-<?php echo $icon ?>">
                <span><?php echo $label ?></span></a><?php
            }
        }

        while (have_posts()) {
            the_post();
            $section_id = strtolower(str_replace(' ', '-', get_the_title()));
            $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
            ?>
            <div class="person grid-25" id="<?php echo $section_id ?>" data-person-id="<?php the_ID() ?>">
                <div class="person-image"
                     style="background-image:url(<?php echo $img[0] ?>);">
                    <div class="intro-name"><?php the_title() ?></div>
                    <div class="person-intro">
                        <div class="content-wrapper">
                            <div class="name"><?php the_title() ?></div>
                            <div class="role"><?php echo get_post_meta(get_the_ID(), '_role', true) ?></div>
                            <a class="button" href="#<?php echo $section_id ?>">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="person-details">
                    <div class="grid-container">
                        <div class="grid-100 tablet-grid-100 mobile-grid-100">
                            <div class="name"><?php the_title() ?></div>
                            <div class="role"><?php echo get_post_meta(get_the_ID(), '_role', true) ?></div>
                            <div class="bio"><?php the_content() ?></div>
                            <div class="social">
                                <?php
                                $fields = array(
                                    'twitter' => 'Twitter',
                                    'facebook' => 'Facebook',
                                    '_email_address' => 'Email',
                                    'linkedin' => 'LinkedIn',
                                    'instagram' => 'Instagram',
                                    'pinterest' => 'Pinterest'
                                );

                                foreach ($fields as $field => $label) {
                                    if ($field == '_email_address') {
                                        print_link('_email_address', 'envelope-o', 'Email');
                                    } else {
                                        print_link("_{$field}_url", $field, $label);
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php
        } ?>
        <div class="grid-container">
            <?php the_posts_pagination(array(
                'prev_text' => __('<', 'wdw'),
                'next_text' => __('>', 'wdw'),
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
            )); ?></div>
    </div>
<?php
get_footer();