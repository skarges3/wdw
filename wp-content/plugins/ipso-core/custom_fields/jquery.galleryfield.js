(function ($) {
    $(function () {
        $(".gallery-launch-button").click(function (e) {
            e.preventDefault();
            var me = $(this);
            var n = me.data("gallery-field");
            var input = me.parent().find("input[name="+n+"]");
            var frame = input.data("frame");
            if (frame == null) {
                var frame = wp.media.gallery.edit('[gallery ids="' + input.val() + '"]');
                frame.on("update", function () {
                    var controller = frame.states.get('gallery-edit');
                    var library = controller.get("library");
                    var ids = library.pluck("id");
                    input.val(ids);
                    $.get(ajaxurl, {action: 'gallery_field_get_thumbnails', ids:ids}, function(html, status, xhr){
                        me.parent().find(".thumbnails").html(html);
                    });
                });
                input.data("frame", frame);
            }
            frame.open();
        });
        $(".gallery-clear-button").click(function(e){
            e.preventDefault();
            if (window.confirm("Remove all images from this gallery?")){
                var me = $(this);
                var n = me.data("gallery-field");
                var input = me.parent().find("input[name="+n+"]");
                input.val("");
                me.parent().find(".thumbnails").html('');
                var frame = input.data("frame");
                if (frame!=null){
                    input.data("frame", null);
                }
            }
        });
        $(".attach-launch-button").click(function(e){
            e.preventDefault();
            var me = $(this);
            var input = me.prev("input");
            var frame = me.data("frame");
            if (frame == null) {

                var frame = wp.media.string.image('[gallery ids="' + input.val() + '"]');
                frame.on("update", function () {
                    var controller = frame.states.get('gallery-edit');
                    var library = controller.get("library");
                    var ids = library.pluck("id");
                    input.val(ids);
                    $.get(ajaxurl, {action: 'gallery_field_get_thumbnails', ids:ids}, function(html, status, xhr){
                        me.parent().find(".thumbnails").html(html);
                    });
                });
                me.data("frame", frame);
            }
            frame.open();
        })
    });
})(jQuery);
