///<reference path="../DefinitelyTyped/jquery/jquery.d.ts"/>
///<reference path="requestAnimationFrame.ts"/>
var Carousel = (function () {
    function Carousel(w, l, r, h) {
        var me = this;
        jQuery(function ($) {
            me.wrapper = jQuery(w);
            me.left = jQuery(l);
            me.right = jQuery(r);
            me.host = jQuery(h);
            me.setup();
        });
    }
    Carousel.prototype.setup = function () {
        var me = this;
        this.left.click(function () {
            me.moveLeft();
        });
        this.right.click(function () {
            me.moveRight();
        });
        this.selectItem(this.wrapper.find("li a").click(function (e) {
            e.preventDefault();
            me.selectItem(jQuery(this));
        }).filter(":first-child"));
    };

    Carousel.prototype.moveLeft = function () {
        var $items = this.wrapper.find("li");
        var $last = jQuery($items.get($items.length - 1));
        var $first = jQuery($items.get(0));
        var $forth = jQuery($items.get(3));

        var width = $first.width();
        var startTime = new Date();
        var duration = 300;

        var animationId = window.setAnimation(function () {
            var now = new Date();
            var pct = (now.getTime() - startTime.getTime()) / duration;
            if (pct > 1) {
                $first.insertAfter($last);
                $first.css("margin-left", "");
                $forth.css("margin-right", "");
                window.clearAnimation(animationId);
            } else {
                $forth.css("margin-right", -(width * (1 - pct)) + "px");
                $first.css("margin-left", -(width * pct) + "px");
            }
        });
    };

    Carousel.prototype.moveRight = function () {
        var $items = this.wrapper.find("li");
        var $last = jQuery($items.get($items.length - 1));
        var $first = jQuery($items.get(0));

        var width = $first.width();
        var duration = 300;

        $last.css("margin-left", "-" + width + "px");
        $last.insertBefore($first);
        var startTime = null;

        var anim = function () {
            if (startTime == null) {
                startTime = new Date();
            }
            var now = new Date();
            var pct = (now.getTime() - startTime.getTime()) / duration;
            if (pct > 1) {
                $last.css("margin-left", "");
            } else {
                window.requestAnimationFrame(anim);
                $last.css("margin-left", -(width * (1 - pct)) + "px");
            }
        };

        window.requestAnimationFrame(anim);
    };

    Carousel.prototype.selectItem = function ($item) {
        var permalink = $item.attr("href");
        var videoUrl = $item.data("video-url");
        var location = $item.data("location");
        var submitter = $item.data("submitter");
        var title = $item.attr("title");
        var img = $item.find("img").attr("src");

        this.host.find("a[data-base-href]").each(function () {
            var $link = jQuery(this);
            var u = $link.data("base-href");
            u = u.replace(/{URL}/g, encodeURIComponent(permalink));
            u = u.replace(/{IMG}/g, encodeURIComponent('<img src="' + img + '"/>'));
            $link.attr("href", u);
        });
        var videoLaunch = this.host.find(".video-launch");
        videoLaunch.css("position", "relative");
        var launch = jQuery("<img src='" + img + "'/>");
        launch.click(function () {
            var $iframe = jQuery("<iframe src='" + videoUrl + "'  border='0' frameborder='0' />");
            var off = launch.position();
            $iframe.css({ position: 'absolute', top: off.top, left: off.left, height: launch.height(), width: launch.width() });
            videoLaunch.append($iframe);
        });
        videoLaunch.html("").append(launch);

        var details = "<h4>" + title + "</h4>";
        if (submitter) {
            details += "<h5>SUBMITTED BY:</h5><div>" + submitter + "</div>";
        }
        if (location) {
            details += "<div>" + location + "</div>";
        }
        this.host.find(".video-info .details").html(details);
    };
    return Carousel;
})();

new Carousel("#video-slider ul", ".videowidget #scroll-left", ".videowidget #scroll-right", "#video-content");
//# sourceMappingURL=ipso_Widget_Videos.js.map
