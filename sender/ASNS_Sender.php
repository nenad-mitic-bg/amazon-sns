<?php

class ASNS_Sender extends ASNS_Ajax
{

    public static function enqueue_scripts()
    {
        $version = get_plugin_data(__DIR__ . '/../amazon-sns.php')['Version'];
        wp_enqueue_script('asns-sender', plugins_url('/sender.js', __FILE__), array('jquery', 'jquery-ui-dialog', 'jquery-ui-progressbar'), $version, true);
        wp_enqueue_style('wp-jquery-ui-dialog');
    }

    public static function send_notification()
    {
        require_once __DIR__ . '/../aws.phar';
        $settings = ASNS_Settings::get_settings();
        self::validate_settings($settings);
        $topic = self::get_topic(isset($_POST['topicKey']) ? $_POST['topicKey'] : 0);
        $notification = self::get_notification(isset($_POST['id']) ? $_POST['id'] : 0);
        $message = '';
        $success = false;

        try {
            self::push_to_sns($notification, $topic[1], $settings['amazon_key'], $settings['amazon_secret'], $settings['amazon_region']);
            $message = 'Message Sent';
            $success = true;
        } catch (Aws\Sns\Exception\SnsException $ex) {
            $message = $ex->getStatusCode()
                    . ' / ' . $ex->getAwsErrorCode()
                    . ' / ' . $ex->getMessage();
        }

        self::update_notification($notification, $topic[0], $message);
        self::send_ajax_response($message, $success);
    }

    /**
     * @param ASNS_Notification $notification
     * @param string $topic_arn
     * @param string $key
     * @param string $secret
     * @param string $region
     * @return Guzzle\Service\Resource\Model
     */
    private static function push_to_sns(ASNS_Notification $notification, $topic_arn, $key, $secret, $region)
    {
        $args = array(
            'credentials' => array(
                'key' => $key,
                'secret' => $secret
            ),
            'region' => $region,
            'version' => '2010-03-31'
        );
        $sdk = new Aws\Sdk($args);
        $sns_client = $sdk->createSNS();

        return $sns_client->publish(array(
                    'TopicArn' => $topic_arn,
                    'Message' => $notification->get_pn_text()
        ));
    }

    private static function update_notification(ASNS_Notification $notifiacation, $topic_name, $status)
    {
        $meta = get_post_meta($notifiacation->get_id());

        // Increment count
        $current_count = isset($meta['sent_count'][0]) ? intval(isset($meta['sent_count'][0])) : 0;
        update_post_meta($notifiacation->get_id(), 'sent_count', $current_count + 1);

        // Set last sent
        $timestamp = current_time('timestamp');
        update_post_meta($notifiacation->get_id(), 'last_sent_at', $timestamp);

        // Add push to history
        $history = isset($meta['history']) ? unserialize($meta['history'][0]) : array();
        $format = get_option('date_format') . ' ' . get_option('time_format');
        $history[] = date_i18n($format, $timestamp)
                . ' to ' . $topic_name
                . ': ' . $status;
        update_post_meta($notifiacation->get_id(), 'history', $history);
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

}

add_action('admin_enqueue_scripts', 'ASNS_Sender::enqueue_scripts');
add_action('wp_ajax_asns_get_modal', 'ASNS_Sender::get_modal');
add_action('wp_ajax_asns_send', 'ASNS_Sender::send_notification');
