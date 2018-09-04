///<reference path="../js/shortcode-editor.ts" />
///<reference path="../../js/typings/jquery/jquery.d.ts"/>
declare var ajaxurl;
(function () {
    var fields = null;
    var args:ShortcodeEditorArgs;

    args = {
        tag: "people",
        tooltip: "list of people",
        fieldBuilder: function (callback:(fields:ShortcodeEditorElement[])=>void) {
            if (fields == null) {
                jQuery.get(ajaxurl, {action: 'get_people'}, function (people) {
                    fields = [{
                        name: "disable_scroll",
                        type: "checkbox",
                        text: "Disable Scroll"
                    }];

                    for (var i = 0; i < people.length; i++) {
                        fields.push({
                            name: "person_" + people[i].id,
                            type: "checkbox",
                            text: people[i].title
                        });
                    }

                    callback(fields);
                });
            }
            else {
                callback(fields);

            }
        }
    };

    new ShortcodeEditor(args);


})();
