<?php

namespace RssReviews;

class WP_Widget_RssReviews extends \WP_Widget
{
    public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
    {
        parent::__construct( $id_base, $name, $widget_options, $control_options );
    }

    public function form( $instance )
    {
        if ($instance) {

            $title = esc_attr($instance['title']);
            $text = esc_attr($instance['text']);
            $amount = esc_textarea($instance['amount']);

        } else {

            $title = '';
            $text = '';
            $amount = '';
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'rss_reviews'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('RSS Feed URL:', 'rss_reviews'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('text'); ?>"
                   name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('amount'); ?>"><?php _e('Amount of Reviews:', 'rss_reviews'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('amount'); ?>"
                   name="<?php echo $this->get_field_name('amount'); ?>" type="text" value="<?php echo $amount; ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['text'] = strip_tags($new_instance['text']);
        $instance['amount'] = strip_tags($new_instance['amount']);
        return $instance;
    }

    public function widget($args, $instance)
    {
        extract($args);

        $title = apply_filters('widget_title', $instance['title']);
        $text = $instance['text'];
        $amount = $instance['amount'];

        echo $before_widget;

        if (function_exists('fetch_feed')) {

            $rss = fetch_feed($text);

            if (!is_wp_error($rss)) :
                $maxitems = $rss->get_item_quantity($amount);
                $rss_items = $rss->get_items(0, $maxitems);
            endif;

            echo '<div id="slider" class="widget-text wp_widget_plugin_box">';

            if ($title) {

                echo $before_title . $title . $after_title;
            }

            echo '<ul class="rslides">';

            if ($maxitems == 0) echo '<p>'.esc_html('Feed not available', 'rss_reviews').'</p>';
            else foreach ($rss_items as $item) :

                echo '<li>' . $item->get_description() . '</li>';

            endforeach;

            echo '</ul>';
            echo '</div>';
        }

        echo $after_widget;
    }
}
