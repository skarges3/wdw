///<reference path="../../../DefinitelyTyped/jquery/jquery.d.ts"/>
///<reference path="../../tinymce.d.ts"/>
var Helpers = (function () {
    function Helpers() {
    }
    Helpers.hasGrid = function (ed) {
        var blocks = ed.selection.getSelectedBlocks();
        if (blocks.length == 0) {
            return false;
        }
        for (var i = 0; i < blocks.length; i++) {
            var block = blocks[i];
            if (Helpers.testForGrid(block)) {
                return true;
            }
        }
        var element;
        element = blocks[0].parentNode;
        return Helpers.testForGrid(element);
    };
    Helpers.testForGrid = function (block) {
        return Helpers.hasAClass(block, "grid-1", "grid-2", "grid-3", "grid-4", "grid-5", "grid-6", "grid-7", "grid-8", "grid-9", "tablet-grid-", "mobile-grid-", "hide-on-");
    };
    Helpers.isRow = function (ed) {
        var blocks = ed.selection.getSelectedBlocks();
        for (var i = 0; i < blocks.length; i++) {
            var block = blocks[i];
            if (Helpers.hasGridRowClass(block) != null) {
                return true;
            }
        }
        return false;
    };
    Helpers.hasGridRowClass = function (block) {
        return Helpers.hasAClass(block, 'grid-row');
    };
    Helpers.hasAClass = function (block) {
        var classes = [];
        for (var _i = 1; _i < arguments.length; _i++) {
            classes[_i - 1] = arguments[_i];
        }
        if (block != null && block.nodeName != "#document") {
            var clazz = block.getAttribute('class');
            if (clazz != null) {
                for (var i = 0; i < classes.length; i++) {
                    if (clazz.indexOf(classes[i]) > -1) {
                        return block;
                    }
                }
            }
            return Helpers.hasAClass.apply(Helpers.hasAClass, [block.parentNode].concat(classes));
        }
        return null;
    };
    Helpers.addClass = function (element, newClass) {
        var classes = element.getAttribute('class');
        var c = (classes || '').split(' ');
        for (var i = 0; i < c.length; i++) {
            if (c[i] == newClass) {
                return;
            }
        }
        c[c.length] = newClass;
        element.setAttribute('class', c.join(' '));
    };
    Helpers.removeClass = function (element, oldClass) {
        var classes = element.getAttribute('class');
        var c = (classes || '').split(' ');
        for (var i = 0; i < c.length; i++) {
            if (c[i] == oldClass) {
                c = c.slice(0, i).concat(c.slice(i + 1));
                i--;
            }
        }
        if (c.length == 0) {
            element.removeAttribute('class');
        }
        else {
            element.setAttribute('class', c.join(' '));
        }
    };
    Helpers.removeWrapper = function (wrapper) {
        while (wrapper.childNodes.length > 0) {
            wrapper.parentNode.insertBefore(wrapper.childNodes[0], wrapper);
        }
        wrapper.remove();
    };
    return Helpers;
})();
var GridUtils = (function () {
    function GridUtils() {
    }
    GridUtils.getValues = function () {
        return {
            desktop: GridUtils.getValue("desktop"),
            tablet: GridUtils.getValue("tablet"),
            mobile: GridUtils.getValue("mobile"),
            "hide-on-": GridUtils.getHiddens()
        };
    };
    GridUtils.getHiddens = function () {
        var v = [];
        for (var i = 0; i < GridUtils.modes.length; i++) {
            var m = GridUtils.modes[i];
            v[m] = jQuery("#" + m + "_hide").is(":checked") ? 1 : '';
        }
        return v;
    };
    GridUtils.getValue = function (type) {
        var slider = jQuery("#" + type);
        var v = slider.slider("values");
        return {
            width: v[1] - v[0],
            push: v[0] > 0 ? v[0] : 0,
            pull: v[0] < 0 ? -v[0] : 0
        };
    };
    GridUtils.toClasses = function (values) {
        var cls = {};
        for (var x in values) {
            var v = values[x];
            if (x == "hide-on-") {
                for (var y in v) {
                    cls[x + y] = v[y] ? '' : null;
                }
            }
            else {
                cls[((x == "desktop") ? "" : x + "-") + "grid-"] = v.width == 0 ? null : v.width;
                cls[((x == "desktop") ? "" : x + "-") + "pull-"] = v.pull == 0 ? null : v.pull;
                cls[((x == "desktop") ? "" : x + "-") + "push-"] = v.push == 0 ? null : v.push;
            }
        }
        return cls;
    };
    GridUtils.applyAction = function () {
        var ed = tinyMCEPopup.editor;
        var form = document.getElementById("gridForm");
        var blocks = GridUtils.blocks;
        var wrapper;
        if (GridUtils.needToWrap) {
            wrapper = ed.dom.create('div');
            blocks = [wrapper];
        }
        for (var i = 0; i < blocks.length; i++) {
            var block = blocks[i];
            var newClasses = GridUtils.toClasses(GridUtils.getValues());
            var clss = (block.getAttribute("class") || "").split(' ');
            for (var j = 0; j < clss.length; j++) {
                var cn = clss[j];
                for (var prefix in newClasses) {
                    if (cn.indexOf(prefix) == 0) {
                        if (newClasses[prefix] == null) {
                            clss[j] = '';
                        }
                        else {
                            clss[j] = prefix + newClasses[prefix];
                            newClasses[prefix] = null;
                        }
                    }
                }
            }
            for (var prefix in newClasses) {
                if (newClasses[prefix] != null) {
                    clss[clss.length] = prefix + newClasses[prefix];
                }
            }
            clss = clss.filter(function (v) {
                return v != '';
            });
            if (clss.length == 0) {
                GridUtils.needToWrap = false;
                Helpers.removeWrapper(block);
            }
            else {
                block.setAttribute("class", clss.join(" "));
            }
        }
        if (GridUtils.needToWrap && clss.length > 0) {
            blocks = ed.selection.getSelectedBlocks();
            blocks[0].parentNode.insertBefore(wrapper, blocks[0]);
            for (var i = 0; i < blocks.length; i++) {
                wrapper.appendChild(blocks[i]);
            }
        }
    };
    GridUtils.updateAction = function () {
        GridUtils.applyAction();
        tinyMCEPopup.close();
    };
    GridUtils.cancelEdit = function () {
        tinyMCEPopup.close();
    };
    GridUtils.nearest = function (v) {
        if (v >= 99) {
            return 100;
        }
        var m = v % 5;
        if (m == 0 || v % 33 == 0) {
            return v;
        }
        if (m > 2) {
            return (v + (5 - m));
        }
        else {
            return (v - m);
        }
    };
    GridUtils.updateRangeSize = function (elem, values) {
        var ranges = elem.find(".ui-slider-range");
        for (var i = 0; i < values.length - 1; i++) {
            var range = values[i + 1] - values[i];
            jQuery(ranges[i]).text(range);
        }
    };
    GridUtils.setupSlider = function (s1) {
        s1.slider({
            min: -100,
            max: 200,
            range: true,
            values: [0, 100],
            change: function (event, ui) {
                var ref = 0;
                for (var i = 0; i < ui.values.length; i++) {
                    var n = ui.values[i];
                    var gap = n - ref;
                    gap = GridUtils.nearest(gap);
                    var newV = ref + gap;
                    if (ui.values[i] != newV) {
                        ui.values[i] = newV;
                        s1.slider("values", i, newV);
                    }
                    ref = ui.values[i];
                }
                GridUtils.updateRangeSize(s1, ui.values);
            }
        }).each(function () {
            // Add labels to slider whose values
            // are specified by min, max
            jQuery(this).css("position", "relative");
            // Get the options for this slider (specified above)
            var opt = jQuery(this).data().uiSlider.options;
            // Get the number of possible values
            var vals = opt.max - opt.min;
            // Position the labels
            var ents = {
                '-25': '&#188;',
                '-33': '&#8531;',
                '-50': '&#189;',
                '-66': '&#8532;',
                '-75': '&#190;',
                '-100': '',
                0: '|',
                25: '&#188;',
                33: '&#8531;',
                50: '&#189;',
                66: '&#8532;',
                75: '&#190;',
                100: '|',
                125: '&#188;',
                133: '&#8531;',
                150: '&#189;',
                166: '&#8532;',
                175: '&#190;',
                200: ''
            };
            for (var pct in ents) {
                var pctInt = parseInt(pct);
                var el = jQuery('<label class="slider-label">' + (ents[pct]) + '</label>').css('left', ((100 + pctInt) / 3) + '%');
                jQuery(this).append(el);
                el.css({
                    "margin-left": (-el.width() / 2) + "px"
                });
            }
        });
        return s1;
    };
    GridUtils.init = function () {
        var desktop = GridUtils.setupSlider(jQuery("#desktop"));
        var tablet = GridUtils.setupSlider(jQuery("#tablet"));
        var mobile = GridUtils.setupSlider(jQuery("#mobile"));
        var ed = tinyMCEPopup.editor;
        var form = document.getElementById("gridForm");
        var range = ed.selection.getRng(false);
        var blocks = ed.selection.getSelectedBlocks();
        if (range.startOffset == range.endOffset) {
            blocks = [Helpers.testForGrid(blocks[0])];
        }
        var found = false;
        for (var i = 0; i < blocks.length; i++) {
            var block = blocks[i];
            var blockClass = (block.getAttribute("class") || "");
            if (window['classDefaults']) {
                blockClass += ((blockClass.length > 0) ? " " : "") + window['classDefaults'];
            }
            var clss = blockClass.split(' ');
            var info = {
                'desktop': {
                    'width': 0,
                    'pull': 0,
                    'push': 0
                },
                'tablet': {
                    'width': 0,
                    'pull': 0,
                    'push': 0
                },
                'mobile': {
                    'width': 0,
                    'pull': 0,
                    'push': 0
                }
            };
            var map = {
                'grid-': 'width',
                'pull-': 'pull',
                'push-': 'push'
            };
            for (var j = 0; j < clss.length; j++) {
                var cn = clss[j];
                var s = cn.split("-");
                var num = parseInt(s[s.length - 1]);
                if (isNaN(num)) {
                    continue;
                }
                for (var classKey in map) {
                    if (cn.indexOf(classKey) == 0) {
                        info['desktop'][map[classKey]] = num;
                        found = true;
                    }
                    else if (cn.indexOf('tablet-' + classKey) == 0) {
                        info['tablet'][map[classKey]] = num;
                        found = true;
                    }
                    else if (cn.indexOf('mobile-' + classKey) == 0) {
                        info['mobile'][map[classKey]] = num;
                        found = true;
                    }
                }
            }
            for (var size in info) {
                var arg = info[size];
                var start = -arg.pull + arg.push;
                var end = start + arg.width;
                jQuery("#" + size).slider("values", 0, start);
                jQuery("#" + size).slider("values", 1, end);
                var hide_class = "hide-on-" + size;
                var checkbox = jQuery("#" + size + "_hide");
                if (blockClass.indexOf(hide_class) > -1) {
                    checkbox.attr("checked", "checked");
                    found = true;
                }
                else {
                    checkbox.removeAttr("checked");
                }
            }
            if (found) {
                GridUtils.blocks = blocks;
                GridUtils.needToWrap = false;
            }
            else {
                GridUtils.blocks = ed.selection.getSelectedBlocks();
                GridUtils.needToWrap = true;
            }
        }
    };
    GridUtils.resetSliders = function () {
        var sizes = ["desktop", "tablet", "mobile"];
        for (var i = 0; i < sizes.length; i++) {
            var size = sizes[i];
            jQuery("#" + size).slider("values", 0, 0).slider("values", 1, 0);
            jQuery("#" + size + "_hide").removeAttr("checked");
        }
    };
    GridUtils.modes = ["desktop", "tablet", "mobile"];
    return GridUtils;
})();
tinyMCEPopup.onInit.add(GridUtils.init);
//# sourceMappingURL=gridutils.js.map