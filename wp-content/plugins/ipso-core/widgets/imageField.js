///<reference path="../DefinitelyTyped/jquery/jquery.d.ts"/>

var ImageFieldManager = (function () {
    function ImageFieldManager($inputField) {
        this.$inputField = $inputField;
    }
    ImageFieldManager.prototype.updatePreview = function (html) {
        var url = jQuery(html).find('img').attr('src');
        this.$inputField.val(url);
        ImageFieldManager.updateImage(this.$inputField);
        tb_remove();
        jQuery("#TB_window").html('');
    };

    ImageFieldManager.updateImage = function ($input) {
        var url = $input.val();
        var $wrap = $input.parents(".field_row").find(".image_wrap");
        $wrap.html('');
        if (url) {
            var $img = jQuery("<img>");
            $img.attr("src", url);
            $wrap.append($img);
        }
    };

    ImageFieldManager.launchMedia = function ($element) {
        var $parent = $element.parent().parent('.field_row');
        var $inputField = jQuery($parent).find("input.meta_image_url");
        jQuery("#TB_window").html("");
        tb_show('', 'media-upload.php?TB_iframe=true');

        var manager = new ImageFieldManager($inputField);
        window.send_to_editor = function (html) {
            manager.updatePreview(html);
            manager = null;
            window.send_to_editor = null;
        };
    };

    ImageFieldManager.clearImage = function ($element) {
        var $parent = $element.parents("#dynamic_form");
        var $input = $parent.find("input.meta_image_url");
        $input.val('');
        ImageFieldManager.updateImage($input);
    };

    ImageFieldManager.setup = function ($) {
        $(function () {
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
    };
    return ImageFieldManager;
})();
ImageFieldManager.setup(jQuery);
//# sourceMappingURL=imageField.js.map
