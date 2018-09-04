<?php

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 7/9/14
 * Time: 5:28 PM
 */
abstract class ipso_Cached_Widget_Base extends ipso_Widget_Base
{

    function __construct($id_base, $name, $widget_options = array(), $control_options = array())
    {
        parent::__construct($id_base, $name, $widget_options, $control_options);
        $this->enable_cache();
    }

    private function enable_cache()
    {
        add_action('save_post', array($this, 'clear_cache'));
        add_action('deleted_post', array($this, 'clear_cache'));
        add_action('switch_theme', array($this, 'clear_cache'));
    }

    private function get_cache_name()
    {
        return $this->name;
    }

    function widget($args, $instance)
    {
        if (!$this->is_preview()) {
            $cache = wp_cache_get($this->get_cache_name(), 'widget');
        }
        if (!is_array($cache)) {
            $cache = array();
        }

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }

        ob_start();
        $this->render_widget($args, $instance);
        $data = ob_get_flush();
        if (!$this->is_preview()) {
            $cache[$args['widget_id']] = $data;
            wp_cache_add($this->get_cache_name(), $cache, 'widget');
        }
    }

    function update($new_instance, $old_instance)
    {
        $instance = $this->update_instance($new_instance, $old_instance);
        $this->clear_cache();
        return $instance;
    }

    abstract function render_widget($args, $instance);

    abstract function update_instance($new_instance, $old_instance);

    function clear_cache()
    {
        wp_cache_delete($this->get_cache_name(), 'widget');
    }
}
