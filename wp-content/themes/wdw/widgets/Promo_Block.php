<?php


if (class_exists('ipso_Widget_Base')) {
    class ipso_Widget_Promo_Block extends ipso_Widget_Base
    {
        function __construct()
        {
            $widget_ops = array('classname' => 'widget_Promo_Block', 'description' => __(''));
            $control_ops = array('width' => 400, 'height' => 350);
            parent::__construct('Promo_Block', __('Promotional Block'), $widget_ops, $control_ops);
        }

        function widget($args, $instance)
        {
            $instance = shortcode_atts(array(
                'tag' => '',
                'title' => '',
                'sub-title' => '',
                'url' => '#',
                'background' => null,
                'format' => '',
                'tag-url'=>'',
                'cta_text'=>'Read More'
            ), $instance);

            $this->before_widget($args);

            ?>
            <div class="feature-block">
            <?php echo wp_get_attachment_image($instance['background']) ?>
            <div class="bottom-inset">
                <?php switch ($instance['format']) {
                    case 'full':
                        ?>
                        <div class="format-full">
                        <div class="post-title"><?php echo $instance['title']; ?></div><?php
                        $sub_title = $instance['sub-title'];
                        if (!empty($sub_title)) {
                            ?>
                            <div class="post-sub-title"><?php echo $sub_title ?></div><?php
                        }
                        ?><a href="<?php echo $instance['url'] ?>" class="button"><?php echo $instance['cta_text']?></a></div><?php
                        break;
                    case 'default':
                    default:
                        ?>
                        <div class="format-default">
                        <div class="post-title"><?php echo $instance['title'] ?></div><?php
                        ?><a href="<?php echo $instance['url'] ?>"><?php echo $instance['cta_text']?></a></div><?php
                        break;
                } ?>
            </div>
            <div class="category-info">
                <a href="<?php echo $instance['tag-url']?>"><?php echo $instance['tag'] ?></a>
            </div>
            </div><?php

            $this->after_widget($args);
        }

        function form($instance)
        {
            $this->labeledInput($instance, 'tag', 'Tag');
            $this->labeledInput($instance, 'tag-url', 'Tag Link');
            $this->labeledMedia($instance, 'background', 'Background');
            $this->labeledInput($instance, 'title', 'Title');
            $this->labeledInput($instance, 'sub-title', 'Sub Title (Full format only)');
            $this->labeledInput($instance, 'url', 'Link URL');
            $this->labeledInput($instance, 'cta_text', 'Link Text', 'Read More');
            $this->labeledSelect($instance, 'format', 'Format', array(
                'default' => 'Default',
                'full' => 'Full'
            ));
        }

        function update($new_instance, $old_instance)
        {
            return $this->updateFields(array('title', 'sub-title', 'tag', 'tag-url', 'background', 'url', 'cta_text', 'format'), $new_instance);
        }

        static function register()
        {
            register_widget('ipso_Widget_Promo_Block');
        }
    }

    add_action('widgets_init', array('ipso_Widget_Promo_Block', 'register'));
}