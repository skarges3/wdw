///<reference path="../../typings/jquery/jquery.d.ts" />

declare var wp;
interface AttachmentInfo {
    id:number;
    title:string;
    filename:string;
    url:string;
    link:string;
    attachment_id:number;
    alt:string;
    author:string;
    description:string;
    caption:string;
    name:string;
    status:string;
    uploadedTo:number;
    date:string;
    modified:string;
    menuOrder:number;
    mime:string;
    type:string;
    subtype:string;
    icon:string;
    dateFormatted:string;
    nonces:Nonces;
    editLink:string;
    sizes:Sizes;
    height:number;
    width:number;
    orientation:Orientation;
    compat: Compatibility;
}

interface Compatibility {
    item:string;
    meta:string;
}

interface Nonces {
    update:string;
    delete:string;
}

interface Sizes {
    full: SizeInfo;
}

interface SizeInfo {
    url:string;
    height:number;
    width:number;
    orientation:Orientation;
}

enum Orientation{
    portrait,
    landscape
}

interface MediaModalOptions {
    calling_selector: any;
    callback: (caller:JQuery, attachment:AttachmentInfo)=>void;
    args:DialogArgs;
}

interface DialogArgs {
    title: string;
    button:ButtonArgs;
    type: LibraryArgs;
}

interface ButtonArgs {
    text: string;
}

interface LibraryArgs {
    type:string;
}


class FileMediaModal {
    private frame:any;
    private settings:MediaModalOptions;

    constructor(settings:MediaModalOptions) {
        this.settings = jQuery.extend(
            {
                calling_selector: false,
                callback: function () {
                },
                args: {}
            },
            settings);
        this.attachEvents();
    }

    public attachEvents() {
        var me = this;
        jQuery(this.settings.calling_selector).click(function () {
            me.openFrame(this);
        });
    }

    public openFrame(caller:any) {
        var $caller = jQuery(caller);
        var uploaderTitle:string = $caller.data("uploader_title");
        var buttonText:string = $caller.data("uploader_button_text");
        var library:string;
        var args = jQuery.extend(
            {
                title: uploaderTitle,
                button: {
                    text: buttonText
                },
                library: {
                    type: library
                }
            }, this.settings.args
        );
        var me = this;
        this.frame = wp.media(args);

        this.frame.on('toolbar:create:select', function () {
            me.frame.state().set('filterable', 'uploaded');
        });

        // When an image is selected, run the callback.
        this.frame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            var attachment = me.frame.state().get('selection').first().toJSON();
            me.settings.callback($caller, attachment);
        });

        this.frame.on('open activate', function () {
            // Get the link/button/etc that called us
            // Select the thumbnail if we have one
            if ($caller.data('thumbnail_id')) {
                var Attachment = wp.media.model.Attachment;
                var selection = me.frame.state().get('selection');
                selection.add(Attachment.get($caller.data('thumbnail_id')));
            }
        });

        this.frame.open();
    }
}
;