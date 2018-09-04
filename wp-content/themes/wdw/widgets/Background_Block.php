<?php


if (class_exists('ipso_Widget_Base')) {
    class ipso_Widget_Background_Block extends ipso_Widget_Base
    {
        function __construct()
        {
            $widget_ops = array('classname' => 'widget_background_block', 'description' => __('Displays introductory content over a background image'));
            $control_ops = array('width' => 400, 'height' => 350);
            parent::__construct('Background_Block', __('Block'), $widget_ops, $control_ops);
        }

        function widget($args, $instance)
        {
            $img = wp_get_attachment_url($instance['background']);
            $this->before_widget($args);
            ?>
            <div class="block-wrapper" style="background-image:url(<?php echo $img ?>)"><div class="block"><?php
            $this->widget_title($args, $instance);
            ?>
            <div class="content"><?php echo $instance['text'] ?></div><?php
            ?><a class="button" href="<?php echo $instance['button_url'] ?>"><?php echo $instance['button_text'] ?></a><?php
            ?></div></div><?php
            $this->after_widget($args);
        }

        function form($instance)
        {
            $this->labeledInput($instance, 'title', 'Title');
            $this->labeledInput($instance, 'text', 'Text');
            $this->labeledInput($instance, 'button_text', 'Button Text');
            $this->labeledInput($instance, 'button_url', 'Button URL');
            $this->labeledMedia($instance, 'background', 'Background');
        }

        function update($new_instance, $old_instance)
        {
            return $this->updateFields(array('title', 'text', 'button_text', 'button_url', 'background'), $new_instance);
        }

        static function register()
        {
            register_widget('ipso_Widget_Background_Block');
        }
    }

    add_action('widgets_init', array('ipso_Widget_Background_Block', 'register'));
}