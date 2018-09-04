<?php

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 1/2/14
 * Time: 8:43 AM
 * To change this template use File | Settings | File Templates.
 */
class ThemePage
{
    private $settings;
    private $page_title;
    private $menu_title;
    private $options;

    function __construct($page_title = 'Theme Settings', $menu_title = 'Settings', $options = array())
    {
        $this->page_title = $page_title;
        $this->menu_title = $menu_title;
        add_action('admin_menu', array($this, 'add_theme_page'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        $this->settings = array();
        $this->options = $options;
    }

    function admin_enqueue_scripts()
    {
        wp_enqueue_style('ipso_admin');
    }

    function add_theme_page()
    {
        $parent = isset($this->options['parent_page']) ? $this->options['parent_page'] : 'themes.php';
        $slug = isset($this->options['slug']) ? $this->options['slug'] : 'theme/settings.php';
        add_submenu_page($parent,
            $this->page_title,
            $this->menu_title,
            'manage_options',
            $slug,
            array($this, 'page'));
        /** @var ThemeSetting $setting */
        foreach ($this->settings as $setting) {
            register_setting($this->page_title, $setting->field);
        }
    }

    function addSetting($name, $title, $type)
    {
        $this->settings[] = new ThemeSetting($name, $title, $type);
    }

    function before_form()
    {
        if (isset($this->options['before_form'])) {
            echo $this->options['before_form'];
        }
        settings_errors();
    }

    function after_form()
    {
        if (isset($this->options['after_form'])) {
            echo $this->options['after_form'];
        }
    }

    function page()
    {
        ?>
        <div class="wrap"><h2 class="page_title"><?php echo $this->page_title ?></h2><?php
        $this->before_form();
        ?>
        <form method="POST" action="options.php">
            <table class="form-table"><?php
                settings_fields($this->page_title);
                do_settings_sections($this->page_title);
                /** @var ThemeSetting $setting */
                foreach ($this->settings as $setting) {
                    ?>
                    <tr valign="top"><?php
                    ?>
                    <th scope="row"><label for="<?php echo $setting->field ?>"><?php echo $setting->label ?></label>
                    </th>
                    <td><?php
                        $setting->fields_html();
                        ?></td></tr><?php
                }
                ?></table><?php submit_button() ?></form></div><?php
        $this->after_form();
    }
}

class ThemeSetting extends SettingControl
{
    function __construct($name, $title, $type)
    {
        parent::__construct($name, $name, $title, $type);
    }
}

