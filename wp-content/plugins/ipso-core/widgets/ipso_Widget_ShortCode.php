<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 8/21/13
 * Time: 2:03 PM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_ShortCode extends ipso_Widget_Base
{
    function __construct()
    {
        $widget_ops = array('classname' => 'widget_shortcode', 'description' => __('Arbitrary text or HTML (including support for short codes)'));
        $control_ops = array('width' => 400, 'height' => 350);
        WP_Widget::__construct('shortcode', __('HTML'), $widget_ops, $control_ops);
    }

    function form($instance)
    {
        $this->labeledInput($instance, 'title', 'Title');
        $this->labeledEditor($instance, 'text', 'HTML');
        $this->checkBoxInput($instance, 'filter', 'Automatically add paragraphs');
        $this->labeledInput($instance, 'classname', 'Class');
        $this->checkBoxInput($instance, 'global', 'Apply class to whole widget');
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        if ( current_user_can('unfiltered_html') )
            $instance['text'] =  $new_instance['text'];
        else
            $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
        if (isset($new_instance['filter'])){
            $instance['filter'] = $new_instance['filter'];
        }
        else{
            unset($instance['filter']);
        }

        $instance['classname'] = $new_instance['classname'];

        if (isset($new_instance['global'])){
            $instance['global'] = $new_instance['global'];
        }
        else{
            unset($instance['global']);
        }

        return $instance;
    }

    function widget($args, $instance)
    {
        $text = empty($instance['text']) ? '' : $instance['text'];
        if (isset($instance['filter']) && $instance['filter']){
           $text = wpautop($text);
        }
        $text = do_shortcode($text);
        $class_name = (isset($instance['classname'])) ? $instance['classname'] : $this->widget_options['classname'];
        $text = apply_filters('widget_text', $text, $instance);
        $global = !empty($instance['global']);
        $global_class = $global ? $class_name : '';
        $inner_class = $global ? 'widget-content' : $class_name;

        if (!empty($global_class)){
            echo "<div class='{$class_name}'>";
        }

        $this->before_widget($args);
        $this->widget_title($args, $instance);
        ?><div class="<?php echo $inner_class ?>"><?php echo $text; ?></div><?php
        $this->after_widget($args);

        if (!empty($global_class)){
            echo "</div>";
        }

    }
}

register_widget('ipso_Widget_ShortCode');
