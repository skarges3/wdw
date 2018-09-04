///<reference path='DefinitelyTyped/jquery/jquery.d.ts'/>
///<reference path='DefinitelyTyped/jqueryui/jqueryui.d.ts'/>
jQuery(function ($) {
    $(document).on("click", ".open-link-dialog-button", function (e) {
        var fieldId = $(this).data("field-id");
        wpLink.open(fieldId);
        e.preventDefault();
    });
});
//# sourceMappingURL=LinkField.js.map