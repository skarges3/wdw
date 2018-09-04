<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript" src="../../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/tinymce/utils/mctabs.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/tinymce/utils/editable_selects.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/tinymce/utils/form_utils.js"></script>
    <script type="text/javascript" src="js/gridutils.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/jquery/jquery.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/jquery/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/jquery/ui/core.min.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/jquery/ui/widget.min.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/jquery/ui/mouse.min.js"></script>
    <script type="text/javascript" src="../../../../../wp-includes/js/jquery/ui/slider.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../css/cmyk/jquery-ui-1.10.4.custom.min.css"/>
    <style>
        .ui-slider-range{
            text-align: center;;
        }
        .slider-label{
            position: absolute;
            top: 14px;
            font-size: 12px;
        }
        .ui-slider{
            margin-bottom: 20px;
            margin-left: 10px;
            margin-right: 10px;
        }
        #controls{
            text-align: center;
        }
        #resetButton{
            float: left;
        }
        #cancelButton{
            float: right;
        }
        h2 label{
            font-size: 14px;
            color: #000;
        }
        h2{
            vertical-align: bottom;
        }
    </style>
    <?php if (!empty($_GET['classes'])){?>
    <script>
        window.classDefaults = <?php echo json_encode($_GET['classes'])?>;
    </script>
    <?php } ?>
</head>
<body>
<img style="float:right;" src="../../images/ipso-logo-dark.png">
<br/>

<form id="gridForm" onsubmit="return false">
    <h2>Desktop <input type="checkbox" id="desktop_hide" value="hide-on-desktop"/><label for="desktop_hide">hide</label></h2>

    <div id="desktop" name="desktop"></div>

    <h2>Tablet <input type="checkbox" id="tablet_hide" value="hide-on-tablet"/><label for="tablet_hide">hide</label></h2>

    <div id="tablet" name="mobile"></div>

    <h2>Mobile <input type="checkbox" id="mobile_hide" value="hide-on-mobile"/><label for="mobile_hide">hide</label></h2>

    <div id="mobile" name="mobile"></div>


    <div id="controls">
        <button id="resetButton" onclick="GridUtils.resetSliders();return false;">Reset</button>
    <button id="updateButton" onclick="GridUtils.updateAction();return false;">Update</button>
    <button id="cancelButton" onclick="GridUtils.cancelEdit();return false;">Cancel</button>
    </div>
</form>
</body>
</html>