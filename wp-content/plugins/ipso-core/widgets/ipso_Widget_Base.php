<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 9/20/13
 * Time: 9:40 AM
 * To change this template use File | Settings | File Templates.
 */

add_action('admin_init', array('ipso_Widget_Base', 'admin_init'));
add_action('widgets_admin_page', array('ipso_Widget_Base', 'widgets_admin_page'));
add_action('siteorigin_panel_enqueue_admin_scripts', array('ipso_Widget_Base', 'widgets_admin_page'));
class ipso_Widget_Base extends WP_Widget
{
    const VERSION = '1.1';
    const TEXTDOMAIN = 'ipso';

    public static function admin_init()
    {
       WidgetHelper::registerIncludes();
    }

    public static function widgets_admin_page()
    {
       WidgetHelper::sharedEditor();
    }

    protected  function before_widget($args){
        if (isset($args['before_widget'])){
            echo $args['before_widget'];
        }
    }

    protected function after_widget($args){
        if (isset($args['after_widget'])){
            echo $args['after_widget'];
        }
    }

    protected function widget_title($args, $instance, $title_field = 'title'){
        if (empty($instance[$title_field])){
            return;
        }
        if (isset($args['before_title'])){
            echo $args['before_title'];
        }
        echo $instance[$title_field];
        if (isset($args['after_title'])){
            echo $args['after_title'];
        }
    }

    private function printLabel($name, $title)
    {
        $this->label($this->get_field_id($name), $title);
    }

    public function label($id, $title, $suffix=':')
    {
        ?><label for="<?php echo $id; ?>"><?php _e($title . $suffix); ?></label><?php
    }

    public function labeledInput($instance, $name, $title, $defaultValue = null, $attrs = null)
    {
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : '', 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php $this->inputField(
                $this->get_field_id($name),
                $this->get_field_name($name),
                $value, 'text', 'widefat', $attrs);?>
        </p>
    <?php
    }

    private static $added_color_script;

    public function labeledColor($instance, $name, $title, $defaultValue = null){
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : '', 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php
            if (!self::$added_color_script) {
                new FieldScriptRenderer(null, '(function ($) {
                $(function () {
                    $(".color-input").wpColorPicker();
                });
            })(jQuery);');
                self::$added_color_script = true;
            }
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            ?><input type="text" name="<?php echo $this->get_field_name($name) ?>" value="<?php echo esc_attr($value) ?>" class="color-input"/>
        </p>
        <?php
    }

    public function labeledMedia($instance, $name, $title, $defaultValue = null){
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : '', 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php
            MediaField::buildField($this->get_field_name($name), $value);
            ?>
        </p>
    <?php
    }

    public function labeledSelect($instance, $name, $title, $options, $defaultValue = null, $attrs = null)
    {
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : '', 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php $this->selectField(
                $this->get_field_id($name),
                $this->get_field_name($name),
                $value, $options, 'widefat', $attrs);?>
        </p>
    <?php
    }

    public function labeledPageSelect($instance, $name, $title, $defaultValue = null, $attrs = null)
    {
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : '', 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php $this->pageSelectField(
                $this->get_field_id($name),
                $this->get_field_name($name),
                $value, 'widefat', $attrs);?>
        </p>
    <?php
    }

    private function isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function pageSelectField($id, $name, $value, $class = null, $attrs = null)
    {
        $q = new WP_Query(array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'orderby'=>'title')
        );
        global $post;
        $pages = array();
        while ($q->have_posts()) {
            $q->the_post();
            $pages[get_the_ID()] = $post;
        }
        $ordered = get_page_hierarchy($pages);
        ?>
        <select name="<?php echo $name ?>" id="<?php echo $id ?>" <?php $this->outputAttibutes($attrs)?>
                <?php if (!empty($class)){?>class="<?php echo $class?>" <?php }?>
            >
            <option value=''>[Select Page]</option>
            <?php
            $stack = array();
            foreach ($ordered as $id=>$name) {
                $page = $pages[$id];
                while (count($stack)>0 && $page->post_parent != end($stack)){
                    array_pop($stack);
                }
                $stack[] = $id;
                ?>
                <option value='<?php echo $id ?>'
                <?php if ($value == 'page_id='.$id || $value == $id){?>
                    selected
                <?php }?>><?php
                    for($i=1;$i<count($stack);$i++){
                        echo '&mdash;';
                    }
                ?><?php echo $page->post_title?> (<?php echo get_permalink($id)  ?>)</option><?php
            }
            ?>
        </select>
    <?php
    }

    public function selectField($id, $name, $value, $options, $class = null, $attrs = null){
        $is_assoc =$this->isAssoc($options);
        ?> <select name="<?php echo $name ?>" id="<?php echo $id ?>" <?php $this->outputAttibutes($attrs)?>
        <?php if (!empty($class)){?>class="<?php echo $class?>" <?php }?>
        ><option value=''></option>
        <?php
        foreach($options as $key=>$text){
            if (!$is_assoc){
                $key = $text;
            }
            ?><option value='<?php echo esc_attr($key) ?>'<?php if ($value == $key) {?> selected <?php }?>><?php echo htmlentities($text)?></option><?php
        }
        ?></select><?php
    }

    public function inputField($id, $name, $value, $type, $class, $attrs = null)
    {
        ?>
        <input class="<?php echo $class ?>"
               id="<?php echo $id; ?>"
               type="<?php echo $type ?>"
               name="<?php echo $name; ?>" type="text"
               value="<?php echo esc_attr($value); ?>"
            <?php $this->outputAttibutes($attrs);?>/>
    <?php
    }

    private function outputAttibutes($attrs){
        if ($attrs){
            if (is_array($attrs)){
                foreach($attrs as $name=>$val){
                    echo ' ';
                    echo $name;
                    echo '="';
                    echo esc_attr($val);
                    echo '"';
                }
            }
            else{
                echo $attrs;
            }
        }
    }

    public function labeledNumber($instance, $name, $title, $defaultValue = null)
    {
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : 0, 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php $this->inputField(
                $this->get_field_id($name),
                $this->get_field_name($name),
                $value, 'number', '');?>
        </p>
    <?php
    }

    public function labeledEditor($instance, $name, $title, $defaultValue = null)
    {
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : '', 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php $this->editorField(
                $this->get_field_id($name),
                $this->get_field_name($name),
                $value);
            ?>
        </p>
    <?php
    }

    public function editorField($id, $name, $value, $attrs=null)
    {
        ?>
        <a
            data-target-editor='<?php echo $id ?>'
            class='button button-primary'
            style='float:right'
            href='#'
            onclick='WPEditorWidget.showEditor(jQuery(this).data("target-editor"));return false;'><?php _e('Edit content', ipso_Widget_ShortCode::TEXTDOMAIN) ?></a>
        <textarea rows="10" class='widefat' id="<?php echo $id ?>" <?php $this->outputAttibutes($attrs);?>
                  name="<?php echo $name ?>"><?php
            echo esc_textarea($value);
            ?></textarea>
    <?php
    }


    private static $added_image_script = false;

    public function labeledImage($instance, $name, $title, $defaultValue = null)
    {
        if (!ipso_Widget_Base::$added_image_script) {
            ipso_Widget_Base::$added_image_script = true;
            wp_enqueue_script('imageField', plugins_url('imageField.js', __FILE__), array('jquery'), 1);
        }
        $value = isset($instance[$name]) ? $instance[$name] : __(!empty($defaultValue) ? $defaultValue : '', 'text_domain');
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
        <?php $this->imageField($this->get_field_id($name), $this->get_field_name($name), $value)?>
        </p>
    <?php
    }

    public function checkBoxInput($instance, $name, $title)
    {
        $value = isset($instance[$name]);
        ?>
        <p>
            <?php $this->printLabel($name, $title); ?>
            <?php $this->checkboxField($this->get_field_id($name), $this->get_field_name($name), $value); ?>
        </p>
    <?php

    }

    public function checkboxField($id, $name, $value, $checkedValue = 'on')
    {
        ?>
        <input id="<?php echo $id; ?>"
               name="<?php echo $name; ?>"
               type="checkbox"
               value="<?php echo $checkedValue ?>"
            <?php if ($value == $checkedValue) echo " checked" ?>
            />
    <?php
    }

    public function linkField($id, $name, $value)
    {
        wp_enqueue_script('ipso-link-field', plugins_url('../LinkField.js', __FILE__), array('wplink'), 1);
        ?><input
        type="text"
        id="<?php echo $id ?>"
        name="<?php echo $name ?>"
        value="<?php echo $value ?>"/>
        <button class='open-link-dialog-button'
                data-field-id="<?php echo $id?>"
                id="<?php echo $id ?>_button">Select
        </button>
    <?php
    }

    public function imageField($id, $name, $value)
    {
        new FieldScriptRenderer(__DIR__ . '/../custom_image_field_client_side.php');
        ?>
        <div id="dynamic_form">
            <div class="field_row">
                <div class="image_wrap">
                    <?php
                    if (!empty($value)) {
                        ?><img src="<?php echo esc_attr($value) ?>"/><?php
                    }
                    ?>
                </div>
                <div class="field_wrap">
                    <input
                        class="meta_image_url"
                        value="<?php echo esc_attr($value) ?>"
                        type="text"
                        name="<?php echo $name ?>"
                        id="<?php echo $id ?>"/><br/>
                    <input id="add-image-button" type="button" class="button" value="Choose File"/>
                    <input id="clear-image-button" type="button" class="button" value="Clear"/>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    <?php
    }

    public function updateFields($fields, $new_instance)
    {
        $instance = array();
        foreach ($fields as $field) {
            $instance[$field] = (!empty($new_instance[$field])) ? strip_tags($new_instance[$field]) : '';
        }
        return $instance;
    }

    public function updateNonEmptyArray($source, &$target, $field){
        $items = array();
        foreach($source[$field] as $item){
            $has_value = false;
            foreach($item as $key=>$val){
                if (!empty($val)){
                    $has_value = true;
                    break;
                }
            }
            if ($has_value){
                $items[] = $item;
            }
        }
        $target[$field] = $items;
    }

}


class WidgetHelper{
    public static function registerIncludes(){
        wp_register_script('wp-editor-widget-js', plugins_url('assets/js/admin-shortcode.js', __DIR__), array('jquery', 'jquery-ui-droppable', 'jquery-ui-draggable'), "1.0", true);
        wp_enqueue_script('wp-editor-widget-js');

        wp_register_style('wp-editor-widget-css', plugins_url('assets/css/admin-shortcode.css', __DIR__), array(), "1.0");
        wp_enqueue_style('wp-editor-widget-css');
    }

    public static function sharedEditor(){
        ?>
        <div id="wp-editor-widget-container" style="display: none;">
            <a class="close" href="javascript:WPEditorWidget.hideEditor();"
               title="<?php esc_attr_e('Close', 'ipso') ?>"><span class="icon"></span></a>

            <div class="editor">
                <?php
                wp_editor('', 'wp-editor-widget');
                ?>
                <p>
                    <a href="javascript:WPEditorWidget.hideEditor();"
                       class="button "><?php _e('Cancel', 'ipso') ?></a>
                    <a href="javascript:WPEditorWidget.updateWidgetAndCloseEditor();"
                       class="button button-primary"><?php _e('Update', 'ipso') ?></a>
                </p>
            </div>
        </div>
        <div id="wp-editor-widget-backdrop" style="display: none;"></div>
    <?php

    }
    public static function buildWidget($class, $id, $name, $data)
    {
        $widget = new $class();
        $widget->id = 'temp';
        $widget->number = '{$id}';

        ob_start();
        $widget->form($data);
        $form = ob_get_clean();

        // Convert the widget field naming into ones that panels uses
        $exp = preg_quote($widget->get_field_name('____'));
        $exp = str_replace('____', '(.*?)', $exp);
        $form = preg_replace('/' . $exp . '/', $name . '[instance][$1]', $form);

        $exp = preg_quote($widget->get_field_id('____'));
        $exp = str_replace('____', '(.*?)', $exp);
        $form = preg_replace('/' . $exp . '/', $id . '_instance__$1_', $form);

        $widget->form = $form;
        return $widget;
    }

    public static function createWidgetList($id, $name, $value, $args = array()){
        global $wp_widget_factory;
        $args = array_merge(array('onchange'=>''), $args);
        ?><select id="<?php echo $id ?>__class_" class="widget-list" name="<?php echo $name ?>[class]"
        data-base-name="<?php echo $name?>"
                  data-base-id="<?php echo $id?>"
                  onchange="<?php echo $args['onchange']?>">
        <option value="">Select a Widget</option><?php
        $selected_widget = null;
        $widgets = $wp_widget_factory->widgets;
        uasort($widgets, array('WidgetHelper', 'sort_widgets') );
        foreach ($widgets as $wc => $info) {
            $class_info = new ReflectionClass($wc);
            if ($class_info->isSubclassOf('ipso_Widget_Container')) {
                continue;
            }
            $selected = $wc == $value;
            ?>
            <option value="<?php echo $wc ?>"
            <?php if ($selected) :
                echo 'selected';
            endif;?>><?php echo $info->name ?></option><?php
        }
        ?></select><?php
    }

    public static function sort_widgets($a, $b){
        if ($a->name == $b->name){
            return 0;
        }
        return $a->name > $b->name ? 1 : -1;
    }
}

abstract class ipso_Widget_Container extends ipso_Widget_Base
{

    public function labeledWidget($instance, $id, $name, $label)
    {
        global $wp_widget_factory;
        $data = array_merge(array('class' => '', 'instance' => array()), $instance);
        asort($data);
        ?>
        <span style="display:inline-block; vertical-align: top; margin-top: 5px; margin-right: .5em;">
        <?php
        $this->label($id . '__class_', $label);
        ?></span><?php
        WidgetHelper::createWidgetList($id, $name, $data['class'], array('onchange'=>"jQuery(this).parents('form').find('input[name=savewidget]').click()"));
        if (!empty($data['class'])) {
            $w = WidgetHelper::buildWidget($data['class'], $id, $name, $data['instance']);
            ?>
            <br/>
            <span class="description"><?php echo $w->widget_options['description'] ?></span>
            <div class='widget'>
                <div class='widget-top' style='cursor:pointer;padding: 10px 10px 0 10px;'>
                    <div style="float:right;" class='button'
                         onclick='return false;'>
                        Edit
                    </div>
                    <div class='widget-title'><?php echo $w->name ?></div>
                    <br/>
                </div>
                <div class='widget-inside'><?php echo $w->form;
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
        <hr/>
    <?php
    }
}

abstract class ipso_Widget_List extends ipso_Widget_Container{
    protected function render_widgets($args, $instance){
        $widgets = isset($instance['widgets']) ? $instance['widgets'] : array();

        foreach ($widgets as $index => $w) {
            if (empty($w['class'])) continue;
            $class = $w['class'];
            $values = $w['instance'];

            $widget = new $class();

            $widget->widget(array(
                'before_widget' => $this->get_before_widget($args, $instance, $widget, $index),
                'after_widget' => $this->get_after_widget($args, $instance, $widget, $index),
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>'
            ), $values);
        }
    }

    protected function get_before_widget($args, $instance, $widget, $index){
        $id = "{$widget->id_base}_$index";
        $class_name = $widget->widget_options['classname'];
        return sprintf('<div id="%1$s" class="widget %2$s">', $id, $class_name);
    }

    protected function get_after_widget($args, $instance, $widget, $index){
        return "</div>";
    }

    protected function render_widgets_form($instance){
        $widgets = isset($instance['widgets']) ? $instance['widgets'] : array();
        $cellCount = count($widgets);

        ?><span class='button button-primary' style="float:right;margin-bottom: 4px;"
                onclick="var m=jQuery(this).find('input');m.attr('name', '<?php echo $this->get_field_name('cell_count') ?>');m.parents('form').find('input[name=savewidget]').click(); return false;"><input
            type='hidden'
            value='<?php echo($cellCount + 1) ?>'/>Add Cell</span><?php

        for ($i = 0; $i < $cellCount; $i++) {
            $item = isset($widgets[$i]) ? $widgets[$i] : array();
            $num = $i + 1;
            if (isset($item['class'])) {
                if ($i > 0) {
                    ?><span class='button'
                            onclick='var m=jQuery(this),f=m.parents("form");f.find(".move-up").val(<?php echo $i ?>);f.find("input[name=savewidget]").click();'>&uarr;</span><?php
                }
                if ($num < $cellCount) {
                    ?><span class='button'
                            onclick='var m=jQuery(this),f=m.parents("form");f.find(".move-down").val(<?php echo $i ?>);f.find("input[name=savewidget]").click();'>&darr;</span><?php
                }
            }
            $this->before_labeled_widget_form($num-1, $item, $instance);
            $this->labeledWidget($item,
                $this->get_field_id('widgets') . "_{$i}",
                $this->get_field_name('widgets') . "[$i]",
                "Cell #{$num}");
            $this->after_labeled_widget_form($num-1, $item, $instance);
        }
    }

    protected function before_labeled_widget_form($num, $item, $instance){

    }

    protected function after_labeled_widget_form($num, $item, $instance){

    }

    protected abstract function update_additional_fields($new_instance, $old_instance);


    public function update($new_instance, $old_instance)
    {
        $instance = $this->update_additional_fields($new_instance, $old_instance);
        $widgets = $new_instance['widgets'];
        $new_widgets = array();
        if (isset($new_instance['move_down']) && $new_instance['move_down'] !== '') {
            $from = $new_instance['move_down'];
            $to = intval($from) + 1;
            $w = $widgets[$from];
            $widgets[$from] = $widgets[$to];
            $widgets[$to] = $w;
        } elseif (isset($new_instance['move_up']) && $new_instance['move_up'] !== '') {
            $from = $new_instance['move_up'];
            $to = intval($from) - 1;
            $w = $widgets[$from];
            $widgets[$from] = $widgets[$to];
            $widgets[$to] = $w;
        }
        foreach ($widgets as $w) {
            if (!empty($w['class'])) {
                $new_widgets[] = $w;
            }
        }
        if (!empty($new_instance['cell_count'])) {
            while ($new_instance['cell_count'] > count($new_widgets)) {
                $new_widgets[] = array();
            }
        }
        $instance['widgets'] = $new_widgets;
        return $instance;
    }
}