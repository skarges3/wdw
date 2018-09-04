<?php

include_once(__DIR__ . '/full_shortcode_base.php');



class PeopleShortcode extends ipso_Shortcode_Base
{
    function __construct()
    {
        parent::__construct('people');
        add_action('wp_ajax_get_people', array($this, 'wp_ajax_get_people'));
    }

    function print_shortcode($atts, $content)
    {
        $people = array();
        if (is_array($atts)) {
            foreach ($atts as $name => $value) {
                $matches = null;
                if ($value == 'true' && preg_match('/person_([0-9]+)/', $name, $matches)) {
                    $people[] = $matches[1];
                }
            }
        }
        $args = array(
            'post_type' => 'person',
            'posts_per_page' => '-1',
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
        if (!empty($people)) {
            $args['post__in'] = $people;

        }
        $query = new WP_Query($args);
        if (empty($atts['disable_scroll'])) {
            ?>
            <div class="person-list-wrapper"><div class="person-list"><?php
        } else {
            ?><div class="person-archive col-4"><?php
        }
//        $i = 0;
//        while($i<20) {
        while ($query->have_posts()) {
            $query->the_post();
            $section_id = strtolower(str_replace(' ', '-', get_the_title()));
//                $i++;
            $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium'); ?>

        <a class="person" href="/people/#<?php echo $section_id ?>">
            <div class="person-image"
                 style="background-image:url(<?php echo $img[0] ?>);">
                <?php if (false) { ?>
                    <div class="person-intro">
                        <div class="content-wrapper">
                            <div class="name"><?php the_title() ?></div>
                            <div class="role"><?php echo get_post_meta(get_the_ID(), '_role', true) ?></div>
                            <div class="bio"><?php the_excerpt() ?></div>
                            <div class="social">
                                <?php
                                $fb = get_post_meta(get_the_ID(), '_facebook_url', true);
                                if (!empty($fb)) {
                                    ?><a href="<?php echo $fb ?>" target="_blank" rel="nofollow" class="fa fa-facebook">
                                        <span>Facebook</span></a><?php
                                }
                                $tw = get_post_meta(get_the_ID(), '_twitter', true);
                                if (!empty($tw)) {
                                    ?><a href="https://www.twitter.com/<?php echo $tw ?>" target="_blank" rel="nofollow"
                                         class="fa fa-twitter"><span>Twitter</span></a><?php
                                }
                                ?>


                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            </a><?php
        }
//            $query->rewind_posts();
//        }
        if (empty($atts['disable_scroll'])) {
            ?></div></div><?php
        } else {
            ?></div><?php
        }
    }

    function wp_ajax_get_people()
    {
        $people = new WP_Query(array(
            'post_type' => 'person',
            'orderby' => 'post_title',
            'order' => 'ASC',
            'posts_per_page' => -1
        ));
        $data = array();
        while ($people->have_posts()) {
            $people->the_post();
            $data[] = array('id' => get_the_ID(), 'title' => get_the_title());
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }
}

new PeopleShortcode();


