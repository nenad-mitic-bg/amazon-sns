<?php

/*
  Plugin Name: Amazon SNS
  Description: A plugin for sending push notifications via Amazon SNS (mainly to Android and Apple devices)
  Plugin URI: https://github.com/shonezlo/amazon-sns
  Version: 1.0
  Author: Shone Zlo
  Author URI: https://github.com/shonezlo
  License: WTFPL
 */

require_once __DIR__ . '/settings/ASNS_Settings.php';
require_once __DIR__ . '/custom-type/ASNS_Device.php';
require_once __DIR__ . '/custom-type/ASNS_Notification.php';
require_once __DIR__ . '/ASNS_Ajax.php';
require_once __DIR__ . '/sender/ASNS_Sender.php';
require_once __DIR__ . '/registrator/ASNS_Device_Registration_Handler.php';

add_action('init', 'asns_session_start');
add_action('wp_logout', 'asns_session_end');
add_action('wp_login', 'asns_session_end');

function asns_session_start()
{
    if (!session_id()) {
        session_start();
    }
}

function asns_session_end()
{
    session_destroy();
}
