<?php

get_header();

get_template_part('page', 'header-events');
$this_type = get_queried_object();

?>
    <div id="page-intro">
        <h3><?php echo $this_type->name?></h3>
        <?php do_action('breadcrumbs') ?>
    </div>
    <div class="grid-container">
        <?php
        $types = get_terms('initiative_type');
        ?>
        <div class="term-links"><?php
            foreach ($types as $type) {
                $selected = $type->term_id == $this_type->term_id;
                ?><a class="term-link<?php echo $selected ? ' current' : '' ?>"
                     href="<?php echo get_term_link($type) ?>"><?php echo $type->name ?></a><?php
            }
            ?>
        </div>
        <?php if (!empty($this_type)){
            ?><div class="term-description grid-100 tablet-grid-100 mobile-grid-100">
                <?php echo apply_filters('the_content', $this_type->description);?>
            </div><?php
        }?>
        <div class="initiative-archive">
            <?php while (have_posts()) {
                the_post();
                get_template_part('content', 'initiative-intro');
            } ?>
        </div><h2 style="margin-bottom: 0; clear: both;">Our Promise</h2>
        <?php
        if (!empty($this_type)){
            ?><div class="initiative-footer grid-100 tablet-grid-100 mobile-grid-100" style="padding: 30px 0;"><?php
            echo apply_filters('the_content', get_metadata('initiative_type', $this_type->term_id, 'footer',true));
            ?></div><?php
        }
        ?>
        <?php the_posts_pagination(array(
            'prev_text' => __('<', 'wdw'),
            'next_text' => __('>', 'wdw'),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'wdw') . ' </span>',
        )); ?>
    </div>
<?php
get_footer();