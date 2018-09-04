<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 8/22/13
 * Time: 8:49 AM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_Events extends WP_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'widget_events', 'description' => __('Index of upcoming events'));
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct('events', __('Events'), $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        $before_widget = null;
        $after_widget = null;
        extract($args);
        $event_count = !empty($instance['event_count']) ? $instance['event_count'] : 5;
        $title = !empty($instance['title']) ? $instance['title'] : 'Events';
        $query = new WP_Query(array('post_type' => 'ipso_event'));
        if (!$query->have_posts()) {
            return;
        }
        echo $before_widget;
        ?>
        <h3 class="widget-title"><?php echo $title ?></h3>
        <?php for ($i = 0; $i < $event_count; $i++):
        if (!$query->have_posts()) {
            break;
        } else {
            $query->the_post();
        }
        $dateTimeText = getEventDate();
        ?>
        <section>
            <h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>

            <div class="date"><?php echo $dateTimeText ?></div>
            <p><?php echo get_post_meta(get_the_ID(), 'location', true) ?></p>
        </section>
    <?php endfor ?>
        <footer>
            <a href="<?php echo get_post_type_archive_link('ipso_event') ?>">View All</a>
        </footer>
        <?php
        wp_reset_postdata();
        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['event_count'] = strip_tags($new_instance['event_count']);
        return $instance;
    }

    function form($instance)
    {
        $instance = wp_parse_args((array)$instance, array('title' => '', 'event_count' => 5));
        $title = strip_tags($instance['title']);
        $event_count = esc_textarea($instance['event_count']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>

        <p><label for="<?php echo $this->get_field_id('event_count'); ?>"><?php _e('Max Events to Display:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('event_count'); ?>"
                   name="<?php echo $this->get_field_name('event_count'); ?>" type="number"
                   value="<?php echo esc_attr($event_count); ?>"/></p>
    <?php
    }

}

/**
 * @return string
 */
function getEventDate()
{
    $date = get_post_meta(get_the_ID(), 'date', true);
    $dateTS = strtotime($date);
    $date = date('F j, Y', $dateTS);
    $time = get_post_meta(get_the_ID(), 'time', true);
    if (!empty($time)) {
        $hours = intval(substr($time, 0, 2));
        $pm = false;
        if ($hours == 0) {
            $hours = '12';
        } else if ($hours > 11) {
            $pm = true;
            if ($hours > 12) {
                $hours -= 12;
            }
        }
        $time = $hours . substr($time, 2) . ($pm ? 'PM' : 'AM');
    }

    $dateTimeText = $date . ' ' . $time;
    return $dateTimeText;
}


//register_widget('ipso_Widget_Events');
