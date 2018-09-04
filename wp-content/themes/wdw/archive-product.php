<?php

get_header();

get_template_part('page', 'header-shop');
?>
    <div id="page-intro">
        <h3>Store</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="product-archive">
        <?php
        $odd = true;
        while (have_posts()) {
            the_post(); ?>
            <div class="grid-container" id="product-<?php the_ID() ?>" class="single-product">
                <div style="position: relative"
                     class="grid-60 tablet-grid-60 mobile-grid-100<?php echo $odd ? '' : ' push-40 tablet-push-40' ?>">
                    <div class="product">
                        <a href="#product-<?php the_ID() ?>" class="product-image view-product">
                            <?php the_post_thumbnail('full') ?>
                        </a>

                        <div class="bottom-inset">
                            <div class="product-name"><?php the_title() ?></div>
                            <!--                    <div class="product-price">-->
                            <?php //echo get_post_meta(get_the_ID(), '_price', true) ?><!--</div>-->
                            <div class="product-actions">
                                <a href="#product-<?php the_ID() ?>"
                                   class="button button-outline-pink button-wide view-product">View Item</a>
                                <a href="<?php echo get_post_meta(get_the_ID(), '_url', true) ?>"
                                   class="button button-outline-pink button-wide">Buy Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="grid-40 tablet-grid-40 mobile-grid-100<?php echo $odd ? '' : ' pull-60 tablet-pull-60' ?>">
                    <?php
                    $versions = get_post_meta(get_the_ID(), '_versions', true);
                    if (!empty($versions)) {
                        $first = $versions[0];
                        ?>
                        <div class="product-version"><img
                            src="<?php echo wp_get_attachment_url($first['image']) ?>"></div><?php
                        if (count($versions) > 1) {
                            ?>
                            <select class="product-version-selector">
                                <?php foreach ($versions as $version) {
                                    ?>
                                    <option
                                    value="<?php echo wp_get_attachment_url($version['image']) ?>"><?php echo $version['name'] ?></option><?php
                                } ?>
                            </select>
                        <?php } ?>
                        <div class="std"><?php the_content() ?></div>
                        <?php
                    }
                    ?>
                </div>
                <div style="display: none;">
                    <div
                        id="gallery-<?php the_ID() ?>"><?php
                        $gallery = get_post_meta(get_the_ID(), '_gallery', true);
                        if (!empty($gallery)) {
                            $attachments = get_posts(array(
                                'post_type' => 'attachment',
                                'post__in' => explode(',', $gallery),
                                'posts_per_page' => -1
                            ));
                            foreach ($attachments as $attachment) {
                                ?>
                                <figure><?php echo wp_get_attachment_image($attachment->ID, 'medium', true) ?>
                                <?php if (!empty($attachment->post_excerpt)) { ?>
                                    <figcaption
                                        class="bottom-inset"><?php echo $attachment->post_excerpt ?></figcaption>
                                <?php } ?></figure><?php
                            }
                        } ?></div>
                </div>
            </div>
            <?php
            $odd = !$odd;
        } ?>
        <div class="grid-container">
            <?php the_posts_pagination(array(
                'prev_text' => __('<', 'wdw'),
                'next_text' => __('>', 'wdw'),
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
            )); ?></div>
    </div>

<?php
get_footer();