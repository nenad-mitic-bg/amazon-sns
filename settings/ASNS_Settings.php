<?php

class ASNS_Settings
{

    public static function add_admin_menu()
    {
        add_options_page('Amazon SNS', 'Amazon SNS', 'manage_options', 'asns', 'ASNS_Settings::print_options_page');
    }

    public static function init()
    {
        register_setting('asns', 'asns_settings');
        add_settings_section('asns_settings', 'Amazon SNS', 'asns_settings_callback', 'asns');
    }

    public static function print_options_page()
    {
        require __DIR__ . '/settings-panel.php';
    }

    public static function get_settings()
    {
        $defaults = array(
            'amazon_key' => '',
            'amazon_secret' => '',
            'amazon_region' => '',
            'app_keys' => array(),
            'app_arns' => array(),
            'topic_keys' => array(),
            'topic_arns' => array()
        );

        $options = get_option('asns_settings');

        if (!$options) {
            return $defaults;
        }

        return array_merge($defaults, $options);
    }

    public static function enqueue_scripts()
    {
        $version = get_plugin_data(__DIR__ . '/../amazon-sns.php')['Version'];
        wp_enqueue_script('asns-settings', plugins_url('/settings.js', __FILE__), array('jquery'), $version, true);
    }

}

add_action('admin_menu', 'ASNS_Settings::add_admin_menu');
add_action('admin_init', 'ASNS_Settings::init');
add_action('admin_enqueue_scripts', 'ASNS_Settings::enqueue_scripts');
