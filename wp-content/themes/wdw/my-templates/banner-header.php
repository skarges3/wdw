<?php
/**
 * Template Name: Banner Header
 */

get_header();

the_post();
global $post;
$banner_set = get_term_by('slug', "page---{$post->post_name}", 'banner_set');

if (!empty($banner_set->term_id)) { ?>
    <div id="page-header">
        <?php
        ipso_Widget_Banners::banner_content(array(
            'banner_set' => $banner_set->term_id,
            'delay' => 5000,
            'height' => 375,
            'mobile_height' => 375
        ));
        ?>
    </div>
<?php } ?>
    <div id="page-intro">
        <h3><?php the_title() ?></h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <article class="grid-100 tablet-grid-100 mobile-grid-100">
            <section id="primary">
                <?php the_content() ?>
            </section>
            </nav>
        </article>
    </div>
<?php
get_footer();