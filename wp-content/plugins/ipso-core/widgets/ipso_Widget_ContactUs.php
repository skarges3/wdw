<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 7/19/13
 * Time: 1:25 PM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_ContactUs extends WP_Widget
{
    public function __construct()
    {
        // widget actual processes
        parent::__construct('contact_widget', 'Contact Info', array('description' => __('A Widget to show contact information', 'text_domain')));
    }

    function widget($args, $instance)
    {
        // outputs the content of the widget
        $title = apply_filters('widget_title', $instance['title']);

        $phone = isset($instance['phone']) ? $instance['phone'] : '';
        $email = isset($instance['email']) ? $instance['email'] : '';
        $address = isset($instance['address']) ? $instance['address'] : '';
        $address2 = isset($instance['address2']) ? '<br>' . $instance['address2'] : '';

        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];
        if (!empty($phone)):?>
            <div class="phone"><a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a></div>
        <?php endif;
        if (!empty($email)):?>
            <div class="email"><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></div>
        <?php endif;
        if (!empty($address)):?>
            <div class="address"><?php echo $address ?><?php echo $address2 ?></div>
        <?php endif;
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        // outputs the options form on admin
        $this->labeledInput($instance, 'title', 'Title');
        $this->labeledInput($instance, 'phone', 'Phone Number');
        $this->labeledInput($instance, 'email', 'Email Address');
        $this->labeledInput($instance, 'address', 'Location 1');
        $this->labeledInput($instance, 'address2', 'Location 2');
    }

    private function labeledInput($instance, $name, $title)
    {
        $value = isset($instance[$name]) ? $instance[$name] : __('New ' . $name, 'text_domain');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id($name); ?>"><?php _e($title . ':'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id($name); ?>"
                   name="<?php echo $this->get_field_name($name); ?>" type="text"
                   value="<?php echo esc_attr($value); ?>"/>
        </p>
    <?php

    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        foreach (array('title', 'phone', 'email', 'address', 'address2') as $field) {
            $instance[$field] = (!empty($new_instance[$field])) ? strip_tags($new_instance[$field]) : '';
        }
        return $instance;
    }

    public static function register(){
        register_widget('ipso_Widget_ContactUs');
    }
}

add_action('widgets_init', array('ipso_Widget_ContactUs', 'register'));
