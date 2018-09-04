///<reference path="../../typings/jquery/jquery.d.ts"/>
///<reference path="../../typings/jqueryui/jqueryui.d.ts"/>
var EditorManager = (function () {
    function EditorManager() {
        this.currentContentId = '';
    }
    /**
     * Show the editor
     * @param string contentId
     */
    EditorManager.prototype.showEditor = function (contentId) {
        this.currentContentId = contentId;
        this.setEditorContent(this.currentContentId);
        jQuery('#wp-editor-widget-backdrop').fadeIn();
        jQuery('#wp-editor-widget-container').show();
    };
    /**
     * Hide editor
     */
    EditorManager.prototype.hideEditor = function () {
        jQuery('#wp-editor-widget-backdrop').fadeOut();
        jQuery('#wp-editor-widget-container').hide();
    };
    /**
     * Set editor content
     */
    EditorManager.prototype.setEditorContent = function (contentId) {
        var editor = tinyMCE.get('wp-editor-widget');
        var $control = this.getControl();
        var text = $control.val();
        jQuery('#wp-editor-widget').val(text);
        if (typeof editor != "undefined") {
            this.ensureVisualEditor();
            editor.setContent(text);
        }
    };
    EditorManager.prototype.ensureVisualEditor = function () {
        if (window.switchEditors) {
            switchEditors.go('wp-editor-widget', 'tmce');
        }
    };
    EditorManager.prototype.getControl = function () {
        return jQuery('#' + this.currentContentId);
    };
    /**
     * Update widget and close the editor
     */
    EditorManager.prototype.updateWidgetAndCloseEditor = function () {
        var editor = tinyMCE.get('wp-editor-widget');
        var $control = this.getControl();
        if (typeof editor == "undefined") {
            $control.val(jQuery('#wp-editor-widget').val());
        }
        else {
            this.ensureVisualEditor();
            $control.val(editor.getContent());
        }
        if (window.wpWidgets) {
            wpWidgets.save(jQuery('#' + this.currentContentId).closest('div.widget'), 0, 1, 0);
        }
        this.hideEditor();
    };
    return EditorManager;
})();
var WPEditorWidget = new EditorManager();
(function ($) {
    $(function () {
        $(document).on("click", ".widget-section-header", function (e) {
            if (arguments[0].srcElement == this) {
                var me = $(this);
                me.toggleClass("open");
                me.next(".widget-section-body").slideToggle();
            }
        });
    });
})(jQuery);
(function ($) {
    $(function () {
        $(".sortable-sections").on("load", function () {
            var section = $(this);
            var rows = section.find(".field-section");
            var dragged;
            var parent;
            rows.on("load", function () {
                rows.draggable({
                    //containment: '.form-table',
                    revert: true,
                    snap: true,
                    revertDuration: 0,
                    stack: ".field-section",
                    axis: "y",
                    start: function () {
                        dragged = this;
                    },
                    stop: function () {
                    }
                });
                rows.droppable({
                    accept: ".field-section",
                    drop: function (evt, ui) {
                        var my_index = parseInt($(this).find(".index-value").val());
                        var dragged_index = parseInt($(dragged).find(".index-value").val());
                        if (dragged_index < my_index) {
                            $(dragged).insertAfter(this);
                        }
                        else {
                            $(dragged).insertBefore(this);
                        }
                        section.find(".field-section").each(function (idx) {
                            $(this).find(".index-value").val(idx);
                        });
                    }
                });
            }).trigger("load");
        }).trigger("load");
    });
})(jQuery);
//# sourceMappingURL=admin-shortcode.js.map