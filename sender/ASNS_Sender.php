<?php

class ASNS_Sender
{

    public static function enqueue_scripts()
    {
        $version = get_plugin_data(__DIR__ . '/../amazon-sns.php')['Version'];
        wp_enqueue_script('asns-sender', plugins_url('/sender.js', __FILE__), array('jquery', 'jquery-ui-dialog', 'jquery-ui-progressbar'), $version, true);
        wp_enqueue_style('wp-jquery-ui-dialog');
    }

    public static function send_notification()
    {
        $settings = ASNS_Settings::get_settings();
        $notification = self::get_notification(isset($_POST['id']) ? $_POST['id'] : 0);

        // send actual data to SNS

        self::send_ajax_response('');
    }

    public static function get_modal()
    {

        $topics = ASNS_Settings::get_settings()['topic_keys'];
        $topic_options = '';

        if ($topics) {

            $topic_options = '<option value="">Choose a topic</option>';

            foreach ($topics as $topic) {
                $topic_options .= '<option value="' . $topic . '">' . $topic . '</option>';
            }
        } else {
            $topic_options = '<option value="">No topics defined!</option>';
        }

        $search = array(
            '<!--ASNS_TOPICS-->',
            '<!--ASNS_LOADER-->'
        );

        $replace = array(
            $topic_options,
            plugins_url('/../img/ajax-loader.gif', __FILE__)
        );

        echo str_replace($search, $replace, file_get_contents(__DIR__ . '/modal.html'));
        wp_die();
    }

    /**
     * @param mixed $id
     * @return ASNS_Notification
     */
    private static function get_notification($id)
    {
        $post = get_post(intval($id));

        if ($post instanceof WP_Post) {
            return new ASNS_Notification($post);
        }

        return self::send_ajax_response('Notification post not found', false);
    }

    private static function send_ajax_response($message, $success = true)
    {
        echo json_encode(array(
            'status' => $success ? 'ok' : 'error',
            'message' => $message
        ));

        wp_die();
    }

}

add_action('admin_enqueue_scripts', 'ASNS_Sender::enqueue_scripts');
add_action('wp_ajax_asns_get_modal', 'ASNS_Sender::get_modal');
add_action('wp_ajax_asns_send', 'ASNS_Sender::send_notification');
