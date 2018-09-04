<?php


if (class_exists('ipso_Widget_Base')) {
    class ipso_Widget_Featured_Post extends ipso_Widget_Base
    {
        function __construct()
        {
            $widget_ops = array('classname' => 'widget_featured_post', 'description' => __('Displays an intro to the most recent post in a category or a specific post'));
            $control_ops = array('width' => 400, 'height' => 350);
            parent::__construct('Featured_Post', __('Featured/Most Recent Post'), $widget_ops, $control_ops);
        }

        function widget($args, $instance)
        {
            $query_args = array(
                'post_type' => 'post',
                'posts_per_page' => 1,
                'orderby' => 'post_date',
                'order', 'DESC'
            );

            if (!empty($instance['post_id'])) {
                $query_args['post__in'] = array($instance['post_id']);
            } else {
                if (!empty($instance['category_id']) || !empty($instance['post_tag_id'])) {
                    $query_args['tax_query'] = array(
                        array(
                            'taxonomy' => !empty($instance['category_id']) ? 'category' : 'post_tag',
                            'terms' => !empty($instance['category_id']) ? array($instance['category_id']) : array($instance['post_tag_id']),
                            'field' => 'term_id'
                        )
                    );
                }
            }

            $query = new WP_Query($query_args);

            if ($query->have_posts()) {
                $query->the_post();
                global $post;
                $this->before_widget($args);

                ?>
                <div class="feature-block">
                <?php the_post_thumbnail('large') ?>
                <div class="bottom-inset">
                    <?php switch ($instance['format']) {
                        case 'full':
                            ?>
                            <div class="format-full">
                            <div class="post-date fa fa-calendar-o"><?php echo get_the_date('j F') ?></div><?php
                            ?>
                            <div class="post-title"><a href="<?php the_permalink() ?>"><?php echo $post->post_title ?></a></div><?php
                            $sub_title = get_post_meta(get_the_ID(), 'sub_title', true);
                            if (!empty($sub_title)) {
                                ?>
                                <div class="post-sub-title"><?php echo $sub_title ?></div><?php
                            }
                            ?><a href="<?php the_permalink() ?>" class="button">Read More</a></div><?php
                            break;
                        case 'default':
                        default:
                            ?>
                            <div class="format-default">
                            <div class="post-title"><?php the_title() ?></div><?php
                            ?><a href="<?php the_permalink() ?>">Read More</a></div><?php
                            break;
                    } ?>
                </div>
                <div class="category-info">
                    <a href="<?php echo (get_option('show_on_front') == 'page') ? get_permalink(get_option('page_for_posts')) : bloginfo('url'); ?>">Blog</a>
                </div>
                </div><?php

                $this->after_widget($args);
                wp_reset_postdata();
            }
        }

        function form($instance)
        {
            $categories = $this->get_taxonomy_options('category');
            $tags = $this->get_taxonomy_options('post_tag');
            $this->labeledSelect($instance, 'post_tag_id', 'Tag', $tags);
            $this->labeledSelect($instance, 'category_id', 'Category', $categories);
            $this->labeledInput($instance, 'post_id', 'Post ID');
            $this->labeledSelect($instance, 'format', 'Format', array(
                'default' => 'Default',
                'full' => 'Full'
            ));
        }

        function update($new_instance, $old_instance)
        {
            return $this->updateFields(array('category_id', 'post_tag_id', 'post_id', 'format'), $new_instance);
        }

        static function register()
        {
            register_widget('ipso_Widget_Featured_Post');
        }

        /**
         * @return array
         */
        private function get_taxonomy_options($taxonomy)
        {
            $terms = get_terms($taxonomy, array('hide_empty' => 0));
            $options = array();
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
            return $options;
        }
    }

    add_action('widgets_init', array('ipso_Widget_Featured_Post', 'register'));
}