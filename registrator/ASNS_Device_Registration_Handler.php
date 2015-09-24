<?php

class ASNS_Device_Registration_Handler extends ASNS_Ajax
{

    public static function register_device()
    {
        self::validate_settings(ASNS_Settings::get_settings());
        $topic_arn = self::get_topic()[1];
        $app = self::get_app();
        $token = self::get_request_param('token');

        if (!$token) {
            return self::send_ajax_response('Device token not found', false, false);
        }

        // Register device with WP
        $device_id = ASNS_Device::create($app[0], $token);

        // Register device with Amazon
        $client = self::get_client();

        try {
            $device_arn = $client->createPlatformEndpoint([
                        'PlatformApplicationArn' => $app[1],
                        'Token' => $token
                    ])
                    ->get('EndpointArn');

            // Update device ARN
            ASNS_Device::set_arn($device_id, $device_arn);

            // Subscribe device to topic
            $client->subscribe([
                'Endpoint' => $device_arn,
                'TopicArn' => $topic_arn,
                'Protocol' => 'application'
            ]);
        } catch (Aws\Sns\Exception\SnsException $ex) {
            $message = $ex->getStatusCode()
                    . ' / ' . $ex->getAwsErrorCode()
                    . ' / ' . $ex->getMessage();

            self::send_ajax_response($message, false, false);
        }

        self::send_ajax_response('ok', true, false);
    }

}

add_action('wp_ajax_asns_register_device', 'ASNS_Device_Registration_Handler::register_device');
add_action('wp_ajax_nopriv_asns_register_device', 'ASNS_Device_Registration_Handler::register_device');
