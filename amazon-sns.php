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
