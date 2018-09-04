///<reference path="../typings/jquery/jquery.d.ts"/>
declare var ajaxurl:string;
jQuery(function ($) {
    $(document).on("click", ".add-a-section", function (e) {
        e.preventDefault();
        var me = $(this);
        var post_id = me.data("post_id");
        var field = me.data("field");
        var className = me.data("class");
        var index = me.data("index");

        var data = {
            action: className + '_new_section',
            post_id: post_id,
            field: field,
            index: index
        };
        $.ajax(ajaxurl, {
            type: "POST",
            dataType: "html",
            data: data
        }).done(function(html){
            me.data("index", index+1);
            me.prev(".sortable-sections").append(html).trigger("load");
        });
    });

    $(document).on("change", ".widget-list", function(e){
        var me = $(this);
        var baseName = me.data("base-name");
        var baseId = me.data("base-id");
        var form = me.parents("form");
        var values = form.serializeArray();
        var data = me.data("data-cache") || {};
        var substrPos = baseName.length + 1;
        for(var i=0,j=values.length;i<j;i++){
            var v = values[i];
            if (v.name.indexOf(baseName) == 0){
                data[v.name.substring(substrPos)] = v.value;
            }
        }
        me.data("data-cache", data);
        $.post(ajaxurl, {
            action: "widget_form",
            widget_name: baseName,
            widget_id: baseId,
            widget :data
        }, function(html, status,xhr){
            me.parents(".widget-section-header").next(".widget-section-body").html(html);
        });
    });

    $(document).on("click", ".sortable-sections .widget-list", function(e:JQueryEventObject){
        e.preventDefault();
    });

});