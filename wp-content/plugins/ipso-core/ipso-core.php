<?php
/**
 * Plugin Name: ipso Creative Core
 * Description: Reusable functionality for easy Wordpress Development
 * Version: 1.0
 * Author: ipsoCreative
 * Author URI: http://www.ipsocreative.com
 */
function ipso_core_scripts(){
    wp_register_style('ipso_admin', plugins_url('ipso-core/css/admin.css'));
    wp_register_style('ipso_admin_products', plugins_url('ipso-core/css/woocommerce.css'), array('woocommerce_admin_styles'));
}
add_action('admin_enqueue_scripts', 'ipso_core_scripts');

include('custom_fields.php');
require_once('custom_fields/gallery.php');
include('lib/Cache.php');
include('shortcodes.php');
include('widgets/ipso_Widget_Base.php');
include('widgets/ipso_Widget_Cached_Base.php');
include('GeneralSetting.php');
include('ThemeSetting.php');
include('lib/GoogleAnalytics.php');
include('lib/ipsoFavIcon.php');
include('lib/simple-copy-post.php');
include('custom_fields/CustomMenuField.php');
include('custom_fields/DownloadsField.php');
function ipso_core_widgets_init()
{
    include('widgets/ipso_Widget_ContactUs.php');
    include('widgets/ipso_Widget_Events.php');
    include('widgets/ipso_Widget_Excerpt.php');
    include('widgets/ipso_Widget_Facts.php');
    include('widgets/ipso_Widget_ShortCode.php');
    include('widgets/ipso_Widget_SocialIcons.php');
    include('widgets/ipso_Widget_Videos.php');
    include('widgets/ipso_Widget_TwitterFeed.php');
    include('widgets/ipso_Widget_Responsive_Grid.php');
}
add_action('widgets_init', 'ipso_core_widgets_init');

$ipso_loaded_animation_script = false;
function ipso_load_animation_script(){
    global $ipso_loaded_animation_script;
    if ($ipso_loaded_animation_script){
        return;
    }
    $ipso_loaded_animation_script = true;
    add_action('wp_print_footer_scripts', function(){
        echo '<script src="'.plugins_url('/ipso-core/widgets/requestAnimationFrame.js').'"></script>';
    });
}

add_action('ipso_load_animation_script', 'ipso_load_animation_script');

function ipso_init()
{
    do_action('ipso_init');
}

add_action('init', 'ipso_init', 0, 1);

function ipso_register_animation_script(){
    wp_register_script('ipso-animation', plugins_url('/ipso-core/widgets/requestAnimationFrame.js'), null, 1);
}

add_action('wp_enqueue_scripts', 'ipso_register_animation_script');

include('tinymce/editor_customizations.php');

function ipso_admin_enqueue_scripts(){
    wp_register_script('media-dialog', plugins_url('custom_fields/media-dialog.js', __FILE__), array('jquery'), 1.0);
    wp_register_script('media-field', plugins_url('custom_fields/media-field.js', __FILE__), array('media-dialog'), 1.0);
}

add_action('admin_enqueue_scripts', 'ipso_admin_enqueue_scripts');