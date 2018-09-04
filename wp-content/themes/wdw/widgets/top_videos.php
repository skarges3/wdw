<?php


if (class_exists('ipso_Widget_Base')) {
    class ipso_Widget_top_videos extends ipso_Widget_Base
    {
        function __construct()
        {
            $widget_ops = array('classname' => 'widget_top_videos', 'description' => __('Displays up to 5 of the most recent videos'));
            $control_ops = array('width' => 400, 'height' => 350);
            parent::__construct('top_videos', __('Top Videos'), $widget_ops, $control_ops);
        }

        function widget($args, $instance)
        {
            add_thickbox();
            $videos = get_posts(array(
                'posts_per_page' => 5,
                'post_type' => 'video_url'
            ));

            $this->before_widget($args);
            ?>
            <div class="videos">
            <?php $this->widget_title($args, $instance); ?>
            <div class="video-wrapper">
                <div class="current-container grid-50 tablet-grid-100 mobile-grid-100">
                    <div class="current-video">
                        <?php
                        $video_id = $videos[0]->ID;
                        $video = get_post_meta($video_id, '_video', true);
                        ?>
                        <a href="<?php echo $video['url'] ?>" class="play-video"><?php
                            $tn_id = get_post_thumbnail_id($video_id);
                            if (empty($tn_id)) {
                                ?><img src="<?php echo $video['thumbnail'] ?>"/><?php
                            } else {
                                echo wp_get_attachment_image($tn_id, 'full');
                            }
                            ?></a>

                        <div class="bottom-inset">
                            <div class="format-default">
                                <div class="post-title">
                                    <?php echo $videos[0]->post_title ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="recent-container grid-50 tablet-grid-100 mobile-grid-100">
                    <?php for ($i = 1; $i < count($videos); $i++) {
                        $video_id = $videos[$i]->ID;
                        $video = get_post_meta($video_id, '_video', true);

                        ?>

                        <div class="grid-50 tablet-grid-50 mobile-grid-100">
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
                                    <?php echo $videos[$i]->post_title ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="grid-50 tablet-grid-100 mobile-grid-100 push-50">
                    <a class="link" href="<?php echo get_post_type_archive_link('video_url')?>">view all</a>
                </div>
            </div>
            </div><?php
            $this->after_widget($args);
        }

        function form($instance)
        {
            $this->labeledInput($instance, 'title', 'Title');
        }

        function update($new_instance, $old_instance)
        {
            return $this->updateFields(array('title'), $new_instance);
        }

        static function register()
        {
            register_widget('ipso_Widget_top_videos');
        }
    }

    add_action('widgets_init', array('ipso_Widget_top_videos', 'register'));
}
