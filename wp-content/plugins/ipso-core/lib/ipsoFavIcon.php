<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 2/20/14
 * Time: 12:27 PM
 */

class ipsoFavIcon {
    function __construct(){
        new GeneralSetting('favicon', 'favicon', 'Favicon', 'encoded-image');
        add_action('wp_head', array($this, 'include_favicon'));
        add_action('wp_ajax_ipso_favicon', array($this, 'get_icon'));
        add_action('wp_ajax_nopriv_ipso_favicon', array($this, 'get_icon'));
    }

    function include_favicon(){
        $favicon = get_option('favicon');
        if (!empty($favicon)){
            ?><link rel="icon" type="image/png" href="<?php echo admin_url('admin-ajax.php', 'relative')?>?action=ipso_favicon"/><?php
        }
    }

    function get_icon(){
        $data = get_option('favicon');
        $etag = crc32($data);
        $etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
        if ($etag == $etagHeader){
            header("HTTP/1.1 304 Not Modified");
            exit();
        }

        list($prefix, $data) = explode(':', $data);
        if ($prefix == 'data'){
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
            header("ETag: $etag");
            header('Content-Length: '.strlen($data));
            header('Content-Type: '.$type);
            header('Cache-Control: public');
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", time())." GMT");
            header('Expires: 0');
            header('Pragma: public');

            echo $data;
        }
        die();
    }
}

new ipsoFavIcon();