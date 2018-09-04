<?php
$dte_value = get_post_meta(get_the_ID(), '_date', true);
global $post;
?>
<div class="initiative grid-50 tablet-grid-50 mobile-grid-100">
    <div class="initiative-image">
        <?php the_post_thumbnail('medium') ?>
        <div class="bottom-inset">
            <?php echo get_post_meta(get_the_ID(), '_city', true) ?>
        </div>
    </div>

    <div class="initiative-city"><?php echo get_post_meta(get_the_ID(), '_city', true) ?></div>
    <div class="initiative-meta">
        <div
            class="initiative-date"><?php echo empty($dte_value) ? 'Date TBD' : date('F j, Y', strtotime($dte_value)) ?></div>
        <div class="initiative-location"><?php echo get_post_meta(get_the_ID(), '_location', true) ?></div>
    </div>
    <div class="initiative-info"><?php
        $extra_title = get_post_meta(get_the_ID(), '_extra_title', true);
        if (!empty($extra_title)){
            ?><div class="initiative-city"><?php echo $extra_title?></div><?php
        }
        echo apply_filters('the_content', $post->post_excerpt);
        ?></div>
    <div class="initiative-actions">
    <a href="<?php echo get_post_meta(get_the_ID(), '_register', true) ?>" class="button">Register</a>
    <a href="<?php the_permalink() ?>" class="button">Learn More</a>
    </div>
</div>