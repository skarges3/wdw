<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>
<?php $ff = get_option('footer_form');
if (!empty($ff)){?>
<section id="fixed-footer">
    <div class="grid-container">
        <?php echo do_shortcode($ff)?>
    </div>
</section>
<?php }?>
    </div><!-- #main -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div id="footer">
            <div class="grid-container">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
        </div>
        <div class="site-info">
            <div class="grid-container all-inline">
                <div class="grid-100 tablet-grid-100 mobile-grid-100">
                    <?php do_action('credits'); ?>
                </div>
            </div>
        </div>
    </footer><!-- #colophon -->

</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>