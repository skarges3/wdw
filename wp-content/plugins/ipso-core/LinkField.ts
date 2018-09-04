///<reference path='DefinitelyTyped/jquery/jquery.d.ts'/>
///<reference path='DefinitelyTyped/jqueryui/jqueryui.d.ts'/>
declare var wpLink:{
    open(control:string):void
};
jQuery(function ($) {
    $(document).on("click", ".open-link-dialog-button", function (e) {
        var fieldId = $(this).data("field-id");
        wpLink.open(fieldId);
        e.preventDefault();
    });
});
