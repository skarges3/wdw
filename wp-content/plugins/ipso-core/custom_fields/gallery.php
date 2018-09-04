<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 1/15/14
 * Time: 3:21 PM
 * To change this template use File | Settings | File Templates.
 */

class GalleryField implements IFieldRenderer
{
    public static $instance;
    private $added_script;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get_thumbnails($meta){
        if ($meta){
            foreach(is_array($meta) ? $meta : explode(',',$meta) as $thumbnail_id){
                ?><?php echo wp_get_attachment_image($thumbnail_id, array(100,100))?><?php
            }
        }
    }

    public static function wp_ajax_gallery_field_get_thumbnails(){
        self::get_thumbnails($_GET['ids']);
        die;
    }

    public function renderField($post_id, $field, $meta)
    {
        ?>
        <div class="thumbnails"><?php self::get_thumbnails($meta)?></div>
        <input type="hidden" name="<?php echo $field->id ?>" value="<?php echo $meta ?>">
        <input type="button" value="Edit Gallery" class="button gallery-launch-button button-primary"
               data-gallery-field="<?php echo $field->id ?>"/>
        <input type='button' value="Remove Gallery" class="button gallery-clear-button" data-gallery-field="<?php echo $field->id?>">
        <?php
        add_action('admin_print_footer_scripts', array($this, 'add_script'));
    }

    public function add_script()
    {
        if ($this->added_script) {
            return;
        }
        $this->added_script = true;
        $src = plugins_url('/ipso-core/custom_fields/jquery.galleryfield.js');
        echo "<script src='$src'></script>";
    }
}

add_action('wp_ajax_gallery_field_get_thumbnails', array('GalleryField', 'wp_ajax_gallery_field_get_thumbnails'));