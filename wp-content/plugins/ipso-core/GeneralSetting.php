<?php

class SettingControl
{
    function __construct($name, $field, $label, $type = 'textarea')
    {
        $this->name = $name;
        $this->label = $label;
        $this->field = $field;
        $this->type = $type;
    }

    function get_value()
    {
        return get_option($this->name, '');
    }

    function fields_html()
    {
        $value = $this->get_value();
        if (is_string($this->type)) {
            switch ($this->type) {
                case 'password':
                case 'text':
                case 'date':
                case 'time':
                    echo '<input type="' . $this->type . '" id="' . $this->name . '" name="' . $this->name . '" value="' . esc_attr($value) . '" class="regular-text ltr"/>';
                    break;
                case 'datetime':
                    if (!is_array($value)) {
                        $value = array('', '');
                    }
                    echo '<input type="date" id="' . $this->name . '" name="' . $this->name . '[]" value="' . esc_attr($value[0]) . '" class="regular-text ltr"/>';
                    echo '<input type="time" id="' . $this->name . '" name="' . $this->name . '[]" value="' . esc_attr($value[1]) . '" class="regular-text ltr"/>';
                    break;
                case 'wysiwyg':
                    wp_editor(html_entity_decode($value), $this->name);
                    break;
                case 'textarea':
                    echo '<textarea id="' . $this->name . '" name="' . $this->name . '" rows="5" cols="80">' . esc_textarea($value) . '</textarea>';
                    break;
                case 'image':
                    FieldHelper::imageField($this->name, $this->name, $value);
                    break;
                case 'input':
                    echo '<input type="text" id="' . $this->name . '" name="' . $this->name . '" value="' . esc_attr($value) . '" class="regular-text ltr"/>';
                    break;
                case 'encoded-image':
                    add_thickbox();
                    echo '<div id="uploaded-image-';
                    echo $this->name;
                    echo '">';
                    if (!empty($value)) {
                        echo '<img src="' . $value . '"/>';
                    }
                    echo '</div>';
                    echo '<input type="hidden" id="' . $this->name . '" name="' . $this->name . '" value="' . esc_attr($value) . '" class="regular-text ltr"/>';
                    echo '<a href="#" class="button clear-button">Clear</a><a href="#TB_inline?width=300&height=200&inlineId=upload-form-' . $this->name . '" class="button thickbox">Upload</a>';
                    $frame = new UploadFrame($this->name, $this->label);
                    add_action('admin_print_footer_scripts', array($frame, 'render'));
                    break;
                case 'media':
                    MediaField::buildField($this->name, $value);
                    break;
                case 'color':
                    ColorCodeField::getInstance()->renderField(null, $this, $value);
                    break;
            }
        } elseif (is_callable($this->type)) {
            call_user_func($this->type, $this, $value);
        } elseif (is_object($this->type)) {
            $implements = class_implements($this->type);
            if (isset($implements['IFieldRenderer'])) {
                /** @var IFieldRenderer $renderer */
                $renderer = $this->type;
                $renderer->renderField(null, (object)array(
                    'id' => $this->name
                ), $value);
            }
        }
    }
}

class GeneralSetting extends SettingControl
{
    function __construct($name, $field, $label, $type = 'textarea')
    {
        parent::__construct($name, $field, $label, $type);
        add_filter('admin_init', array(&$this, 'register_fields'));
    }

    function register_fields()
    {
        register_setting('general', $this->name);
        add_settings_field($this->field, '<label for="' . $this->name . '">' . __($this->label, $this->name) . '</label>', array(&$this, 'fields_html'), 'general');
    }

}

class NetworkSettings
{
    private $title;
    private $settings;

    function __construct($title)
    {
        $this->title = $title;
        $this->settings = array();
        add_filter('wpmu_options', array(&$this, 'html'));
    }

    function add_setting($name, $field, $label, $type)
    {
        $this->settings[] = new NetworkSetting($name, $field, $label, $type);
    }

    function html()
    {
        echo "<h3>{$this->title}</h3>";
        echo '<table class="form-table">';
        /** @var NetworkSetting $setting */
        foreach ($this->settings as $setting) {
            $setting->field_row();
        }
        echo '</table>';
    }
}

class NetworkSetting extends SettingControl
{
    function __construct($name, $field, $label, $type = 'textarea')
    {
        parent::__construct($name, $field, $label, $type);
        add_filter('update_wpmu_options', array($this, 'save_network_settings'));
    }

    function get_value()
    {
        return get_site_option($this->name, '');
    }

    public function save_network_settings()
    {
        $value = sanitize_text_field($_POST[$this->name]);
        update_site_option($this->name, $value);
    }

    public function field_row()
    {
        echo '<tr valign="top"><th scope="row">';
        echo '<label for="' . $this->name . '">' . __($this->label, $this->name) . '</label>';
        echo '</th><td>';
        $this->fields_html();
        echo '</td></tr>';
    }
}

class UploadFrame
{
    private $name;
    private $label;
    private static $added_script;

    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    private static function add_script()
    {
        if (self::$added_script) return;
        self::$added_script = true;
        ?>
        <script>
            (function ($) {
                $(function () {
                    $(".clear-button").click(function (e) {
                        e.preventDefault();
                        $(this).prev("input").val('').prev("div").html("");
                    });
                })
            })(jQuery);
            function updateImage(name, src) {
                tb_remove();
                (function ($) {
                    $("#uploaded-image-" + name).html('<img src="' + src + '"/>');
                    $("#" + name).val(src);
                })(jQuery);
            }
        </script>
        <?php
    }

    public function render()
    {
        self::add_script();
        ?>
        <div style="display:none;" id="upload-form-<?php echo $this->name ?>">
            <div style="text-align:center">
                <h2>Upload <?php echo $this->label ?></h2>

                <form method="POST" enctype="multipart/form-data"
                      target="upload-result-<?php echo $this->name ?>"
                      action="<?php echo admin_url('admin-ajax.php', 'relative'); ?>">
                    <?php wp_nonce_field('save_option', 'upload_nonce'); ?>
                    <input type="hidden" name="option" value="<?php echo $this->name ?>"/>
                    <input type="hidden" name="action" value="option_upload"/>
                    <input type="file" name="upload"/>
                    <br/>
                    <br/>
                    <input type="submit" text="Upload" class="button button-primary" value="Upload"/>
                </form>
            </div>
            <iframe name="upload-result-<?php echo $this->name ?>"
                    id="upload-result-<?php echo $this->name ?>"></iframe>
        </div>
        <?php
    }
}

class OptionUpload
{
    static function setup()
    {
        add_action('wp_ajax_option_upload', array(__CLASS__, 'callback'));
    }

    public static function callback()
    {
        $message = 'window.alert("Invalid request");';
        if (wp_verify_nonce($_POST['upload_nonce'], 'save_option')) {
            $option = $_POST['option'];
            $file = $_FILES['upload'];
            $data = file_get_contents($file['tmp_name']);
            $b64 = base64_encode($data);
            $src = "data:{$file['type']};base64,$b64";
            update_option($option, $src);
            $message = "window.parent.updateImage('$option', '$src')";
        }
        ?>
        <script>
            <?php echo $message?>
        </script><?php
        wp_die();
    }


}

OptionUpload::setup();
