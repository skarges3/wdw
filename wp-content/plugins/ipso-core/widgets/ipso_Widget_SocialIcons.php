<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 7/19/13
 * Time: 1:25 PM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_SocialIcons extends WP_Widget
{
    static $networks = array(
        'facebook' => 'Facebook',
        'myspace' => 'My Space',
        'twitter' => 'Twitter',
        'flickr' => 'Flickr',
        'picassa' => 'Picassa',
        'youtube' => 'YouTube',
        'linkedin' => 'LinkedIn',
        'tumblr' => 'tumblr',
        'vimeo' => 'Vimeo',
        'rss'=>'RSS'
    );

    public function __construct()
    {
        // widget actual processes
        parent::__construct('social_widget', 'Social Icons', array('description' => __('A Widget to provide links to all of your social networks', 'text_domain')));
    }

    public function form($instance)
    {
        // outputs the content of the widget

        $networks = isset($instance['networks']) ? $instance['networks'] : array();
        $data = array_merge(ipso_Widget_SocialIcons::$networks);


        $this->labeledInput($instance, 'title', 'Title');

        if (is_array($networks)) {
            foreach ($networks as $idx => $item) {
                $title = $data[$item['type']];
                $url = $item['url'];
                $type = $item['type'];
                ?>
                <p class='ipso-network'>
                <input class='ipso-type' type='hidden'
                       name='<?php echo $this->get_field_name("networks") . "[$idx][type]"; ?>'
                       value='<?php echo esc_attr($type); ?>'/>
                <label
                    for="<?php echo $this->get_field_id("networks") . "[$idx][url]"; ?>"><?php if ($idx > 0): ?>
                        <a class='button' onclick='function t($,v){var $m = $(v); $m = $m.hasClass("ipso-network") ? $m : $m.parents(".ipso-network"); return {parent: $m,label:$m.find("label"),type:$m.find(".ipso-type"),url:$m.find(".ipso-url")};} var $one=t(jQuery, this), $two=t(jQuery, $one.parent.prev());function s($x,$y,p){var o=$x[p].val();$x[p].val($y[p].val()); $y[p].val(o);};s($one, $two, "url"); s($one, $two, "type"); jQuery(this).parents("form").find("[name=savewidget]").click();return false;'>&uarr;</a><?php endif; ?>
                    <?php if ($idx < count($networks) - 1): ?>
                        <a class='button' onclick='function t($,v){var $m = $(v); $m = $m.hasClass("ipso-network") ? $m : $m.parents(".ipso-network"); return {parent: $m,label:$m.find("label"),type:$m.find(".ipso-type"),url:$m.find(".ipso-url")};} var $one=t(jQuery, this), $two=t(jQuery, $one.parent.next());function s($x,$y,p){var o=$x[p].val();$x[p].val($y[p].val()); $y[p].val(o);};s($one, $two, "url"); s($one, $two, "type"); jQuery(this).parents("form").find("[name=savewidget]").click();return false;'>&darr;</a><?php endif; ?>
                    <?php _e($title . ':'); ?>
                </label>
                <input class="ipso-url widefat" id="<?php echo $this->get_field_id("networks") . "[$idx]->url"; ?>"
                       name="<?php echo $this->get_field_name("networks") . "[$idx][url]"; ?>" type="text"
                       value="<?php echo esc_attr($url); ?>"/>
                </p><?php

            }
        }

        ?><select name="<?php echo $this->get_field_name('new_network') ?>">
        <option value=''>Add Network</option><?php
        foreach ($data as $type => $text) {
            ?>
            <option value="<?php echo $type ?>"><?php echo $text ?></option><?php
        }
        ?></select><?php

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

    public function widget($args, $instance)
    {
        // outputs the content of the widget
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        $networks = isset($instance['networks']) ? $instance['networks'] : array();
        $data = ipso_Widget_SocialIcons::$networks;
        foreach ($networks as $item) {
            ?><a class="social <?php echo $item['type'] ?>" target="_blank"
                 href="<?php echo $item['url'] ?>"><?php echo $data[$item['type']] ?></a><?php
        }
        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        foreach (array('title') as $field) {
            $instance[$field] = (!empty($new_instance[$field])) ? $new_instance[$field] : '';
        }
        $networks = isset($new_instance['networks']) ? $new_instance['networks'] : array();
        for ($i = 0; $i < count($networks); $i++) {
            if (empty($networks[$i]['url'])) {
                array_splice($networks, $i, 1);
                $i--;
            }
        }
        if (!empty($new_instance['new_network'])) {
            $networks[] = array('type' => $new_instance['new_network'], 'url' => 'http://');
        }
        $instance['networks'] = $networks;
        return $instance;
    }
}

register_widget('ipso_Widget_SocialIcons');
