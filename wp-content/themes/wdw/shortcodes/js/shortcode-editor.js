///<reference path="../../js/typings/jquery/jquery.d.ts"/>
var ShortcodeEditor = (function () {
    function ShortcodeEditor(args) {
        this.args = args;
        this.tag = args.tag;
        this.tooltip = args.tooltip;
        this.fields = args.fields;
        this.fieldBuilder = args.fieldBuilder;
        this.setup();
    }
    ShortcodeEditor.prototype.setup = function () {
        var me = this;
        tinymce.PluginManager.add(this.tag, function (editor, url) {
            me.plugin(editor, url);
        });
    };
    ShortcodeEditor.prototype.plugin = function (editor, url) {
        var me = this;
        editor.addCommand(this.tag + "_popup", function (ui, value) {
            me.popup(editor, ui, value);
        });
        editor.addButton(this.tag, {
            icon: this.tag,
            tooltip: "Add " + this.tooltip,
            onclick: function () {
                editor.execCommand(me.tag + "_popup", "", {
                    attributes: me.defaults || {},
                    content: ''
                });
            }
        });
        editor.on('BeforeSetcontent', function (event) {
            event.content = me.replaceShortcodes(event.content, url);
        });
        editor.on('GetContent', function (event) {
            event.content = me.restoreShortcodes(event.content);
        });
        editor.on('DblClick', function (e) {
            var hasCls = e.target.className.indexOf('wp-' + me.tag) > -1;
            if (e.target.nodeName == 'IMG' && hasCls) {
                var attributes = e.target.attributes['data-sh-attr'].value;
                attributes = decodeURIComponent(attributes);
                var content = e.target.attributes['data-sh-content'].value;
                editor.execCommand(me.tag + '_popup', '', {
                    attributes: attributes,
                    content: content
                });
            }
        });
    };
    ShortcodeEditor.prototype.popup = function (editor, ui, input) {
        var attributes = input.attributes || {};
        var content = input.content || '';
        var me = this;
        function displayPopup(fields) {
            var args;
            args = {};
            args.title = "Edit " + me.tooltip;
            args.body = [];
            args.minHeight = me.args.minHeight;
            args.minWidth = me.args.minWidth;
            args.layout = me.args.layout;
            args.padding = me.args.padding;
            for (var i = 0, j = fields.length; i < j; i++) {
                var value = null;
                if (fields[i].hasOwnProperty('name')) {
                    var n = fields[i]['name'];
                    if (n == "content") {
                        value = decodeURIComponent(content);
                    }
                    else {
                        value = me.getAttr(attributes, n);
                    }
                }
                args.body[args.body.length] = me.buildField(fields[i], value);
            }
            args.onsubmit = function (e) {
                editor.insertContent(me.buildShortcode(e.data));
            };
            editor.windowManager.open(args);
        }
        if (this.fields) {
            displayPopup(this.fields);
        }
        else if (this.fieldBuilder) {
            this.fieldBuilder(function (fields) {
                me.fields = fields;
                displayPopup(fields);
            });
        }
        else {
            alert("Shortcode fields have not be defined");
        }
    };
    ShortcodeEditor.prototype.buildField = function (field, value) {
        var obj;
        obj = {};
        for (var p in field) {
            obj[p] = field[p];
        }
        if (field.type == "checkbox" || field.type == "radio") {
            obj.checked = value;
        }
        else {
            obj.value = value;
        }
        return obj;
    };
    ShortcodeEditor.prototype.buildShortcode = function (data) {
        var sc = ["[", this.tag];
        for (var i = 0, j = this.fields.length; i < j; i++) {
            if (this.fields[i].hasOwnProperty('name')) {
                var name = this.fields[i]['name'];
                if (name == "content")
                    continue;
                var v = data[name];
                if (v) {
                    sc[sc.length] = " ";
                    sc[sc.length] = name;
                    sc[sc.length] = "=\"";
                    sc[sc.length] = encodeURIComponent(v);
                    sc[sc.length] = "\"";
                }
            }
        }
        var c = data["content"];
        if (c) {
            sc[sc.length] = "]";
            sc[sc.length] = c;
            sc[sc.length] = "[/";
            sc[sc.length] = this.tag;
            sc[sc.length] = "]";
        }
        else {
            sc[sc.length] = "/]";
        }
        return sc.join("");
    };
    ShortcodeEditor.prototype.getAttr = function (s, n) {
        var v = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
        return v ? decodeURIComponent(v[1]) : '';
    };
    ShortcodeEditor.html = function (tag, data, content, url) {
        var placeholder = url + '/placeholder.png';
        data = encodeURIComponent(data);
        content = encodeURIComponent(content);
        return '<img data-shortcode="' + tag + '" src="' + placeholder + '" class="mceItem wp-' + tag + '" ' + 'data-sh-attr="' + data + '" data-sh-content="' + content + '" data-mce-resize="false" data-mce-placeholder="1" />';
    };
    ShortcodeEditor.prototype.replaceShortcodes = function (content, url) {
        var me = this;
        function r(all, attr, con) {
            return ShortcodeEditor.html(me.tag, attr, con, url);
        }
        function r1(all, attr) {
            return ShortcodeEditor.html(me.tag, attr, '', url);
        }
        return content.replace(new RegExp("\\[" + this.tag + " ([^\\]]*)\\]([^\\]]*)\\[\\/" + this.tag + "\\]", "g"), r).replace(new RegExp("\\[" + this.tag + " ([^\\]]*)\\]", "g"), r1);
    };
    ShortcodeEditor.prototype.restoreShortcodes = function (content) {
        var me = this;
        var re = new RegExp('(?:<p(?: [^>]+)?>)*(<img [^>]+data-shortcode="' + me.tag + '"[^>]+>)(?:<\/p>)*', "g");
        return content.replace(re, function (match, image) {
            var data = me.getAttr(image, 'data-sh-attr');
            var con = me.getAttr(image, 'data-sh-content');
            var out = [];
            if (data || con) {
                //out[out.length] = '<p>';
                out[out.length] = '[';
                out[out.length] = me.tag;
                if (data) {
                    data = data.trim();
                    if (data.lastIndexOf('/') == data.length - 1) {
                        data = data.substr(0, data.length - 1);
                    }
                    out[out.length] = " ";
                    try {
                        out[out.length] = decodeURIComponent(data);
                    }
                    catch (Ex) {
                        out[out.length] = data;
                    }
                }
                if (con) {
                    out[out.length] = ']';
                    try {
                        out[out.length] = decodeURIComponent(con);
                    }
                    catch (Ex) {
                        out[out.length] = con;
                    }
                    out[out.length] = '[/';
                    out[out.length] = me.tag;
                    out[out.length] = ']';
                }
                else {
                    out[out.length] = '/]';
                }
            }
            return out.join('');
        });
    };
    ShortcodeEditor.htmlEscape = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    };
    ShortcodeEditor.htmlUnescape = function (str) {
        return String(str).replace(/&amp;/g, '&').replace(/&quot;/g, '\"').replace(/&#39;/g, '\'').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
    };
    return ShortcodeEditor;
})();
var ShortcodeMediaEditor = (function () {
    function ShortcodeMediaEditor(name, label, tooltip) {
        this.name = name;
        this.label = label;
        this.tooltip = tooltip;
        this.type = "button";
        this.subtype = "file";
        this.text = "Change";
    }
    ShortcodeMediaEditor.prototype.onclick = function (sender) {
        var modal = new FileMediaModal({
            callback: function (caller, attachment) {
                var id = attachment.id;
                var filename = attachment.url;
                var title = attachment.title;
                sender.control.value(id);
                sender.control.getEl().nextSibling.setAttribute("src", filename);
            },
            args: {
                library: {}
            }
        });
        modal.openFrame();
    };
    ShortcodeMediaEditor.prototype.onpostrender = function (sender) {
        var btn = sender.control.getEl();
        var id = sender.control.value();
        var img = btn.ownerDocument.createElement("img");
        img.setAttribute("src", 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAD0lEQVQIW2NkwAIYaSAIAAGkAAbQmcMRAAAAAElFTkSuQmCC');
        if (id) {
            jQuery.get(ajaxurl, {
                action: 'get_attachment_thumb_url',
                attachment_id: id
            }, function (result, status, xhr) {
                if (result) {
                    img.setAttribute("src", result);
                }
            });
        }
        else {
        }
        btn.parentElement.appendChild(img);
        img.style.width = "100px";
        img.style.height = "auto";
        img.style.display = "block";
        img.style.position = "absolute";
        img.style.cssText += "left:100%;top:0;margin-left: 20px;";
    };
    ShortcodeMediaEditor.extend = function (obj) {
        return new ShortcodeMediaEditor(obj.name, obj.label, obj.tooltip);
    };
    return ShortcodeMediaEditor;
})();
//# sourceMappingURL=shortcode-editor.js.map