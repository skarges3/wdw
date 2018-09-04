<?php

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 11/14/14
 * Time: 11:52 AM
 */
class CustomMenuField
{
    private $field_name;
    private $field_type;
    private $field_type_args;
    private $class_name;
    private $label;
    private $half_width;
    private static $is_initialized;

    protected static function __init__()
    {
        if (!self::$is_initialized) {
            self::$is_initialized = true;
            add_filter('wp_edit_nav_menu_walker', array('CustomMenuField', 'wp_edit_nav_menu_walker'), 10, 2);
        }
    }

    function __construct($args)
    {
        self::__init__();

        $defaults = array(
            'field_name' => '',
            'field_type' => 'text',
            'field_type_args' => null,
            'class_name' => '',
            'label' => '',
            'half_width' => false
        );
        $args = wp_parse_args($args, $defaults);

        $this->field_name = $args['field_name'];
        $this->field_type = $args['field_type'];
        $this->field_type_args = $args['field_type_args'];
        $this->class_name = empty($args['class_name']) ? $this->field_name : $args['class_name'];
        $this->label = $args['label'];
        $this->half_width = !(!$args['half_width']);

        add_filter('wp_setup_nav_menu_item', array($this, 'wp_setup_nav_menu_item'));

        add_action('wp_update_nav_menu_item', array($this, 'wp_update_nav_menu_item'), 10, 3);

        ipso_Nav_Menu_Edit::add_field($this);
    }

    /**
     * @param WP_Post $menu_item
     */
    function wp_setup_nav_menu_item($menu_item)
    {
        $menu_item->{$this->field_name} = get_post_meta($menu_item->ID, $this->field_name, true);
        return $menu_item;
    }

    function wp_update_nav_menu_item($menu_id, $menu_item_db_id, $args)
    {
        $source = 'menu-item-attr-' . $this->field_name;
        if (isset($_REQUEST[$source][$menu_item_db_id])) {
            $value = $_REQUEST[$source][$menu_item_db_id];
            update_post_meta($menu_item_db_id, $this->field_name, $value);
        }
    }

    static function wp_edit_nav_menu_walker($walker, $menu_id)
    {
        return 'ipso_Nav_Menu_Edit';
    }

    function render_item($item, $depth, $args, $id)
    {
        $item_id = esc_attr($item->ID);
        $value = $item->{$this->field_name};
        $input_name = "menu-item-attr-{$this->field_name}[{$item_id}]";
        ?>
    <p class="<?php echo $this->class_name ?> description<?php echo $this->half_width ? '-thin' : '' ?>">
        <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>"><?php _e($this->label); ?>
            <br/>
            <?php switch ($this->field_type) {
                case "media":
                    MediaField::buildField($input_name, $value);
                    break;
                default:
                    ?>
                    <input type="<?php echo $this->field_type ?>"
                           id="edit-menu-item-attr-<?php echo $this->field_name ?>-<?php echo $item_id; ?>"
                           class="widefat edit-menu-item-attr-<?php echo $this->field_name ?>"
                           name="<?php echo $input_name ?>"
                           value="<?php echo esc_attr($value); ?>"/>
                    <?php
                    break;
            }?></label>
        </p><?php
    }
}

class ipso_Nav_Menu_Edit extends Walker_Nav_Menu
{

    public static $fields;

    public static function add_field($field)
    {
        if (is_null(self::$fields)) {
            self::$fields = array();
        }
        self::$fields[] = $field;
    }

    public function start_lvl(&$output, $depth = 0, $args = array())
    {
    }

    public function end_lvl(&$output, $depth = 0, $args = array())
    {
    }
    //public function start_lvl( &$output, $depth = 0, $args = array() )
    //public function end_lvl( &$output, $depth = 0, $args = array() )
    //public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        ob_start();
        $item_id = esc_attr($item->ID);
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ('taxonomy' == $item->type) {
            $original_title = get_term_field('name', $item->object_id, $item->object, 'raw');
            if (is_wp_error($original_title))
                $original_title = false;
        } elseif ('post_type' == $item->type) {
            $original_object = get_post($item->object_id);
            $original_title = get_the_title($original_object->ID);
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr($item->object),
            'menu-item-edit-' . ((isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item']) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if (!empty($item->_invalid)) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf(__('%s (Invalid)'), $item->title);
        } elseif (isset($item->post_status) && 'draft' == $item->post_status) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf(__('%s (Pending)'), $item->title);
        }

        $title = (!isset($item->label) || '' == $item->label) ? $title : $item->label;

        $submenu_text = '';
        if (0 == $depth)
            $submenu_text = 'style="display: none;"';

        ?>
    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes); ?>">
        <dl class="menu-item-bar">
            <dt class="menu-item-handle">
                <span class="item-title"><span class="menu-item-title"><?php echo esc_html($title); ?></span> <span
                        class="is-submenu" <?php echo $submenu_text; ?>><?php _e('sub item'); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html($item->type_label); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-up-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-down-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">
                                    &#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>"
                           title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
                        echo (isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item']) ? admin_url('nav-menus.php') : add_query_arg('edit-menu-item', $item_id, remove_query_arg($removed_args, admin_url('nav-menus.php#menu-item-settings-' . $item_id)));
                        ?>"><?php _e('Edit Menu Item'); ?></a>
					</span>
            </dt>
        </dl>

        <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
            <?php if ('custom' == $item->type) : ?>
                <p class="field-url description description-wide">
                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                        <?php _e('URL'); ?><br/>
                        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>"
                               class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]"
                               value="<?php echo esc_attr($item->url); ?>"/>
                    </label>
                </p>
            <?php endif; ?>
            <p class="description description-thin">
                <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                    <?php _e('Navigation Label'); ?><br/>
                    <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>"
                           class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]"
                           value="<?php echo esc_attr($item->title); ?>"/>
                </label>
            </p>

            <p class="description description-thin">
                <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                    <?php _e('Title Attribute'); ?><br/>
                    <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>"
                           class="widefat edit-menu-item-attr-title"
                           name="menu-item-attr-title[<?php echo $item_id; ?>]"
                           value="<?php echo esc_attr($item->post_excerpt); ?>"/>
                </label>
            </p>
            <?php $this->render_custom_fields($item, $depth, $args, $id); ?>
            <p class="field-link-target description">
                <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                    <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank"
                           name="menu-item-target[<?php echo $item_id; ?>]"<?php checked($item->target, '_blank'); ?> />
                    <?php _e('Open link in a new window/tab'); ?>
                </label>
            </p>

            <p class="field-css-classes description description-thin">
                <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                    <?php _e('CSS Classes (optional)'); ?><br/>
                    <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>"
                           class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]"
                           value="<?php echo esc_attr(implode(' ', $item->classes)); ?>"/>
                </label>
            </p>

            <p class="field-xfn description description-thin">
                <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                    <?php _e('Link Relationship (XFN)'); ?><br/>
                    <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>"
                           class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]"
                           value="<?php echo esc_attr($item->xfn); ?>"/>
                </label>
            </p>

            <p class="field-description description description-wide">
                <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                    <?php _e('Description'); ?><br/>
                    <textarea id="edit-menu-item-description-<?php echo $item_id; ?>"
                              class="widefat edit-menu-item-description" rows="3" cols="20"
                              name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html($item->description); // textarea_escaped
                        ?></textarea>
                    <span
                        class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
                </label>
            </p>

            <p class="field-move hide-if-no-js description description-wide">
                <label>
                    <span><?php _e('Move'); ?></span>
                    <a href="#" class="menus-move-up"><?php _e('Up one'); ?></a>
                    <a href="#" class="menus-move-down"><?php _e('Down one'); ?></a>
                    <a href="#" class="menus-move-left"></a>
                    <a href="#" class="menus-move-right"></a>
                    <a href="#" class="menus-move-top"><?php _e('To the top'); ?></a>
                </label>
            </p>

            <div class="menu-item-actions description-wide submitbox">
                <?php if ('custom' != $item->type && $original_title !== false) : ?>
                    <p class="link-to-original">
                        <?php printf(__('Original: %s'), '<a href="' . esc_attr($item->url) . '">' . esc_html($original_title) . '</a>'); ?>
                    </p>
                <?php endif; ?>
                <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                echo wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'delete-menu-item',
                            'menu-item' => $item_id,
                        ),
                        admin_url('nav-menus.php')
                    ),
                    'delete-menu_item_' . $item_id
                ); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a
                    class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>"
                    href="<?php echo esc_url(add_query_arg(array('edit-menu-item' => $item_id, 'cancel' => time()), admin_url('nav-menus.php')));
                    ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]"
                   value="<?php echo $item_id; ?>"/>
            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]"
                   value="<?php echo esc_attr($item->object_id); ?>"/>
            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]"
                   value="<?php echo esc_attr($item->object); ?>"/>
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]"
                   value="<?php echo esc_attr($item->menu_item_parent); ?>"/>
            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]"
                   value="<?php echo esc_attr($item->menu_order); ?>"/>
            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]"
                   value="<?php echo esc_attr($item->type); ?>"/>
        </div>
        <!-- .menu-item-settings-->
        <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();

    }

    function render_custom_fields($item, $depth, $args, $id)
    {
        /** @var CustomMenuField $field */
        foreach (self::$fields as $field) {
            $field->render_item($item, $depth, $args, $id);
        }
    }
    // public function end_el( &$output, $item, $depth = 0, $args = array() )
}