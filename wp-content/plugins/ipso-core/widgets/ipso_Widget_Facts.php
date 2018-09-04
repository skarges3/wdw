<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 8/22/13
 * Time: 8:49 AM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_Facts extends WP_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'widget_facts', 'description' => __('Fact image gallery'));
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct('facts', __('Facts'), $widget_ops, $control_ops);
    }

    function getFactData()
    {
        return new WP_Query(array('post_type' => 'ipso_fact'));
    }

    function widget($args, $instance)
    {
        wp_enqueue_script('jquery');
        $before_widget = null;
        $after_widget = null;
        $before_title = null;
        $after_title = null;
        extract($args);
        $fact_count = !empty($instance['fact_count']) ? $instance['fact_count'] : 2;
        $title = isset($instance['title']) ? $instance['title'] : '';
        $style = !empty($instance['img_type']) ? $instance['img_type'] : 'thumbnail';
        $more_link = isset($instance['more_link_text']) ? $instance['more_link_text'] : 'View More';
        $include_description = !empty($instance['include_desc']);
        $query = $this->getFactData();
        $mode = isset($instance['mode']) ? $instance['mode'] : 'ordered';
        $show_share_icons = isset($instance['share']);
        if (!$query->have_posts()) {
            return;
        }
        echo $before_widget;

        if (!empty($title)) {
            echo $before_title;
            echo $title;
            echo $after_title;
        }?>
        <div class="factswidget">
            <div id="facts">
                <?php
                $grid_width = (100 / $fact_count);
                $has_more = true;
                for ($i = 0; $i < $fact_count; $i++) {
                    if (!$query->have_posts()) {
                        $has_more = false;
                        break;
                    } else {
                        $query->the_post();
                    }
                    $link = get_permalink();
                    $this->outputSlide($include_description, $grid_width, $show_share_icons, $style, $link);
                }
                if ($has_more) {
                    $has_more = $query->have_posts();
                }
                ?>
            </div>
            <div class="clearfix"></div>
            <?php if ($mode == 'ordered') {
                if ($has_more) {
                    ?>
                    <script>
                        var facts_options = {
                            "include_description": "<?php echo $include_description?>",
                            "fact_count": "<?php echo $fact_count?>",
                            "show_share_icons": "<?php echo $show_share_icons?>",
                            "style": "<?php echo $style?>",
                            "link": "<?php echo $link?>",
                            "start": <?php echo $fact_count?>,
                            "total": <?php echo $query->post_count?>
                        };
                        function loadMore() {
                            jQuery.ajax({
                                url: "<?php echo get_template_directory_uri()?>/widgets/get_more_facts.php",
                                data: facts_options,
                                type: "GET",
                                dataType: "jsonp",
                                success: function (html) {
                                    facts_options.start += facts_options.fact_count;
                                    if (facts_options.start >= facts_options.total) {
                                        jQuery("#load-more-link").hide();
                                    }
                                    var $newFacts = jQuery(html);
                                    $newFacts.hide();
                                    jQuery("#facts").append($newFacts);
                                    $newFacts.fadeIn();
                                }
                            });
                            return false;
                        }
                    </script>
                    <footer>
                        <a href="javascript:loadMore()" id="load-more-link"><?php echo $more_link; ?></a>
                    </footer>
                <?php } ?>
            <?php } else { ?>
                <footer>
                    <a href="<?php echo get_option('siteurl') ?>/facts"><?php echo $more_link; ?></a>
                </footer>
            <?php } ?>
        </div>
        <?php
        wp_reset_postdata();
        echo $after_widget;
    }

    function outputSlide($include_description, $grid_width, $show_share_icons, $style, $link)
    {
        $ulink = urlencode($link);
        ?>
    <div class="grid-<?php echo $grid_width ?> tablet-grid-<?php echo $grid_width ?>">
        <section>
            <?php the_post_thumbnail($style) ?>
            <?php if ($include_description): ?>
                <?php the_content() ?>
            <?php endif; ?>
            <?php if ($show_share_icons): ?>
                <div class="share">
                    <a target="share"
                       href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $ulink ?>"><img
                            src="<?php echo get_template_directory_uri() ?>/images/share-facebook.gif"/></a>
                    <a target="share"
                       href="https://twitter.com/share?url=<?php echo $ulink ?>&text=<?php echo urlencode('Did you know...' . get_the_title() . ' #JustDriveWV ' . $link) ?>"><img
                            src="<?php echo get_template_directory_uri() ?>/images/share-twitter.gif" border="0"/></a>
                    <a href="mailto:?subject=Did you know...&amp;body=<?php echo "<a href='$link'>" . urlencode(get_the_post_thumbnail(get_the_ID(), $style) . "</a>") ?>"><img
                            src="<?php echo get_template_directory_uri() ?>/images/share-email.gif"
                            border="0"/></a>
                </div>
            <?php endif; ?>
        </section>
        </div><?php
    }

    function getFacts($start, $fact_count, $include_description, $show_share_icons, $style)
    {
        $query = $this->getFactData();
        $grid_width = (100 / $fact_count);
        $has_more = true;
        for ($i = 0; $i < $start; $i++) {
            if (!$query->have_posts()) {
                $has_more = false;
                break;
            } else {
                $query->the_post();
            }
        }
        if ($has_more) {
            for ($i = 0; $i < $fact_count; $i++) {
                if (!$query->have_posts()) {
                    break;
                } else {
                    $query->the_post();
                }
                $link = urlencode(get_permalink());
                $this->outputSlide($include_description, $grid_width, $show_share_icons, $style, $link);
            }
        }
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['fact_count'] = strip_tags($new_instance['fact_count']);
        $instance['img_type'] = strip_tags($new_instance['img_type']);
        $instance['mode'] = $new_instance['mode'];
        if (isset($new_instance['share'])) {
            $instance['share'] = true;
        } else {
            unset($instance['share']);
        }
        if (isset($new_instance['include_desc'])) {
            $instance['include_desc'] = 1;
        } else {
            unset($instance['include_desc']);
        }

        return $instance;
    }

    function form($instance)
    {
        $instance = wp_parse_args((array)$instance, array('title' => '', 'fact_count' => 5, 'img_type' => 'thumbnail', 'include_desc' => ''));
        $title = strip_tags($instance['title']);
        $img_type = strip_tags($instance['img_type']);
        $include_desc = $instance['include_desc'];
        $fact_count = esc_textarea($instance['fact_count']);
        $share = isset($instance['share']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>

        <p><label for="<?php echo $this->get_field_id('fact_count'); ?>"><?php _e('Max facts to Display:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('fact_count'); ?>"
                   name="<?php echo $this->get_field_name('fact_count'); ?>" type="number"
                   value="<?php echo esc_attr($fact_count); ?>"/></p>

        <p><input id="<?php echo $this->get_field_id('share'); ?>"
                  name="<?php echo $this->get_field_name('share'); ?>" type="checkbox"
                  value="1"<?php echo $share ? ' checked' : '' ?>/><label
                for="<?php echo $this->get_field_id('share'); ?>"><?php _e('Share Icons'); ?></label></p>

        <p><label for="<?php echo $this->get_field_id('img_type'); ?>"><?php _e('Size:'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('img_type'); ?>"
                    name="<?php echo $this->get_field_name('img_type'); ?>">
                <option value="full"<?php echo ($img_type == 'full') ? ' selected' : '' ?>>full</option>
                <?php
                $sizes = get_intermediate_image_sizes();
                foreach ($sizes as $size) {
                    ?>
                    <option value="<?php echo $size ?>"<?php echo ($img_type == $size) ? ' selected' : '' ?>><?php echo $size ?></option><?php
                }?>
            </select>
        </p>

        <p><label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Mode:'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('mode'); ?>"
                    name="<?php echo $this->get_field_name('mode'); ?>">
                <option value="random"<?php echo ($img_type == 'random') ? ' selected' : '' ?>>Random</option>
                <option value="ordered"<?php echo ($img_type == 'ordered') ? ' selected' : '' ?>>Ordered</option>
            </select>
        </p>

    <?php
    }

}

