<?php

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 1/10/15
 * Time: 2:23 PM
 */
abstract class ipso_Shortcode_Base
{
    /**
     * $shortcode_tag
     * holds the name of the shortcode tag
     * @var string
     */
    public $shortcode_tag = null;

    /**
     * __construct
     * class constructor will set the needed filter and action hooks
     *
     * @param array $args
     */
    function __construct($tag, $args = array())
    {
        $this->shortcode_tag = $tag;
        //add shortcode
        add_shortcode($this->shortcode_tag, array($this, 'shortcode_handler'));

        if (is_admin()) {
            add_action('admin_head', array($this, 'admin_head'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }
    }

    /**
     * shortcode_handler
     * @param  array $atts shortcode attributes
     * @param  string $content shortcode content
     * @return string
     */
    function shortcode_handler($atts, $content = null)
    {
        ob_start();

        $this->print_shortcode($atts, $content);

        return ob_get_clean();
    }

    abstract function print_shortcode($atts, $content);

    /**
     * admin_head
     * calls your functions into the correct filters
     * @return void
     */
    function admin_head()
    {
        // check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // check if WYSIWYG is enabled
        if ('true' == get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', array($this, 'mce_external_plugins'));
            add_filter('mce_buttons', array($this, 'mce_buttons'));
        }
    }

    private function shortcode_url($relative_path)
    {
        return get_template_directory_uri() . "/shortcodes/{$this->shortcode_tag}/$relative_path";
    }

    /**
     * mce_external_plugins
     * Adds our tinymce plugin
     * @param  array $plugin_array
     * @return array
     */
    function mce_external_plugins($plugin_array)
    {
        $plugin_array[$this->shortcode_tag] = $this->shortcode_url('mce-button.js');
        return $plugin_array;
    }

    /**
     * mce_buttons
     * Adds our tinymce button
     * @param  array $buttons
     * @return array
     */
    function mce_buttons($buttons)
    {
        $buttons[] = $this->shortcode_tag;
        return $buttons;
    }

    /**
     * admin_enqueue_scripts
     * Used to enqueue custom styles
     * @return void
     */
    function admin_enqueue_scripts()
    {
        wp_enqueue_script('media-dialog', plugins_url('ipso-core/custom_fields/media-dialog.js'), array('jquery'), 1.0);
        wp_enqueue_script('media-field', plugins_url('ipso-core/custom_fields/media-field.js'), array('media-dialog'), 1.0);
        wp_enqueue_script('shortcode-editor', get_template_directory_uri().'/shortcodes/js/shortcode-editor.js');
        wp_enqueue_style($this->shortcode_tag.'_shortcode', $this->shortcode_url('mce-button.css'));
    }

    function replace_shortcodes(&$list)
    {
        global $shortcode_tags;
        foreach ($list as $key => $callback) {
            if (isset($shortcode_tags[$key])) {
                $list[$key] = $shortcode_tags[$key];
            } else {
                $list[$key] = null;
            }
            add_shortcode($key, $callback);
        }
    }

    function restore_shortcodes(&$list)
    {
        foreach ($list as $key => $callback) {
            if (!empty($callback)) {
                add_shortcode($key, $callback);
            } else {
                remove_shortcode($key);
            }
        }
    }
}//end class


function wp_ajax_get_attachment_thumb_url()
{
    $attachment_id = $_GET['attachment_id'];
    echo wp_get_attachment_thumb_url($attachment_id);
    die;
}

add_action('wp_ajax_get_attachment_thumb_url', 'wp_ajax_get_attachment_thumb_url');