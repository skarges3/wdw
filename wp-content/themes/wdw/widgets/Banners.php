<?php


if (class_exists('ipso_Widget_Base')) {
    class ipso_Widget_Banners extends ipso_Widget_Base
    {
        function __construct()
        {
            $widget_ops = array('classname' => 'widget_Banners', 'description' => __('Display a set of auto-rotating banners'));
            $control_ops = array('width' => 400, 'height' => 350);
            parent::__construct('Banners', __('Banners'), $widget_ops, $control_ops);
        }

        function widget($args, $instance)
        {
            $this->before_widget($args);
            self::banner_content($instance);
            $this->after_widget($args);
        }

        static function banner_content($instance)
        {
            $instance = array_merge(array(
                'class_name' => '',
                'banner_set' => '',
                'delay' => '',
                'show_triggers' => '',
                'height' => '',
                'mobile_height' => ''
            ), $instance);

            $banners = new WP_Query(array(
                'post_type' => 'banner',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'banner_set',
                        'terms' => $instance['banner_set'],
                        'field' => 'term_id'
                    )
                )
            ));

            if ($banners->have_posts()) {
                $banner_ids = array();
                ?>
            <div class="banners<?php echo empty($instance['class_name']) ? '' : " {$instance['class_name']}" ?>"
                 data-delay="<?php echo $instance['delay'] ?>">
                <ul class="items">
                    <?php
                    $first = true;
                    while ($banners->have_posts()) {
                        $banners->the_post();
                        global $post;
                        $banner_ids[] = get_the_ID();
                        ?>
                    <li class="banner<?php echo $first ? ' active' : '' ?>" data-banner-id="<?php the_ID() ?>">
                        <div
                            class="desktop-banner" <?php self::background_image(array('_thumbnail_id'), $instance['height']) ?>>
                            <div class="banner-content">
                                <?php the_content() ?>
                            </div>
                        </div>
                        <div
                            class="mobile-banner" <?php self::background_image(array('_mobile_thumbnail_id', '_thumbnail_id'), $instance['mobile_height']) ?>>
                            <div class="banner-content" <?php
                            $text_color = get_post_meta(get_the_ID(), '_mobile_banner_color', true);
                            if (!empty($text_color)){
                            ?>style="color:<?php echo $text_color ?>;"<?php
                            } ?>>
                                <?php
                                if (empty($post->post_excerpt)) {
                                    the_content();
                                } else {
                                    echo apply_filters('the_content', $post->post_excerpt);
                                }
                                ?>
                            </div>
                        </div>
                        </li><?php
                        $first = false;
                    } ?>
                </ul>
                <?php if (!empty($instance['show_triggers']) && count($banner_ids) > 1) {
                    ?>
                    <ul class="triggers">
                        <?php foreach ($banner_ids as $index => $banner_id) { ?>
                            <li><a class="trigger<?php echo $index == 0 ? ' active' : '' ?>"
                                   href="#"
                                   data-banner-id="<?php echo $banner_id ?>"><?php echo $index + 1 ?></a></li>
                            <?php
                        } ?>
                    </ul>

                    <?php
                } ?>
                </div><?php

                wp_reset_postdata();
            }
        }

        static function background_image($fields, $height)
        {
            $bg = '';
            foreach ($fields as $field) {
                $img_id = get_post_meta(get_the_ID(), $field, true);
                if ($img_id) {
                    $url = wp_get_attachment_url($img_id);
                    if (!empty($url)) {
                        $bg = "background-image:url({$url});";
                        break;
                    }
                }
            }
            echo " style='{$bg}height:{$height}px'";
        }


        function form($instance)
        {
            $banner_terms = get_terms('banner_set', array('hide_empty' => false));
            $banner_sets = array();
            foreach ($banner_terms as $term) {
                $banner_sets[$term->term_id] = $term->name;
            }

            $this->labeledSelect($instance, 'banner_set', 'Banner Set', $banner_sets);
            $this->labeledNumber($instance, 'delay', 'Delay');
            $this->labeledNumber($instance, 'height', 'Height');
            $this->labeledNumber($instance, 'mobile_height', 'Mobile Height');
            $this->checkBoxInput($instance, 'show_triggers', 'Show Trigger');
            $this->labeledInput($instance, 'class_name', 'Class Name');
        }

        function update($new_instance, $old_instance)
        {
            $instance = $this->updateFields(array('delay', 'banner_set', 'height', 'mobile_height', 'class_name'), $new_instance);
            if (!empty($new_instance['show_triggers'])) {
                $instance['show_triggers'] = 1;
            }
            return $instance;
        }

        static function register()
        {
            register_widget('ipso_Widget_Banners');
        }

        static function ipso_init()
        {
            $banners = new CustomPostType('banner', array(
                'name' => 'Banners',
                'singular_name' => 'Banner',
                'menu_name' => 'Banners',
                'all_items' => 'All Banner',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Banner',
                'edit_item' => 'Edit Banner',
                'new_item' => 'New Banner',
                'view_item' => 'View Banner',
                'search_items' => 'Search banners',
                'not_found' => 'No banners found',
                'not_found_in_trash' => 'No banners found',
                'parent_item_colon' => ''),
                array(
                    'menu_icon' => 'dashicons-slides',
                    'publicly_queryable' => false,
                    'exclude_from_search' => true
                )
            );
            $banners->addThumbnailSupport();
            $banners->addExcerptSupport();
            $banners->addSection('side', 'Mobile Banner', 'side');
            $banners->addFieldToSection('side', '_mobile_thumbnail_id', 'media', '', 'Image');
            $banners->addFieldToSection('side', '_mobile_banner_color', 'color', '', 'Text Color');

            $banners->addTaxonomy('banner_set', array(
                'labels' =>
                    array('name' => 'Sets',
                        'singular_name' => 'Set',
                        'menu_name' => 'Sets',
                        'all_items' => 'All Sets',
                        'edit_item' => 'Edit Set',
                        'view_item' => 'View Set',
                        'update_item' => 'Update Set',
                        'add_new_item' => 'Add New Set',
                        'new_item_name' => 'New Set',
                        'parent_item' => 'Parent Set',
                        'parent_item_colon' => 'Parent Set:',
                        'search_items' => 'Search Sets',
                        'popular_items' => 'Popular sets',
                        'separate_items_with_commas' => 'Separate sets with commas',
                        'add_or_remove_items' => 'Add/Remove Sets',
                        'choose_from_most_used' => 'Choose from most used sets',
                        'not_found' => 'Set not found'
                    )
            ));
        }
    }

    add_action('ipso_init', array('ipso_Widget_Banners', 'ipso_init'));
    add_action('widgets_init', array('ipso_Widget_Banners', 'register'));
}