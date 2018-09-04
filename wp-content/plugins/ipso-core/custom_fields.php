<?php
require_once(__DIR__ . '/custom_fields/color_picker.php');

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 8/22/13
 * Time: 9:15 AM
 * To change this template use File | Settings | File Templates.
 */
class CustomPostTypeSectionRenderer
{
    var $custom_post_type;
    var $section_name;

    function __construct($custom_post_type, $section_name)
    {
        $this->custom_post_type = $custom_post_type;
        $this->section_name = $section_name;
    }

    function renderFields()
    {
        $this->custom_post_type->renderFields($this->section_name);
    }
}

class CustomProductTabRenderer
{
    /**
     * @var CustomPostType $custom_post_type ;
     */
    var $custom_post_type;
    var $section;
    var $section_name;

    function __construct($custom_post_type, $section)
    {
        $this->custom_post_type = $custom_post_type;
        $this->section = $section;
        $this->section_name = $section['id'];

        $this->add_hooks();
    }

    function add_hooks()
    {
        add_action('woocommerce_product_write_panel_tabs', array($this, 'product_write_panel_tab'));
        add_action('woocommerce_product_write_panels', array($this, 'product_write_panel'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }

    function admin_enqueue_scripts()
    {
        wp_enqueue_style('ipso_admin_products');
    }

    /**
     * Adds a new tab to the Product Data postbox in the admin product interface
     */
    public function product_write_panel_tab()
    {
        echo "<li class=\"ipso_custom_tab\"><a href=\"#{$this->section_name}\">" . __($this->section['label']) . "</a></li>";
    }

    /**
     * Adds the panel to the Product Data postbox in the product interface
     */
    public function product_write_panel()
    {
        ?>
    <div id="<?php echo $this->section_name ?>" class="panel woocommerce_options_panel ipso_custom_panel">
        <?php
        $this->custom_post_type->renderFields($this->section_name);
        ?></div><?php
    }

}

class CustomTaxonomy
{
    public $taxonomy;

    function __construct($tax)
    {
        global $wpdb;
        $this->taxonomy = $tax;

        $table_name = $this->taxonomy . 'meta';
        $wpdb->$table_name = $wpdb->prefix . $table_name;
        require_once(__DIR__ . '/lib/EditHelpers.php');
    }

    static function create_metadata_table($type)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $type . 'meta';

        $charset_collate = '';
        if (!empty ($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        if (!empty ($wpdb->collate))
            $charset_collate .= " COLLATE {$wpdb->collate}";

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        meta_id bigint(20) NOT NULL AUTO_INCREMENT,
        {$type}_id bigint(20) NOT NULL default 0,

        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext DEFAULT NULL,

        UNIQUE KEY meta_id (meta_id)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    function addCustomField($name, $type, $label = '', $description = null)
    {
        return new CustomTaxonomyField($this->taxonomy, $name, $type, $label, $description);
    }
}

class WoocommerceTaxonomyField extends CustomTaxonomyField
{
    function save_form_fields($term_id)
    {
        if (isset($_POST[$this->name])) {
            $value = $_POST[$this->name];
            $result = update_woocommerce_term_meta($term_id, $this->name, $value);
            if ($result === false) {
                //This may be because the custom table has not been created.  Use create_metadata_table to create if needed!
            }
        }
    }

    function create_form_fields($term_id)
    {
        if (isset($_POST[$this->name])) {
            $value = $_POST[$this->name];
            $result = update_woocommerce_term_meta($term_id, $this->name, $value);
            if ($result === false) {
                //This may be because the custom table has not been created.  Use create_metadata_table to create if needed!
            }
        }
    }

    function get_value($tag)
    {
        return get_woocommerce_term_meta($tag->term_id, $this->name, true);
    }
}

class CustomTaxonomyField
{
    public $name;
    public $type;
    public $label;
    public $taxonomy;
    public $description;

    function __construct($taxonomy, $name, $type, $label, $description)
    {
        $this->taxonomy = $taxonomy;
        $this->name = $name;
        switch ($type) {
            case 'color':
                require_once(__DIR__ . '/custom_fields/color_picker.php');
                $type = ColorCodeField::getInstance();
                break;
        }
        $this->type = $type;
        $this->label = $label;
        $this->description = $description;

        add_action($taxonomy . '_edit_form_fields', array($this, 'edit_form_fields'), 10, 2);
        add_action($taxonomy . '_add_form_fields', array($this, 'add_form_fields'), 10, 2);
        add_action('edited_' . $taxonomy, array($this, 'save_form_fields'), 10, 2);
        add_action('create_' . $taxonomy, array($this, 'create_form_fields'), 10, 2);
    }

    function get_value($tag)
    {
        return get_metadata($tag->taxonomy, $tag->term_id, $this->name, true);
    }

    function edit_form_fields($tag, $tax)
    {
        $value = $this->get_value($tag);
        ?>
        <tr class="form-field">
        <th scope="row" valign="top"><label><?php echo $this->label ?></label></th>
        <td>
            <?php $this->field($value); ?>
        </td>
        </tr><?php
    }

    function field($value)
    {
        if (is_string($this->type)) {
            switch ($this->type) {
                case 'wysiwyg':
                    wp_editor($value, $this->name);
                    break;
                case 'date':
                case 'time':
                case 'text':
                case 'phone':
                case 'email':
                case 'number':
                case 'hidden':
                    ?><input type="<?php echo $this->type ?>" name="<?php echo $this->name ?>"
                             value="<?php echo esc_attr($value) ?>"/><?php
                    break;
                case 'checkbox':
                    echo '<input type="checkbox" name="' . $this->name . '"  ', !$value ? ' checked="checked"' : '', ' value="0" style="display:none;"/>';
                    echo '<input type="checkbox" name="' . $this->name . '" id="' . $this->name . '" ', $value ? ' checked="checked"' : '', ' onclick="this.previousSibling.checked=!this.checked;" value="1" style="width:auto;margin-top:0;float:left;clear:left;"/>
        <label for="' . $this->name . '" style="float:left;padding-top:0;padding-bottom:4px;">' . $this->description . '</label>';
                    break;
                case 'image':
                    add_thickbox();
                    FieldHelper::imageField($this->name, $this->name, $value);
                    break;
                case 'media':
                    MediaField::buildField($this->name, $value);
                    break;
            }
        } else {
            $this->type->renderField(null, $this, $value);
        }?>
        <?php if (!empty($this->description) && $this->type != 'checkbox') {
        ?><p class="description"><?php echo $this->description ?></p><?php
    }

    }

    function add_form_fields()
    {
        ?>
        <div class="form-field">
        <label><?php echo $this->label ?></label>
        <?php $this->field(''); ?>
        </div><?php
    }

    function save_form_fields($term_id)
    {
        if (isset($_POST[$this->name])) {
            $value = $_POST[$this->name];
            $old_value = get_metadata($this->taxonomy, $term_id, $this->name, true);
            $result = update_metadata($this->taxonomy, $term_id, $this->name, $value, $old_value);
            if ($result === false) {
                //This may be because the custom table has not been created.  Use create_metadata_table to create if needed!
            }
        }
    }

    function create_form_fields($term_id)
    {
        if (isset($_POST[$this->name])) {
            $value = $_POST[$this->name];
            $result = add_metadata($this->taxonomy, $term_id, $this->name, $value);
            if ($result === false) {
                //This may be because the custom table has not been created.  Use create_metadata_table to create if needed!
            }
        }
    }

}

class MediaField
{
    static function buildField($name, $value)
    {
        $blank = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=';
        wp_enqueue_media();
        wp_enqueue_script('media-dialog');
        wp_enqueue_script('media-field');
        $re = '/\.(gif|jpeg|jpg|png)$/';
        $src = $blank;
        $nme = '';
        $id = preg_replace("/[^a-zA-Z0-9]/", "_", $name);
        if (!empty($value)) {
            $img = wp_get_attachment_image_src($value);
            if (!empty($img) && preg_match($re, $img[0]) !== false) {
                $src = $img[0];
            }
            $p = get_post($value);
            if (is_a($p, 'WP_Post')) {
                $nme = $p->post_name;
            }

        }
        ?><span class="media-field"><input type="hidden" name="<?php echo $name ?>" id="<?php echo $id?>_hidden" value="<?php echo $value ?>" class="hidden-field"/>
        <span style="overflow:hidden;display:block;">
        <span class="image-area"
            id="<?php echo $id ?>_img" style="margin-right:5px; float:left;">
            <label><?php echo $nme ?></label><br/><img src='<?php echo esc_attr($src) ?>' width='60' height='60'/></span>
        <a class='button add-button' id="<?php echo $id ?>_add">Upload/Add</a> <a class="button clear-button" id="<?php echo $id ?>_clear">Remove</a>
        </span>
    </span>
        <?php
    }
}

class CustomMediaFieldRenderer implements IFieldRenderer
{
    private static $instance;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new CustomMediaFieldRenderer();
        }
        return self::$instance;
    }

    /**
     * @param int $post_id
     * @param object $field object
     * @param string $value
     * @return void
     */
    function renderField($post_id, $field, $value)
    {
        MediaField::buildField($field->id, $value);
    }
}

interface IFieldRenderer
{
    /**
     * @param int $post_id
     * @param object $field object
     * @param string $value
     * @return void
     */
    function renderField($post_id, $field, $value);
}

interface IFieldProcessor
{
    function processField($meta);
}

interface IFieldProcessorByReference
{
    function processField($post_id, &$meta, $post_data, $is_update);
}

abstract class RepeatingSortableField implements IFieldRenderer, IFieldProcessor
{
    private static $registered_script;
    private $class_name;

    static function admin_enqueue_scripts($hook)
    {
        if (!in_array($hook, array('post-new.php', 'post.php', 'media-upload-popup')) || self::$registered_script)
            return;
        self::$registered_script = true;
        wp_enqueue_script('repeating.sortable.fields', plugins_url('/custom_fields/repeating.sortable.field.js', __FILE__), array('jquery'));
    }

    protected function __construct($class_name = null)
    {
        if (empty($class_name)){
            $class_name = get_class($this);
        }
        $this->class_name = $class_name;
        add_action("wp_ajax_{$class_name}_new_section", array($this, 'ajax_new_section'));
        add_action('admin_enqueue_scripts', array('RepeatingSortableField', 'admin_enqueue_scripts'));
    }

    public function renderField($post_id, $field, $meta)
    {
        $this->beforeRenderField($post_id, $field, $meta);
        if (!is_array($meta)) {
            if (empty($meta)){
                $meta = array();
            }
            else {
                try {
                    $meta = @unserialize($meta);
                } catch (Exception $ex) {
                    $meta = array();
                }
            }
        }
        ?>
        <div class="sortable-sections"><?php
        $i = 0;
        foreach ($meta as $value) {
            if ($this->has_value($value)) {
                $this->doRenderOneItem($post_id, $field, $i, $value);
                $i++;
            }
        }
        ?></div><?php
        $this->afterRenderField($post_id, $field, $meta);
    }

    public function ajax_new_section()
    {
        $post_id = $_POST['post_id'];
        $field = (object)$_POST['field'];
        $index = $_POST['index'];
        $this->doRenderOneItem($post_id, $field, $index, array());
        die();
    }

    private function doRenderOneItem($post_id, $field, $i, $value)
    {
        $id = $this->getFieldID($field, $i);
        $name = $this->getFieldName($field, $i);
        $this->beforeOneItem($field, $i, $value);
        $this->renderOneItem($post_id, $id, $name, $value);
        $this->afterOneItem($field, $i, $value);
    }

    protected function beforeOneItem($field, $i, $value)
    {
        ?>
        <div class="field-section"
        data-section-index="<?php echo $i ?>"><input
        type="hidden"
        name="<?php echo $field->id ?>[<?php echo $i ?>][index]"
        class="index-value"
        value="<?php echo $i ?>"><a
        onclick="if (!window.confirm('Remove this section?')) return;var s = jQuery(this).parent();s.remove()"
        style="border:solid 1px #000;background-color:#f00;position: absolute; right:10px; top: 3px;font-family:Tahoma;padding:3px 5px;font-size: 8px;color:#fff;">X</a><?php
    }

    protected function afterOneItem($field, $i, $meta)
    {
        ?></div><?php
    }

    protected function getFieldID($field, $i)
    {
        return "{$field->id}_{$i}_";
    }

    protected function getFieldName($field, $i)
    {
        return "{$field->id}[{$i}]";
    }

    protected function beforeRenderField($post_id, &$field, &$meta)
    {
        if (!empty($field->desc)) {
            ?>
            <p class='description'><?php echo $field->desc ?></p>
        <?php
        }
    }

    protected function afterRenderField($post_id, &$field, &$meta)
    {
        $index = count($meta) + 1;
        $field_info = json_encode($field);
        echo "<a href='#add' class='add-a-section button button-primary' style='margin-top: 10px;' data-post_id='{$post_id}' data-field='{$field_info}' data-class='{$this->class_name}' data-index='{$index}' >Add</a>";
    }

    abstract function renderOneItem($post_id, $field_id, $field_name, $value);

    protected function beforeProcessField(&$meta)
    {

    }

    protected function afterProcessField(&$meta)
    {

    }

    /**
     * @param $v
     * @return bool
     */
    abstract function has_value($v);

    public function processField($meta)
    {
        if (is_array($meta)) {
            $this->beforeProcessField($meta);
            $meta = array_filter($meta, array($this, 'has_value'));
            uasort($meta, array($this, 'sort_by_index'));
            $this->afterProcessField($meta);
        }
        return $meta;
    }

    private function sort_by_index($a, $b)
    {
        $v1 = isset($a['index']) ? $a['index'] : 0;
        $v2 = isset($b['index']) ? $b['index'] : 0;
        if ($v1 == $v2) {
            return 0;
        }
        return ($v1 < $v2) ? -1 : 1;
    }
}

class WidgetFieldRenderer extends RepeatingSortableField
{

    public function __construct(){
        add_action('wp_ajax_widget_form', array($this, 'wp_ajax_widget_form'));
        parent::__construct();
    }

    /** @var  $instance WidgetFieldRenderer */
    public static $instance;

    static function init()
    {
        self::$instance = new WidgetFieldRenderer();
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    protected function beforeRenderField($post_id, &$field, &$meta)
    {
        parent::beforeRenderField($post_id, $field, $meta);
        add_action('admin_footer', array('WidgetHelper', 'sharedEditor'));
        ?><p class="description">Choose the type of widget from the list and add any custom
        content.</p></td></tr><tr><td colspan='2'><?php
    }

    public function renderOneItem($post_id, $widget_id, $widget_name, $widget)
    {
        $widget = array_merge(array('class' => ''), $widget);
        ?>
        <div class="widget-section-header"><?php
            echo WidgetHelper::createWidgetList($widget_id, $widget_name, $widget['class']);
            ?></div>
        <div class='widget-section-body'><?php
            $this->widget_form($widget_id, $widget_name, $widget);
            ?></div>
    <?php
    }

    function has_value($v)
    {
        return is_array($v)
        && !empty($v['class']);
    }

    /**
     * @param $widget_id
     * @param $widget_name
     * @param $widget
     */
    private function widget_form($widget_id, $widget_name, $widget)
    {
        if (!empty($widget['class'])) {
            $w = WidgetHelper::buildWidget($widget['class'], $widget_id, $widget_name, empty($widget['instance']) ? array() : $widget['instance']);
            echo $w->form;
        }
    }

    public function wp_ajax_widget_form(){
        $this->widget_form($_POST['widget_id'], $_POST['widget_name'], $_POST['widget']);
        die;
    }
}

WidgetFieldRenderer::init();

add_action('admin_init', array('CustomPostType', 'register_admin_scripts'));

class ipso_Support_Remover{
    private $support;
    private $post_type;
    function __construct($post_type, $support){
        $this->post_type = $post_type;
        $this->support = $support;
        add_action('admin_init', array($this, 'admin_init'));
    }

    function admin_init(){
        if (is_array($this->support)) {
            foreach ($this->support as $feature) {
                remove_post_type_support($this->post_type, $feature);
            }
        } else {
            remove_post_type_support($this->post_type, $this->support);
        }
    }
}

class CustomPostType
{
    public $id;
    public $post_type;
    public $labels;
    public $has_archive = true;
    public $is_public = true;
    private $custom_fields = array();
    private $sections = array();

    /*
     * array(
'name' => '',
'singular_name' => '',
'menu_name' => '',
'all_items' => '',
'add_new' => '',
'add_new_item' => '',
'edit_item' => '',
'new_item' => '',
'view_item' => '',
'search_items' => '',
'not_found' => '',
'not_found_in_trash' => '',
'parent_item_colon' => '')
         * */
    private $post_type_args = null;

    const DO_NOT_REGISTER = 'do_not_register';

    static function register_admin_scripts()
    {
        wp_register_script('ipso-core-datepicker', plugins_url('ipso-core/custom_fields/datepicker-init.js'), array('jquery-ui-datepicker'), 1, false);
        wp_register_style('ipso-core-jquery-ui', 'http://code.jquery.com/ui/1.9.2/themes/overcast/jquery-ui.css');
    }

    function __construct($post_type, $labels = array(), $post_type_args = null)
    {
        $this->id = $post_type . '_edit';
        $this->post_type = $post_type;
        $this->labels = $labels;
        $this->post_type_args = $post_type_args;

        $this->addDefaultSection();

        add_action('init', array($this, 'registerPostType'));
        add_action('add_meta_boxes_' . $this->post_type, array($this, 'addMetaBox'), 1, 5);
        if (get_bloginfo('version') > 3.7) {
            add_action('save_post_' . $this->post_type, array($this, 'save'), 10, 3);
        } else {
            add_action('save_post', array($this, 'save'), 10, 3);
        }
        add_filter('manage_' . $post_type . '_posts_columns', array($this, 'addColumnHeaders'));
        add_action('manage_' . $post_type . '_posts_custom_column', array($this, 'addColumn'), 10, 2);
        add_action('manage_edit-' . $post_type . '_sortable_columns', array($this, 'getSortableColumns'));
    }

    public function setDoNotRegister($should_register = false)
    {
        if ($this->post_type_args == null) {
            $this->post_type_args = array();
        }
        $this->post_type_args[self::DO_NOT_REGISTER] = 1;
    }

    private $columns;

    function addColumnHeaders($defaults)
    {
        if ($this->columns == null) {
            $this->columns = array();
            foreach ($this->custom_fields as $section => $fields) {
                foreach ($fields as $field) {
                    if (!empty($field->args) && !empty($field->args['show_column'])) {
                        $defaults[$field->id] = $field->label;
                        $this->columns[$field->id] = $field;
                    }
                }
            }
        } else {
            foreach ($this->columns as $field) {
                $defaults[$field->id] = $field->label;
            }
        }
        return $defaults;
    }

    function getSortableColumns()
    {
        $arr = array();
        foreach ($this->columns as $id => $field) {
            $arr[$id] = $id;
        }
        return $arr;
    }

    function addColumn($column_name, $post_ID)
    {
        if (!empty($this->columns[$column_name])) {
            $field = $this->columns[$column_name];
            $value = get_post_meta($post_ID, $field->id, true);
            if (!empty($field->args['value_formatter'])) {
                $value = call_user_func_array($field->args['value_formatter'], array($value));
            }
            echo $value;
        }
    }

    function addDefaultSection()
    {
        $labels = (object)$this->getLabels();
        $defaultSectionLabel = $labels->singular_name . ' Fields';
        $this->addSection('default', $defaultSectionLabel, 'normal', 'high');
    }

    /**
     * @param $id
     * @param $label
     * @param $context - ('normal', 'advanced', or 'side')
     * @param $priority - ('high', 'core', 'default' or 'low')
     */
    function addSection($id, $label, $context, $priority = 'default')
    {
        if (isset($this->custom_fields[$id])) {
            return;
        }
        $this->custom_fields[$id] = array();
        $this->sections[$id] = array(
            'id' => $id,
            'label' => $label,
            'context' => $context,
            'priority' => $priority
        );
    }

    function getLabels()
    {
        $labels = array(
            'singular_label' => $this->post_type,
            'singular_name' => $this->post_type,
            'name' => $this->post_type . 's',
        );
        if ($this->labels != null) {
            $labels = array_merge(
                $labels,
                $this->labels
            );
        }
        $labels = array_merge(
            array(
                'all_items' => 'All ' . $labels['name'],
                'slug' => strtolower($labels['name']),
                'add_new_item' => 'New ' . $labels['singular_label'],
                'edit_item' => 'Edit ' . $labels['singular_label'],
                'new_item' => 'New ' . $labels['singular_label'],
                'view_item' => 'View ' . $labels['singular_label']
            ),
            $labels
        );
        return $labels;
    }

    function registerPostType()
    {
        if ($this->post_type == 'page' || $this->post_type == 'post') {
            return;
        }
        if ($this->post_type_args != null && isset($this->post_type_args[self::DO_NOT_REGISTER])) {
            return;
        }
        $labels = $this->getLabels();
        $args = array(
            'labels' => $labels,
            'public' => $this->is_public,
            'has_archive' => $this->has_archive,
            'rewrite' => array('slug' => isset($labels['slug']) ? $labels['slug'] : $labels['name'])
        );
        if ($this->post_type_args != null) {
            $args = array_merge($args, $this->post_type_args);
        }

        register_post_type($this->post_type, $args);
    }

    /**
     *
     *
     * array('name' => '',
     * 'singular_name' => '',
     * 'menu_name' => '',
     * 'all_items' => '',
     * 'edit_item' => '',
     * 'view_item' => '',
     * 'update_item' => '',
     * 'add_new_item' => '',
     * 'new_item_name' => '',
     * 'parent_item' => '',
     * 'parent_item_colon' => '',
     * 'search_items' => '',
     * 'popular_items' => '',
     * 'separate_items_with_commas' => '',
     * 'add_or_remove_items' => '',
     * 'choose_from_most_used' => '',
     * 'not_found' => '')
     *
     * @param $tax
     * @param array $args
     * @return CustomTaxonomy
     */
    function addTaxonomy($tax, $args = array())
    {
        register_taxonomy($tax, $this->post_type, $args);
        return new CustomTaxonomy($tax, $args);
    }

    function addSupport($support)
    {
        if (is_array($support)) {
            foreach ($support as $feature) {
                add_post_type_support($this->post_type, $feature);
            }
        } else {
            add_post_type_support($this->post_type, $support);
        }
    }

    function removeSupport($support)
    {
        new ipso_Support_Remover($this->post_type, $support);
    }

    /*
     'title', 'editor', 'comments', 'revisions', 'trackbacks', 'author',
* 'excerpt', 'page-attributes', 'thumbnail', and 'custom-fields'*/

    function addThumbnailSupport()
    {
        $this->addSupport('thumbnail');
    }

    function addTitleSupport()
    {
        $this->addSupport('title');
    }

    function addPageAttributeSupport()
    {
        $this->addSupport('page-attributes');
    }

    function addExcerptSupport()
    {
        $this->addSupport('excerpt');
    }

    function addCommentSupport()
    {
        $this->addSupport('comments');
    }

    function addRevisionSupport()
    {
        $this->addSupport('revisions');
    }

    function addTrackbackSupport()
    {
        $this->addSupport('trackbacks');
    }

    function addCustomFieldSupport()
    {
        $this->addSupport('custom-fields');
    }

    function removeTitleSupport(){
        $this->removeSupport('title');
    }

    function removeEditorSupport(){
        $this->removeSupport('editor');
    }

    function addMetaBox($post)
    {
        foreach ($this->sections as $section) {
            if (count($this->custom_fields[$section['id']]) == 0) {
                continue;
            }
            if ($section['context'] == 'woocommerce') {
                new CustomProductTabRenderer($this, $section);
            } else {
                $renderer = new CustomPostTypeSectionRenderer($this, $section['id']);
                add_meta_box(
                    $this->id . '_' . $section['id'],
                    $section['label'],
                    array($renderer, 'renderFields'),
                    $this->post_type,
                    $section['context'],
                    $section['priority']);
            }
        }
    }

    function addField($id, $type, $description, $label, $args = null)
    {
        $this->addFieldToSection('default', $id, $type, $description, $label, $args);
    }

    function addImageField($id, $description, $label, $args = null)
    {
        require_once(__DIR__ . '/custom_fields/multi-post-thumbnails.php');
        $default_args = array(
            'label' => $label,
            'id' => $id,
            'post_type' => $this->post_type
        );
        if ($args == null) {
            $args = $default_args;
        } else {
            $args = array_merge($default_args, $args);
        }
        new MultiPostThumbnails(array_merge($args));
    }

    function addFieldToSection($section, $id, $type, $description, $label, $args = null)
    {
        $this->custom_fields[$section][] = (object)array(
            'id' => $id,
            'type' => $type,
            'desc' => $description,
            'label' => $label,
            'args' => $args
        );
        if ($type == 'video' || $type == 'videos'){
            self::ensure_google_api_option();
        }
    }

    private static $google_api_option_added = false;
    private static function ensure_google_api_option(){
        if (!self::$google_api_option_added){
            self::$google_api_option_added = true;
            new GeneralSetting('_google_api_key','_google_api_key', 'Google API Key<div style="font-size: 11px;">(required for video fields)</div>', 'text');
        }
    }

    function renderField($post_id, $field)
    {
        global $post;
        switch ($field->id) {
            case 'post_content':
                $meta = $post->post_content;
                break;
            case 'post_title':
                $meta = $post->post_title;
                break;
            default:
                $meta = get_post_meta($post_id, $field->id, true);
                break;
        }
        if (is_object($field->type)) {
            $field->type->renderField($post_id, $field, $meta);
            return;
        }
        switch ($field->type) {
            case 'datetime':
            case 'datetime-local':
            case 'month':
            case 'number':
            case 'range':
            case 'search':
            case 'email':
            case 'phone':
            case 'date':
            case 'time':
            case 'url':
            case 'week':
            case 'text':
                $class_name = !empty($field->args) && !empty($field->args['class']) ? $field->args['class'] : 'widefat';
                echo '<input type="' . $field->type . '" name="' . $field->id . '" id="' . $field->id . '" value="' . esc_attr($meta) . '" size="30" class="'.$class_name.'"/>';
                break;
            case 'datepicker':
                wp_enqueue_style('ipso-core-jquery-ui');
                wp_enqueue_style('jquery-ui-datepicker');
                wp_enqueue_script('ipso-core-datepicker');
                echo '<input type="text" name="' . $field->id . '" id="' . $field->id . '" value="' . esc_attr($meta) . '" size="30" class="datepicker"/>';
                break;

            case 'textarea':
                $args = array('cols' => '60', 'rows' => '4', 'style' => '');
                if (!empty($field->args)) {
                    $args = array_merge($args, $field->args);
                }
                echo '<textarea name="' . $field->id . '" id="' . $field->id . '" cols="' . $args['cols'] . '" rows="' . $args['rows'] . '" style="' . $args['style'] . '">' . htmlentities($meta) . '</textarea>';
                break;

            case
            'checkbox':

                echo '<input type="checkbox" name="' . $field->id . '"  ', !$meta ? ' checked="checked"' : '', ' value="0" style="display:none;"/>';
                echo '<input type="checkbox" name="' . $field->id . '" id="' . $field->id . '" ', $meta ? ' checked="checked"' : '', ' onclick="this.previousSibling.checked=!this.checked;" value="1" style="width:auto;margin-top:0;float:left;clear:left;"/>
        <label for="' . $field->id . '" style="float:left;padding-top:0;padding-bottom:4px;">' . $field->desc . '</label>';
                return;

            case 'checkboxes':
                foreach ($field->args['options'] as $idx => $option) {
                    echo '<div><input type="checkbox" name="' . $field->id . '[]" id="' . $field->id . '[' . $idx . ']" ', is_array($meta) && in_array($option['value'], $meta) ? ' checked="checked"' : '', ' value="' . $option['value'] . '"/>
        <label for="' . $field->id . '[' . $idx . ']">' . $option['label'] . '</label></div>';
                }
                break;

            case 'select':
                echo '<select name="' . $field->id . '" id="' . $field->id . '">';
                foreach ($field->args['options'] as $option) {
                    echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
                }
                echo '</select>';
                break;

            case 'tax_select':
                echo '<select name="' . $field->id . '" id="' . $field->id . '">
            <option value="">Select One</option>'; // Select One  
                $terms = get_terms($field->id, 'get=all');
                $selected = wp_get_object_terms($post_id, $field->id);
                foreach ($terms as $term) {
                    if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))
                        echo '<option value="' . $term->slug . '" selected="selected">' . $term->name . '</option>';
                    else
                        echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                }
                $taxonomy = get_taxonomy($field->id);
                echo '</select><br /><span class="description"><a href="' . get_bloginfo('home') . '/wp-admin/edit-tags.php?taxonomy=' . $field->id . '">Manage ' . $taxonomy->label . '</a></span>';
                break;

            case 'image':
                $this->imageField($field, $meta);
                return;

            case 'videos':
                $this->videoSetField($post_id, $field, $meta);
                break;
            case 'video':
                $this->videoField($post_id, $field, $meta);
                break;

            case 'link':
                $this->linkField($post_id, $field, $meta);
                return;
            case 'wysiwyg':
                $this->wysiwygField($field, $meta);
                break;
            case 'color':
                ColorCodeField::getInstance()->renderField($post_id, $field, $meta);
                break;
            case 'gallery':
                GalleryField::getInstance()->renderField($post_id, $field, $meta);
                break;
            case 'widgets':
                WidgetFieldRenderer::getInstance()->renderField($post_id, $field, $meta);
                return;
            case 'media':
                CustomMediaFieldRenderer::get_instance()->renderField($post_id, $field, $meta);
                break;
        }
        if (!empty($field->desc)) {
            echo '<br /><span class="description">' . $field->desc . '</span>';
        }

    }

    private function wysiwygField($field, $meta)
    {
        echo '</td></tr><tr><td colspan="2">';
        $settings = array('media_buttons' => true);
        if (isset($field->args)) {
            $settings = array_merge($settings, $field->args);
        }
        wp_editor($meta, $field->id, $settings);
    }

    private function get_extra_fields($field)
    {
        if (isset($field->args) && isset($field->args['extra'])) {
            return $field->args['extra'];
        } else {
            return array();
        }
    }

    private function videoField($post_id, $field, $meta)
    {
        VideoSetField::videoFieldDirect($post_id, $field->id, $meta, $this->get_extra_fields($field));
        if (!empty($field->desc)) {
            ?>
            <br/>
            <span class='description'><?php echo $field->desc ?></span>
        <?php
        }
    }


    private function videoSetField($post_id, $field, $meta)
    {
        $extra = $this->get_extra_fields($field);
        $renderer = new VideoSetField($extra);
        $renderer->renderField($post_id, $field, $meta);
    }

    private function linkField($post_id, $field, $meta)
    {
        wp_enqueue_script('ipso-link-field', plugins_url('LinkField.js', __FILE__), array('wplink'), 1);
        $simple_link = true;
        if (isset($field->args) && !empty($field->args['full_link'])) {
            $simple_link = !$field->args['full_link'];
        }

        if (!$simple_link) {
            if (is_array($meta)) {
                $meta = array_merge(array(
                        'href' => '',
                        'title' => '',
                        'new_window' => ''),
                    $meta);
            } else {
                $meta = array(
                    'href' => $meta,
                    'title' => '',
                    'new_window' => ''
                );
            }

        }
        ?>
        <div id="dynamic_form">
            <div class="field_row">
                <div class="field_wrap">
                    <?php if ($simple_link) { ?>
                        <input type="text"
                               id="<?php echo $field->id ?>"
                               name="<?php echo $field->id ?>"
                               value="<?php echo esc_attr($meta) ?>"
                               size="30"
                            />
                    <?php } else { ?>
                        <div id="<?php echo $field->id ?>">
                            <label for="<?php echo $field->id ?>_href_">URL</label><br/>
                            <input class="href"
                                   type="text"
                                   id="<?php echo $field->id ?>_href_"
                                   name="<?php echo $field->id ?>[href]"
                                   value="<?php echo esc_attr($meta['href']) ?>"
                                   size="30"/><br/>
                            <label for="<?php echo $field->id ?>_title_">Text</label><br/>
                            <input class="title"
                                   type="text"
                                   id="<?php echo $field->id ?>_title_"
                                   name="<?php echo $field->id ?>[title]"
                                   value="<?php echo esc_attr($meta['title']) ?>"
                                   size="30"/><br/>
                            <input class="target"
                                   type="checkbox"
                                   id="<?php echo $field->id ?>_new_window_"
                                   name="<?php echo $field->id ?>[new_window]"
                                <?php if ($meta['target']) {
                                    echo ' checked';
                                } ?> /><label for="<?php echo $field->id ?>_new_window_">Open in new window</label>
                        </div>
                    <?php } ?>
                    <button id="<?php echo $field->id ?>_button" data-field-id="<?php echo $field->id ?>"
                            class="open-link-dialog-button">Select
                    </button>
                </div>
            </div>
        </div>
    <?php
    }

    private function imageField($field, $meta)
    {
        include_once('custom_image_field_client_side.php');
        ?>
        <div id="dynamic_form">
            <div class="field_row">
                <div class="field_wrap">
                    <input class="meta_image_url" value="<?php echo esc_attr($meta) ?>" type="text"
                           name="<?php echo $field->id ?>"/>
                    <input id="add-image-button" type="button" class="button" value="Choose File"/>
                    <input id="clear-image-button" type="button" class="button" value="Clear"/>

                    <?php if (!empty($field->desc)): ?>
                        <br/>
                        <span class='description'><?php echo $field->desc ?></span>
                    <?php endif ?>
                </div>
                <div class="image_wrap">
                    <?php
                    if (!empty($meta)) {
                        ?><img src="<?php echo esc_attr($meta) ?>"/><?php
                    }
                    ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php
    }

    function renderFields($section_name)
    {
        global $post;
        $fields = $this->custom_fields[$section_name];
        if (count($fields) == 0) {
            return;
        }
        $noncefield = 'custom_meta_box_' . $this->post_type . $section_name . '_nonce';
        $noncename = basename(__FILE__) . $this->post_type . $section_name;
        echo '<input type="hidden" name="' . $noncefield . '" value="' . wp_create_nonce($noncename) . '" />';

        $section = $this->sections[$section_name];
        // Begin the field table and loop
        switch ($section['context']) {
            case 'side':
            case 'woocommerce':
                $format = 'inline';
                break;
            default:
                $format = 'table';
        }
        if ($format == 'table') echo '<table class="form-table">';
        foreach ($fields as $field) {
            // get value of this field if it exists for this post
            // begin a table row with
            if ($format == 'table') {
                echo '<tr>';
                if (empty($field->label)) {
                    echo '<td colspan="2">';
                } else {
                    echo "<th><label for='{$field->id}'>{$field->label}</label></th><td>";
                }
            } else {
                echo "<div class='options_group'><p class='form-field'><label for='{$field->id}'>{$field->label}</label>";
            }
            $this->renderField($post->ID, $field);
            if ($format == 'table') {
                echo '</td></tr>';
            } else {
                echo '</p></div>';
            }
        } // end foreach
        echo '</table>'; // end table
    }

    function checkNonce($section_name)
    {
        $noncefield = 'custom_meta_box_' . $this->post_type . $section_name . '_nonce';
        $noncename = basename(__FILE__) . $this->post_type . $section_name;
        if (empty($_POST[$noncefield])) {
            return false;
        }
        return wp_verify_nonce($_POST[$noncefield], $noncename);
    }

    function isAutosave()
    {
        return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE;
    }

    function checkUserPermission($post_id)
    {
        return current_user_can('edit_page', $post_id);
    }

    function save($post_id, $post, $update)
    {
        foreach ($this->sections as $section) {
            if ($this->checkNonce($section['id'])
                && !$this->isAutosave()
                && $this->checkUserPermission($post_id)
            ) {
                // loop through fields and save the data
                foreach ($this->custom_fields[$section['id']] as $field) {
                    if ($field->id == 'post_content' || $field->id == 'post_title') {
                        continue;
                    }
                    $old_values = get_post_meta($post_id, $field->id);
                    $old = empty($old_values) ? null : $old_values[0];
                    if (isset($_POST[$field->id])) {
                        $new = $_POST[$field->id];
                        if (is_object($field->type)) {
                            $type = $field->type;
                            $implements = class_implements($type);
                            if (isset($implements['IFieldProcessor'])) {
                                $new = $type->processField($new);
                            }
                            if (isset($implements['IFieldProcessorByReference'])) {
                                $type->processField($post_id, $new, $post, $update);
                            }
                        }
                        if ($new != $old) {
                            if (is_null($old)) {
                                add_post_meta($post_id, $field->id, $new);
                            } else {
                                update_post_meta($post_id, $field->id, $new);
                            }
                        } elseif (empty($new) && !empty($old)) {
                            delete_post_meta($post_id, $field->id, $old);
                        }
                    }
                } // end foreach
            }
        }
        return $post_id;
    }
}

class FieldScriptRenderer
{
    public $script;
    public $include;

    function __construct($include, $script = null, $priority = 10)
    {
        $this->include = $include;
        $this->script = $script;
        if (defined('DOING_AJAX')){
            $this->render();
        }
        else {
            add_action('admin_footer', array($this, 'render'), $priority);
        }
    }

    function render()
    {
        if (!empty($this->include)) {
            include_once($this->include);
        }
        if (!empty($this->script)) {
            ?>
            <script><?php echo $this->script; ?></script>
        <?php
        }
    }
}


class FieldHelper
{
    public static function imageField($id, $name, $value)
    {
        new FieldScriptRenderer(__DIR__ . '/custom_image_field_client_side.php');
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
                        id="<?php echo $id ?>"/>
                    <input id="add-image-button" type="button" class="button" value="Choose File"/>
                    <input id="clear-image-button" type="button" class="button" value="Clear"/>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    <?php
    }
}

class VideoSetField extends RepeatingSortableField
{
    private static $instance;
    private $extra_fields;

    function __construct($extra_fields = null)
    {
        $this->extra_fields = $extra_fields;
        parent::__construct();
    }

    public function renderOneItem($post_id, $field_id, $field_name, $value)
    {
        self::videoFieldDirect($post_id, $field_name, $value, $this->extra_fields);
    }

    public function has_value($v)
    {
        return !empty($v['url']);
    }

    public static function videoFieldDirect($post_id, $field_name, $meta, $extra_fields)
    {
        if (!is_array($meta)) {
            $meta = array();
        }
        $meta = array_merge(array('type' => '', 'id' => '', 'thumbnail' => '', 'url' => ''), $meta);
        $field_id = str_replace('[', '_', str_replace(']', '_', $field_name));
        $renderer = new FieldScriptRenderer('custom_video_field_client_side.php', "new VideoField('$post_id', '$field_id', '{$meta['url']}');");
        if (!get_option('_google_api_key')){
            ?><div class="error">Google API Key is missing</div><?php
        }
        ?>
        <div id="dynamic_form">
            <div class="field_row">
                <div class="field_wrap">
                    <?php if (is_array($extra_fields) && count($extra_fields) > 0) {
                        foreach ($extra_fields as $name => $extra) {
                            $attrs = shortcode_atts(array(
                                'label' => $name,
                                'type' => 'text'
                            ), $extra);
                            $value = isset($meta[$name]) ? $meta[$name] : '';
                            ?><label><?php echo $attrs['label'] ?></label><?php
                            switch ($attrs['type']) {
                                case 'text':
                                    ?><input class="meta_video_<?php echo $name ?> widefat" type="text"
                                             name="<?php echo $field_name ?>[<?php echo $name ?>]"
                                             value="<?php echo esc_attr($value) ?>"/><?php        break;
                                case 'textarea':
                                    ?><textarea class="meta_video_<?php echo $name ?> widefat" type="text"
                                                name="<?php echo $field_name ?>[<?php echo $name ?>]"><?php echo esc_textarea($value) ?></textarea><?php
                                    break;
                            }
                        }
                        ?><label>URL</label><?php
                    }?>

                    <input class="meta_video_url widefat" type="text" name="<?php echo $field_name ?>[url]"
                           id="<?php echo $field_id ?>" value="<?php echo $meta['url'] ?>"/>
                    <input class="meta_video_type" type="hidden" name="<?php echo $field_name ?>[type]"
                           value="<?php echo $meta['type'] ?>"/>
                    <input class="meta_video_id" type="hidden" name="<?php echo $field_name ?>[id]"
                           value="<?php echo $meta['id'] ?>"/>
                    <input class="meta_video_thumb" type="hidden" name="<?php echo $field_name ?>[thumbnail]"
                           value="<?php echo $meta['thumbnail'] ?>"/>

                    <div class="thumbnails" style="overflow:hidden;"></div>
                </div>
            </div>
        </div>
    <?php
    }

    public static function init(){
        self::$instance = new VideoSetField();
    }
}

VideoSetField::init();