<?php

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 2/7/14
 * Time: 7:54 PM
 */
class EditorCustomizations
{
    function __construct()
    {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('tiny_mce_before_init', array($this, 'tiny_mce_before_init'));
    }

    function admin_init()
    {
        if (current_theme_supports('unsemantic-grid')) {
            add_filter('mce_external_plugins', array($this, 'add_unsemantic'));
            add_filter('mce_buttons_2', array($this, 'mce_buttons_2'));
        }
    }

    function add_unsemantic($plugins)
    {
        $plugins['unsemantic_grid'] = plugins_url('ipso-core/tinymce/unsemantic_grid/editor_plugin_src.js');
        return $plugins;
    }


    function mce_buttons_2($buttons)
    {
        $buttons[] = 'grid';
        $buttons[] = 'grid-row';
        $buttons[] = 'grid-left-half';
        $buttons[] = 'grid-center-half';
        $buttons[] = 'grid-right-half';
        return $buttons;
    }

    function tiny_mce_before_init($init_array)
    {
        $init_array['valid_children'] = "+div[div]";
        return $init_array;
    }
}

new EditorCustomizations();