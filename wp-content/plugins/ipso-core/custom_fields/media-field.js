jQuery(function ($) {
    var blank = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=';
    $(document).on("widget-updated", function (e, widgets) {
        for (var i = 0; i < widgets.length; i++) {
            $(widgets[i]).find(".media-field").each(setupMediaField);
        }
    });
    function promptForMedia(e) {
        e.preventDefault();
        var me = $(this).parents(".media-field");

        var hidden = me.find(".hidden-field");
        var imgWrap = me.find(".image-area");

        var modal = imgWrap.data("modal");
        if (modal == null) {
            modal = new FileMediaModal({
                //calling_selector: "#<?php echo $id?>_add",
                callback: function (caller, attachment) {
                    var id = attachment.id;
                    var filename = attachment.url;
                    var title = attachment.title;
                    hidden.val(id);
                    var img = imgWrap.find("img");
                    var lbl = imgWrap.find("label");
                    lbl.text(attachment.name);
                    if (/\.(gif|jpeg|jpg|png)$/.test(filename)) {
                        img.attr("src", filename);
                    }
                    else {
                        img.attr("src", blank);
                    }
                },
                args: {
                    library: {}
                }
            });
            imgWrap.data("modal", modal);
        }
        modal.openFrame();
    }

    function clearImage(e) {
        e.preventDefault();
        var me = $(this).parents(".media-field");
        var hidden = me.find(".hidden-field");
        var imgWrap = me.find(".image-area");

        hidden.val('');
        imgWrap.find("label").html('');
        imgWrap.find("img").attr("src", blank);

    }

    $(document).on("click", ".media-field .add-button", promptForMedia);
    $(document).on("click", ".media-field .clear-button", clearImage);
});