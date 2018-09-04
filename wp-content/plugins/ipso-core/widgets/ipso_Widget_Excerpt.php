<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 7/19/13
 * Time: 1:25 PM
 * To change this template use File | Settings | File Templates.
 */

class ipso_Widget_Excerpt extends ipso_Widget_Base
{
    public function __construct()
    {
        // widget actual processes
        parent::__construct('excerpt_widget', 'Excerpt Widget', array('description' => __('A Widget to show the excerpt for a specific content item', 'text_domain')));
    }

    public function widget($args, $instance)
    {
        // outputs the content of the widget
        $title =  $instance['title'];
        $secondary_title = $instance['secondary-title'];
        $search = $instance['query'];
        $size = isset($instance['thumbnail-size']) ? $instance['thumbnail-size'] : '';

        $this->before_widget($args);
        $this->widget_title($args, $instance);
        if (!empty($search)) {
            if (strpos($search, 'page_id=')===0){
                $search = substr($search, 8);
            }
            //wpml.org support
            if (function_exists('icl_object_id')){
                $search = icl_object_id($search, 'page', true);
            }
            $query = new WP_Query('page_id='.$search);
            if ($query->have_posts()) {
                $query->the_post();
                $thumbnail_id = get_post_meta(get_the_ID(), '_thumbnail_id', true);
                $class = 'square';
                ?><div class="image-wrapper"><?php
                if (!empty($thumbnail_id)) {
                    $attrs = wp_get_attachment_image_src($thumbnail_id, $size);
                    $src = $attrs[0];
                    if ($attrs[1]!=$attrs[2]){
                        $class = $attrs[1] < $attrs[2] ? 'vertical' : 'horizontal';
                    }
                } else {
                    if (!empty($size)) {
                        global $_wp_additional_image_sizes;
                        $width = "100%";
                        $height = "100%";
                        if (isset($_wp_additional_image_sizes[$size])) {
                            $info = $_wp_additional_image_sizes[$size];
                            $width = $info['width'];
                            $height = $info['height'];
                        }
                        $src = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
                    }
                }
                ?><img class="<?php echo $class?>" width="<?php echo $width ?>" height="<?php echo $height ?>"
       src="<?php echo $src?>"><?php
                ?>
                </div>
                <div class="post-excerpt">
                <section><?php
                    if (isset($args['before_secondary_title'])){
                        echo $args['before_secondary_title'];
                    }
                    if (!empty($secondary_title)) {
                        echo $secondary_title;
                    } else {
                        echo get_the_title();
                    }
                    if (isset($args['after_secondary_title'])){
                        echo $args['after_secondary_title'];
                    }
                    the_excerpt();
                    ?></section></div><?php
            } else {
                echo 'No Content Found';
            }
            wp_reset_postdata();
        }

        $this->after_widget($args);
    }

    public function form($instance)
    {
        // outputs the options form on admin
        $this->labeledInput($instance, 'title', 'Title');
        $this->labeledInput($instance, 'secondary-title', 'Secondary Title');
        $this->labeledSelect($instance, 'thumbnail-size', 'Image Format', get_intermediate_image_sizes());

        $this->labeledPageSelect($instance, 'query', 'Referenced Content');
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        foreach (array('title', 'secondary-title', 'query', 'thumbnail-size') as $field) {
            $instance[$field] = (!empty($new_instance[$field])) ? strip_tags($new_instance[$field]) : '';
        }
        return $instance;
    }
}

register_widget('ipso_Widget_Excerpt');
