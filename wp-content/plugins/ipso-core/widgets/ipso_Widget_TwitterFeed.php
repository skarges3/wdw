<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 7/19/13
 * Time: 1:25 PM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_TwitterFeed extends ipso_Widget_Base
{
    public function __construct()
    {
        // widget actual processes
        WP_Widget::__construct('tweets_widget', 'Twitter Feed Widget', array('description' => __('Displays a twitter feed for a specified screen name', 'text_domain')));
    }

    public function widget($args, $instance)
    {
        // outputs the content of the widget
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        echo ipso_shortcode_tweets($instance);

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        // outputs the options form on admin
        $this->labeledInput($instance, 'title', 'Title');
        $this->labeledInput($instance, 'screen_name', 'Screen Name');
        $this->labeledInput($instance, 'user_id', 'User ID');
        $this->checkBoxInput($instance, 'show_images', 'Show User Icons');
        $this->labeledInput($instance, 'timestamp_format', 'Timestamp Format');
        $this->labeledNumber($instance, 'max_tweets', 'Maximum # of Tweets');
    }

    public function update($new_instance, $old_instance)
    {
        $new_instance = $this->updateFields(array('title', 'screen_name', 'user_id', 'show_images', 'timestamp_format', 'max_tweets'), $new_instance);
        if (empty($new_instance['show_images'])){
            unset($new_instance['show_images']);
        }
        return $new_instance;
    }
}

register_widget('ipso_Widget_TwitterFeed');
