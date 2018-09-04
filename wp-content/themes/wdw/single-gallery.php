<?php
$doing_ajax = isset($_POST['ajax']);
if (!$doing_ajax) {
    get_header();
    get_template_part('page', 'header-blog');
    ?><div id="page-intro">
        <h3><?php get_the_title(get_queried_object_id())?></h3>
        <?php do_action('breadcrumbs') ?>
    </div><?php
    echo '<div class="grid-container"><div id="primary"><div class="grid-100 tablet-grid-100 mobile-grid-100"></div>';
}
the_post();
$gallery = get_post_meta(get_the_ID(), '_gallery', true);
$wp_query = new WP_Query(array(
    'post_type' => 'attachment',
    'post__in' => explode(',', $gallery),
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'orderby' => 'post__in',
));

?>
<div class="gallery-view">
    <?php
    if ($doing_ajax) {
        ?>
    <div class="gallery-header">
        <div><span class="current-image">1</span>/<?php echo $wp_query->post_count?></div>
    </div>
    <?php

    } ?>
    <div class="gallery-view-items">
    <?php
    $first = true;
    while ($wp_query->have_posts()) {
        $wp_query->the_post();
        ?>
        <figure<?php echo $first ? ' class="active"' : ''?>>
        <?php echo wp_get_attachment_image(get_the_ID(), 'large') ?>
        <figcaption><?php the_excerpt() ?></figcaption>
        </figure><?php
        $first = false;
    }
    ?>
    </div>
    <?php
    if ($doing_ajax) {
        ?>
        <div class="gallery-nav">
                <span class="go-left">&lt;</span>
                <span class="go-right">&gt;</span>
        </div>
        <?php

    }
    ?>
</div>
<?php
if (!$doing_ajax) {
    echo '</div></div>';
    get_footer();
}
 ?>
