<?php

class ProductVersionField extends RepeatingSortableField
{
    private static $instance;

    static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function renderField($post_id, $field, $meta)
    {
        wp_enqueue_media();
        wp_enqueue_script('media-dialog');
        wp_enqueue_script('media-field');
        parent::renderField($post_id, $field, $meta);
    }

    function renderOneItem($post_id, $field_id, $field_name, $value)
    {
        // TODO: Implement renderOneItem() method.
        $value = array_merge(array(
            'color' => '',
            'image' => '',
            'name' => ''
        ), $value);

        $field = (object)array(
            'id' => "{$field_name}[color]",
            'label' => 'Color'
        );
        ?>
        <div><label>Name</label><br/><?php
            ?><input class="widefat" type="text" name="<?php echo $field_name ?>[name]"
                     value="<?php echo esc_attr($value['name']) ?>"/></div>
        <div><label>Image</label><br/><?php

        MediaField::buildField("{$field_name}[image]", $value['image']);
        ?></div><?php
    }

    /**
     * @param $v
     * @return bool
     */
    function has_value($v)
    {
        // TODO: Implement has_value() method.
        return !empty($v['name']) || !empty($v['image']);
    }
}
