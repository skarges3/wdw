<?php
function customImageJs()
{
    ?>
    <style type="text/css">
        .image_wrap img{
            max-width:140px;
            max-height:140px;
            height:auto;
            float:right;
        }
        .field_wrap{
            float:left;
        }
    </style>

    <script type="text/javascript">

        (function ($) {
            function updateImage($input){
                var url = $input.val();
                var $wrap =$input.parents(".field_row").find(".image_wrap");
                $wrap.html('');
                if (url){
                    var $img = $("<img>");
                    $img.attr("src", url);
                    $wrap.append($img);
                }
            }
            $(function () {
                $("#add_field_row input").on('click', function (e) {
                    e.preventDefault();
                    var row = $('#master-row').html();
                    var $me = $(this);
                    var $form = $me.parents("#dynamic_form");
                    var $wrap = $form.find("#field_wrap");
                    $wrap.append(row);
                    return false;
                });
                $(document).on('change', 'input.meta_image_url', function(){
                    updateImage($(this));
                });
                $(document).on('click', "#add-image-button", function (e) {
                    var parent = $(this).parent().parent('.field_row');
                    var inputField = $(parent).find("input.meta_image_url");

                    tb_show('', 'media-upload.php?TB_iframe=true');

                    window.send_to_editor = function (html) {
                        var element = $(html);
                        var img;
                        if (element.length>0){
                            if (element[0].tagName=="IMG"){
                                img = element;
                            }
                        }
                        if (img==null){
                            img = element.find("img");
                        }
                        var url = img.attr('src');
                        inputField.val(url);
                        updateImage(inputField);
                        tb_remove();
                    };

                    return false;
                });
                $(document).on('click', "#clear-image-button", function (e) {
                    e.preventDefault();
                    var $parent = $(this).parents("#dynamic_form");
                    var $input = $parent.find("input.meta_image_url");
                    $input.val('');
                    updateImage($input);
                    return false;
                });
            })
        })(jQuery);

    </script><?php

}

customImageJs();
