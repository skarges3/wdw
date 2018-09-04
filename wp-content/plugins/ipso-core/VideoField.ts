///<reference path='typings/jquery/jquery.d.ts'/>
///<reference path='lib/youtube.d.ts'/>
/**
 * Created with IntelliJ IDEA.
 * User: joe
 * Date: 7/23/13
 * Time: 8:44 AM
 * To change this template use File | Settings | File Templates.
 */
declare var ajaxurl;
declare var googleApiKey;
class VideoField {

    constructor(public post_id:string, public field:string, public url:string) {
        var me:VideoField = this;
        jQuery(function () {
            me.parseUrl(jQuery("#" + field).change(function () {
                me.parseUrl(jQuery(this));
            }));
        })
    }

    public loadVimeoTumbnails(videoId:string, $thumbnails:JQuery, $thumb:JQuery) {
        var me:VideoField = this;
        jQuery.ajax({
            url: "http://vimeo.com/api/v2/video/" + videoId + ".json",
            dataType: "jsonp",
            success: function (data, status, xhr) {
                if (data.length > 0) {
                    var sizes = ["small", "medium", "large"];
                    $thumbnails.html("");
                    for (var i = 0; i < sizes.length; i++) {
                        me.addImage($thumbnails, data[0]["thumbnail_" + sizes[i]], $thumb, sizes[i]);
                    }
                    me.addImage($thumbnails, '', $thumb, 'None');
                }
            }
        });
    }

    public loadYouTubeThumbnails(videoId:string, $thumbnails:JQuery, $thumb:JQuery) {
        var me:VideoField = this;
        jQuery.ajax({
            url: "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=" + encodeURIComponent(videoId) + "&key=" + encodeURIComponent(googleApiKey),
            dataType: "jsonp",
            success: function (result, status, xhr) {
                var data:YouTube.Response = result;
                if (data.items.length > 0) {
                    $thumbnails.html("");
                    for (var size in data.items[0].snippet.thumbnails) {
                        var thumbnail:YouTube.Thumbnail = data.items[0].snippet.thumbnails[size];
                        var url = thumbnail.url;
                        me.addImage($thumbnails, url, $thumb, size);
                    }
                    me.addImage($thumbnails, '', $thumb, 'None');
                }
                else {
                    $thumbnails.html("No thumbnails found");
                }
            }
        });
    }

    private static emptyUrl = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAoAAAAHgCAYAAAA10dzkAAAABmJLR0QAAAAAAAD5Q7t/AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3wYCExAZOjyeyQAABL5JREFUeNrtwQENAAAAwqD3T20ON6AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA+DMLuAAH7E9MmAAAAAElFTkSuQmCC";

    private addImage($thumbnails:JQuery, url:string, $thumb:JQuery, label:string = 'test') {
        var $wrap = jQuery("<div></div>");
        $wrap.css({
            display: "block",
            width: "100px",
            float: "left",
            height: "90px",
            "padding": "4px 8px 4px 0",
            position: "relative"
        });
        var $copy = jQuery("<img>");
        $copy.attr("src", url || VideoField.emptyUrl);
        $copy.css({
            position: "absolute",
            "width": "300px",
            "height": "auto",
            "display": "none"
        });
        jQuery("body").append($copy);
        var showing = false;
        $wrap
            .mouseover(function (e) {
                $copy.css({
                    left: ((e.pageX - e.offsetX) - (($copy.width() - $wrap.width()) / 2)) + "px",
                    top: (e.pageY - (e.offsetY + $copy.height())) + "px"
                });
                if (!showing) {
                    $copy.fadeIn();
                    showing = true;
                }
            })
            .mouseout(function () {
                $copy.stop().hide();
                showing = false;
            });
        var $img = jQuery("<img>");
        $img.css({
            display: "inline-block",
            "max-width": "100%",
            "height": "auto"
        });
        $img.attr("src", url || VideoField.emptyUrl);
        var selectedState = {"border": "dotted 4px #000"};
        if ($thumb.val() == url) {
            $img.css(selectedState);
        }
        $img.click(function () {
            $thumbnails.find("img").css("border", "none");
            $thumb.val(url);
            $img.css(selectedState);
        });
        $wrap.append($img);
        $wrap.append("<div style='position:absolute; bottom: 0;'>" + label + "</div>");
        $thumbnails.append($wrap);
    }

    public parseUrl($input:JQuery) {
        var $parent = $input.parent();
        var $type = $parent.find(".meta_video_type");
        var $id = $parent.find(".meta_video_id");
        var $thumbnails = $parent.find(".thumbnails");
        $thumbnails.addClass("description");
        var $thumb = $parent.find(".meta_video_thumb");
        var value = $input.val();
        if (value == '') {
            $thumbnails.html('Enter a URL from either YouTube or Vimeo');
            return;
        }
        var vimeo = /vimeo.com\/(video\/)?([\d]+)/;
        var youtube = /(youtu\.be\/|youtube\.com\/watch\?v=|youtube\.com\/embed\/)([^\?]+)/;
        var match = value.match(vimeo);
        $type.val("");
        $id.val("");
        $thumbnails.html("Loading...");
        if (match) {
            $type.val("V");
            $id.val(match[2]);
            this.loadVimeoTumbnails(match[2], $thumbnails, $thumb);
            this.setUrl($input, $type, $id);
            return;
        }
        else {
            match = value.match(youtube);
            if (match) {
                $type.val("YT");
                $id.val(match[2]);
                this.loadYouTubeThumbnails(match[2], $thumbnails, $thumb);
                this.setUrl($input, $type, $id);
                return;
            }
        }
        $thumbnails.html("Unknown video");
    }

    public setUrl($input:JQuery, $type:JQuery, $id:JQuery) {
        switch ($type.val()) {
            case 'YT':
                $input.val("//youtube.com/embed/" + $id.val() + "?autoplay=1");
                break;
            case 'V':
                $input.val("//player.vimeo.com/video/" + $id.val() + "?autoplay=1");
                break;
        }
    }

    public attachThumbnail(post_id, video_id, thumbnail_size, callback:Function) {
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: "ensure_video_thumbnail",
                post_id: post_id,
                video_id: video_id,
                thumbnail_size: thumbnail_size
            },
            dataType: "json",
            success: function (data, status, xhr) {
                callback(data);
            },
            error: function (xhr, status, message) {
                if (message) {
                    alert(message);
                }
            }
        });
    }

    public setThumbnail(result:string) {
        jQuery.post(ajaxurl, {
            action: "set-post-thumbnail",
            post_id: this.post_id,
            thumbnail_id: result,
            _wpnonce: jQuery("#_wpnonce").val(),
            json: true
        }, function (response) {

            if (!response || !response.success) {
                alert(response.data);
            } else {
                window.WPSetThumbnailID && window.WPSetThumbnailID(result);
                window.WPSetThumbnailHTML && window.WPSetThumbnailHTML(response.data);
            }
        });

    }
}

interface Window {
    WPSetThumbnailID(result:any);
    WPSetThumbnailHTML(response:any);
}