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

}
