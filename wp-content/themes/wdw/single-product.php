<?php

get_header();

get_template_part('page', 'header-shop');
?>
    <div id="page-intro">
        <h3>Store</h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <div class="product-archive">
            <?php while (have_posts()) {
                the_post(); ?>
                <div class="grid-50 tablet-grid-50 mobile-grid-100">
                    <div class="product-image">
                        <?php the_post_thumbnail('full') ?>
                    </div>
                </div>
                <div id="primary" class="grid-50 tablet-grid-50 mobile-grid-100">

                    <h1 class="product-name"><?php the_title() ?></h1>

                    <div class="product-price"><?php echo get_post_meta(get_the_ID(), '_price', true) ?></div>
                    <?php the_content() ?>
                    <?php
                    $versions = get_post_meta(get_the_ID(), '_versions', true);
                    if (!empty($versions)) {
                        ?><br/><select class="product-version-selector">
                        <option value="<?php echo wp_get_attachment_url(get_post_thumbnail_id()) ?>">Select a Version
                        </option>
                        <?php foreach ($versions as $version) {
                            ?>
                            <option
                            value="<?php echo wp_get_attachment_url($version['image']) ?>"><?php echo $version['name'] ?></option><?php
                        } ?>
                        </select><?php
                    }
                    ?>
                    <br/>
                    <a href="<?php echo get_post_meta(get_the_ID(), '_url', true) ?>" class="button button-green">Buy Now</a>
                </div>
            <?php } ?>
        </div>

        <?php the_posts_pagination(array(
            'prev_text' => __('<', 'wdw'),
            'next_text' => __('>', 'wdw'),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
        )); ?>
    </div>
<?php
get_footer();