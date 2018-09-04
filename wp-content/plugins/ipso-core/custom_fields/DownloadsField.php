<?php

class DownloadsField implements IFieldRenderer
{

    private function __construct()
    {
        $file_types = new CustomPostType('file_type', array(
            'name' => 'File Types',
            'singular_name' => 'File Type',
            'menu_name' => 'File Types',
            'all_items' => 'All File Types',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New File Type',
            'edit_item' => 'Edit File Type',
            'new_item' => 'New File Type',
            'view_item' => 'View File Type',
            'search_items' => 'Search File Types',
            'not_found' => 'No file types found'),
            array(
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'show_in_admin_bar' => false,
                'show_in_nav_menus' => false,
                'menu_icon' => 'dashicons-media-default'
            )
        );
        $file_types->addThumbnailSupport();
    }

    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function renderField($post_id, $field, $value)
    {
        if (!is_array($value)) {
            if (empty($value)) {
                $value = array();
            } else {
                $value = array($value);
            }
        }
        foreach ($value as $dl) {
            $info = self::get_attachment_info($dl);
            echo '<div class="download"><a href="#remove" class="remove-download">x</a>';
            echo $info['icon'];
            echo '<div>' . $info['name'] . '</div>';
            ?><input type="hidden" name="<?php echo $field->id ?>[]" value="<?php echo $dl ?>"/><?php
            echo '</div>';
        }

        ?>
        <p class="form-field"><a href="#" class="button add-file-button" data-uploader_title="Add File"
                                 data-uploader_button_text="Attach">Add</a></p><?php

        ?>
        <script src="<?php echo plugins_url('../assets/js/media-dialog.js', __FILE__) ?>"></script>
        <script>
            (function ($) {
                var removeDownload = function () {
                    $(this).parent(".download").remove();
                };
                var fileTypes = <?php echo json_encode(self::ensure_icon_index()) ?>;
                var fieldName = "<?php echo $field->id?>[]";
                new FileMediaModal({
                    calling_selector: ".add-file-button",
                    callback: function (caller, attachment) {
                        var id = attachment.id;
                        var filename = attachment.filename;
                        var sections = filename.split('.');
                        var ext = sections[sections.length - 1].toUpperCase();
                        var title = attachment.title;
                        var icon = fileTypes[ext];

                        jQuery("<div class='download'><a href='#remove' class='remove-download'>x</a>" + icon + "<div>" + title + "</div><input type='hidden' name='" + fieldName + "' value='" + id + "'></div>").insertBefore(caller.parent()).find(".remove-download").click(removeDownload);
                    },
                    args: {
                        library: {}
                    }
                });
                $(function () {
                    $('.download .remove-download').on("click", removeDownload);
                });
            })(jQuery);

        </script><?php
    }

    private static $icon_index;

    public static function ensure_icon_index()
    {
        if (self::$icon_index == null) {
            self::$icon_index = array();
            $posts = get_posts(array(
                'post_type' => 'file_type',
                'nopaging' => 1
            ));
            foreach ($posts as $post) {
                self::$icon_index[trim($post->post_title)] = get_the_post_thumbnail($post->ID, 'full');
            }
        }
        return self::$icon_index;
    }

    public static function getIcon($type)
    {
        self::ensure_icon_index();
        if (isset(self::$icon_index[$type])) {
            return self::$icon_index[$type];
        }
        return '<strong>Unknown File Type</strong>';
    }

    public static function attachment_link($aid)
    {
        $info = self::get_attachment_info($aid);
        echo "<a class='document-link' target='_blank' href='{$info['url']}' title='{$info['name']}'>{$info['icon']}{$info['caption']}</a>";
    }

    public static function get_attachment_info($aid)
    {
        $post = get_post($aid);
        $matches = null;
        if (preg_match('/^.*?\.(\w+)$/', get_attached_file($post->ID), $matches))
            $ext = esc_html(strtoupper($matches[1]));
        else
            $ext = strtoupper(str_replace('image/', '', $post->post_mime_type));
        $url = wp_get_attachment_url($aid);
        return array(
            'name' => $post->post_title,
            'caption' => $post->post_excerpt,
            'url' => $url,
            'type' => trim($ext),
            'icon' => self::getIcon($ext)
        );
    }
}
