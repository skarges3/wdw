(function ($) {
    $(function () {
        var datepickerOpts = {
            dateFormat: 'm/d/yy',
            showAnim: 'fadeIn',
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            showButtonPanel: true
        };
        $(".datepicker").datepicker(datepickerOpts);
    });
})(jQuery);