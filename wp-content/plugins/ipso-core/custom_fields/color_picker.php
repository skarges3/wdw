<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 1/15/14
 * Time: 12:50 AM
 * To change this template use File | Settings | File Templates.
 */

class ColorCodeField implements IFieldRenderer
{
    /** @var  IFieldRenderer $instance */
    static $instance;

    static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private static $added_script;

    function renderField($id = null, $field, $value)
    {
        if (!self::$added_script) {
            new FieldScriptRenderer(null, '(function ($) {
                $(function () {
                    $(".color-field").wpColorPicker();
                });
            })(jQuery);');
            self::$added_script = true;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        $name = (isset($field->id) ? $field->id : $field->name);
        ?><input type="text" name="<?php echo $name ?>" value="<?php echo esc_attr($value) ?>"
                 class="color-field"/>
    <?php
    }
}