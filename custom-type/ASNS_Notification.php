<?php

class ASNS_Notification
{

    /**
     * @var WP_Post
     */
    private $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function get_apns_payload()
    {
        return array(
            'aps' => array(
                'alert' => $this->get_pn_text()
            )
        );
    }

    public function get_gcm_payload()
    {
        return array(
            'message' => $this->get_pn_text()
        );
    }

    /**
     * @return integer
     */
    public function get_id()
    {
        return $this->post->ID;
    }

    public function get_pn_text()
    {
        $ret = $this->post->post_title;
        $content = trim($this->post->post_content);

        if ($content) {
            $ret .= ': ' . $content;
        }

        return $ret;
    }

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
                $count = intval(get_post_meta($post->ID, 'sent_count', true));

                if (!$count) {
                    echo 'Not sent';
                } else if ($count === 1) {
                    echo '1 time';
                } else {
                    echo $count . ' times';
                }

                break;
            case 'asns_last_sent_at':
                $timestamp = get_post_meta($post->ID, 'last_sent_at', true);

                if ($timestamp) {
                    $format = get_option('date_format') . ' ' . get_option('time_format');
                }

                echo $timestamp ? date_i18n($format, $timestamp) : 'Never';
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

    public static function admin_notices()
    {
        if (!isset($_SESSION['asns_notice'])) {
            return;
        }

        $class = $_SESSION['asns_notice']['status'] == 'ok' ? 'updated' : 'error';
        ?>

        <div class="<?php echo $class ?> notice is-dismissible">
            <p><?php echo $_SESSION['asns_notice']['message'] ?></p>
            <button type="button" class="notice-dismiss"></button>
        </div>

        <?php
        unset($_SESSION['asns_notice']);
    }

    public static function register_meta_boxes()
    {
        add_meta_box('asns-history', 'Sending Log', 'ASNS_Notification::history_meta_box');
    }

    /**
     * @param WP_Post $post
     */
    public static function history_meta_box($post)
    {
        $history = get_post_meta($post->ID, 'history', true);

        if (!$history) {
            $history = array('Not sent yet');
        }

        foreach ($history as $entry) {
            echo "<p>$entry</p>";
        }
    }

}

add_action('init', 'ASNS_Notification::register_post_type');
add_action('manage_asns_pn_posts_columns', 'ASNS_Notification::columns');
add_action('manage_asns_pn_posts_custom_column', 'ASNS_Notification::column_val');
add_action('post_row_actions', 'ASNS_Notification::row_actions');
add_action('admin_notices', 'ASNS_Notification::admin_notices');
add_action('add_meta_boxes_asns_pn', 'ASNS_Notification::register_meta_boxes');
