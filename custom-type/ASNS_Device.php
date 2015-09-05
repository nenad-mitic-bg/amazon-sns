<?php

class ASNS_Device
{

    public static function register_post_type()
    {
        register_post_type('asns_device', array(
            'label' => 'ASNS Devices',
            'public' => false,
            'supports' => array(
                'title'
            )
        ));
    }

    /**
     * Checks if the given device id is already registered
     * @param string $device_id
     * @return WP_Post
     */
    public static function find($device_id)
    {
        $devices = get_posts(array(
            'post_type' => 'asns_device',
            'nopaging' => true,
            'post_status' => 'any',
            'meta_key' => 'asns_device_id',
            'meta_value' => $device_id
        ));

        if ($devices) {
            return $devices[0];
        }

        return null;
    }

    /**
     * @param string $app
     * @param string $device_id
     * @param string $token
     * @param string $endpoint_arn
     * @return integer
     */
    public static function create($app, $device_id, $token, $endpoint_arn)
    {
        $post_id = wp_insert_post(array(
            'post_title' => uniqid("asns_{$app}_"),
            'post_type' => 'asns_device',
            'post_status' => 'private'
        ));

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'asns_device_id', $device_id);
            update_post_meta($post_id, 'asns_device_token', $token);
            update_post_meta($post_id, 'asns_endpoint_arn', $endpoint_arn);
        }

        return $post_id;
    }

    public static function set_token($post_id, $token)
    {
        update_post_meta($post_id, 'asns_device_token', $token);
    }

    public static function set_arn($post_id, $arn)
    {
        update_post_meta($post_id, 'asns_endpoint_arn', $arn);
    }

}

add_action('init', 'ASNS_Device::register_post_type');
