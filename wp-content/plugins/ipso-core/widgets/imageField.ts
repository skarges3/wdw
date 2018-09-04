///<reference path="../DefinitelyTyped/jquery/jquery.d.ts"/>
declare function tb_show(arg1:string, url:string);
declare function tb_remove();
interface Window{
    send_to_editor(html:string):void;
}
class ImageFieldManager {

    constructor(public $inputField:JQuery) {

    }

    public updatePreview(html:string) {
        var url = jQuery(html).find('img').attr('src');
        this.$inputField.val(url);
        ImageFieldManager.updateImage(this.$inputField);
        tb_remove();
        jQuery("#TB_window").html('');
    }

    public static updateImage($input:JQuery) {
        var url = $input.val();
        var $wrap = $input.parents(".field_row").find(".image_wrap");
        $wrap.html('');
        if (url) {
            var $img = jQuery("<img>");
            $img.attr("src", url);
            $wrap.append($img);
        }
    }

    public static launchMedia($element:JQuery) {
        var $parent:JQuery = $element.parent().parent('.field_row');
        var $inputField:JQuery = jQuery($parent).find("input.meta_image_url");
        jQuery("#TB_window").html("");
        tb_show('', 'media-upload.php?TB_iframe=true');

        var manager = new ImageFieldManager($inputField);
        window.send_to_editor = function (html:string) {
            manager.updatePreview(html);
            manager = null;
            window.send_to_editor = null;
        };
    }

    public static clearImage($element:JQuery) {
        var $parent = $element.parents("#dynamic_form");
        var $input = $parent.find("input.meta_image_url");
        $input.val('');
        ImageFieldManager.updateImage($input);
    }

    public static setup($:JQueryStatic) {
        $(function(){
            $(document).on('change', 'input.meta_image_url', function () {
                ImageFieldManager.updateImage($(this));
            });
            $(document).on('click', "#add-image-button", function (e) {
                e.preventDefault();
                ImageFieldManager.launchMedia($(this));
                return false;
            });
            $(document).on('click', "#clear-image-button", function (e) {
                e.preventDefault();
                ImageFieldManager.clearImage($(this));
                return false;
            });
        });
    }
}
ImageFieldManager.setup(jQuery);