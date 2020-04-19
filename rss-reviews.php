<?php
/*
Plugin Name: RSS Reviews
Plugin URI: https://github.com/frugan-it/rss-reviews
Description: Use this plugin to receive your latest reviews using an RSS feed from sites like TripAdvisor.
Version: 2.0.0
Author: Gregory Pearcey
Author URI: http://gregorypearcey.com/
License: Creative Commons Attribution-ShareAlike 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

require __DIR__ . '/vendor/autoload.php';

add_action( 'wp_enqueue_scripts', function() {

    wp_enqueue_style( 'rss-reviews', plugins_url( 'assets/css/rss-reviews.min.css', __FILE__));
    wp_enqueue_script( 'rss-reviews', plugins_url( 'assets/js/rss-reviews.min.js', __FILE__), array('jquery'), '2.0.0', true);
});

add_action( 'widgets_init', function() {

    return new \RssReviews\WP_Widget_RssReviews( 'rss_reviews' );
});

add_action( 'init', function() {

    add_shortcode( 'rssreviews', function($atts, $content = null) {

        extract(shortcode_atts( array('id' => ''), $atts));

        if( function_exists('fetch_feed') ) {

            $rss = fetch_feed($id);

            if(!is_wp_error($rss)) :
                $maxitems = $rss->get_item_quantity($amount);
                $rss_items = $rss->get_items(0, $maxitems);
            endif;

            if($maxitems == 0) {

                return '<p>'.esc_html('Feed not available.', 'rss_reviews').'</p>';

            } else {

                $middle = '';

                foreach ($rss_items as $item) {

                    $middle .= '<li>
			            <a href="'.$item->get_permalink().'"title="'.$item->get_date('j F Y @ g:i a').'"><h3>'.$item->get_title().'</h3></a>'
                        .$item->get_description().'</li><hr>';
                }

                $returnfeed = '<ul class="nobullets">'.$middle.'</ul>';

                return $returnfeed;
            }
        }
    });
});
