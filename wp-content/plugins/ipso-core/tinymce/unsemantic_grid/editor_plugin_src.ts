///<reference path="../../DefinitelyTyped/jquery/jquery.d.ts"/>
///<reference path="../../DefinitelyTyped/jqueryui/jqueryui.d.ts"/>
///<reference path="../tinymce.d.ts"/>

declare module tinymce {
    module plugins {
        var UnsemanticGrid:tinymce.Plugin;
    }
}
(function () {
    function hasGrid(ed:tinymce.Editor) {
        var blocks = ed.selection.getSelectedBlocks();
        if (blocks.length == 0) {
            return false;
        }
        for (var i = 0; i < blocks.length; i++) {
            var block = blocks[i];
            if (testForGrid(block)) {
                return true;
            }
        }
        var element:any;
        element = blocks[0].parentNode;
        return testForGrid(element);
    }

    function testForGrid(block:Element) {
        return hasAClass(block, "grid-1", "grid-2", "grid-3", "grid-4", "grid-5", "grid-6", "grid-7", "grid-8", "grid-9", "tablet-grid-", "mobile-grid-", "hide-on-");
    }

    function isRow(ed:tinymce.Editor) {
        var blocks = ed.selection.getSelectedBlocks();
        for (var i = 0; i < blocks.length; i++) {
            var block = blocks[i];
            if (hasGridRowClass(block) != null) {
                return true;
            }
        }
        return false;
    }

    function hasGridRowClass(block) {
        return hasAClass(block, 'grid-row');
    }

    function hasAClass(block, ...classes:string[]) {
        if (block != null && block.nodeName != "#document") {
            var clazz = block.getAttribute('class');
            if (clazz != null) {
                for (var i = 0; i < classes.length; i++) {
                    if (clazz.indexOf(classes[i]) > -1) {
                        return block;
                    }
                }
            }
            return hasAClass.apply(hasAClass, [block.parentNode].concat(classes));
        }
        return null;
    }


    function addClass(element, newClass) {
        var classes = element.getAttribute('class');
        var c = (classes || '').split(' ');
        for (var i = 0; i < c.length; i++) {
            if (c[i] == newClass) {
                return;
            }
        }
        c[c.length] = newClass;
        element.setAttribute('class', c.join(' '));
    }

    function removeClass(element, oldClass) {
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
    }

    function removeWrapper(wrapper) {
        while (wrapper.childNodes.length > 0) {
            wrapper.parentNode.insertBefore(wrapper.childNodes[0], wrapper);
        }
        wrapper.remove();
    }

    tinymce.create('tinymce.plugins.UnsemanticGrid', {
            init: function (editor:tinymce.Editor, url:string) {
                editor.addCommand('mceGrid', function (ui, value) {
                    editor.windowManager.open({
                        url: url + '/grid.php',
                        width: 640,
                        height: 360,
                        inline: true
                    });
                    return true;
                });

                editor.addCommand('mceGridRow', function (ui, value) {
                    var blocks = editor.selection.getSelectedBlocks();
                    if (isRow(editor)) {
                        //remove grid-row
                        if (blocks.length == 1) {
                            var wrapper1 = hasGridRowClass(blocks[0]);
                            if (wrapper1 != null) {
                                removeClass(wrapper1, "grid-row");
                                if (!wrapper1.getAttribute("class")) {
                                    removeWrapper(wrapper1);
                                }
                            }
                        }
                    }
                    else {
                        var range = editor.selection.getRng(false);
                        if (range.startOffset == range.endOffset) {
                            //i.e. no selection
                            var row = editor.dom.create("div");
                            row['className'] = "grid-row";
                            for (var i = 0; i < blocks.length; i++) {
                                if (i == 0) {
                                    blocks[i].parentNode.replaceChild(row, blocks[i]);
                                }
                                row.appendChild(blocks[i]);
                            }
                        }
                        else {
                            var selectedText = editor.selection.getContent({"format": "html"});

                            editor.execCommand("mceInsertContent", false, "<div class='grid-row'>" + selectedText + "</div>");
                        }
                    }
                });

                function halfCommand(side:string) {
                    return positionCommand(side, 50);
                }

                function positionCommand(side:string, width:number) {
                    return function (ui, value) {
                        var extra:string = '';
                        switch (side) {
                            case 'left':
                                extra = " push-0 tablet-push-0 mobile-push-0";
                                break;
                            case 'right':
                                extra = " push-" + width + " tablet-push-" + width + " mobile-push-" + width;
                                break;
                            case 'center':
                                var half = width / 2;
                                extra = " push-" + half + " tablet-push-" + half + " mobile-push-" + half;
                                break;
                        }
                        editor.windowManager.open({
                            url: url + '/grid.php?classes=grid-' + width + ' tablet-grid-' + width + ' mobile-grid-' + width + extra,
                            width: 640,
                            height: 360,
                            inline: true
                        });
                        return true;
                    }
                }

                editor.addCommand('mceGridLeft', halfCommand('left'));
                editor.addCommand('mceGridRight', halfCommand('right'));
                editor.addCommand('mceGridCenter', halfCommand('center'));

                editor.addButton('grid', {
                    title: 'Responsive Grid Configuration',
                    cmd: 'mceGrid',
                    image: url + '/css/images/grid-icon.png'
                });

                editor.addButton('grid-row', {
                    title: 'Force New Grid Row',
                    cmd: 'mceGridRow',
                    image: url + '/css/images/grid-row-icon.png'
                });

                editor.addButton('grid-left-half', {
                    title: 'Left 1/2',
                    cmd: 'mceGridLeft',
                    image: url + '/css/images/grid-left-half.png'
                });

                editor.addButton('grid-center-half', {
                    title: 'Center 1/2',
                    cmd: 'mceGridCenter',
                    image: url + '/css/images/grid-center-half.png'
                });

                editor.addButton('grid-right-half', {
                    title: 'Right 1/2',
                    cmd: 'mceGridRight',
                    image: url + '/css/images/grid-right-half.png'
                });

                editor.onNodeChange.add(function (ed:tinymce.Editor, cm:tinymce.ControlManager, e:any) {
                    var hg = hasGrid(ed);
                    cm.setActive('grid', hg);
                    var r = ed.selection.getRng(false);
                    cm.setDisabled('grid', !hg && r.startOffset == r.endOffset);
                    cm.setActive('grid-row', isRow(ed));
                });
            }
        }
    );
    tinymce.PluginManager.add('unsemantic_grid', tinymce.plugins.UnsemanticGrid);
})();
