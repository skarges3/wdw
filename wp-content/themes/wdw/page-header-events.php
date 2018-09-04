<?php
$obj = get_queried_object();

if (!empty($obj->taxonomy) && $obj->taxonomy == 'initiative_type') {
    $img_id = get_metadata('initiative_type', $obj->term_id, 'header_image', true);
    $title = get_metadata('initiative_type', $obj->term_id, 'header_title', true);
    $sub_title = get_metadata('initiative_type', $obj->term_id, 'header_sub_title', true);
    ?>
    <div id="page-header" style="background-image:url(<?php echo wp_get_attachment_url($img_id) ?>);">
    <h1><?php echo $title ?></h1>

    <h2><?php echo $sub_title ?></h2>
    </div>
    <?php
} else {
    ?>
    <div id="page-header" style="background-image:url(<?php do_action('header_image', 'events') ?>);">
        <h1><?php echo get_option('events_header_title') ?></h1>

        <h2><?php echo get_option('events_header_sub_title') ?></h2>
    </div>
<?php }