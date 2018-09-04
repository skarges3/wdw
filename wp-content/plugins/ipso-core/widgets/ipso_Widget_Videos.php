<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 8/26/13
 * Time: 1:00 PM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_Videos extends WP_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'widget_videos', 'description' => __('Video gallery'));
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct('videos', __('Videos'), $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        wp_enqueue_script('ipso_Widget_Videos');

        $before_widget = null;
        $after_widget = null;
        $before_title = null;
        $after_title = null;
        extract($args);
        $title = isset($instance['title']) ? $instance['title'] : '';
        $more_link = isset($instance['more_link_text']) ? $instance['more_link_text'] : 'View More';
        $include_description = !empty($instance['include_desc']);
        $query = new WP_Query(array('post_type' => 'ipso_video'));
        $mode = isset($instance['mode']) ? $instance['mode'] : 'random';
        if (!$query->have_posts()) {
            return;
        }
        echo $before_widget;

        if (!empty($title)) {
            echo $before_title;
            echo $title;
            echo $after_title;
        }?>
        <div class="videowidget">
            <div id="video-content" class="grid-container grid-parent">
                <div class="video-launch grid-75">
                </div>
                <div class="video-info grid-25">
                    <span class="details">
                    </span>

                    <div class="share">
                        <a data-base-href="https://www.facebook.com/sharer/sharer.php?u={URL}" class="facebook"><img
                                src="<?php echo get_template_directory_uri() ?>/images/share-facebook.png"/></a>
                        <a data-base-href="https://twitter.com/share?url={URL}" class="twitter"><img
                                src="<?php echo get_template_directory_uri() ?>/images/share-twitter.png"
                                border="0"/></a>
                        <a data-base-href="mailto:?subject=I have a video to share...&amp;body={IMG}<a href='{URL}'>Watch Now</a>"><img
                                src="<?php echo get_template_directory_uri() ?>/images/share-email.png"
                                border="0"/></a>
                    </div>
                </div>
            </div>
            <div id="video-slider" class="grid-parent">
                <div id="scroll-left">
                    <img src="<?php echo get_template_directory_uri() ?>/images/arrow-left.png"/>
                </div>
                <ul>
                    <?php
                    for ($i = 0; $i < 100; $i++):
                        if (!$query->have_posts()) {
                            break;
                        } else {
                            $query->the_post();
                        }
                        $link = get_permalink();
                        $submitter = get_post_meta(get_the_ID(), 'submitter', true);
                        $location = get_post_meta(get_the_ID(), 'location', true);
                        $video = get_post_meta(get_the_ID(), 'video', true);
                        if (!is_array($video)) {
                            continue;
                        }
                        $video_type = $video['type'];
                        $video_id = $video['id'];
                        $video_img = get_the_post_thumbnail(null, 'video');
                        if (empty($video_img)) {
                            $video_img = "<img src='" . esc_attr($video['thumbnail']) . "'/>";
                        }
                        switch ($video_type) {
                            case 'YT':
                                $video_url = "//youtube.com/embed/$video_id?autoplay=1";
                                break;
                            case 'V':
                                $video_url = "//player.vimeo.com/video/$video_id?autoplay=1";
                                break;
                        }
                        ?>
                        <li>
                            <a href="<?php the_permalink() ?>"
                               data-video-url="<?php echo esc_attr($video_url) ?>"
                               data-location="<?php echo esc_attr($location) ?>"
                               data-submitter="<?php echo esc_attr($submitter) ?>"
                               title="<?php the_title() ?>">
                                <?php echo $video_img ?>
                                <div class="play-overlay"></div>
                            </a>

                            <div class="clearfix"></div>
                        </li>
                    <?php endfor ?>
                </ul>
                <div id="scroll-right">
                    <img src="<?php echo get_template_directory_uri() ?>/images/arrow-right.png"/>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <script>
        </script>
        <?php
        wp_reset_postdata();
        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function form($instance)
    {
        $instance = wp_parse_args((array)$instance, array('title' => '', 'img_type' => 'thumbnail', 'include_desc' => ''));
        $title = strip_tags($instance['title']);
        $img_type = strip_tags($instance['img_type']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>

    <?php
    }

}

wp_register_script('ipso_Widget_Videos', get_template_directory_uri() . '/widgets/ipso_Widget_Videos.js', array('jquery'), '1.0');

