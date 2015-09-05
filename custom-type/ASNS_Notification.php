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

    /**
     * Adds columns to listing page
     * 
     * @param array $columns
     * @return array
     */
    public static function columns($columns)
    {
        $columns['asns_sent_count'] = 'Sent';
        $columns['asns_last_sent_at'] = 'Last sent at';
        return $columns;
    }

    /**
     * Prints value of the given custom column
     * 
     * @global WP_Post $post
     * @param string $col_name
     */
    public static function column_val($col_name)
    {
        global $post;

        switch ($col_name) {
            case 'asns_sent_count':
                $count = get_post_meta($post->ID, 'sent_count', true);
                echo $count ? $count . ' times' : 'Not sent';
                break;
            case 'asns_last_sent_at':
                $last_datetime = get_post_meta($post->ID, 'last_sent_at', true);
                echo $last_datetime ? $last_datetime : 'Never';
                break;
            default:
                echo "Unexpected column $col_name";
        }
    }

    /**
     * Adds a "send" link to "post actions" block
     * 
     * @global WP_Post $post
     * @param array $actions
     * @return array
     */
    public static function row_actions($actions)
    {
        global $post;

        if ($post->post_type == 'asns_pn') {
            $actions['asns_pn_send'] = '<a href="#"'
                    . ' class="asns-send"'
                    . ' data-id="' . $post->ID . '"'
                    . '>Send notification</a>';
        }

        return $actions;
    }

}

add_action('init', 'ASNS_Notification::register_post_type');
add_action('manage_asns_pn_posts_columns', 'ASNS_Notification::columns');
add_action('manage_asns_pn_posts_custom_column', 'ASNS_Notification::column_val');
add_action('post_row_actions', 'ASNS_Notification::row_actions');
