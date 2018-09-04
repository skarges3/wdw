<?php

/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 10/8/13
 * Time: 1:04 PM
 * To change this template use File | Settings | File Templates.
 */
class ipso_Widget_Responsive_Grid extends ipso_Widget_List
{
    public function __construct()
    {
        // widget actual processes
        $widget_ops = array('classname' => 'ipso_grid', 'description' => __('Creates a responsive grid with open HTML editing functionality'));
        $control_ops = array('width' => 600, 'height' => 650);
        WP_Widget::__construct('ipso-grid', __('Responsive Grid'), $widget_ops, $control_ops);
    }

    protected function get_before_widget($args, $instance, $widget, $index)
    {
        $d_cols = isset($instance['desktop']) ? $instance['desktop'] : 4;
        $t_cols = isset($instance['tablet']) ? $instance['tablet'] : 4;
        $m_cols = isset($instance['mobile']) ? $instance['mobile'] : 2;
        $colspan = isset($instance['colspans'][$index]) ? $instance['colspans'][$index] : 1;

        $grid_d = 'grid-' . $this->get_grid_percent($d_cols / $colspan);
        $grid_t = 'tablet-grid-' . $this->get_grid_percent($t_cols / $colspan);
        $grid_m = 'mobile-grid-' . $this->get_grid_percent($m_cols / $colspan);

        return "<div class='ipso-grid-cell $grid_d $grid_t $grid_m'>" . parent::get_before_widget($args, $instance, $widget, $index);
    }

    protected function get_after_widget($args, $instance, $widget, $index)
    {
        return parent::get_after_widget($args, $instance, $widget, $index) . '</div>';
    }

    public function widget($args, $instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : null;

        if (isset($args['before_widget'])) {
            echo $args['before_widget'];
        }

        if (isset($instance['include_gc'])) {
            ?><div class='grid-container'><?php
        }

        if (!empty($title)) {
            if (isset($args['before_title'])) {
                echo $args['before_title'];
            }

            echo $title;

            if (isset($args['after_title'])) {
                echo $args['after_title'];
            }
        }

        parent::render_widgets($args, $instance);

        if (isset($instance['include_gc'])) {
            ?></div><?php
        }

        if (isset($args['after_widget'])) {
            echo $args['after_widget'];
        }


    }

    private function get_grid_percent($num)
    {
        $v = round(100 / $num);
        if ($v % 33 == 0){
            return $v;
        }
        $v -= $v % 5;
        return $v;
    }

    protected function before_labeled_widget_form($num, $item, $instance)
    {
        $colspans = isset($instance['colspans'][$num]) ? $instance['colspans'][$num] : 1;
        $id = $this->get_field_id("colspans") . "_{$num}_";
        $name = $this->get_field_name("colspans") . "[{$num}]";
        ?><p><?php
        $this->label($id, 'Columns');
        $this->inputField($id, $name, $colspans, 'number', '');
        ?></p><?php
    }

    public function form($instance)
    {


        $this->labeledInput($instance, 'title', "Title", '');
        $this->checkBoxInput($instance, 'include_gc', 'Include Grid Container');


        parent::render_widgets_form($instance);

        ?>
        <h4>Columns</h4>
        <input type="hidden" name="<?php echo $this->get_field_name('move_up') ?>" class="move-up"/>
        <input type="hidden" name="<?php echo $this->get_field_name('move_down') ?>" class="move-down"/>
        <p>
            <?php

            $this->label($this->get_field_id('desktop'), 'Desktop');
            $this->inputField($this->get_field_id('desktop'), $this->get_field_name('desktop'), isset($instance['desktop']) ? $instance['desktop'] : '', 'number', null, 'min="1" max="10"');

            $this->label($this->get_field_id('tablet'), 'Tablet');
            $this->inputField($this->get_field_id('tablet'), $this->get_field_name('tablet'), isset($instance['tablet']) ? $instance['tablet'] : '', 'number', null, 'min="1" max="10"');

            $this->label($this->get_field_id('mobile'), 'Mobile');
            $this->inputField($this->get_field_id('mobile'), $this->get_field_name('mobile'), isset($instance['mobile']) ? $instance['mobile'] : '', 'number', null, 'min="1" max="10"');
            ?>
        </p>
    <?php
    }

    protected function update_additional_fields($new_instance, $old_instance)
    {
        $instance = $this->updateFields(array('title', 'desktop', 'tablet', 'mobile'), $new_instance);
        if (isset($new_instance['include_gc'])) {
            $instance['include_gc'] = 1;
        }
        if (isset($new_instance['colspans'])) {
            $instance['colspans'] = $new_instance['colspans'];
        }
        return $instance;
    }
}

register_widget('ipso_Widget_Responsive_Grid');
