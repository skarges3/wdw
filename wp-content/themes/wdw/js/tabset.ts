///<reference path="typings/jquery/jquery.d.ts" />
(function ($) {
    $.fn.tabset = function (opts?:any) {
        $(this).each(function () {
            opts = $.extend({
                'tab_selector': '.tab',
                'tab_title_selector': '.tab-title',
                'tab_title_wrapper_class': 'tab-titles'
            }, opts);

            var tabset = $(this);
            tabset.data("opts", opts);
            var tabs = tabset.find(opts.tab_selector);
            var tab_titles = tabset.find(opts.tab_title_selector);
            var tab_title_wrapper = tabset.find("." + opts.tab_title_wrapper_class);
            if (tab_title_wrapper.length == 0) {
                tab_title_wrapper = $("<div></div>");
                tab_title_wrapper.addClass(opts.tab_title_wrapper_class);
                tab_title_wrapper.insertBefore(tabset.children(opts.tab_selector)[0]);
            }
            tab_titles.each(function (i) {
                var me = $(this);
                me.data('tab-index', i);
            });
            tabs.each(function (i) {
                var me = $(this);
                me.attr('tab-index', i);
                me.data('tab-index', i);
                if (i == 0) {
                    me.show();
                }
                else {
                    me.hide();
                }
            });
            var activateTab = function () {
                if ($(window).width() < 1024) return;
                tabs.hide();
                var me = $(this);
                var tabIndex = me.data('tab-index');
                tabs.each(function () {
                    var tab = $(this);
                    if (tab.data('tab-index') == tabIndex) {
                        tab.show();
                    }
                });
                tab_titles.removeClass("active");
                me.addClass('active');
            };

            tab_titles.click(activateTab);
            activateTab.apply(tab_titles.get(0));
            tabset.parents(".panel").css('border', 'none');

            var me = this;
            setupTabsForScreenSize.apply(this);
            $(window).resize(function () {
                setupTabsForScreenSize.apply(me);
            });
        });
    };

    function setupTabsForScreenSize() {
        var tabset = $(this);
        var opts = tabset.data("opts");
        var tabs = tabset.find(opts.tab_selector);
        var tab_titles = tabset.find(opts.tab_title_selector);
        var tab_title_wrapper = tabset.find("." + opts.tab_title_wrapper_class);

        var mode = $(window).width() < 1024 ? "mobile" : "desktop";
        if (tabset.data("mode") == mode) {
            return;
        }
        tabset.data("mode", mode);
        if (mode == "desktop") {
            tab_titles.appendTo(tab_title_wrapper);
            if (!tabset.hasClass("vertical")) {
                //tab_titles.css("width", (100 / Math.max(2, tab_titles.length)) + "%");
                tabs.find(".tab-content").css("min-height", "");
            }
            else {
                tab_titles.css("width", '');
                var titles_height = tab_title_wrapper.outerHeight();
                tabs.find(".tab-content").css("min-height", titles_height + "px");
            }
            tab_titles.filter(".active").trigger("click");

        }
        else {
            tab_titles.css("width", "");
            tabs.find(".tab-content").css("min-height", "");
            tab_titles.each(function () {
                var tt = $(this);
                var tab = null;
                tabs.each(function () {
                    var t = $(this);
                    if (t.data("tab-index") == tt.data("tab-index")) {
                        tab = t;
                    }
                })
                tt.insertBefore(tab);
                tab.show();
            });
        }
    }

    $(function () {
        $(".tabset").tabset();
    });
})(jQuery);

