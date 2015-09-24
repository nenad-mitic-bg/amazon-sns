<?php

class ASNS_Ajax
{

    public static function send_ajax_response($message, $success = true, $adminNotice = true)
    {
        $data = array(
            'status' => $success ? 'ok' : 'error',
            'message' => $message
        );

        if ($adminNotice) {
            $_SESSION['asns_notice'] = $data;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        wp_die();
    }

    /**
     * Returns a parameter from the request. Order: POST, GET
     * 
     * @param string $name
     * @param mixed $default
     * @return string
     */
    protected static function get_request_param($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }

    protected static function validate_settings($settings)
    {
        if (!$settings['amazon_key'] || !$settings['amazon_secret']) {
            return self::send_ajax_response('Amazon credentials not set!', false);
        }

        if (!$settings['amazon_region']) {
            return self::send_ajax_response('Amazon region not set!', false);
        }
    }

    protected static function get_topic()
    {
        $topic = ASNS_Settings::get_topic(self::get_request_param('topic', false));

        if (!$topic) {
            return self::send_ajax_response('Topic not found', false);
        }

        return $topic;
    }

    protected static function get_app()
    {
        $app = ASNS_Settings::get_application(self::get_request_param('app'));

        if (!$app) {
            return self::send_ajax_response('Application not found', false);
        }

        return $app;
    }

    protected static function get_client()
    {
        require_once __DIR__ . '/aws.phar';
        $settings = ASNS_Settings::get_settings();

        $args = array(
            'credentials' => array(
                'key' => $settings['amazon_key'],
                'secret' => $settings['amazon_secret']
            ),
            'region' => $settings['amazon_region'],
            'version' => '2010-03-31'
        );

        return (new Aws\Sdk($args))->createSNS();
    }

}
