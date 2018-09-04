///<reference path="../../typings/jquery/jquery.d.ts" />
var Orientation;
(function (Orientation) {
    Orientation[Orientation["portrait"] = 0] = "portrait";
    Orientation[Orientation["landscape"] = 1] = "landscape";
})(Orientation || (Orientation = {}));
var FileMediaModal = (function () {
    function FileMediaModal(settings) {
        this.settings = jQuery.extend({
            calling_selector: false,
            callback: function () {
            },
            args: {}
        }, settings);
        this.attachEvents();
    }
    FileMediaModal.prototype.attachEvents = function () {
        var me = this;
        jQuery(this.settings.calling_selector).click(function () {
            me.openFrame(this);
        });
    };
    FileMediaModal.prototype.openFrame = function (caller) {
        var $caller = jQuery(caller);
        var uploaderTitle = $caller.data("uploader_title");
        var buttonText = $caller.data("uploader_button_text");
        var library;
        var args = jQuery.extend({
            title: uploaderTitle,
            button: {
                text: buttonText
            },
            library: {
                type: library
            }
        }, this.settings.args);
        var me = this;
        this.frame = wp.media(args);
        this.frame.on('toolbar:create:select', function () {
            me.frame.state().set('filterable', 'uploaded');
        });
        // When an image is selected, run the callback.
        this.frame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            var attachment = me.frame.state().get('selection').first().toJSON();
            me.settings.callback($caller, attachment);
        });
        this.frame.on('open activate', function () {
            // Get the link/button/etc that called us
            // Select the thumbnail if we have one
            if ($caller.data('thumbnail_id')) {
                var Attachment = wp.media.model.Attachment;
                var selection = me.frame.state().get('selection');
                selection.add(Attachment.get($caller.data('thumbnail_id')));
            }
        });
        this.frame.open();
    };
    return FileMediaModal;
})();
;
//# sourceMappingURL=media-dialog.js.map