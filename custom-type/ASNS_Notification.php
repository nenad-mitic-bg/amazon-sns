<?php

class ASNS_Notification
{

    public static function register_post_type()
    {
        register_post_type('asns_pn', array(
            'labels' => array(
                'name' => 'Push Notifications',
                'singular_name' => 'Push Notification'
            ),
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'menu_icon' => 'dashicons-megaphone',
            'supports' => array(
                'title',
                'editor'
            ),
            'rewrite' => false
        ));
    }

}

add_action('init', 'ASNS_Notification::register_post_type');
