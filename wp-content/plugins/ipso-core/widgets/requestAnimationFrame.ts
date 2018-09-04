(function (w) {
    "use strict";

    // Find vendor prefix, if any
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    var lastTime = 0;
    var nativeVersion = "native";
    for (var x = 0; x < vendors.length && !w.requestAnimationFrame; ++x) {
        w.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        w.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
        if (w.requestAnimationFrame) {
            nativeVersion = vendors[x];
        }
    }

    if (!w.requestAnimationFrame) {
        //console.log("Using fallback request animation frame");
        w.requestAnimationFrame = function (callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = w.setTimeout(function () {
                    callback(currTime + timeToCall);
                },
                timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };
    }
    else {
        //console.log("Using native request animation frame: " + nativeVersion);
    }

    if (!w.cancelAnimationFrame) {
        w.cancelAnimationFrame = function (id) {
            clearTimeout(id);
        };
    }

    var next = 1,
        anims:any = {};

    w.setAnimation = function (callback:()=>any, element?:Element) {
        var current = next++;
        anims[current] = true;

        var animate = function () {
            if (!anims[current]) {
                return;
            } // deleted?
            w.requestAnimationFrame(animate, element);
            callback();
        };
        w.requestAnimationFrame(animate, element);
        return current;
    };

    w.clearAnimation = function (id:Number) {
        delete anims[id];
    };

    if (w.jQuery) {
        var jQueryAnimationTimer = null;
        w.jQuery.fx.start = function () {
            if (!jQueryAnimationTimer) {
                jQueryAnimationTimer = w.setAnimation(w.jQuery.fx.tick);
            }
        }
        w.jQuery.fx.stop = function () {
            w.clearAnimation(jQueryAnimationTimer);
            jQueryAnimationTimer = null;
        }
    }

    w.performAnimation = function (callback:(pct:number)=>void, duration:number) {
        var start = new Date().getTime();
        var a = null;
        var step = function () {
            var pct = Math.min((new Date().getTime() - start) / duration, 1);
            if (pct == 1) {
                w.clearAnimation(a);
            }
            callback(pct);
        };
        a = w.setAnimation(step, null);
    };

    w.executeTimedAnimation = function (duration, callback, element) {
        var start = new Date();
        callback(0);
        var timer = w.setAnimation(function () {
            var now = new Date();
            var elapsed = now.getTime() - start.getTime();
            if (elapsed > duration) {
                w.clearAnimation(timer);
            }
            var pct = Math.min(1, elapsed / duration);
            callback(pct);
        }, element);
        return timer;
        ;
    };

}(window));

interface Window{
    requestAnimationFrom(animate:(num:number)=>void, element);
    setAnimation(callback:()=>void, element?:Element): Number;
    clearAnimation(id:Number):void;
}