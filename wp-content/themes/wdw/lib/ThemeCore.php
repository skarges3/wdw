<?php

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 4/1/14
 * Time: 9:59 PM.
 */
abstract class SimpleApplication
{
    public function on($action, $method = null, $priority = 10, $args = 1)
    {
        if ($method == null) {
            $method = $action;
        }
        add_action($action, array($this, $method), $priority, $args);
    }
}

class SocialShares extends SimpleApplication
{
    public function __construct()
    {
        $this->on('social_shares');
        add_filter('share_link', array($this, 'share_link'), 10, 4);
    }

    public function share_link($url, $service, $title, $thumbnail_url)
    {
        switch ($service) {
            case 'email':
                $subject = urldecode($title);

                return "mailto:info@womendoingwell.org?body={$url}&subject={$subject}";
            case
                'twitter':
                return "https://twitter.com/share?url={$url}&text={$title}";
            case 'pinterest':
                return "https://www.pinterest.com/pin/create/button/?url={$url}&description={$title}&media={$thumbnail_url}";
            case 'facebook':
                if ($facebook_app_id = '212676979108023') {
                    return "https://www.facebook.com/dialog/feed?app_id={$facebook_app_id}&display=popup&caption={$title}&link={$url}";
                } else {
                    return "https://www.facebook.com/sharer/sharer.php?u={$url}";
                }
            case 'google':
                return "https://plus.google.com/share?url={$url}";
            case 'tumblr':
                if (empty($thumbnail_url)) {
                    return "http://www.tumblr.com/share/link?url={$url}&description={$title}";
                } else {
                    return "http://www.tumblr.com/share/photo?source={$thumbnail_url}&url={$url}&caption={$title}";
                }

        }

        return $url;
    }

    private static $ipso_share_options = null;

    private static function get_share_options()
    {
        if (self::$ipso_share_options == null) {
            self::$ipso_share_options = get_option('ipso_share');
        }

        return self::$ipso_share_options;
    }

    public function social_shares($post_id = null)
    {
        $options = self::get_share_options();
        if (empty($options)) {
            return;
        }
        if (empty($post_id)) {
            $post_id = get_the_ID();
        }
        $url = get_permalink($post_id);
        $urlencoded = urlencode($url);

        $title = urlencode(get_the_title($post_id));
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if (empty($thumbnail_id)) {
            $thumbnail_url = '';
        } else {
            $thumbnail_url = urlencode(wp_get_attachment_url($thumbnail_id));
        }

        ?>
        <ul class="share">
        <?php foreach ($options as $type) {
    $type_lower = strtolower($type);
    $share_url = apply_filters('share_link', $urlencoded, $type_lower, $title, $thumbnail_url);

    ?>
        <li><a class="fa fa-<?php echo $type_lower ?>" href="<?php echo $share_url ?>" rel="nofollow"
               target="_blank"><span>Share
            on <?php echo $type ?></span></a></li><?php

}
        ?>

        </ul><?php

    }
}

new SocialShares();

class ThemeApplication extends SimpleApplication
{
    public function __construct()
    {
        require_once __DIR__.'/class-tgm-plugin-activation.php';
        $this->on('init');
        $this->on('wp_enqueue_scripts');
        $this->on('after_setup_theme');
        $this->on('breadcrumbs');
        $this->on('credits');
        $this->on('ipso_init');
        $this->on('widget_field');
        $this->on('after_switch_theme');
        $this->on('tgmpa_register');
        $this->on('wp_head', null, 1);
        $this->on('wp_head', 'wp_head_last', 100);
        $this->on('header_image');
        $this->on('single_page_header');
        add_filter('tiny_mce_before_init', array($this, 'tiny_mce_before_init'));
        add_filter('mce_buttons_2', array($this, 'mce_buttons_2'));
        add_filter('body_class', array($this, 'body_class'), 10, 2);
        add_shortcode('current_year', array($this, 'current_year'));
        add_shortcode('tabs', array($this, 'tabs'));
        add_shortcode('tab', array($this, 'tab'));
        $this->import_directory('widgets');
        add_filter('the_title', array($this, 'add_secondary_title'));
        $this->on('pre_get_posts');
        $this->on('wp_ajax_custom_styles');
        $this->on('wp_ajax_nopriv_custom_styles', 'wp_ajax_custom_styles');
    }

    public function wp_ajax_custom_styles()
    {
        header('Content-Type: text/css');
        $color1 = get_option('color1');
        $color2 = get_option('color2');
        $footer_background = get_option('footer_background');
        include __DIR__.'/../custom-styles.php';
        die;
    }

    /** @var WP_Query $wp_query */
    public function pre_get_posts($wp_query)
    {
        if ($wp_query->is_main_query()) {
            if ($wp_query->is_post_type_archive('initiative')
                || $wp_query->is_tax('initiative_type')
            ) {
                $wp_query->set('posts_per_page', -1);
                $wp_query->set('order', 'ASC');
                $wp_query->set('orderby', 'meta_value');
                $wp_query->set('meta_key', '_date');
            } elseif ($wp_query->is_post_type_archive('person')) {
                $wp_query->set('posts_per_page', 20);
                $wp_query->set('order', 'ASC');
            }
        }

        return $wp_query;
    }

    public function header_image($version = 'default')
    {
        $img = get_option("{$version}_header_image");
        if (empty($img) && $version != 'default') {
            return $this->header_image('default');
        }
        echo $img;
    }

    public function single_page_header($page_id)
    {
        $header_image = get_post_meta($page_id, '_header_image', true);
        if ($header_image) {
            $header_image = wp_get_attachment_url($header_image);
        }
        $header_image_mobile = get_post_meta($page_id, '_header_image_mobile', true);
        if ($header_image_mobile) {
            $header_image_mobile = wp_get_attachment_url($header_image_mobile);
        }
        $header_title = get_post_meta($page_id, '_header_title', true);
        $header_subtitle = get_post_meta($page_id, '_header_sub_title', true);

        ?>
    <div id="page-header" style="background-image:url(<?php if (empty($header_image)) {
    do_action('header_image');
} else {
    echo $header_image;
}
        ?>);">
        <?php if (!empty($header_image_mobile)) {
    ?>
        <div class="mobile-page-header" style="background-image:url(<?php echo $header_image_mobile ?>)"></div>
    <?php

}
        ?>
        <h1><?php echo empty($header_title) ? get_option('default_header_title') : $header_title ?></h1>

        <h2><?php echo empty($header_subtitle) ? get_option('default_header_sub_title') : $header_subtitle ?></h2>
        </div><?php

    }

    public function add_secondary_title($title, $item_id = null)
    {
        if (is_admin() && is_null($item_id)) {
            $item_id = get_the_ID();
        }
        $meta = get_post_meta($item_id, 'sub_title', true);
        if (!empty($meta)) {
            return "$title $meta";
        }

        return $title;
    }

    public function tgmpa_register()
    {
        $plugins = array(
            array(
                'name' => 'ipsoCore',
                'slug' => 'ipso-core',
            ),
        );

        $theme_text_domain = 'ipso';
        $config = array(

            /*'domain'       => $theme_text_domain,         // Text domain - likely want to be the same as your theme. */
            /*'default_path' => '',                         // Default absolute path to pre-packaged plugins */
            /*'menu'         => 'install-my-theme-plugins', // Menu slug */
            'strings' => array(
                /*'page_title'             => __( 'Install Required Plugins', $theme_text_domain ), // */
                /*'menu_title'             => __( 'Install Plugins', $theme_text_domain ), // */
                /*'instructions_install'   => __( 'The %1$s plugin is required for this theme. Click on the big blue button below to install and activate %1$s.', $theme_text_domain ), // %1$s = plugin name */
                /*'instructions_activate'  => __( 'The %1$s is installed but currently inactive. Please go to the <a href="%2$s">plugin administration page</a> page to activate it.', $theme_text_domain ), // %1$s = plugin name, %2$s = plugins page URL */
                /*'button'                 => __( 'Install %s Now', $theme_text_domain ), // %1$s = plugin name */
                /*'installing'             => __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name */
                /*'oops'                   => __( 'Something went wrong with the plugin API.', $theme_text_domain ), // */
                /*'notice_can_install'     => __( 'This theme requires the %1$s plugin. <a href="%2$s"><strong>Click here to begin the installation process</strong></a>. You may be asked for FTP credentials based on your server setup.', $theme_text_domain ), // %1$s = plugin name, %2$s = TGMPA page URL */
                /*'notice_cannot_install'  => __( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', $theme_text_domain ), // %1$s = plugin name */
                /*'notice_can_activate'    => __( 'This theme requires the %1$s plugin. That plugin is currently inactive, so please go to the <a href="%2$s">plugin administration page</a> to activate it.', $theme_text_domain ), // %1$s = plugin name, %2$s = plugins page URL */
                /*'notice_cannot_activate' => __( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', $theme_text_domain ), // %1$s = plugin name */
                /*'return'                 => __( 'Return to Required Plugins Installer', $theme_text_domain ), // */
            ),
        );

        tgmpa($plugins, $config);
    }

    public function current_year($att, $content)
    {
        return date('Y');
    }

    public function import_directory($dir)
    {
        $path = get_template_directory().'/'.$dir.'/';
        $dir = opendir($path);
        while (($file = readdir($dir)) !== false) {
            if (strpos($file, '.') === 0 || substr($file, strlen($file) - 4) != '.php') {
                continue;
            }
            require $path.$file;
        }
        closedir($dir);
    }

    public function init()
    {
        switch (get_option('menu_version')) {
            case 'version_2':
                $menus = array(
                    'primary_left' => __('Primary Navigation', 'ipso'),
                );
                break;
            case 'version_1':
            default:
                $menus = array(
                    'primary_left' => __('Primary Navigation (Left)', 'ipso'),
                    'primary_right' => __('Primary Navigation (Right)', 'ipso'),
                );
                break;
        }
        register_nav_menus($menus);
        register_sidebar(array(
            'name' => __('Blog Sidebar', 'ipso'),
            'id' => 'sidebar-1',
            'description' => __('Appears on blog pages.', 'ipso'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name' => __('Home Page', 'ipso'),
            'id' => 'home-1',
            'description' => __('Appears on the home page.', 'ipso'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name' => __('Gallery', 'ipso'),
            'id' => 'gallery-1',
            'description' => __('Content added to the bottom of photo galleries', 'ipso'),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name' => __('Footer', 'ipso'),
            'id' => 'footer-1',
            'description' => __('Content added to the bottom of all pages', 'ipso'),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        add_editor_style('editor.css');
    }

    public function wp_head()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            ?>
            <link rel="stylesheet/less" type="text/css"
                  href="<?php echo get_template_directory_uri() ?>/style.less?v=<?php echo rand() ?>" /><?php

        }
    }

    public function wp_head_last()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="<?php echo admin_url('admin-ajax.php') ?>?action=custom_styles"/><?php

    }

    public function wp_enqueue_scripts()
    {
        wp_register_script('masonry', get_template_directory_uri().'/js/vendor/masonry/dist/mastonry.pkgd.min.js', array('jquery'), '3.3.2');
        wp_register_script('masonry-gallery', get_template_directory_uri().'/js/masonry-gallery-init.js', array('masonry'), '1.0');
        wp_enqueue_script('site', get_template_directory_uri().'/js/site.js', array('jquery'), '1.1');

        if (defined('WP_DEBUG') && WP_DEBUG) {
            wp_register_script('less-setup', get_template_directory_uri().'/js/setup-less.js', null, '1.5');
            wp_enqueue_script('less', '//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.0/less.min.js', array('less-setup'), '2.5');
        } else {
            wp_enqueue_style('site', get_stylesheet_uri(), null, '1.8');
        }
    }

    public function after_setup_theme()
    {
        add_theme_support('html5', array('gallery'));
        add_theme_support('post-thumbnails');
        add_theme_support('unsemantic-grid');
        add_theme_support('custom-header', array(
            'width' => 525,
            'height' => 215,
            'default-image' => get_template_directory_uri().'/images/ipso-logo.svg',
            'uploads' => true,
            'flex-width' => true,
            'flex-height' => true,
        ));
    }

    public function after_switch_theme()
    {
        if (class_exists('CustomTaxonomy')) {
            CustomTaxonomy::create_metadata_table('initiative_type');
        }
    }

    public function breadcrumbs()
    {
        $breadcrumbs = '';
        if (function_exists('yoast_breadcrumb')) {
            $breadcrumbs = yoast_breadcrumb('', '', false);
        }
        if (!empty($breadcrumbs)) {
            ?>
            <div id="breadcrumbs">
            <div class="grid-container">
                <div class="grid-100 tablet-grid-100 mobile-grid-100"><?php echo $breadcrumbs;
            ?></div>
            </div>
            </div><?php

        }
    }

    public function credits()
    {
        echo do_shortcode(get_option('copyright'));
    }

    private static $theme_page;

    public static function get_theme_page()
    {
        if (!self::$theme_page) {
            self::$theme_page = new ThemePage();
        }

        return self::$theme_page;
    }

    public function share_options_input($setting, $value)
    {
        $name = $setting->name;
        if (!is_array($value)) {
            $value = array();
        }
        $ops = array('Twitter',
            'Pinterest',
            'Facebook',
            'Google',
            'Tumblr',
            'Email', );
        foreach ($ops as $idx => $op) {
            ?>
            <div><input type="checkbox" name="<?php echo $name ?>[]" value="<?php echo $op ?>"
                        id="<?php echo $name ?>_<?php echo $idx ?>"<?php echo in_array($op, $value) ? ' checked' : '' ?>/><label
                for="<?php echo $name ?>_<?php echo $idx ?>"><?php echo $op ?></label></div><?php

        }
    }

    public function menu_versions($field, $val)
    {
        $versions = array(
            'version_1' => 'Left and Right',
            'version_2' => 'Right Only',
        );
        ?><select name="<?php echo $field->name ?>"><?php
        foreach ($versions as $value => $text) {
            ?>
            <option value="<?php echo $value ?>"<?php echo $value == $val ? ' selected' : '' ?>><?php echo $text ?></option><?php

        }
        ?></select><?php

    }

    public function ipso_init()
    {
        $page = $this->get_theme_page();
        $page->addSetting('ipso_share', 'Shares', array($this, 'share_options_input'));

        $page->addSetting('menu_version', 'Menu Style', array($this, 'menu_versions'));

        $page->addSetting('default_header_title', 'Default Header Title', 'text');
        $page->addSetting('default_header_sub_title', 'Default Header Sub Title', 'text');
        $page->addSetting('default_header_image', 'Default Header Image', 'image');

        $page->addSetting('blog_header_title', 'Blog Header Title', 'text');
        $page->addSetting('blog_header_sub_title', 'Blog Header Sub Title', 'text');
        $page->addSetting('blog_header_image', 'Blog Header Image', 'image');

        $page->addSetting('video_header_title', 'Video Header Title', 'text');
        $page->addSetting('video_header_sub_title', 'Video Header Sub Title', 'text');
        $page->addSetting('video_header_image', 'Video Header Image', 'image');

        $page->addSetting('gallery_header_title', 'Gallery Header Title', 'text');
        $page->addSetting('gallery_header_sub_title', 'Gallery Header Sub Title', 'text');
        $page->addSetting('gallery_header_image', 'Gallery Header Image', 'image');

        $page->addSetting('events_header_title', 'Events Header Title', 'text');
        $page->addSetting('events_header_sub_title', 'Events Header Sub Title', 'text');
        $page->addSetting('events_header_image', 'Events Header Image', 'image');

        $page->addSetting('shop_header_title', 'Shop Header Title', 'text');
        $page->addSetting('shop_header_sub_title', 'Shop Header Sub Title', 'text');
        $page->addSetting('shop_header_image', 'Shop Header Image', 'image');

        $page->addSetting('color1', 'Primary Color', 'color');
        $page->addSetting('color2', 'Secondary Color', 'color');

        $page->addSetting('footer_background', 'Footer Background', 'media');

        $page->addSetting('facebook_page', 'Facebook Page', 'text');
        $page->addSetting('twitter_page', 'Twitter Page', 'text');
        $page->addSetting('pinterest_page', 'Pinterest Page', 'text');

        $page->addSetting('header_reveal', 'Header Drop Down', 'wysiwyg');
        $page->addSetting('footer_form', 'Footer Form', 'wysiwyg');
        $page->addSetting('copyright', 'Copyright', 'textarea');

        $page_type = new CustomPostType('page');
        $page_type->setDoNotRegister();
        $page_type->addSection('header', 'Page Header', 'side');
        $page_type->addFieldToSection('header', '_header_image', 'media', '', 'Background');
        $page_type->addFieldToSection('header', '_header_image_mobile', 'media', '', 'Background Mobile');
        $page_type->addFieldToSection('header', '_header_title', 'text', '', 'Title');
        $page_type->addFieldToSection('header', '_header_sub_title', 'text', '', 'Sub Title');

        $events = new CustomPostType('initiative', array(
            'name' => 'Events',
            'singular_name' => 'Event',
            'menu_name' => 'Events',
            'all_items' => 'All Events',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'new_item' => 'New Event',
            'view_item' => 'View Event',
            'search_items' => 'Search events',
            'not_found' => 'No events found',
            'not_found_in_trash' => 'No events found',
            'parent_item_colon' => '', ),
            array(
                'menu_icon' => 'dashicons-calendar-alt',
                'rewrite' => array(
                    'slug' => 'event',
                ),
            )
        );

        $events->addThumbnailSupport();
        $events->addExcerptSupport();
        $events->addSection('event', 'When/Where', 'side');
        $events->addFieldToSection('event', '_extra_title', 'text', '', 'Title');
        $events->addFieldToSection('event', '_date', 'date', '', 'Date');
        $events->addFieldToSection('event', '_time', 'time', '', 'Time');
        $events->addFieldToSection('event', '_city', 'text', '', 'City');
        $events->addFieldToSection('event', '_location', 'text', '', 'Location');
        $events->addFieldToSection('event', '_price', 'text', '', 'Price');
        $events->addFieldToSection('event', '_register', 'url', '', 'Register URL');

        $initiative_type = $events->addTaxonomy('initiative_type', array(
            'labels' => array('name' => 'Event Types',
                    'singular_name' => 'Type',
                    'menu_name' => 'Types',
                    'all_items' => 'All Types',
                    'edit_item' => 'Edit Type',
                    'view_item' => 'View Type',
                    'update_item' => 'Update Type',
                    'add_new_item' => 'Add New Type',
                    'new_item_name' => 'New Type',
                    'parent_item' => 'Parent Type',
                    'parent_item_colon' => 'Parent Type:',
                    'search_items' => 'Search Types',
                    'popular_items' => 'Popular types',
                    'separate_items_with_commas' => 'Separate types with commas',
                    'add_or_remove_items' => 'Add/Remove Types',
                    'choose_from_most_used' => 'Choose from most used types',
                    'not_found' => 'Type not found',
                ),
            'rewrite' => array(
                'slug' => 'events',
            ),
        ));

        $initiative_type->addCustomField('header_image', 'media', '', 'Header Image');
        $initiative_type->addCustomField('header_title', 'text', 'Header Title');
        $initiative_type->addCustomField('header_sub_title', 'text', 'Header Sub-Title');
        $initiative_type->addCustomField('footer', 'text', 'Footer');

        $post = new CustomPostType('post');
        $post->setDoNotRegister();
        $post->addField('sub_title', 'text', 'Secondary title that will get added to the title in various places.', 'Secondary Title');

        $product = new CustomPostType('product', array(
            'name' => 'Products',
            'singular_name' => 'Product',
            'menu_name' => 'Products',
            'all_items' => 'All Product',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Product',
            'edit_item' => 'Edit Product',
            'new_item' => 'New Product',
            'view_item' => 'View Product',
            'search_items' => 'Search products',
            'not_found' => 'No products found',
            'not_found_in_trash' => 'No products found',
            'parent_item_colon' => '',
        ), array(
            'menu_icon' => 'dashicons-products',
        ));

        $product->addThumbnailSupport();
        $product->addField('_url', 'url', 'Infusionsoft link', 'URL');
        $product->addField('_price', 'text', '', 'Price');
        include_once __DIR__.'/ProductVersionField.php';
        $product->addField('_versions', ProductVersionField::get_instance(), '', 'Product Versions');
        $product->addField('_gallery', 'gallery', '', 'Gallery');

        $person = new CustomPostType('person', array(
            'name' => 'People',
            'singular_name' => 'Person',
            'menu_name' => 'People',
            'all_items' => 'All Person',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Person',
            'edit_item' => 'Edit Person',
            'new_item' => 'New Person',
            'view_item' => 'View Person',
            'search_items' => 'Search people',
            'not_found' => 'No people found',
            'not_found_in_trash' => 'No people found',
            'parent_item_colon' => '',
        ), array(
            'menu_icon' => 'dashicons-admin-users',
        ));

        $person->addSection('side', 'Details', 'side');
        $person->addFieldToSection('side', '_role', 'text', '', 'Role');
        $person->addFieldToSection('side', '_twitter_url', 'url', '', 'Twitter');
        $person->addFieldToSection('side', '_facebook_url', 'url', '', 'Facebook');
        $person->addFieldToSection('side', '_email_address', 'email', '', 'Email');
        $person->addFieldToSection('side', '_linkedin_url', 'url', '', 'Linkedin');
        $person->addFieldToSection('side', '_instagram_url', 'url', '', 'Instagram');
        $person->addFieldToSection('side', '_pinterest_url', 'url', '', 'Pinterest');

        $person->addThumbnailSupport();

        $gallery = new CustomPostType('gallery', array(
            'name' => 'Galleries',
            'singular_name' => 'Gallery',
            'menu_name' => 'Gallery',
            'all_items' => 'All Gallery',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Gallery',
            'edit_item' => 'Edit Gallery',
            'new_item' => 'New Gallery',
            'view_item' => 'View Gallery',
            'search_items' => 'Search Galleries',
            'not_found' => 'No galleries found',
            'not_found_in_trash' => 'No galleries found',
            'parent_item_colon' => '',
        ), array(
            'menu_icon' => 'dashicons-images-alt',
        ));

        $gallery->addThumbnailSupport();
        $gallery->addField('_gallery', 'gallery', '', 'Gallery');
        $gallery->addField('_featured', 'checkbox', '', 'Featured');
        $gallery->addTaxonomy('gallery-category', array(
            'labels' => array(
                'name' => 'Categories',
                'singular_name' => 'Category',
                'menu_name' => 'Category Name',
                'all_items' => 'All Category',
                'edit_item' => 'Edit Category',
                'view_item' => 'View Category',
                'update_item' => 'Update Category',
                'add_new_item' => 'Add New Category',
                'new_item_name' => 'New Category Name',
                'parent_item' => null,
                'parent_item_colon' => null,
                'search_items' => 'Search Categories',
                'popular_items' => 'Popular Categories',
                'separate_items_with_commas' => '',
                'add_or_remove_items' => '',
                'choose_from_most_used' => '',
                'not_found' => 'No Categories Found',
            ),
        ));

        $video = new CustomPostType('video_url', array(
            'name' => 'Videos',
            'singular_name' => 'Video',
            'menu_name' => 'Video',
            'all_items' => 'All Videos',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Videos',
            'edit_item' => 'Edit Video',
            'new_item' => 'New Video',
            'view_item' => 'View Video',
            'search_items' => 'Search Video',
            'not_found' => 'No videos found',
            'not_found_in_trash' => 'No videos found',
            'parent_item_colon' => '',
        ), array(
            'menu_icon' => 'dashicons-video-alt',
        ));
        $video->addField('_video', 'video', '', 'Video');
        $video->addThumbnailSupport();
    }

    public function tabs($att, $content)
    {
        wp_enqueue_script('tabset', get_template_directory_uri().'/js/tabset.js', array('jquery'), '1.0');
        wp_enqueue_style('tabset', get_template_directory_uri().'/css/tabset.css', null, '1.0');

        return '<div class="tabset grid-parent">'.do_shortcode($content).'</div>';
    }

    public function tab($att, $content)
    {
        $att = shortcode_atts(array('title' => ''), $att);

        return '<div class="tab"><div class="tab-title">'.$att['title'].'</div><div class="tab-content">'.do_shortcode($content).'</div></div>';
    }

    public function widget_field($field, $args = array(
        'before_widget' => "<div class='widget'>",
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ))
    {
        $widgets = get_post_meta(get_the_ID(), $field, true);
        foreach ($widgets as $w) {
            if (empty($w['class'])) {
                continue;
            }
            $class = $w['class'];
            $values = $w['instance'];
            /** @var $widget WP_Widget */
            $widget = new $class();

            $widget->widget($args, $values);
        }
    }

    public function body_class($classes, $class)
    {
        if (wp_is_mobile()) {
            $classes[] = 'mobile';
        }

        return $classes;
    }

    public function tiny_mce_before_init($init_array)
    {
        $style_formats = array(
            array(
                'title' => 'Heading Styles',
                'items' => array(
                    array(
                        'title' => 'Style 1',
                        'inline' => 'span',
                        'classes' => 'heading-1',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 2',
                        'inline' => 'span',
                        'classes' => 'heading-2',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 3',
                        'inline' => 'span',
                        'classes' => 'heading-3',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 4',
                        'inline' => 'span',
                        'classes' => 'heading-4',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 5',
                        'inline' => 'span',
                        'classes' => 'heading-5',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 6',
                        'inline' => 'span',
                        'classes' => 'heading-6',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 7',
                        'inline' => 'span',
                        'classes' => 'heading-7',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 8',
                        'inline' => 'span',
                        'classes' => 'heading-8',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 9',
                        'inline' => 'span',
                        'classes' => 'heading-9',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 10',
                        'inline' => 'span',
                        'classes' => 'heading-10',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 11',
                        'inline' => 'span',
                        'classes' => 'heading-11',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 12',
                        'inline' => 'span',
                        'classes' => 'heading-12',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Style 13',
                        'inline' => 'span',
                        'classes' => 'heading-13',
                        'wrapper' => true,
                    ),
                ),
            ),
            array(
                'title' => 'Button Styles',
                'items' => array(
                    array(
                        'title' => 'Purple',
                        'inline' => 'a',
                        'classes' => 'button button-purple button-wide',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Pink',
                        'inline' => 'a',
                        'classes' => 'button button-pink button-wide',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Green',
                        'inline' => 'a',
                        'classes' => 'button button-green button-wide',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Gray',
                        'inline' => 'a',
                        'classes' => 'button button-gray button-wide',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Outline',
                        'inline' => 'a',
                        'classes' => 'button button-outline button-wide',
                        'wrapper' => true,
                    ),
                ),
            ),
            array(
                'title' => 'Section Styles',
                'items' => array(
                    array(
                        'title' => 'Purple',
                        'block' => 'section',
                        'classes' => 'purple',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Deep Purple',
                        'block' => 'section',
                        'classes' => 'deep-purple',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Pink',
                        'block' => 'section',
                        'classes' => 'pink',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Green',
                        'block' => 'section',
                        'classes' => 'green',
                        'wrapper' => true,
                    ),
                    array(
                        'title' => 'Orchid',
                        'block' => 'section',
                        'classes' => 'orchid',
                        'wrapper' => true,
                    ),

                ),
            ),
        );
        // Insert the array, JSON ENCODED, into 'style_formats'
        $init_array['style_formats'] = json_encode($style_formats);
        $init_array['style_formats_merge'] = false;

        return $init_array;
    }

    public function mce_buttons_2($buttons)
    {
        array_unshift($buttons, 'styleselect');

        return $buttons;
    }
}

new ThemeApplication();
