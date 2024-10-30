<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Base;

use \Inc\Base\BaseController;

class Session extends BaseController {
    public function register() {
        add_action('init', array($this, 'initSession'), 1);
//        add_action('wp_logout', array($this, 'endSession'), 1);
//        add_action('wp_login', array($this, 'endSession'), 1);
    }

    public function initSession() {
//        echo session_id();
        if (!session_id()) {
            session_start();
        }
    }

//    public function endSession() {
//        session_destroy();
//    }

}