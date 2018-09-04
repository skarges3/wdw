///<reference path="typings/jquery/jquery.d.ts"/>
///<reference path="typings/hammerjs/hammerjs.d.ts"/>
/* jQuery Tiny Pub/Sub - v0.7 - 10/27/2011
 * http://benalman.com/
 * Copyright (c) 2011 "Cowboy" Ben Alman; Licensed MIT, GPL */
(function (a) {
    var b = a({});
    a.subscribe = function () {
        b.on.apply(b, arguments)
    }, a.unsubscribe = function () {
        b.off.apply(b, arguments)
    }, a.publish = function () {
        b.trigger.apply(b, arguments)
    }
})(jQuery)

interface JQuery {
    publish: (evt, args:any[])=>void;
    subscribe: (evt, handler:(...args:any[])=>void)=>void;
    unsubscribe: (evt, handler:(...args:any[])=>void)=>void;
}

class ContentRow {
    public constructor(private containerSelector:string, private elementSelector:string) {
        var me = this;
        jQuery(function () {
            me.init();
        });
    }

    private init() {
        var me = this;
        var $ = jQuery;
        var forceFinishTimerID = null;
        $(this.containerSelector).each(function () {
            var wrapper = $(this);
            var imgs = wrapper.find("img");
            var remaining = imgs.length;
            if (remaining) {
                imgs.each(function () {
                    var finish = function (status:boolean) {
                        remaining--;
                        if (remaining == 0) {
                            clearTimeout(forceFinishTimerID);
                            me.maintainHeight(wrapper);
                        }

                    }
                    forceFinishTimerID = setTimeout(function () {
                        me.maintainHeight(wrapper);
                    }, 1000);
                    $("<img/>")
                        .load(function () {
                            finish(true);
                        })
                        .error(function () {
                            finish(false);
                        })
                        .attr("src", $(this).attr("src"));
                });
            }
            else {
                me.maintainHeight(wrapper);
            }
        });
    }

    private maintainHeight(row:JQuery) {
        var $ = jQuery;
        var items = row.find(this.elementSelector);

        function fillHeight() {
            items.css("height", "");
            if ($(window).width() > 767) {
                var max = 0;
                items.each(function () {
                    max = Math.max(max, $(this).height());
                });
                items.each(function () {
                    $(this).css("height", max + "px");
                });
            }
        }

        fillHeight();

        var resizeTimerID;
        $(window).resize(function () {
            clearTimeout(resizeTimerID);
            resizeTimerID = setTimeout(fillHeight, 100);
        });
    }
}
class Triggers {
    public constructor(private itemSelector:string, private triggerSelector:string, private activeClass:string, private dataAttr:string) {
        var me = this;
        jQuery(function ($) {
            me.init($);
            me = null;
        })
    }

    public init($:JQueryStatic) {
        var me = this;
        var items = {};
        $(this.itemSelector).each(function () {
            var item = $(this);
            var key:any;
            key = item.data(me.dataAttr);
            items[key] = item;
        });
        var $triggers = $(me.triggerSelector).click(function (e) {
            e.preventDefault();
            var link = $(this);
            var active = $(me.itemSelector + "." + me.activeClass);
            var id:any;
            id = link.data(me.dataAttr);
            active.removeClass(me.activeClass);
            items[id].addClass(me.activeClass);
            $triggers.removeClass(me.activeClass);
            link.addClass(me.activeClass);
        });
    }
}
interface RespondAction {
    (oldMode:string, newMode:string):void;
}
interface RespondActions {
    desktop:RespondAction[];
    tablet:RespondAction[];
    mobile:RespondAction[];
}
class Respond {
    private currentMode:string;
    private actions:RespondActions = {
        desktop: [],
        tablet: [],
        mobile: []
    };

    constructor(public tabletBreakPoint:number = 1024, public mobileBreakPoint:number = 768) {
    }

    respond() {
        var win = jQuery(window);
        var width:number = win.width();
        var newMode = "desktop";
        if (width <= this.mobileBreakPoint) {
            newMode = "mobile";
        }
        else if (width <= this.tabletBreakPoint) {
            newMode = "tablet";
        }

        if (this.currentMode != newMode) {
            this.setMode(newMode);
        }
    }

    setMode(mode:string) {
        if (mode != this.currentMode) {
            var oldMode = this.currentMode;
            this.currentMode = mode;
            var a = this.actions[mode];
            for (var i = 0; i < a.length; i++) {
                a[i](oldMode, this.currentMode);
            }
        }
    }

    addDesktopAction(action:RespondAction) {
        this.actions.desktop[this.actions.desktop.length] = action;
        return this;
    }

    addTabletAction(action:RespondAction) {
        this.actions.tablet[this.actions.tablet.length] = action;
        return this;
    }

    addMobileAction(action:RespondAction) {
        this.actions.mobile[this.actions.mobile.length] = action;
        return this;
    }

    start() {
        var me = this;
        this.respond();
        jQuery(window).resize(function () {
            me.respond();
        });
    }
}

/** Site Specific */

jQuery(function ($) {
    $(".menu-toggle").click(function () {
        $("body").toggleClass("mobile-menu-shown");
    });
});

jQuery(function ($) {
    $(window).scroll(function () {
        var top = $(this).scrollTop();
        var threshold = $("#masthead").offset().top + $("#masthead").height();
        if (top > threshold) {
            $("body").addClass("sticky-header");
        }
        else {
            $("body").removeClass("sticky-header");
        }
    });
});

jQuery(function ($) {
    $(".share a").click(function (e) {
        e.preventDefault();
        var a = $(this);
        var href = a.attr("href");
        window.open(href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    })
});


jQuery(function ($) {
    $(".banners").each(function () {
        var banners = $(this);
        var delay = banners.data("delay");
        var items = banners.find(".banner");
        var triggers = banners.find(".trigger");
        var rotateTimer;

        function rotateBanners() {
            clearTimeout(rotateTimer);
            if (delay <= 0 || items.length < 2) {
                return;
            }
            rotateTimer = setTimeout(showNextBanner, delay);
        }

        var swapBanners = function (current, next) {
            current.fadeOut(function () {
                next.addClass("active");
                current.removeClass("active");
                triggers.removeClass("active");
                triggers.filter("[data-banner-id=" + next.data("banner-id") + "]").addClass("active");
                rotateBanners();
            });
            next.fadeIn();
            $.publish('banner/changed', [next]);
        };

        function showNextBanner() {
            var current = items.filter(".active");
            var next = current.next(".banner");
            if (next.length == 0) {
                next = items.first();
            }
            swapBanners(current, next);
        }

        triggers.click(function (e) {
            e.preventDefault();
            var me = $(this);
            var bid = me.data("banner-id");
            var current = items.filter(".active");
            if (bid == current.data("banner-id")) {
                return;
            }
            triggers.removeClass("active");
            var next = items.filter("[data-banner-id=" + bid + "]");
            swapBanners(current, next);
        });

        rotateBanners();
    });
});

jQuery(function ($) {
    var ff = $("#fixed-footer");
    var colophon = $("#colophon");
    var body = $("body");
    var win = $(window);
    var lastPos = null;

    function adjustFixedFooter() {
        if ($(window).width() < 768) {
            body.removeClass("footer-shown");
            body.removeClass("footer-locked");
            return;
        }
        var thisPos = win.scrollTop();
        if (lastPos == null) {
            lastPos = thisPos;
        }
        if (thisPos > lastPos) {
            body.addClass("footer-shown");
        }
        else {
            body.removeClass("footer-shown");
        }
        if (thisPos > body[0].scrollHeight - win.innerHeight() - colophon.height()) {
            body.addClass("footer-locked");
        }
        else {
            body.removeClass("footer-locked");
        }

        lastPos = thisPos;
    }

    if (ff.length) {
        win.scroll(adjustFixedFooter);
        adjustFixedFooter();
    }
});

jQuery(function ($) {
    var people = $(".person");
    var resizeHandler;

    function showPerson(person:JQuery) {
        var wasActive = person.hasClass("active");
        people.removeClass("active").css("margin-bottom", "");
        if (resizeHandler) {
            $(window).off("resize", resizeHandler);
            resizeHandler = null;
        }
        if (wasActive) {
            return;
        }
        person.addClass("active");


        var details = keepSpacing(person);
        details.hide();
        details.fadeIn(function () {
            details.css("display", "");
        });

        resizeHandler = function () {
            keepSpacing(person);
        };
        $(window).on("resize", resizeHandler);
    }

    function keepSpacing(person) {
        people.css("margin-bottom", "");
        if ($(window).width() < 480) {
            return;
        }
        var t = person.position().top;
        var thisRow = [person[0]];
        var n = person.next(".person");
        while (n.length && n.position().top == t) {
            thisRow.push(n[0]);
            n = n.next(".person");
        }
        n = person.prev(".person");
        while (n.length && n.position().top == t) {
            thisRow.push(n[0]);
            n = n.prev(".person");
        }

        var details = person.find(".person-details");

        $(thisRow).css("margin-bottom", details.outerHeight() + "px");
        return details;

    }

    $(".person-intro").click(function (e) {
        e.preventDefault();
        showPerson($(this).parents(".person"));
    });

    if (document.location.hash) {
        try {
            var elem = $(document.location.hash);
            if (elem.hasClass("person")) {
                showPerson(elem);
            }
        } catch (ex) {

        }
    }
});

jQuery(function ($) {
    var wrappers = $(".person-list-wrapper");
    if (!wrappers.length) {
        return;
    }
    function fillWidth() {
        wrappers.each(function () {
            var w = $(this);
            w.css({"margin-left": 0, "margin-right": 0});
            var margin = (w.width() - $(window).width()) / 2;
            if (margin < 0) {
                w.css({"margin-left": margin, "margin-right": margin});
            }
        });
    }

    $(window).resize(fillWidth);
    fillWidth();
    $(".person-list").each(function () {
        var list = $(this);
        var people = list.find(".person");
        people.click(function () {
            document.location.href = $(this).attr("href");
        });

        var currentMargin;

        function keepCentered() {
            var width = 0;
            people.each(function () {
                width += $(this).width();
            });
            var pWidth = list.parent().width();
            var margin:any;
            margin = "auto";
            if (width > pWidth) {
                margin = (pWidth - width);
            }
            list.css({
                "width": width,
                //"margin-left": margin,
                "margin-right": margin
            });
            currentMargin = margin;
        }

        keepCentered();
        $(window).on("resize", keepCentered);
    });
});

jQuery(function ($) {
    var form = $("#masthead .search-wrapper");
    $("#masthead .search-link").click(function (e) {
        e.preventDefault();
        form.fadeIn(function () {
            form.find("input[type='search']").focus();
        });
    });
    form.click(function (e) {
        e.preventDefault();
        if (e.target == form[0]) {
            form.fadeOut();
        }
    })
});

jQuery(function ($) {
    $(".product-version-selector").change(function () {
        $(this).parent().find(".product-version img").attr("src", $(this).val());
    });
});

jQuery(function ($) {
    $(".view-product").click(function (e) {
        e.preventDefault();
        var me = $(this);
        var href = me.attr("href");
        var target = $(href.replace("product", "gallery"));
        $.launchDialog(target);
    });
});

jQuery(function ($) {
    $.launchDialog = function (target, wrapperClass:string = 'wrapper') {
        var host = target.parent();
        var overlay = $("<div id='overlay' title='click to close' class='" + wrapperClass + "-overlay'><div class='" + wrapperClass + "'><a href='#close' class='close-button'>X</a><div class='content-wrapper'></div></div></div>");
        var wrapper = overlay.find("." + wrapperClass);

        function keepCentered() {
            if ($(window).width() > 767) {
                wrapper.css({
                    "margin-left": (-wrapper.outerWidth() / 2) + "px",
                    "margin-top": (-wrapper.outerHeight() / 2) + "px"
                });
            }
            else {
                wrapper.css({
                    "margin-left": 0,
                    "margin-top": 0
                });
            }
        }

        function closeOnEscape(e) {
            if (e.keyCode == 27 || e.keyCode == 10 || e.keyCode == 13) {
                closeOverlay(e);
            }
        }

        function closeOverlay(e) {
            e.preventDefault();
            overlay.fadeOut(function () {
                host.append(target);
                overlay.remove();
                $(window).off("resize", keepCentered);
                $("body").off("keydown", closeOnEscape);
                $("body").removeClass("dialog-shown");
            });
            $.publish("dialog-closed");
        }

        overlay.hide();
        wrapper.find(".content-wrapper").append(target);
        wrapper.click(function (e) {
            e.stopPropagation();
        });
        overlay.click(closeOverlay);
        overlay.find(".close-button").click(closeOverlay)
        $("body").append(overlay);
        $("body").addClass("dialog-shown");
        $("body").on('keydown', closeOnEscape);
        keepCentered();
        $(window).on("resize", keepCentered);
        overlay.fadeIn();
    }
});

jQuery(function ($) {
    var c = $(".mobile-banner .banner-content");
    if (c.length) {
        function keepContentCentered() {
            c.each(function () {
                $(this).css("margin-top", -$(this).outerHeight() / 2);
            });
        }

        keepContentCentered();
        $(window).on("resize", keepContentCentered);
        $.subscribe('banner/changed', keepContentCentered);
    }
});

jQuery(function ($) {
    var r = $(".header-reveal");
    $("#masthead .toggle").click(function (e) {
        if ($(window).width() > 767) {
            e.preventDefault();

            r.fadeToggle();
        }
    });
});

jQuery(function ($) {
    $(".banner .stats").each(function () {
        var s = $(this);
        s.parent().parent().append(s);
        var figs = s.find("figure");
        figs.each(function (index) {
            var f = $(this);
            if (index == 0) {
                f.show();
            }
            else {
                f.hide();
            }
        });
        setInterval(function () {
            var current = figs.filter(":visible");
            var next = current.next("figure");
            if (!next.length) {
                next = figs.first();
            }
            current.hide();
            next.fadeIn();
        }, s.data("delay") || 8000);
    });
});

jQuery(function ($) {
    $("a[href^='#']").click(function (e) {
        var id = $(this).attr("href");
        var target = $(id);
        if (target.length) {
            e.preventDefault();
            var shift = target.offset().top;
            $("html,body").animate({scrollTop: shift}, shift);
        }
    });
});

jQuery(function ($) {
    $(".accordion").each(function () {
        var a = $(this);

        function collapseActive() {
            var dt = dts.filter(".active");
            var dd = dt.next("dd");
            dd.slideUp(function () {
                dd.removeClass("active");
                dt.removeClass("active");
                dd.css({
                    display: ""
                });
            });
        }

        var dts = a.find("dt").click(function () {
            var dt = $(this);
            var dd = dt.next("dd");
            var expand = !dt.hasClass("active");
            collapseActive();
            if (expand) {
                dt.addClass("active");
                dd.slideDown().addClass("active");
            }

        });
    });
});
declare function tb_show(args:string, url:string);
jQuery(function ($) {
    $(".play-video").click(function (e) {
        if ($(window).width() < 768) {
            return;
        }
        e.preventDefault();
        tb_show('', $(this).attr("href") + "&TB_iframe=1&width=800&height=600");
    });
});

interface Window{
    Hammer:any;
}

jQuery(function ($) {
    var running = false;
    $(".view-gallery-link").click(function (e) {
        e.preventDefault();
        if (running) {
            return;
        }
        running = true;
        var me = $(this);
        var url = me.data("href");

        $.post(url, {ajax:true}).then(function (html, status, xhr) {
            $.launchDialog($(html), "gallery-wrapper");
            setupGalleryCarousel();
            running = false;

        });
    });

    function setupGalleryCarousel() {

        $(".gallery-wrapper").each(function () {
            var wrapper = $(this);
            if (wrapper.data("initialized")) {
                return;
            }

            function changeImage(method, endOfTheLine) {
                var active = wrapper.find("figure.active");
                var next = active[method]("figure");
                if (!next.length) {
                    next = wrapper.find("figure:" + endOfTheLine);
                }
                active.removeClass("active");
                next.addClass("active");
                updateDisplay();
                return next;
            }

            wrapper.find(".go-right").click(function (e) {
                e.preventDefault();
                changeImage("next", "first-child");
            });

            wrapper.find(".go-left").click(function (e) {
                e.preventDefault();
                changeImage("prev", "last-child");
            });

            function updateDisplay() {
                var shown = wrapper.find("figure.active");
                wrapper.find(".current-image").text(shown.index() + 1);

                shown.find("img").css("height", "");

                var shownHeight = shown.outerHeight();
                var height = Math.min($(window).innerHeight() * .9, shownHeight);
                wrapper.animate({
                    "height": height,
                    "margin-top": (-height / 2)
                });

                if (height<shownHeight){
                    shown.find("img").animate({
                        "height":height-(95 + shown.find("figcaption").height())
                    });
                }
            }

            var onKeyDown = function (e) {
                switch (e.keyCode) {
                    case 37://left;
                        changeImage("prev", "last-child");
                        break;
                    case 39://right;
                        changeImage("next", "first-child");
                        break;
                }
            };

            $(document).on("keydown", onKeyDown);

            function removeOnKeyDown() {
                $(document).off("keydown", onKeyDown);
                $.unsubscribe("dialog-closed", removeOnKeyDown);
            }

            $.subscribe("dialog-closed", removeOnKeyDown);

            var images = wrapper.find("img");
            var loadCount = 0;
            images.load(function () {
                loadCount++;
                if (loadCount == images.length) {
                    updateDisplay();
                }
            });
            wrapper.data("initialized", 1);

            //if (!window.Hammer){
            //    window.Hammer = true;
            //    $.getScript('https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.6/hammer.min.js', function(data, status, xhr){
            //        setupHammer();
            //    });
            //}
            //else{
            //    setupHammer();
            //}
            //
            //function setupHammer(){
            //    var h = new Hammer($(".gallery-view-items")[0])
            //    h.on("panleft", function(e){
            //        console.log(e);
            //    });
            //}

        });

    }
});
